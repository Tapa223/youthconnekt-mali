/* YouthConnekt Mali : garde les billets ouverts sur l'appareil pour les présenter même sans connexion.
   Seuls les billets et les fichiers dont ils ont besoin sont gardés ; le reste du site passe normalement. */
const CACHE = "ycm-billets-v1";

self.addEventListener("install", () => self.skipWaiting());
self.addEventListener("activate", (e) => e.waitUntil(self.clients.claim()));

self.addEventListener("fetch", (e) => {
  const req = e.request;
  if (req.method !== "GET") return;
  const url = new URL(req.url);
  const billet = /\/compte\/mon-espace\/participations\/\d+\/billet$/.test(url.pathname);
  const fichier = url.origin === self.location.origin && /\/assets\/(css|js|img)\//.test(url.pathname);
  if (!billet && !fichier) return;
  // Réseau d'abord ; sans connexion, la dernière version gardée
  e.respondWith(
    fetch(req).then((rep) => {
      if (rep.ok && !rep.redirected) { const copie = rep.clone(); caches.open(CACHE).then((c) => c.put(req, copie)); }
      return rep;
    }).catch(() => caches.match(req, { ignoreSearch: fichier }).then((r) => r || Response.error()))
  );
});
