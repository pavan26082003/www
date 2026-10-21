/* Saves Professional Space and Circle Connect API reads on this device,
   and queues text messages when the network is down. Other requests are unchanged. */
(function () {
  if (window.__gridOfflineInstalled || typeof window.fetch !== 'function') return;
  window.__gridOfflineInstalled = true;

  var nativeFetch = window.fetch.bind(window);
  var DB_NAME = 'efactor-grid-offline';
  var MAX_BODY = 1500000;
  var flushing = false;

  function currentUserId() {
    try {
      var chat = window.__APP_DATA__ && window.__APP_DATA__.currentUser;
      if (chat && chat.id != null && chat.id !== '') return 'user:' + chat.id;
      var grid = window.__GRID_DATA__ && window.__GRID_DATA__.user;
      if (grid && grid.id != null && grid.id !== '') return 'user:' + grid.id;
    } catch (e) {}
    return '';
  }

  function absUrl(input) {
    try {
      if (typeof input === 'string') return new URL(input, window.location.href);
      if (input && input.url) return new URL(input.url, window.location.href);
    } catch (e) {}
    return null;
  }

  function isOurGet(url, method) {
    if (method !== 'GET' || !url || url.origin !== window.location.origin) return false;
    return /\/grid\/api\//.test(url.pathname) ||
      /\/grid-chat\/api\//.test(url.pathname) ||
      /\/grid_chat\/api\//.test(url.pathname);
  }

  function openDb() {
    return new Promise(function (resolve, reject) {
      if (!window.indexedDB) {
        reject(new Error('no idb'));
        return;
      }
      var req = indexedDB.open(DB_NAME, 1);
      req.onupgradeneeded = function () {
        var db = req.result;
        if (!db.objectStoreNames.contains('responses')) db.createObjectStore('responses');
        if (!db.objectStoreNames.contains('outbox')) db.createObjectStore('outbox', { keyPath: 'id', autoIncrement: true });
      };
      req.onsuccess = function () { resolve(req.result); };
      req.onerror = function () { reject(req.error); };
    });
  }

  function withStore(name, mode, fn) {
    return openDb().then(function (db) {
      return new Promise(function (resolve, reject) {
        var tx = db.transaction(name, mode);
        var result = fn(tx.objectStore(name));
        tx.oncomplete = function () { resolve(result); };
        tx.onerror = function () { reject(tx.error); };
        tx.onabort = function () { reject(tx.error || new Error('aborted')); };
      });
    });
  }

  function cacheKey(userId, url) {
    return userId + '\n' + url.origin + url.pathname + url.search;
  }

  function saveResponse(userId, url, text) {
    if (!userId || !text || text.length > MAX_BODY) return Promise.resolve();
    var key = cacheKey(userId, url);
    return withStore('responses', 'readwrite', function (store) {
      store.put({ body: text, savedAt: Date.now() }, key);
    }).catch(function () {});
  }

  function readResponse(userId, url) {
    if (!userId) return Promise.resolve(null);
    var key = cacheKey(userId, url);
    return openDb().then(function (db) {
      return new Promise(function (resolve) {
        var tx = db.transaction('responses', 'readonly');
        var req = tx.objectStore('responses').get(key);
        req.onsuccess = function () { resolve(req.result || null); };
        req.onerror = function () { resolve(null); };
      });
    }).catch(function () { return null; });
  }

  function jsonResponse(payload, status) {
    return new Response(JSON.stringify(payload), {
      status: status || 200,
      headers: { 'Content-Type': 'application/json', 'X-Offline-Cache': '1' }
    });
  }

  function note(text) {
    var el = document.getElementById('grid-offline-note');
    if (!el) {
      el = document.createElement('div');
      el.id = 'grid-offline-note';
      el.setAttribute('role', 'status');
      el.style.cssText = 'position:fixed;left:12px;right:12px;bottom:12px;z-index:2147483000;margin:0 auto;max-width:420px;padding:10px 14px;border-radius:10px;background:#111827;color:#fff;font:500 13px/1.4 sans-serif;box-shadow:0 8px 24px rgba(0,0,0,.25);';
      (document.body || document.documentElement).appendChild(el);
    }
    el.textContent = text;
  }

  function clearNote() {
    var el = document.getElementById('grid-offline-note');
    if (el) el.remove();
  }

  function formEntries(body) {
    if (typeof FormData === 'undefined' || !(body instanceof FormData)) return null;
    var entries = [];
    var pair;
    var iterator = body.entries();
    while (!(pair = iterator.next()).done) {
      if (typeof pair.value[1] !== 'string') return null;
      entries.push([pair.value[0], pair.value[1]]);
    }
    return entries;
  }

  function fieldsOf(entries) {
    var out = {};
    entries.forEach(function (pair) { out[pair[0]] = pair[1]; });
    return out;
  }

  function queueItem(userId, url, kind, record) {
    record.userId = userId;
    record.url = url.toString();
    record.kind = kind;
    record.createdAt = Date.now();
    return withStore('outbox', 'readwrite', function (store) {
      store.add(record);
    });
  }

  function chatQueuedResponse(entries) {
    var fields = fieldsOf(entries);
    var now = new Date().toISOString();
    var metadata = null;
    if (fields.metadata) {
      try { metadata = JSON.parse(fields.metadata); } catch (e) { metadata = null; }
    }
    return jsonResponse({
      success: true,
      offlineQueued: true,
      message: 'Saved on this device. It will send when you are back online.',
      data: {
        message: {
          id: 'offline-' + (fields.client_id || now),
          clientId: fields.client_id || '',
          conversationId: Number(fields.conversation_id),
          type: 'outgoing',
          messageType: fields.message_type || 'text',
          text: fields.body || '',
          createdAt: now,
          isRead: false,
          isDeleted: false,
          attachments: [],
          metadata: metadata
        }
      }
    });
  }

  function rememberFailure(userId, url, method, init) {
    if (method !== 'POST' || !userId) return Promise.resolve(null);
    var path = url.pathname;
    if (/\/chat\/send$/.test(path)) {
      var entries = formEntries(init && init.body);
      if (!entries) return Promise.resolve(null);
      var fields = fieldsOf(entries);
      if (fields.message_type === 'call') return Promise.resolve(null);
      if ((fields.message_type || 'text') === 'text' && !String(fields.body || '').trim()) return Promise.resolve(null);
      return queueItem(userId, url, 'chat-send', { entries: entries }).then(function () {
        note('No connection. This message is saved and will send when you are back online.');
        return chatQueuedResponse(entries);
      }).catch(function () { return null; });
    }
    if (/\/messages\/send$/.test(path) && init && typeof init.body === 'string') {
      return queueItem(userId, url, 'grid-message', {
        body: init.body,
        contentType: 'application/json'
      }).then(function () {
        note('No connection. This message is saved and will send when you are back online.');
        return jsonResponse({ success: true, offlineQueued: true, message: 'Queued' });
      }).catch(function () { return null; });
    }
    return Promise.resolve(null);
  }

  function listOutbox(userId) {
    return openDb().then(function (db) {
      return new Promise(function (resolve) {
        var tx = db.transaction('outbox', 'readonly');
        var req = tx.objectStore('outbox').getAll();
        req.onsuccess = function () {
          var rows = (req.result || []).filter(function (row) { return row.userId === userId; });
          rows.sort(function (a, b) { return a.id - b.id; });
          resolve(rows);
        };
        req.onerror = function () { resolve([]); };
      });
    }).catch(function () { return []; });
  }

  function deleteOutbox(id) {
    return withStore('outbox', 'readwrite', function (store) { store.delete(id); }).catch(function () {});
  }

  function replay(row) {
    if (row.kind === 'chat-send') {
      var form = new FormData();
      (row.entries || []).forEach(function (pair) { form.append(pair[0], pair[1]); });
      return nativeFetch(row.url, { method: 'POST', body: form, credentials: 'same-origin' });
    }
    return nativeFetch(row.url, {
      method: 'POST',
      body: row.body,
      credentials: 'same-origin',
      headers: { 'Content-Type': row.contentType || 'application/json', 'Accept': 'application/json' }
    });
  }

  function flushOutbox() {
    var userId = currentUserId();
    if (!userId || flushing || (navigator.onLine === false)) return;
    flushing = true;
    listOutbox(userId).then(function (rows) {
      var chain = Promise.resolve();
      rows.forEach(function (row) {
        chain = chain.then(function () {
          return replay(row).then(function (res) {
            if (res && (res.status === 401 || res.status === 403 || res.status === 408 || res.status === 429)) {
              throw new Error('retry later');
            }
            if (res && (res.ok || (res.status >= 400 && res.status < 500))) {
              return deleteOutbox(row.id);
            }
            throw new Error('retry later');
          });
        });
      });
      return chain;
    }).then(function () {
      flushing = false;
      if (navigator.onLine !== false) clearNote();
    }).catch(function () {
      flushing = false;
    });
  }

  function forgetOtherUsers(userId) {
    if (!userId) return;
    openDb().then(function (db) {
      var tx = db.transaction(['responses', 'outbox'], 'readwrite');
      var responses = tx.objectStore('responses');
      var keys = responses.getAllKeys();
      keys.onsuccess = function () {
        (keys.result || []).forEach(function (key) {
          if (String(key).indexOf(userId + '\n') !== 0) responses.delete(key);
        });
      };
      var outbox = tx.objectStore('outbox');
      var all = outbox.getAll();
      all.onsuccess = function () {
        (all.result || []).forEach(function (row) {
          if (row.userId !== userId) outbox.delete(row.id);
        });
      };
    }).catch(function () {});
  }

  window.fetch = function (input, init) {
    var method = ((init && init.method) || (input && input.method) || 'GET').toUpperCase();
    var url = absUrl(input);
    var userId = currentUserId();

    if (!isOurGet(url, method)) {
      return nativeFetch(input, init).catch(function (err) {
        if (!url) throw err;
        return rememberFailure(userId, url, method, init).then(function (queued) {
          if (queued) return queued;
          throw err;
        });
      });
    }

    return nativeFetch(input, init).then(function (res) {
      var type = (res.headers.get('content-type') || '').toLowerCase();
      if (res.ok && type.indexOf('json') !== -1) {
        res.clone().text().then(function (text) {
          if (text && text.indexOf('"success":false') === -1 && text.indexOf('"success": false') === -1) {
            saveResponse(userId, url, text);
          }
        }).catch(function () {});
      }
      return res;
    }).catch(function (err) {
      return readResponse(userId, url).then(function (saved) {
        if (!saved || !saved.body) throw err;
        try {
          var payload = JSON.parse(saved.body);
        } catch (parseError) {
          throw err;
        }
        note('You are offline. Showing the last saved data on this device.');
        return jsonResponse(payload);
      });
    });
  };

  function pageUrls() {
    var urls = [];
    var nodes = document.querySelectorAll('script[src], link[rel="stylesheet"][href]');
    for (var i = 0; i < nodes.length; i++) {
      var src = nodes[i].src || nodes[i].href;
      if (src && src.indexOf('grid-offline') === -1) urls.push(src);
    }
    return urls;
  }

  function registerWorker() {
    if (!('serviceWorker' in navigator)) return;
    var shell = new URL(window.location.href);
    shell.search = '';
    shell.hash = '';
    navigator.serviceWorker.register('/grid-offline-sw.js', { scope: '/' }).then(function () {
      return navigator.serviceWorker.ready;
    }).then(function (reg) {
      if (!reg.active) return;
      reg.active.postMessage({ type: 'cache-url', url: shell.toString() });
      reg.active.postMessage({ type: 'cache-assets', urls: pageUrls() });
    }).catch(function () {});
  }

  function start() {
    forgetOtherUsers(currentUserId());
    registerWorker();
    flushOutbox();
  }

  window.addEventListener('online', flushOutbox);
  if (document.readyState === 'complete') start();
  else window.addEventListener('load', start);
})();
