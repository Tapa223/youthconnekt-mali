<?php
/*
 * Petits affichages de l'administration (pastilles, avatars, menu) et icônes du menu.
 */

declare(strict_types=1);

const ICONES = [
    'tableau' => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
    'mallette' => '<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>',
    'journal' => '<rect x="3" y="4" width="15" height="16" rx="2"/><path d="M18 8h3v10a2 2 0 0 1-2 2"/><path d="M7 9h7M7 13h7M7 17h4"/>',
    'drapeau' => '<path d="M5 21V4"/><path d="M5 4h11l-2 4 2 4H5"/>',
    'bulle' => '<path d="M4 5h16v11H9l-5 4z"/>',
    'batiment' => '<rect x="4" y="3" width="16" height="18" rx="1.5"/><path d="M9 7h1M14 7h1M9 11h1M14 11h1M9 15h1M14 15h1M10 21v-3h4v3"/>',
    'personne' => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
    'groupe' => '<circle cx="9" cy="8" r="3.5"/><circle cx="17" cy="9" r="2.5"/><path d="M2 20a7 7 0 0 1 14 0M15 14.5a5 5 0 0 1 7 5.5"/>',
    'enveloppe' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
    'lettre' => '<rect x="4" y="4" width="16" height="16" rx="2"/><path d="M4 9h16"/>',
    'graphique' => '<path d="M4 20h16"/><path d="M7 16V9M12 16V5M17 16v-4"/>',
    'coeur' => '<path d="M12 20s-7-4.5-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.5-7 10-7 10z"/>',
    'reglage' => '<circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3M4.9 4.9l2.1 2.1M17 17l2.1 2.1M4.9 19.1 7 17M17 7l2.1-2.1"/>',
    'cible' => '<circle cx="12" cy="12" r="8"/><circle cx="12" cy="12" r="3"/>',
    'cle' => '<circle cx="8" cy="15" r="4"/><path d="m11 12 9-9M17 6l3 3"/>',
];

/** Menu latéral : [rubrique, [[libellé, adresse, icône, droit nécessaire], ...]] */
const MENU_ADMIN = [
    [null, [['Tableau de bord', '', 'tableau', null]]],
    ['Contenus', [
        ['Opportunités', 'opportunites', 'mallette', 'opportunites'],
        ['Actualités et événements', 'actualites', 'journal', 'actualites'],
        ['Initiatives', 'initiatives', 'drapeau', 'initiatives'],
        ['Témoignages', 'temoignages', 'bulle', 'temoignages'],
        ['Partenaires', 'partenaires', 'batiment', 'partenaires'],
    ]],
    ['Communauté', [
        ['Membres individuels', 'membres', 'personne', 'membres'],
        ['Organisations et entreprises', 'organisations', 'batiment', 'organisations'],
        ['Communautés', 'communautes', 'groupe', 'communautes'],
    ]],
    ['Échanges', [
        ['Messages de contact', 'messages', 'enveloppe', 'messages'],
        ['Abonnés newsletter', 'newsletter', 'lettre', 'newsletter'],
    ]],
    ['Site', [
        ['Chiffres clés', 'chiffres', 'graphique', 'chiffres'],
        ['Mot du ministre', 'ministre', 'coeur', 'ministre'],
        ["Axes d'action", 'axes', 'cible', 'axes'],
        ['Paramètres', 'parametres', 'reglage', 'parametres'],
    ]],
    ['Accès', [
        ['Comptes et connexions', 'comptes', 'cle', 'comptes'],
    ]],
];

function menu_admin(): array
{
    $chemin = chemin_actuel();
    $pastilles = [];
    if (peut('messages')) {
        $pastilles['messages'] = (int) valeur('SELECT COUNT(*) FROM messages_contact WHERE traite = 0');
    }
    if (peut('communautes') || peut('demandes')) {
        $pastilles['communautes'] = (int) valeur("SELECT COUNT(*) FROM demandes_adhesion WHERE statut = 'en_attente'");
    }
    $menu = [];
    foreach (MENU_ADMIN as [$rubrique, $elements]) {
        $liens = [];
        foreach ($elements as [$libelle, $slug, $icone, $droit]) {
            if ($droit !== null && !peut($droit)) {
                continue;
            }
            $adresse = '/gestion' . ($slug ? '/' . $slug : '');
            $actif = $slug === '' ? $chemin === '/gestion' : (strpos($chemin . '/', $adresse . '/') === 0);
            if ($slug === 'communautes' && strpos($chemin, '/gestion/demandes') === 0) {
                $actif = true;
            }
            $liens[] = ['libelle' => $libelle, 'url' => url($adresse), 'icone' => ICONES[$icone], 'actif' => $actif, 'pastille' => $pastilles[$slug] ?? 0];
        }
        if ($liens) {
            $menu[] = ['rubrique' => $rubrique, 'liens' => $liens];
        }
    }
    return $menu;
}

function badge(string $texte, string $couleur = 'gris'): string
{
    return '<span class="ycm-badge ycm-badge--' . e($couleur) . '">' . e($texte) . '</span>';
}

function avatar(string $initiales, int $numero = 0, ?string $image = null): string
{
    if ($image) {
        return '<img src="' . e(media($image)) . '" alt="" class="ycm-avatar ycm-avatar--photo">';
    }
    $couleurs = ['orange', 'turquoise', 'bleu', 'vert'];
    return '<span class="ycm-avatar ycm-avatar--' . $couleurs[$numero % 4] . '" aria-hidden="true">' . e($initiales) . '</span>';
}

/** Deux lignes dans une cellule : le nom en gras, un détail en petit dessous. */
function nom_et_detail(string $nom, string $detail = '', string $prefixe = ''): string
{
    return '<span class="ycm-personne">' . $prefixe . '<span><strong>' . e($nom) . '</strong>'
        . ($detail !== '' ? '<small>' . e($detail) . '</small>' : '') . '</span></span>';
}

function miniature(?string $image, int $hauteur = 40): string
{
    return $image ? '<img src="' . e(media($image)) . '" alt="" class="ycm-miniature" style="height:' . $hauteur . 'px">' : '';
}

function point_couleur(string $texte, ?string $couleur): string
{
    return '<span class="ycm-point" style="--couleur:' . e(COULEURS_HEX[$couleur ?? ''] ?? '#F1592A') . '">' . e($texte) . '</span>';
}

function liens_ligne(array $liens): string
{
    $html = '<span class="ycm-actions-ligne">';
    foreach ($liens as $texte => $lien) {
        $externe = strpos($texte, 'Voir') === 0 && strpos($lien, '/gestion') === false;
        $html .= '<a href="' . e($lien) . '"' . ($externe ? ' target="_blank" rel="noopener"' : '') . '>' . e($texte) . '</a>';
    }
    return $html . '</span>';
}
