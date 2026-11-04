<?php
/*
 * Listes de choix utilisées par le site et l'administration.
 * Pour ajouter une valeur (une région, un type d'opportunité...), il suffit de l'ajouter ici.
 */

const REGIONS = [
    'bamako' => 'District de Bamako',
    'bandiagara' => 'Bandiagara',
    'bougouni' => 'Bougouni',
    'dioila' => 'Dioïla',
    'douentza' => 'Douentza',
    'gao' => 'Gao',
    'kayes' => 'Kayes',
    'kidal' => 'Kidal',
    'kita' => 'Kita',
    'koulikoro' => 'Koulikoro',
    'koutiala' => 'Koutiala',
    'menaka' => 'Ménaka',
    'mopti' => 'Mopti',
    'nara' => 'Nara',
    'nioro' => 'Nioro du Sahel',
    'san' => 'San',
    'segou' => 'Ségou',
    'sikasso' => 'Sikasso',
    'taoudenit' => 'Taoudénit',
    'tombouctou' => 'Tombouctou',
    'hors_mali' => 'Hors du Mali',
];

// Profils du membre individuel (cahier des charges v2, section 7.1)
const CATEGORIES_INDIVIDUELLES = [
    'jeune' => 'Jeune',
    'etudiant' => 'Étudiant',
    'professionnel' => 'Jeune professionnel',
    'entrepreneur' => 'Entrepreneur',
    'porteur_projet' => 'Porteur de projet',
    'innovateur' => 'Innovateur',
    'leader_associatif' => "Jeune leader d'association",
];

const TYPES_STRUCTURE = [
    'entreprise' => 'Entreprise',
    'startup' => 'Startup',
    'association' => 'Association',
    'ong' => 'ONG',
    'autre' => 'Autre structure',
];

const TYPES_COMPTE = [
    'individuel' => 'Membre individuel',
    'organisation' => 'Organisation ou entreprise',
    'administration' => "Compte d'administration",
];

const ETATS_INITIATIVE = [
    'a_venir' => 'À venir',
    'en_cours' => 'En cours',
    'terminee' => 'Terminée',
];

const COULEURS_CHARTE = [
    'orange' => 'Orange',
    'turquoise' => 'Bleu sarcelle',
    'bleu' => 'Bleu',
    'vert' => 'Vert',
    'rouge' => 'Rouge foncé',
];

const COULEURS_HEX = [
    'orange' => '#F1592A',
    'turquoise' => '#04B2B1',
    'bleu' => '#0358D9',
    'vert' => '#035E4D',
    'rouge' => '#7C0D02',
];

/** Profil de la personne qui témoigne (tri des témoignages dans l'administration) */
const PROFILS_TEMOIGNAGE = [
    'jeune_femme' => 'Jeune femme',
    'jeune_homme' => 'Jeune homme',
    'organisation' => 'Organisation ou association',
    'entreprise' => 'Entreprise',
    'partenaire' => 'Partenaire ou institution',
];

/** Liens qu'un membre peut ajouter à son profil : [libellé, tracé de l'icône] */
const RESEAUX_PROFIL = [
    'linkedin' => ['LinkedIn', '<path d="M6.9 9.3H3.6V20h3.3V9.3zM5.2 4a1.9 1.9 0 1 0 0 3.8 1.9 1.9 0 0 0 0-3.8zM20.4 13.9c0-3-1.6-4.9-4.2-4.9-1.4 0-2.4.8-2.8 1.5V9.3h-3.2V20h3.3v-5.6c0-1.4.6-2.4 1.9-2.4 1.2 0 1.7.9 1.7 2.4V20h3.3v-6.1z" fill="currentColor" stroke="none"/>'],
    'facebook' => ['Facebook', '<path d="M14 8.5V6.8c0-.8.5-1.3 1.3-1.3H17V2.3h-2.6C11.9 2.3 10.5 3.9 10.5 6.3v2.2H8v3.3h2.5v9.9H14v-9.9h2.6l.5-3.3H14z" fill="currentColor" stroke="none"/>'],
    'instagram' => ['Instagram', '<rect x="3.5" y="3.5" width="17" height="17" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.3" cy="6.7" r=".9" fill="currentColor"/>'],
    'x' => ['X', '<path d="M4 4l16 16M20 4L4 20"/>'],
    'tiktok' => ['TikTok', '<path d="M14 3v11.5a3.5 3.5 0 1 1-3.5-3.5M14 3c.5 2.6 2.4 4.4 5 4.6"/>'],
    'youtube' => ['YouTube', '<rect x="2.5" y="5.5" width="19" height="13" rx="4"/><path d="M10 9.3v5.4l4.6-2.7z" fill="currentColor"/>'],
    'github' => ['GitHub', '<path d="M9 19c-4 1.3-4-2-6-2.5M15 21v-3.5c0-1 .1-1.4-.5-2 2.8-.3 5.5-1.4 5.5-6a4.6 4.6 0 0 0-1.3-3.2 4.3 4.3 0 0 0-.1-3.2s-1.1-.3-3.5 1.3a12 12 0 0 0-6.2 0C6.5 2.8 5.4 3.1 5.4 3.1a4.3 4.3 0 0 0-.1 3.2A4.6 4.6 0 0 0 4 9.5c0 4.6 2.7 5.7 5.5 6-.6.6-.6 1.2-.5 2V21"/>'],
    'site' => ['Site web', '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.7 3.8 5.7 3.8 9s-1.3 6.3-3.8 9c-2.5-2.7-3.8-5.7-3.8-9S9.5 5.7 12 3z"/>'],
    'autre' => ['Autre lien', '<path d="M10 14a4 4 0 0 0 5.7 0l3-3a4 4 0 0 0-5.7-5.7l-1 1M14 10a4 4 0 0 0-5.7 0l-3 3a4 4 0 0 0 5.7 5.7l1-1"/>'],
];

/** Pictogrammes des coordonnées (page Contact) */
const ICONES_CONTACT = [
    'mail' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
    'tel' => '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2"/>',
    'lieu' => '<path d="M12 21s7-6.2 7-12a7 7 0 0 0-14 0c0 5.8 7 12 7 12z"/><circle cx="12" cy="9" r="2.5"/>',
    'heure' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
];

const PROFILS_CONTACT = [
    'jeune' => 'Jeune',
    'organisation' => 'Organisation ou entreprise',
    'partenaire' => 'Partenaire ou institution',
    'media' => 'Média',
    'autre' => 'Autre',
];

const SUJETS_CONTACT = [
    'question' => 'Question générale',
    'suggestion' => 'Suggestion',
    'partenariat' => 'Partenariat',
    'opportunite' => 'Proposer une opportunité',
    'initiative' => 'Faire connaître une initiative',
    'compte' => 'Mon compte',
    'presse' => 'Presse',
];

const STATUTS_DEMANDE = [
    'en_attente' => 'En attente',
    'acceptee' => 'Acceptée',
    'refusee' => 'Refusée',
];

// Rôles de l'équipe d'administration
const ROLES_ADMIN = [
    'super' => 'Administrateur principal',
    'editeur' => 'Éditeur de contenus',
    'moderation' => 'Modération des communautés',
    'membres' => 'Gestion des membres',
];

const DESCRIPTIONS_ROLES = [
    'super' => 'Accès complet, y compris les comptes de l\'équipe et les paramètres.',
    'editeur' => 'Publie les actualités, opportunités et initiatives, et tient à jour l\'accueil.',
    'moderation' => 'Accepte ou refuse les demandes d\'intégration et gère les communautés.',
    'membres' => 'Suit les comptes, les profils, les messages de contact et la newsletter.',
];
