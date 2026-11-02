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
];

const TYPES_OPPORTUNITE = [
    'formation' => 'Formation',
    'appel' => 'Appel à candidatures',
    'evenement' => 'Événement',
    'bourse' => 'Bourse',
    'emploi' => 'Emploi et stage',
];

// Couleur de la charte associée à chaque type d'opportunité
const COULEURS_OPPORTUNITE = [
    'formation' => 'turquoise',
    'appel' => 'orange',
    'evenement' => 'vert',
    'bourse' => 'bleu',
    'emploi' => 'rouge',
];

const CATEGORIES_ACTUALITE = [
    'actualite' => 'Actualité',
    'evenement' => 'Événement',
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

const PROFILS_CONTACT = [
    'jeune' => 'Un jeune',
    'organisation' => 'Une organisation ou une entreprise',
    'partenaire' => 'Un partenaire ou une institution',
    'media' => 'Un média',
    'autre' => 'Autre',
];

const SUJETS_CONTACT = [
    'question' => 'Question générale',
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
