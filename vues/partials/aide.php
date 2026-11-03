<?php
/*
 * Bulle en bas à droite : le visiteur envoie directement une question ou une suggestion.
 * Les messages arrivent dans Administration > Messages de contact.
 * La couleur du bouton s'adapte au fond : orange sur le vert, vert sur le clair.
 */
$u = utilisateur();
$plus_haut = str_starts_with(chemin_actuel(), '/opportunites/'); // laisse la place à la barre « Postuler » sur téléphone
$champ = 'min-h-11 w-full rounded-lg border border-ligne bg-white px-3.5 text-[15px] text-encre placeholder:text-texte/60 focus:border-vert focus:ring-[3px] focus:ring-orange/35 focus:outline-none';
?>
<div class="aide fixed right-4 z-40 sm:right-6 <?= $plus_haut ? 'bottom-24 lg:bottom-6' : 'bottom-4 sm:bottom-6' ?>" data-aide>
  <div id="panneau-aide" class="aide-panneau absolute right-0 bottom-[calc(100%+12px)] w-[min(23rem,calc(100vw-2rem))] overflow-hidden rounded-2xl border border-ligne bg-white shadow-2xl shadow-vert/20" role="dialog" aria-labelledby="titre-aide" hidden>
    <div class="flex items-start justify-between gap-3 bg-vert px-5 py-4 text-white">
      <div>
        <p id="titre-aide" class="text-lg font-bold">Une question, une suggestion&nbsp;?</p>
        <p class="text-sm text-white/85">Écrivez-nous, l'équipe vous répond par e-mail</p>
      </div>
      <button type="button" class="-mt-1 -mr-2 grid size-10 shrink-0 place-items-center rounded-full text-white transition-colors hover:bg-orange" data-fermer-aide aria-label="Fermer">
        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
      </button>
    </div>

    <form action="<?= url('/contact/message-rapide') ?>" method="post" class="grid gap-3 p-4" novalidate data-form-aide>
      <?= csrf() ?>
      <input type="hidden" name="suivant" value="<?= e(adresse_actuelle()) ?>">
      <div class="hidden" aria-hidden="true"><label>Ne pas remplir <input type="text" name="site_web" tabindex="-1" autocomplete="off"></label></div>
      <fieldset class="grid grid-cols-2 gap-1 rounded-xl bg-gris p-1">
        <legend class="sr-only">Type de message</legend>
        <label class="aide-choix"><input type="radio" name="type" value="question" class="sr-only" checked> Une question</label>
        <label class="aide-choix"><input type="radio" name="type" value="suggestion" class="sr-only"> Une suggestion</label>
      </fieldset>
      <?php if (!$u): ?>
      <div class="grid grid-cols-2 gap-2">
        <div><label for="aide-nom" class="sr-only">Votre nom</label><input id="aide-nom" name="nom" type="text" maxlength="120" autocomplete="name" placeholder="Votre nom" class="<?= $champ ?>"></div>
        <div><label for="aide-email" class="sr-only">Votre e-mail</label><input id="aide-email" name="email" type="email" maxlength="190" autocomplete="email" inputmode="email" placeholder="Votre e-mail" class="<?= $champ ?>"></div>
      </div>
      <?php else: ?>
      <p class="text-sm">Réponse envoyée à <strong class="break-all text-encre"><?= e($u['email']) ?></strong></p>
      <?php endif ?>
      <div><label for="aide-message" class="sr-only">Votre message</label><textarea id="aide-message" name="message" rows="4" maxlength="3000" placeholder="Votre message…" class="<?= $champ ?> min-h-28 py-2.5"></textarea></div>
      <p class="hidden text-sm font-medium text-rouge" data-erreur-aide role="alert"></p>
      <button type="submit" class="btn btn-orange min-h-12 w-full">Envoyer</button>
    </form>

    <div class="hidden p-6 text-center" data-merci-aide role="status">
      <span class="mx-auto grid size-14 place-items-center rounded-full bg-vert text-white" aria-hidden="true"><svg class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg></span>
      <p class="mt-4 text-[15px] text-encre" data-texte-merci></p>
      <button type="button" class="btn btn-contour mt-5 min-h-11" data-nouveau-aide>Envoyer un autre message</button>
    </div>
  </div>

  <button type="button" class="aide-bouton grid size-14 place-items-center rounded-full text-white shadow-lg transition-colors" aria-expanded="false" aria-controls="panneau-aide" data-ouvrir-aide>
    <span class="sr-only">Une question, une suggestion ?</span>
    <span class="text-[28px] leading-none font-bold" aria-hidden="true">?</span>
  </button>
</div>
