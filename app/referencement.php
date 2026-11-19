<?php
/*
 * Référencement : titres et descriptions ajustés pour Google, données structurées (schema.org)
 * qui aident les moteurs à comprendre qui est YouthConnekt Mali et ce que contient chaque page.
 */

declare(strict_types=1);

/** Titre de page pour Google (au-delà de 65 caractères, le nom du site, déjà connu par ailleurs, est retiré) */
function titre_page(?string $titre): string
{
    $titre = trim((string) ($titre ?? '')) ?: 'YouthConnekt Mali';
    $fin = ' | YouthConnekt Mali';
    if (mb_strlen($titre) > 65 && str_ends_with($titre, $fin) && mb_strlen($titre) - mb_strlen($fin) >= 25) {
        $titre = mb_substr($titre, 0, -mb_strlen($fin));
    }
    return $titre;
}

/** Description pour Google : une seule ligne, coupée proprement vers 158 caractères */
function description_page(?string $description): string
{
    $d = trim(preg_replace('/\s+/u', ' ', (string) ($description ?? '')));
    if ($d === '') {
        $d = 'YouthConnekt Mali relie les jeunes du pays aux opportunités, aux initiatives du chapitre et entre eux.';
    }
    if (mb_strlen($d) <= 158) {
        return $d;
    }
    $coupe = mb_substr($d, 0, 157);
    $espace = mb_strrpos($coupe, ' ');
    return rtrim(mb_substr($coupe, 0, $espace !== false && $espace > 100 ? $espace : 157), " ,;:–-") . '…';
}

/** Date au format attendu par les moteurs (ISO 8601) */
function date_schema(?string $date): ?string
{
    $t = $date ? strtotime($date) : false;
    return $t ? date('c', $t) : null;
}

/** YouthConnekt Mali, tel que présenté aux moteurs de recherche (référencé par les autres blocs) */
function schema_organisation(): array
{
    $p = parametres();
    $org = [
        '@type' => 'Organization',
        '@id' => url_absolue('/') . '#organisation',
        'name' => 'YouthConnekt Mali',
        'url' => url_absolue('/'),
        'logo' => url_absolue('/assets/img/logo-youthconnekt-mali.png'),
        'description' => 'Chapitre malien de YouthConnekt Africa : opportunités, initiatives et communauté pour les jeunes du Mali.',
        'areaServed' => ['@type' => 'Country', 'name' => 'Mali'],
        'memberOf' => ['@type' => 'Organization', 'name' => 'YouthConnekt Africa', 'url' => 'https://youthconnektafrica.org'],
        'address' => array_filter(['@type' => 'PostalAddress', 'streetAddress' => trim((string) $p['adresse']) ?: null, 'addressLocality' => 'Bamako', 'addressCountry' => 'ML']),
    ];
    if (trim((string) $p['email_contact']) !== '') {
        $org['email'] = trim((string) $p['email_contact']);
    }
    if (trim((string) $p['telephone']) !== '') {
        $org['telephone'] = trim((string) $p['telephone']);
    }
    if ($reseaux = array_values(reseaux_sociaux())) {
        $org['sameAs'] = $reseaux;
    }
    return $org;
}

function schema_site(): array
{
    return [
        '@type' => 'WebSite',
        '@id' => url_absolue('/') . '#site',
        'name' => 'YouthConnekt Mali',
        'url' => url_absolue('/'),
        'inLanguage' => 'fr',
        'publisher' => ['@id' => url_absolue('/') . '#organisation'],
    ];
}

/** Fil d'Ariane : [['Actualités', '/actualites'], ['Titre', '/actualites/slug']] */
function schema_fil_ariane(array $etapes): array
{
    $elements = [['@type' => 'ListItem', 'position' => 1, 'name' => 'Accueil', 'item' => url_absolue('/')]];
    foreach (array_values($etapes) as $k => [$nom, $chemin]) {
        $elements[] = ['@type' => 'ListItem', 'position' => $k + 2, 'name' => $nom, 'item' => url_absolue($chemin)];
    }
    return ['@type' => 'BreadcrumbList', 'itemListElement' => $elements];
}

/** Lieu d'un événement : le texte saisi, rattaché au Mali sauf si une autre ville connue de la région est citée */
function schema_lieu(?string $lieu): array
{
    $lieu = trim((string) $lieu);
    $etrangers = ['Niamey' => 'NE', 'Kigali' => 'RW', 'Dakar' => 'SN', "N'Djamena" => 'TD', 'Ouagadougou' => 'BF', 'Abidjan' => 'CI', 'Nouakchott' => 'MR', 'Accra' => 'GH'];
    $pays = 'ML';
    foreach ($etrangers as $ville => $code) {
        if (stripos($lieu, $ville) !== false) {
            $pays = $code;
        }
    }
    return ['@type' => 'Place', 'name' => $lieu !== '' ? $lieu : 'Mali', 'address' => array_filter(['@type' => 'PostalAddress', 'addressLocality' => $lieu ?: null, 'addressCountry' => $pays])];
}

function schema_actualite(array $a): array
{
    $url = url_absolue('/actualites/' . $a['slug']);
    $images = $a['image'] ? [url_absolue_media($a['image'])] : [];
    $blocs = [[
        '@type' => 'NewsArticle',
        'headline' => mb_substr($a['titre'], 0, 110),
        'description' => description_page($a['chapeau'] ?? ''),
        'mainEntityOfPage' => $url,
        'datePublished' => date_schema($a['publie_le']),
        'dateModified' => date_schema($a['modifie_le'] ?? $a['publie_le']),
        'author' => ['@id' => url_absolue('/') . '#organisation'],
        'publisher' => ['@id' => url_absolue('/') . '#organisation'],
        'inLanguage' => 'fr',
    ] + ($images ? ['image' => $images] : [])];
    // Un événement daté apparaît aussi comme événement (avec sa date et son lieu)
    if (($a['categorie'] ?? '') === 'evenement' && !empty($a['date_evenement'])) {
        $blocs[] = array_filter([
            '@type' => 'Event',
            'name' => $a['titre'],
            'description' => description_page($a['chapeau'] ?? ''),
            'startDate' => substr((string) $a['date_evenement'], 0, 10),
            'endDate' => !empty($a['date_fin']) ? substr((string) $a['date_fin'], 0, 10) : null,
            'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
            'eventStatus' => 'https://schema.org/EventScheduled',
            'location' => schema_lieu($a['lieu'] ?? ''),
            'image' => $images ?: null,
            'url' => $url,
        ]);
    }
    $blocs[] = schema_fil_ariane([['Actualités', '/actualites'], [$a['titre'], '/actualites/' . $a['slug']]]);
    return $blocs;
}

function schema_initiative(array $i): array
{
    $url = url_absolue('/initiatives/' . $i['slug']);
    $blocs = [];
    // Initiative datée : présentée comme un événement organisé par le chapitre
    if (!empty($i['date_debut'])) {
        $blocs[] = array_filter([
            '@type' => 'Event',
            'name' => $i['titre'],
            'description' => description_page($i['resume'] ?? ''),
            'startDate' => substr((string) $i['date_debut'], 0, 10),
            'endDate' => !empty($i['date_fin']) ? substr((string) $i['date_fin'], 0, 10) : null,
            'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
            'eventStatus' => 'https://schema.org/EventScheduled',
            'location' => schema_lieu($i['lieu'] ?? ''),
            'organizer' => ['@id' => url_absolue('/') . '#organisation'],
            'image' => $i['image'] ? [url_absolue_media($i['image'])] : null,
            'url' => $url,
        ]);
    }
    $blocs[] = schema_fil_ariane([['Initiatives', '/initiatives'], [$i['titre'], '/initiatives/' . $i['slug']]]);
    return $blocs;
}

/** Bloc <script type="application/ld+json"> prêt à placer dans <head> */
function donnees_structurees_html(array $blocs): string
{
    $blocs = array_values(array_filter($blocs));
    if (!$blocs) {
        return '';
    }
    $json = json_encode(['@context' => 'https://schema.org', '@graph' => $blocs], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP);
    return '<script type="application/ld+json">' . $json . '</script>';
}
