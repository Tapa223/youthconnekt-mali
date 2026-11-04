<?php
/*
 * Assistant en bas à droite, sous forme de conversation :
 *  - des sujets proposés donnent une réponse prête, avec un bouton vers la bonne page ;
 *  - pour toute autre question, la personne écrit son message : il arrive dans
 *    Administration > Messages de contact et l'équipe répond par e-mail.
 * La couleur du bouton s'adapte au fond : orange sur le vert, vert sur le clair.
 */
$u = utilisateur();
$membre = $u && est_membre($u);
$dans_espace = str_starts_with(chemin_actuel(), '/compte/mon-espace'); // bulle cachée : l'assistant s'ouvre depuis le tableau de bord
$plus_haut = str_starts_with(chemin_actuel(), '/opportunites/'); // laisse la place à la barre « Postuler » sur téléphone
$prenom = $u ? ($u['prenom_affiche'] ?? '') : '';
$sujets = [
    ['Rejoindre le chapitre', "Créez votre compte, en tant que jeune ou en tant qu'organisation : c'est gratuit.", $u ? null : ['Créer mon compte', url('/compte/inscription')]],
    ['Opportunités', 'Formations, bourses, stages et appels sont sur la page Opportunités, triés par date limite.', ['Voir les opportunités', url('/opportunites')]],
    ['Communautés', "Ouvrez une communauté et cliquez sur « Intégrer la communauté ».", ['Voir les communautés', url('/communaute')]],
    ['Événements', 'Les prochains événements du chapitre et les dernières nouvelles sont sur la page Actualités.', ['Voir les événements', url('/actualites')]],
    ['Newsletter', "Recevez les opportunités, les initiatives et les événements du chapitre par e-mail : inscrivez votre adresse dans le formulaire en bas de page.", ['M\'abonner à la newsletter', ($dans_espace ? url('/') : '') . '#newsletter']],
];
$champ = 'min-h-10 w-full rounded-lg border border-ligne bg-white px-3 text-sm text-encre placeholder:text-texte/60 focus:border-vert focus:ring-[3px] focus:ring-orange/35 focus:outline-none';
?>
<div class="aide fixed right-4 z-40 sm:right-6 <?= $dans_espace ? 'aide-espace' : '' ?> <?= $plus_haut ? 'bottom-24 lg:bottom-6' : 'bottom-4 sm:bottom-6' ?>" data-aide>
  <div id="panneau-aide" class="aide-panneau absolute right-0 bottom-[calc(100%+12px)] flex h-[min(34rem,calc(100dvh-7rem))] w-[min(23rem,calc(100vw-2rem))] flex-col overflow-hidden rounded-2xl border border-ligne bg-white shadow-2xl shadow-vert/20" role="dialog" aria-labelledby="titre-aide" hidden>
    <div class="aide-tete flex shrink-0 items-center justify-between gap-3 px-4 py-3 text-white">
      <div>
        <p id="titre-aide" class="text-base font-bold">Assistant YouthConnekt Mali</p>
      </div>
      <button type="button" class="-mr-1.5 grid size-9 shrink-0 place-items-center rounded-full text-white transition-colors hover:bg-white/20" data-fermer-aide aria-label="Fermer">
        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
      </button>
    </div>

    <div class="aide-fil flex-1 space-y-2.5 overflow-y-auto bg-gris/60 px-3.5 py-3.5" data-fil aria-live="polite">
      <div class="aide-bot">Bonjour<?= $prenom !== '' ? ' ' . e($prenom) : '' ?> ! Choisissez un sujet, ou écrivez-nous en bas.</div>
      <div class="flex flex-wrap gap-2 pb-6" data-sujets>
        <?php foreach ($sujets as $k => [$libelle, $reponse, $lien]): ?>
        <button type="button" class="aide-sujet" data-sujet="<?= $k ?>"><?= e($libelle) ?></button>
        <?php endforeach ?>
      </div>
    </div>

    <?php /* Réponses prêtes, recopiées dans la conversation au clic sur un sujet */ ?>
    <?php foreach ($sujets as $k => [$libelle, $reponse, $lien]): ?>
    <template data-reponse="<?= $k ?>"><div class="aide-bot"><?= e($reponse) ?><?php if ($lien): ?><a href="<?= e($lien[1]) ?>" class="aide-lien"><?= e($lien[0]) ?></a><?php endif ?></div></template>
    <?php endforeach ?>

    <form action="<?= url('/contact/message-rapide') ?>" method="post" class="shrink-0 border-t border-ligne bg-white p-3" novalidate data-form-aide>
      <?= csrf() ?>
      <input type="hidden" name="suivant" value="<?= e(adresse_actuelle()) ?>">
      <div class="hidden" aria-hidden="true"><label>Ne pas remplir <input type="text" name="site_web" tabindex="-1" autocomplete="off"></label></div>
      <?php if (!$u): ?>
      <label for="aide-email" class="sr-only">Votre e-mail</label>
      <input id="aide-email" name="email" type="email" maxlength="190" autocomplete="email" inputmode="email" placeholder="Votre e-mail, pour recevoir la réponse" class="<?= $champ ?> mb-2" data-identite>
      <?php endif ?>
      <div class="flex items-end gap-2">
        <label for="aide-message" class="sr-only">Votre message</label>
        <textarea id="aide-message" name="message" rows="2" maxlength="3000" placeholder="Écrire un message… (Entrée pour aller à la ligne)" class="<?= $champ ?> max-h-32 min-h-16 resize-none py-2.5 text-[15px]" data-champ-message></textarea>
        <button type="submit" class="grid size-11 shrink-0 place-items-center rounded-full bg-orange text-white transition-colors hover:bg-vert" aria-label="Envoyer">
          <svg class="size-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M4 4.5 21 12 4 19.5l2.2-7.5L4 4.5z"/></svg>
        </button>
      </div>
    </form>
  </div>

  <button type="button" class="aide-bouton grid size-12 place-items-center rounded-full text-white shadow-lg transition-colors sm:size-14" aria-expanded="false" aria-controls="panneau-aide" data-ouvrir-aide>
    <span class="sr-only">Ouvrir l'assistant</span>
    <svg class="aide-point aide-icone-message size-6 sm:size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 12a8 8 0 0 1-11.6 7.1L4 20l1.1-4A8 8 0 1 1 20 12z"/><path d="M8.5 12h.01M12 12h.01M15.5 12h.01" stroke-width="3"/></svg>
    <svg class="aide-icone-croix hidden size-6 sm:size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
  </button>
</div>
