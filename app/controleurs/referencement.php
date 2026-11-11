<?php
/* Référencement : indications aux moteurs de recherche et plan du site. */

declare(strict_types=1);

function robots(): void
{
    header('Content-Type: text/plain; charset=utf-8');
    echo "User-agent: *\nDisallow: /gestion\nDisallow: /compte\nDisallow: /recherche\n\nSitemap: " . url_absolue('/sitemap.xml') . "\n";
    exit;
}

function plan_du_site(): void
{
    $adresses = [];
    foreach (['/', '/a-propos', '/initiatives', '/opportunites', '/actualites', '/communaute', '/temoignages', '/contact'] as $page) {
        $adresses[] = [url_absolue($page), null];
    }
    foreach (initiatives_publiees('ORDER BY i.publie_le DESC') as $i) {
        $adresses[] = [url_absolue('/initiatives/' . $i['slug']), $i['modifie_le'] ?? $i['publie_le']];
    }
    foreach (opportunites_ouvertes() as $o) {
        $adresses[] = [url_absolue('/opportunites/' . $o['slug']), $o['modifie_le'] ?? $o['publie_le']];
    }
    foreach (actualites_publiees() as $a) {
        $adresses[] = [url_absolue('/actualites/' . $a['slug']), $a['modifie_le'] ?? $a['publie_le']];
    }
    foreach (communautes_actives() as $c) {
        $adresses[] = [url_absolue('/communaute/' . $c['slug']), null];
    }
    header('Content-Type: application/xml; charset=utf-8');
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    foreach ($adresses as [$loc, $date]) {
        echo '  <url><loc>' . htmlspecialchars($loc, ENT_XML1) . '</loc>' . ($date ? '<lastmod>' . date('Y-m-d', strtotime($date)) . '</lastmod>' : '') . "</url>\n";
    }
    echo "</urlset>\n";
    exit;
}
