/**
 * CSRF token helper for the Minniark dashboard.
 *
 * Reads the token from <meta name="csrf-token"> and attaches it to
 *  - every same-origin fetch() and XMLHttpRequest (X-CSRF-Token header),
 *  - every POST form and every form targeting an endpoint (hidden csrf_token field),
 *  - every link to /backend_api/ or /api/ (csrf_token query parameter).
 *
 * Must be loaded synchronously in <head>, before other dashboard scripts.
 * For manual navigation use: location.href = MinniarkCSRF.url('backend_api/…');
 */
(function () {
  'use strict';

  var meta = document.querySelector('meta[name="csrf-token"]');
  if (!meta) return;

  var TOKEN = meta.getAttribute('content') || '';
  var FIELD = 'csrf_token';
  var HEADER = 'X-CSRF-Token';
  var ENDPOINT = /\/(backend_api|api)\/[^/]+\.php$/;

  function toUrl(url) {
    try { return new URL(url, window.location.href); } catch (e) { return null; }
  }

  function isSameOrigin(url) {
    var u = toUrl(url);
    return !!u && u.origin === window.location.origin;
  }

  function isEndpoint(url) {
    var u = toUrl(url);
    return !!u && u.origin === window.location.origin && ENDPOINT.test(u.pathname);
  }

  function withToken(url) {
    var u = toUrl(url);
    if (!u) return url;
    u.searchParams.set(FIELD, TOKEN);
    return u.toString();
  }

  // --- fetch -----------------------------------------------------------------
  if (typeof window.fetch === 'function') {
    var nativeFetch = window.fetch;
    window.fetch = function (input, init) {
      var url = (input && typeof input === 'object' && 'url' in input) ? input.url : String(input);
      if (isSameOrigin(url)) {
        init = init || {};
        var base = init.headers || (input && typeof input === 'object' && input.headers) || undefined;
        var headers = new Headers(base);
        if (!headers.has(HEADER)) headers.set(HEADER, TOKEN);
        init = Object.assign({}, init, { headers: headers });
      }
      return nativeFetch.call(this, input, init);
    };
  }

  // --- XMLHttpRequest --------------------------------------------------------
  var nativeOpen = XMLHttpRequest.prototype.open;
  var nativeSend = XMLHttpRequest.prototype.send;

  XMLHttpRequest.prototype.open = function (method, url) {
    this.__minniarkCsrf = isSameOrigin(url);
    return nativeOpen.apply(this, arguments);
  };

  XMLHttpRequest.prototype.send = function () {
    if (this.__minniarkCsrf) {
      try { this.setRequestHeader(HEADER, TOKEN); } catch (e) { /* already sent */ }
    }
    return nativeSend.apply(this, arguments);
  };

  // --- forms -----------------------------------------------------------------
  function prepareForm(form) {
    if (!form || form.tagName !== 'FORM') return;
    var method = (form.getAttribute('method') || 'get').toLowerCase();
    var action = form.action || window.location.href;
    if (!isSameOrigin(action)) return;
    if (method !== 'post' && !isEndpoint(action)) return;

    var input = form.querySelector('input[name="' + FIELD + '"]');
    if (!input) {
      input = document.createElement('input');
      input.type = 'hidden';
      input.name = FIELD;
      form.appendChild(input);
    }
    input.value = TOKEN;
  }

  var nativeSubmit = HTMLFormElement.prototype.submit;
  HTMLFormElement.prototype.submit = function () {
    prepareForm(this);
    return nativeSubmit.apply(this, arguments);
  };

  document.addEventListener('submit', function (e) { prepareForm(e.target); }, true);

  // --- links -----------------------------------------------------------------
  function prepareLink(a) {
    if (!a || a.tagName !== 'A') return;
    var href = a.getAttribute('href');
    if (!href || href.charAt(0) === '#' || /^(javascript|mailto|tel):/i.test(href)) return;
    if (isEndpoint(a.href) && toUrl(a.href).searchParams.get(FIELD) !== TOKEN) {
      a.href = withToken(a.href);
    }
  }

  document.addEventListener('click', function (e) {
    var a = e.target && e.target.closest ? e.target.closest('a[href]') : null;
    prepareLink(a);
  }, true);

  // --- initial + dynamic content ---------------------------------------------
  function prepareTree(root) {
    if (!root || root.nodeType !== 1) return;
    if (root.tagName === 'FORM') prepareForm(root);
    if (root.tagName === 'A') prepareLink(root);
    root.querySelectorAll('form').forEach(prepareForm);
    root.querySelectorAll('a[href]').forEach(prepareLink);
  }

  function init() {
    prepareTree(document.documentElement);
    new MutationObserver(function (mutations) {
      mutations.forEach(function (m) { m.addedNodes.forEach(prepareTree); });
    }).observe(document.documentElement, { childList: true, subtree: true });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  window.MinniarkCSRF = { token: TOKEN, field: FIELD, url: withToken };
})();
