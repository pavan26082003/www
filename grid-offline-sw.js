/* Professional Space + Circle Connect only.
   Every other request on this site is ignored. */
var CACHE = 'efactor-grid-shell-v1';

var CDN_HOSTS = {
  'cdn.tailwindcss.com': true,
  'fonts.googleapis.com': true,
  'fonts.gstatic.com': true,
  'cdnjs.cloudflare.com': true
};

function stripIndex(pathname) {
  return pathname.replace(/^\/index\.php(?=\/|$)/, '') || '/';
}

function appArea(pathname) {
  var rel = stripIndex(pathname).replace(/\/+$/, '') || '/';
  if (rel === '/grid' || /^\/grid\/(profile|messaging|team|network|jobs|demo_loader)$/.test(rel)) {
    return 'shell';
  }
  if (
    rel === '/grid-chat' ||
    rel === '/grid_chat' ||
    /^\/grid-chat\/media(\/[^/]+)?$/.test(rel) ||
    /^\/grid_chat\/media(\/[^/]+)?$/.test(rel)
  ) {
    return 'shell';
  }
  if (/^\/grid-chat\/assets\//.test(rel) || /^\/grid_chat\/assets\//.test(rel)) {
    return 'asset';
  }
  return '';
}

function shellKey(url) {
  var copy = new URL(url);
  copy.search = '';
  copy.hash = '';
  return copy.toString();
}

function isSignIn(url) {
  return /signin|login/i.test(url);
}

function referrerIsShell(request) {
  try {
    return request.referrer && appArea(new URL(request.referrer).pathname) === 'shell';
  } catch (e) {
    return false;
  }
}

function storableCopy(response) {
  var headers = new Headers(response.headers);
  headers.set('Cache-Control', 'private, max-age=0');
  headers.delete('Pragma');
  headers.delete('Expires');
  headers.delete('Set-Cookie');
  return response.blob().then(function (body) {
    return new Response(body, {
      status: response.status,
      statusText: response.statusText,
      headers: headers
    });
  });
}

async function put(cacheKey, response) {
  try {
    if (!response) return;
    var cache = await caches.open(CACHE);
    if (response.type === 'opaque') {
      await cache.put(cacheKey, response.clone());
      return;
    }
    if (!response.ok || response.status !== 200 || response.redirected || isSignIn(response.url)) return;
    var copy = await storableCopy(response.clone());
    await cache.put(cacheKey, copy);
  } catch (e) {}
}

async function networkFirst(request, cacheKey) {
  var cache = await caches.open(CACHE);
  try {
    var fresh = await fetch(request);
    await put(cacheKey, fresh);
    return fresh;
  } catch (err) {
    var cached = await cache.match(cacheKey);
    if (cached) return cached;
    throw err;
  }
}

self.addEventListener('install', function (event) {
  event.waitUntil(self.skipWaiting());
});

self.addEventListener('activate', function (event) {
  event.waitUntil(
    caches.keys().then(function (keys) {
      return Promise.all(
        keys.map(function (key) {
          if (key !== CACHE && key.indexOf('efactor-grid-shell-') === 0) return caches.delete(key);
          return null;
        })
      );
    }).then(function () {
      return self.clients.claim();
    })
  );
});

self.addEventListener('message', function (event) {
  var data = event.data || {};
  if (data.type === 'cache-url' && data.url) {
    var url;
    try { url = new URL(data.url); } catch (e) { return; }
    if (url.origin !== self.location.origin || appArea(url.pathname) !== 'shell') return;
    event.waitUntil(
      fetch(shellKey(url), { credentials: 'include', cache: 'no-store', redirect: 'follow' }).then(function (res) {
        return put(shellKey(url), res);
      }).catch(function () {})
    );
    return;
  }
  if (data.type !== 'cache-assets' || !data.urls) return;
  event.waitUntil(Promise.all(data.urls.slice(0, 40).map(function (item) {
    var assetUrl;
    try { assetUrl = new URL(item); } catch (e) { return null; }
    var same = assetUrl.origin === self.location.origin;
    if (same) {
      if (appArea(assetUrl.pathname) !== 'asset') return null;
    } else if (!CDN_HOSTS[assetUrl.hostname]) {
      return null;
    }
    return fetch(assetUrl.toString(), same ? { credentials: 'include', cache: 'no-store' } : { mode: 'no-cors', cache: 'no-store' })
      .then(function (res) { return put(assetUrl.toString(), res); })
      .catch(function () {});
  })));
});

self.addEventListener('fetch', function (event) {
  try {
    var request = event.request;
    if (request.method !== 'GET') return;
    var url = new URL(request.url);
    if (url.pathname === '/grid-offline.js' || url.pathname === '/grid-offline-sw.js') return;
    if (/\/(?:grid|grid-chat|grid_chat)\/api\//.test(url.pathname)) return;

    if (url.origin === self.location.origin) {
      var area = appArea(url.pathname);
      if (area === 'shell' && request.mode === 'navigate') {
        event.respondWith(networkFirst(request, shellKey(url)));
      } else if (area === 'asset') {
        event.respondWith(networkFirst(request, request.url));
      }
      return;
    }

    if (!CDN_HOSTS[url.hostname] || !referrerIsShell(request)) return;
    if (url.hostname === 'cdnjs.cloudflare.com' && url.pathname.indexOf('/ajax/libs/font-awesome/') !== 0) return;

    event.respondWith(networkFirst(request, request.url));
  } catch (e) {}
});
