/* awesome-scripts service worker — v1.22.0
   App shell precache + stale-while-revalidate for same-origin GETs;
   network-first for navigations with cached-shell fallback (offline OK). */
var VERSION = "as-v1.22.0";
var SHELL = [
  "./", "./compare.html", "./manifest.json",
  "./data/catalog.js", "./data/stats.js",
  "./assets/favicon.svg", "./assets/icon-192.png", "./assets/icon-512.png"
];
self.addEventListener("install", function (e) {
  e.waitUntil(caches.open(VERSION).then(function (c) { return c.addAll(SHELL); })
    .then(function () { return self.skipWaiting(); }));
});
self.addEventListener("activate", function (e) {
  e.waitUntil(caches.keys().then(function (keys) {
    return Promise.all(keys.filter(function (k) { return k !== VERSION; })
      .map(function (k) { return caches.delete(k); }));
  }).then(function () { return self.clients.claim(); }));
});
self.addEventListener("fetch", function (e) {
  var req = e.request;
  if (req.method !== "GET") return;
  var url = new URL(req.url);
  if (url.origin !== location.origin) return;
  if (req.mode === "navigate") {
    e.respondWith(fetch(req).then(function (res) {
      var copy = res.clone();
      caches.open(VERSION).then(function (c) { c.put(req, copy); });
      return res;
    }).catch(function () {
      return caches.match(req).then(function (hit) { return hit || caches.match("./"); });
    }));
    return;
  }
  e.respondWith(caches.match(req).then(function (hit) {
    var fetching = fetch(req).then(function (res) {
      if (res && res.status === 200) {
        var copy = res.clone();
        caches.open(VERSION).then(function (c) { c.put(req, copy); });
      }
      return res;
    }).catch(function () { return hit; });
    return hit || fetching;
  }));
});
