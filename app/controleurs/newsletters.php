<?php
/*
 * Administration > Newsletter : rédiger, cibler, prévisualiser, tester puis envoyer.
 * Tout envoi passe par une confirmation ; un envoi parti ne se modifie plus (il reste dans l'historique).
 */

declare(strict_types=1);

require_once RACINE . '/app/controleurs/admin.php';
require_once RACINE . '/app/newsletter.php';

function newsletter_ou_404(int $id): array
{
    $n = ligne('SELECT * FROM newsletters WHERE id = ?', [$id]);
    if (!$n) {
        page_erreur(404);
    }
    return newsletter_preparer($n);
}

/** Liste : brouillons puis envois, avec le nombre d'abonnés actifs. */
function newsletters(): void
{
    exiger_droit('newsletter');
    vue_admin('admin/newsletters', [
        'titre_page' => 'Newsletter',
        'ariane' => [['Newsletter', null]],
        'lignes' => lignes("SELECT n.*, COALESCE(NULLIF(TRIM(CONCAT(u.prenom_admin, ' ', u.nom_admin)), ''), u.email) AS auteur
            FROM newsletters n LEFT JOIN utilisateurs u ON u.id = n.cree_par ORDER BY n.statut = 'brouillon' DESC, COALESCE(n.envoyee_le, n.modifie_le) DESC"),
        'nb_abonnes' => (int) valeur('SELECT COUNT(*) FROM abonnes_newsletter WHERE actif = 1'),
        'peut_modifier' => peut('newsletter', 'modifier'),
        'mode' => (string) config('email.mode', 'journal'),
    ]);
}

/** Rédaction (nouvelle newsletter ou brouillon). Un envoi déjà parti s'affiche en lecture seule. */
function newsletter_formulaire(?int $id = null): void
{
    exiger_droit('newsletter');
    $n = $id ? newsletter_ou_404($id) : newsletter_preparer(['id' => null, 'objet' => '', 'titre' => '', 'message' => '', 'image' => '', 'cible' => 'tous', 'communautes' => '[]', 'contenus' => '{}', 'statut' => 'brouillon']);
    $modifiable = $n['statut'] === 'brouillon' && peut('newsletter', 'modifier');
    $erreurs = [];

    if (est_post()) {
        if (!$modifiable) {
            page_erreur(403);
        }
        $n['objet'] = mb_substr(post('objet'), 0, 200);
        $n['titre'] = mb_substr(post('titre'), 0, 200);
        $n['message'] = mb_substr(post('message'), 0, 20000);
        $n['cible'] = isset(CIBLES_NEWSLETTER[post('cible')]) ? post('cible') : 'tous';
        $ids = fn ($cle) => array_values(array_unique(array_filter(array_map('intval', (array) ($_POST[$cle] ?? [])))));
        $n['communautes_ids'] = $ids('communautes');
        $n['opportunites_ids'] = $ids('opportunites');
        $n['actualites_ids'] = $ids('actualites');
        if ($n['objet'] === '') $erreurs['objet'] = "Indiquez l'objet de l'e-mail.";
        if (trim($n['message']) === '' && !$n['opportunites_ids'] && !$n['actualites_ids']) $erreurs['message'] = 'Écrivez un message ou joignez au moins un contenu.';
        if ($n['cible'] === 'communautes' && !$n['communautes_ids']) $erreurs['communautes'] = 'Choisissez au moins une communauté.';
        $image = null;
        if (!$erreurs) {
            try {
                $image = televerser_image($_FILES['image'] ?? null, 'newsletters', 1200);
            } catch (RuntimeException $e) {
                $erreurs['image'] = $e->getMessage();
            }
        }
        if (!$erreurs) {
            $donnees = [
                'objet' => $n['objet'], 'titre' => $n['titre'], 'message' => $n['message'], 'cible' => $n['cible'],
                'communautes' => json_encode($n['communautes_ids']),
                'contenus' => json_encode(['opportunites' => $n['opportunites_ids'], 'actualites' => $n['actualites_ids']]),
                'modifie_le' => maintenant(),
            ];
            if ($image) {
                $donnees['image'] = $image;
            } elseif (post('supprimer_image') !== '') {
                $donnees['image'] = '';
            }
            if ($n['id']) {
                if (isset($donnees['image']) && $n['image'] && $donnees['image'] !== $n['image']) {
                    supprimer_image($n['image']);
                }
                mettre_a_jour('newsletters', $donnees, (int) $n['id']);
                $id = (int) $n['id'];
            } else {
                $id = inserer('newsletters', $donnees + ['statut' => 'brouillon', 'cree_par' => utilisateur()['id'], 'cree_le' => maintenant()]);
            }
            flash('succes', 'Brouillon enregistré. Vérifiez l\'aperçu, envoyez-vous un test, puis envoyez.');
            rediriger(url('/gestion/newsletters/' . $id) . '#envoi');
        }
        flash('erreur', 'Certaines informations sont à corriger : les champs concernés sont indiqués en rouge.');
    }

    $destinataires = destinataires_newsletter($n['cible'], $n['communautes_ids']);
    vue_admin('admin/newsletter_formulaire', [
        'titre_page' => $n['id'] ? ($n['objet'] ?: 'Newsletter') : 'Nouvelle newsletter',
        'ariane' => [['Newsletter', url('/gestion/newsletters')], [$n['id'] ? couper($n['objet'], 40) : 'Nouvelle newsletter', null]],
        'n' => $n,
        'modifiable' => $modifiable,
        'erreurs' => $erreurs,
        'nb_destinataires' => count($destinataires),
        'communautes' => lignes("SELECT c.id, c.nom, (SELECT COUNT(*) FROM demandes_adhesion d WHERE d.communaute_id = c.id AND d.statut = 'acceptee') AS nb FROM communautes c WHERE c.active = 1 ORDER BY c.ordre, c.nom"),
        'opportunites' => lignes("SELECT id, titre, type, date_limite FROM opportunites WHERE statut = 'publie' AND (date_limite IS NULL OR date_limite >= CURDATE()) ORDER BY date_limite IS NULL, date_limite LIMIT 30"),
        'actualites' => lignes("SELECT id, titre, categorie, date_evenement FROM actualites WHERE statut = 'publie' ORDER BY COALESCE(date_evenement, DATE(publie_le)) DESC LIMIT 20"),
        'mode' => (string) config('email.mode', 'journal'),
        'email_test' => (string) (utilisateur()['email'] ?? ''),
    ]);
}

/** Nombre de destinataires pour une cible (mis à jour pendant la rédaction). */
function newsletter_compter(): void
{
    exiger_droit('newsletter');
    $communautes = array_values(array_filter(array_map('intval', (array) ($_GET['communautes'] ?? []))));
    $cible = isset(CIBLES_NEWSLETTER[get('cible')]) ? get('cible') : 'tous';
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['nombre' => count(destinataires_newsletter($cible, $communautes))]);
    exit;
}

/** Aperçu de l'e-mail tel qu'il sera reçu (affiché dans un cadre de la page de rédaction). */
function newsletter_apercu(int $id): void
{
    exiger_droit('newsletter');
    $n = newsletter_ou_404($id);
    header('Content-Type: text/html; charset=utf-8');
    header('X-Frame-Options: SAMEORIGIN');
    echo html_newsletter($n, '#');
    exit;
}

/** Envoi d'un exemplaire de test à une seule adresse (la sienne par défaut). */
function newsletter_tester(int $id): void
{
    exiger_droit('newsletter', 'modifier');
    $n = newsletter_ou_404($id);
    $email = mb_strtolower(post('email_test'));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        flash('erreur', "Indiquez une adresse e-mail valide pour le test.");
        rediriger(url('/gestion/newsletters/' . $id) . '#envoi');
    }
    $lien = lien_desabonnement($email);
    $ok = envoyer_email_html($email, '[Test] ' . $n['objet'], html_newsletter($n, htmlspecialchars($lien, ENT_QUOTES, 'UTF-8')), texte_newsletter($n, $lien));
    $mode = (string) config('email.mode', 'journal');
    flash($ok ? 'succes' : 'erreur', !$ok ? "Le test n'a pas pu partir. Le détail est dans stockage/erreurs.log."
        : ($mode === 'journal' ? "Test enregistré dans le journal (stockage/emails.log) : l'envoi réel commencera une fois Brevo branché." : "Test envoyé à $email."));
    rediriger(url('/gestion/newsletters/' . $id) . '#envoi');
}

/** Envoi à tous les destinataires de la cible, après confirmation. */
function newsletter_envoyer(int $id): void
{
    exiger_droit('newsletter', 'modifier');
    $n = newsletter_ou_404($id);
    if ($n['statut'] !== 'brouillon') {
        flash('erreur', 'Cette newsletter a déjà été envoyée.');
        rediriger(url('/gestion/newsletters/' . $id));
    }
    $adresses = destinataires_newsletter($n['cible'], $n['communautes_ids']);
    if (!$adresses) {
        flash('erreur', 'Aucun destinataire pour cette cible : personne ne recevrait la newsletter.');
        rediriger(url('/gestion/newsletters/' . $id) . '#envoi');
    }
    try {
        [$nombre, $mode] = envoyer_newsletter($n, $adresses);
    } catch (Throwable $e) {
        journaliser('erreurs.log', 'Newsletter ' . $id . ' : ' . $e->getMessage());
        flash('erreur', "L'envoi a échoué : " . $e->getMessage());
        rediriger(url('/gestion/newsletters/' . $id) . '#envoi');
    }
    mettre_a_jour('newsletters', ['statut' => $mode === 'journal' ? 'simulee' : 'envoyee', 'nb_destinataires' => $nombre, 'mode_envoi' => $mode, 'envoyee_le' => maintenant()], $id);
    flash('succes', $mode === 'journal'
        ? "Envoi simulé pour $nombre destinataire" . pluriel($nombre) . " : aucun e-mail n'est parti, Brevo n'étant pas encore branché."
        : "Newsletter envoyée à $nombre destinataire" . pluriel($nombre) . '.');
    rediriger(url('/gestion/newsletters'));
}

/** Copie d'une newsletter (pour réutiliser un envoi passé comme modèle). */
function newsletter_dupliquer(int $id): void
{
    exiger_droit('newsletter', 'modifier');
    $n = newsletter_ou_404($id);
    $nouveau = inserer('newsletters', [
        'objet' => mb_substr('Copie de ' . $n['objet'], 0, 200), 'titre' => $n['titre'], 'message' => $n['message'], 'image' => '',
        'cible' => $n['cible'], 'communautes' => $n['communautes'], 'contenus' => $n['contenus'], 'statut' => 'brouillon',
        'cree_par' => utilisateur()['id'], 'cree_le' => maintenant(), 'modifie_le' => maintenant(),
    ]);
    flash('succes', 'Copie créée : modifiez-la puis envoyez-la.');
    rediriger(url('/gestion/newsletters/' . $nouveau));
}

function newsletter_supprimer(int $id): void
{
    exiger_droit('newsletter', 'modifier');
    $n = newsletter_ou_404($id);
    if ($n['statut'] !== 'brouillon') {
        flash('erreur', "Une newsletter envoyée reste dans l'historique.");
        rediriger(url('/gestion/newsletters'));
    }
    requete('DELETE FROM newsletters WHERE id = ?', [$id]);
    supprimer_image($n['image']);
    flash('succes', 'Brouillon supprimé.');
    rediriger(url('/gestion/newsletters'));
}
