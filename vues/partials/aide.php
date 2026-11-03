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
$plus_haut = str_starts_with(chemin_actuel(), '/opportunites/'); // laisse la place à la barre « Postuler » sur téléphone
$prenom = $u ? ($u['prenom_affiche'] ?? '') : '';
$sujets = [
    ['Rejoindre le chapitre', "Il suffit de créer votre compte, en tant que membre individuel ou en tant qu'organisation. C'est gratuit et votre compte fait de vous un membre du chapitre YouthConnekt Mali.", $u ? null : ['Créer mon compte', url('/compte/inscription')]],
    ['Trouver une opportunité', "Formations, bourses, stages, appels à candidatures : tout est sur la page Opportunités, trié par date limite. Une fois membre d'une communauté, celles qui vous concernent s'affichent aussi dans votre espace.", ['Voir les opportunités', url('/opportunites')]],
    ['Les communautés', "Chaque communauté réunit des jeunes autour d'un domaine ou d'un profil. Ouvrez celle qui vous intéresse et cliquez sur « Intégrer la communauté » : l'équipe étudie votre demande.", ['Voir les communautés', url('/communaute')]],
    ['Proposer une opportunité', "Connecté à votre compte membre, cliquez sur « Proposer une opportunité » en bas de la page Opportunités. Elle est publiée après validation par l'équipe.", ['Proposer une opportunité', url('/opportunites') . '?proposer=1']],
    ['Mon compte', "Mot de passe oublié ? Demandez-en un nouveau depuis la page de connexion. Pas reçu l'e-mail de confirmation ? Regardez dans vos courriers indésirables ou connectez-vous pour en recevoir un nouveau.", $u ? ($membre ? ['Mon espace', url('/compte/mon-espace')] : null) : ['Mot de passe oublié', url('/compte/mot-de-passe-oublie')]],
];
$champ = 'min-h-10 w-full rounded-lg border border-ligne bg-white px-3 text-sm text-encre placeholder:text-texte/60 focus:border-vert focus:ring-[3px] focus:ring-orange/35 focus:outline-none';
?>
<div class="aide fixed right-4 z-40 sm:right-6 <?= $plus_haut ? 'bottom-24 lg:bottom-6' : 'bottom-4 sm:bottom-6' ?>" data-aide>
  <div id="panneau-aide" class="aide-panneau absolute right-0 bottom-[calc(100%+12px)] flex max-h-[min(36rem,calc(100dvh-7rem))] w-[min(24rem,calc(100vw-2rem))] flex-col overflow-hidden rounded-2xl border border-ligne bg-white shadow-2xl shadow-vert/20" role="dialog" aria-labelledby="titre-aide" hidden>
    <div class="flex shrink-0 items-start justify-between gap-3 bg-vert px-5 py-4 text-white">
      <div>
        <p id="titre-aide" class="text-lg font-bold">Assistant YouthConnekt Mali</p>
        <p class="text-sm text-white/85">Réponses rapides, ou un message à l'équipe</p>
      </div>
      <button type="button" class="-mt-1 -mr-2 grid size-10 shrink-0 place-items-center rounded-full text-white transition-colors hover:bg-orange" data-fermer-aide aria-label="Fermer">
        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
      </button>
    </div>

    <div class="aide-fil flex-1 space-y-3 overflow-y-auto bg-gris/60 px-4 py-4" data-fil aria-live="polite">
      <div class="aide-bot">Bonjour<?= $prenom !== '' ? ' ' . e($prenom) : '' ?> ! Sur quel sujet puis-je vous orienter&nbsp;?</div>
      <div class="flex flex-wrap gap-2" data-sujets>
        <?php foreach ($sujets as $k => [$libelle, $reponse, $lien]): ?>
        <button type="button" class="aide-sujet" data-sujet="<?= $k ?>"><?= e($libelle) ?></button>
        <?php endforeach ?>
        <button type="button" class="aide-sujet aide-sujet-autre" data-autre>Autre question</button>
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
      <div class="mb-2 hidden grid-cols-2 gap-2" data-identite>
        <div><label for="aide-nom" class="sr-only">Votre nom</label><input id="aide-nom" name="nom" type="text" maxlength="120" autocomplete="name" placeholder="Votre nom" class="<?= $champ ?>"></div>
        <div><label for="aide-email" class="sr-only">Votre e-mail</label><input id="aide-email" name="email" type="email" maxlength="190" autocomplete="email" inputmode="email" placeholder="Votre e-mail" class="<?= $champ ?>"></div>
      </div>
      <?php endif ?>
      <div class="flex items-end gap-2">
        <label for="aide-message" class="sr-only">Votre message</label>
        <textarea id="aide-message" name="message" rows="1" maxlength="3000" placeholder="Écrire un message…" class="<?= $champ ?> max-h-28 min-h-11 resize-none py-2.5 text-[15px]" data-champ-message></textarea>
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
