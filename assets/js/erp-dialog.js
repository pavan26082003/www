/**
 * ErpDialog — shared Promise-based alert / confirm / prompt for ERP modules.
 * Replaces native browser dialogs without blocking the page chrome.
 *
 * API:
 *   ErpDialog.alert(message, options?)  -> Promise<void>
 *   ErpDialog.confirm(message, options?) -> Promise<boolean>
 *   ErpDialog.prompt(message, defaultValue?, options?) -> Promise<string|null>
 */
(function (global) {
    'use strict';

    if (global.ErpDialog && global.ErpDialog.__version === '1.0.0') {
        return;
    }

    var STYLE_ID = 'erp-dialog-styles';
    var HOST_ID = 'erp-dialog-host';
    var queue = [];
    var busy = false;

    function ensureStyles() {
        if (document.getElementById(STYLE_ID)) {
            return;
        }

        var css = [
            '#' + HOST_ID + '{position:fixed;inset:0;z-index:2147483000;display:none;align-items:center;justify-content:center;padding:20px;background:rgba(15,23,42,.48);backdrop-filter:blur(2px);}',
            '#' + HOST_ID + '.is-open{display:flex;}',
            '#' + HOST_ID + ' .erp-dlg{width:min(420px,100%);background:#fff;border:1px solid #e2e8f0;border-radius:14px;box-shadow:0 24px 60px rgba(15,23,42,.28);overflow:hidden;font-family:Inter,-apple-system,BlinkMacSystemFont,"Segoe UI",Arial,sans-serif;color:#0f172a;}',
            '#' + HOST_ID + ' .erp-dlg-head{display:flex;align-items:flex-start;gap:12px;padding:18px 18px 0;}',
            '#' + HOST_ID + ' .erp-dlg-icon{flex:0 0 auto;width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:700;background:#eef2ff;color:#3730a3;}',
            '#' + HOST_ID + ' .erp-dlg-icon.is-danger{background:#fef2f2;color:#b91c1c;}',
            '#' + HOST_ID + ' .erp-dlg-icon.is-prompt{background:#ecfeff;color:#0e7490;}',
            '#' + HOST_ID + ' .erp-dlg-title{margin:2px 0 0;font-size:15px;font-weight:700;line-height:1.3;}',
            '#' + HOST_ID + ' .erp-dlg-body{padding:10px 18px 4px;}',
            '#' + HOST_ID + ' .erp-dlg-msg{margin:0;font-size:13px;line-height:1.5;color:#475569;white-space:pre-wrap;}',
            '#' + HOST_ID + ' .erp-dlg-input{width:100%;margin-top:12px;box-sizing:border-box;border:1.5px solid #94a3b8;border-radius:8px;padding:10px 12px;font:inherit;font-size:13px;outline:none;background:#fff;}',
            '#' + HOST_ID + ' .erp-dlg-input:focus{border-color:#0e7490;box-shadow:0 0 0 3px rgba(14,116,144,.15);}',
            '#' + HOST_ID + ' .erp-dlg-foot{display:flex;justify-content:flex-end;gap:8px;padding:16px 18px 18px;}',
            '#' + HOST_ID + ' .erp-dlg-btn{appearance:none;border:1px solid #cbd5e1;background:#fff;color:#0f172a;border-radius:8px;padding:8px 14px;font:inherit;font-size:13px;font-weight:650;cursor:pointer;}',
            '#' + HOST_ID + ' .erp-dlg-btn:hover{background:#f8fafc;}',
            '#' + HOST_ID + ' .erp-dlg-btn.primary{background:#0f766e;border-color:#0f766e;color:#fff;}',
            '#' + HOST_ID + ' .erp-dlg-btn.primary:hover{background:#0d9488;}',
            '#' + HOST_ID + ' .erp-dlg-btn.danger{background:#b91c1c;border-color:#b91c1c;color:#fff;}',
            '#' + HOST_ID + ' .erp-dlg-btn.danger:hover{background:#dc2626;}'
        ].join('');

        var style = document.createElement('style');
        style.id = STYLE_ID;
        style.textContent = css;
        document.head.appendChild(style);
    }

    function ensureHost() {
        ensureStyles();
        var host = document.getElementById(HOST_ID);
        if (host) {
            return host;
        }
        host = document.createElement('div');
        host.id = HOST_ID;
        host.setAttribute('aria-hidden', 'true');
        document.body.appendChild(host);
        return host;
    }

    function escapeHtml(value) {
        return String(value == null ? '' : value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function closeHost(host) {
        host.classList.remove('is-open');
        host.setAttribute('aria-hidden', 'true');
        host.innerHTML = '';
        busy = false;
        if (queue.length) {
            var next = queue.shift();
            openDialog(next);
        }
    }

    function openDialog(job) {
        busy = true;
        var host = ensureHost();
        var opts = job.options || {};
        var type = job.type;
        var title = opts.title || (type === 'confirm' ? 'Please confirm' : (type === 'prompt' ? 'Input required' : 'Notice'));
        var okLabel = opts.okLabel || (type === 'confirm' ? 'OK' : (type === 'prompt' ? 'OK' : 'OK'));
        var cancelLabel = opts.cancelLabel || 'Cancel';
        var danger = !!opts.danger;
        var iconClass = type === 'prompt' ? 'is-prompt' : (danger ? 'is-danger' : '');
        var iconText = type === 'prompt' ? '?' : (type === 'confirm' ? '!' : 'i');
        var showCancel = type !== 'alert';
        var defaultValue = job.defaultValue == null ? '' : String(job.defaultValue);

        host.innerHTML =
            '<div class="erp-dlg" role="dialog" aria-modal="true" aria-labelledby="erpDlgTitle">' +
            '<div class="erp-dlg-head">' +
            '<div class="erp-dlg-icon ' + iconClass + '" aria-hidden="true">' + iconText + '</div>' +
            '<div><h3 class="erp-dlg-title" id="erpDlgTitle">' + escapeHtml(title) + '</h3></div>' +
            '</div>' +
            '<div class="erp-dlg-body">' +
            '<p class="erp-dlg-msg">' + escapeHtml(job.message || '') + '</p>' +
            (type === 'prompt' ? '<input class="erp-dlg-input" type="text" data-erp-dlg-input value="' + escapeHtml(defaultValue) + '">' : '') +
            '</div>' +
            '<div class="erp-dlg-foot">' +
            (showCancel ? '<button type="button" class="erp-dlg-btn" data-erp-dlg-cancel>' + escapeHtml(cancelLabel) + '</button>' : '') +
            '<button type="button" class="erp-dlg-btn ' + (danger ? 'danger' : 'primary') + '" data-erp-dlg-ok>' + escapeHtml(okLabel) + '</button>' +
            '</div></div>';

        host.classList.add('is-open');
        host.setAttribute('aria-hidden', 'false');

        var resolved = false;
        var input = host.querySelector('[data-erp-dlg-input]');
        var okBtn = host.querySelector('[data-erp-dlg-ok]');
        var cancelBtn = host.querySelector('[data-erp-dlg-cancel]');

        function finish(value) {
            if (resolved) {
                return;
            }
            resolved = true;
            document.removeEventListener('keydown', onKey, true);
            closeHost(host);
            job.resolve(value);
        }

        function onKey(ev) {
            if (ev.key === 'Escape') {
                ev.preventDefault();
                if (type === 'alert') {
                    finish(undefined);
                } else if (type === 'confirm') {
                    finish(false);
                } else {
                    finish(null);
                }
            } else if (ev.key === 'Enter' && type !== 'alert') {
                if (ev.target && ev.target.tagName === 'TEXTAREA') {
                    return;
                }
                ev.preventDefault();
                if (type === 'confirm') {
                    finish(true);
                } else {
                    finish(input ? input.value : '');
                }
            }
        }

        document.addEventListener('keydown', onKey, true);

        host.onclick = function (ev) {
            if (ev.target === host) {
                if (type === 'alert') {
                    finish(undefined);
                } else if (type === 'confirm') {
                    finish(false);
                } else {
                    finish(null);
                }
            }
        };

        if (okBtn) {
            okBtn.onclick = function () {
                if (type === 'confirm') {
                    finish(true);
                } else if (type === 'prompt') {
                    finish(input ? input.value : '');
                } else {
                    finish(undefined);
                }
            };
        }

        if (cancelBtn) {
            cancelBtn.onclick = function () {
                finish(type === 'confirm' ? false : null);
            };
        }

        setTimeout(function () {
            if (input) {
                input.focus();
                input.select();
            } else if (okBtn) {
                okBtn.focus();
            }
        }, 30);
    }

    function enqueue(job) {
        if (busy) {
            queue.push(job);
        } else {
            openDialog(job);
        }
    }

    function alertDialog(message, options) {
        return new Promise(function (resolve) {
            enqueue({
                type: 'alert',
                message: message == null ? '' : String(message),
                options: options || {},
                resolve: function () { resolve(); }
            });
        });
    }

    function confirmDialog(message, options) {
        return new Promise(function (resolve) {
            enqueue({
                type: 'confirm',
                message: message == null ? '' : String(message),
                options: options || {},
                resolve: resolve
            });
        });
    }

    function promptDialog(message, defaultValue, options) {
        return new Promise(function (resolve) {
            enqueue({
                type: 'prompt',
                message: message == null ? '' : String(message),
                defaultValue: defaultValue == null ? '' : String(defaultValue),
                options: options || {},
                resolve: resolve
            });
        });
    }

    global.ErpDialog = {
        __version: '1.0.0',
        alert: alertDialog,
        confirm: confirmDialog,
        prompt: promptDialog
    };
})(typeof window !== 'undefined' ? window : this);
