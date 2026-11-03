<?php
/* Fenêtre « Parlez-nous de votre initiative » : un vrai formulaire, au centre de l'écran.
   Le message arrive dans Administration > Messages de contact avec l'objet « Proposition d'initiative ». */
$u = utilisateur();
$champ = 'min-h-12 w-full rounded-lg border border-ligne bg-white px-4 text-[15px] text-encre placeholder:text-texte/60 focus:border-vert focus:ring-[3px] focus:ring-orange/35 focus:outline-none';
?>
<dialog id="dialogue-initiative" class="dialogue m-auto w-[min(40rem,calc(100vw-1.5rem))] max-h-[calc(100dvh-1.5rem)] overflow-hidden rounded-3xl border-0 bg-white p-0 shadow-2xl" aria-labelledby="titre-proposition">
  <div class="flex max-h-[calc(100dvh-1.5rem)] flex-col">
    <div class="relative flex items-start justify-between gap-4 bg-orange px-6 py-5 text-white sm:px-8 sm:py-6">
      <div>
        <h2 id="titre-proposition" class="text-2xl leading-tight font-bold text-white sm:text-3xl">Parlez-nous de votre initiative</h2>
        <p class="mt-1.5 text-[15px] text-white/95">Présentez-la en quelques lignes : l'équipe du chapitre vous recontactera par e-mail.</p>
      </div>
      <button type="button" class="-mt-1 -mr-2 grid size-11 shrink-0 place-items-center rounded-full text-white transition-colors hover:bg-white hover:text-orange" data-fermer-dialogue aria-label="Fermer">
        <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
      </button>
    </div>

    <form action="<?= url('/contact/message-rapide') ?>" method="post" class="grid gap-4 overflow-y-auto px-6 py-6 sm:grid-cols-2 sm:px-8" novalidate data-form-dialogue>
      <?= csrf() ?>
      <input type="hidden" name="suivant" value="<?= e(adresse_actuelle()) ?>">
      <input type="hidden" name="sujet" value="initiative">
      <div class="hidden" aria-hidden="true"><label>Ne pas remplir <input type="text" name="site_web" tabindex="-1" autocomplete="off"></label></div>
      <?php if (!$u): ?>
      <div><label for="prop-nom" class="libelle">Votre nom</label><input id="prop-nom" name="nom" type="text" maxlength="120" autocomplete="name" class="<?= $champ ?>"></div>
      <div><label for="prop-email" class="libelle">Votre e-mail</label><input id="prop-email" name="email" type="email" maxlength="190" autocomplete="email" inputmode="email" class="<?= $champ ?>"></div>
      <?php else: ?>
      <p class="rounded-xl bg-gris px-4 py-3 text-sm sm:col-span-2">Envoyé au nom de <strong class="text-encre"><?= e($u['nom_affiche']) ?></strong>, réponse à <strong class="break-all text-encre"><?= e($u['email']) ?></strong></p>
      <?php endif ?>
      <div><label for="prop-titre" class="libelle">Nom de l'initiative</label><input id="prop-titre" name="initiative" type="text" maxlength="150" placeholder="Ex. : Jardins scolaires de Ségou" class="<?= $champ ?>"></div>
      <div><label for="prop-lieu" class="libelle">Où a-t-elle lieu ? <span class="font-normal text-texte">(facultatif)</span></label><input id="prop-lieu" name="lieu" type="text" maxlength="150" placeholder="Ville ou région" class="<?= $champ ?>"></div>
      <div class="sm:col-span-2"><label for="prop-tel" class="libelle">Téléphone <span class="font-normal text-texte">(facultatif)</span></label><input id="prop-tel" name="telephone" type="tel" maxlength="30" autocomplete="tel" class="<?= $champ ?>"></div>
      <div class="sm:col-span-2"><label for="prop-message" class="libelle">Présentez votre initiative</label><textarea id="prop-message" name="message" rows="5" maxlength="3000" placeholder="Ce qu'elle fait, à qui elle s'adresse, où elle en est, et ce que vous attendez du chapitre…" class="<?= $champ ?> min-h-36 py-3"></textarea></div>
      <p class="hidden text-sm font-medium text-rouge sm:col-span-2" data-erreur-dialogue role="alert"></p>
      <div class="flex flex-wrap gap-3 sm:col-span-2">
        <button type="submit" class="btn btn-orange min-h-12 flex-1 sm:flex-none sm:px-9">Envoyer mon initiative</button>
        <button type="button" class="btn btn-contour min-h-12" data-fermer-dialogue>Annuler</button>
      </div>
    </form>

    <div class="hidden px-6 py-10 text-center sm:px-8" data-merci-dialogue role="status">
      <span class="mx-auto grid size-16 place-items-center rounded-full bg-vert text-white" aria-hidden="true"><svg class="size-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg></span>
      <p class="mt-5 text-lg font-bold text-vert">Merci, votre initiative a bien été transmise</p>
      <p class="mt-1 text-[15px]">L'équipe du chapitre vous recontactera par e-mail.</p>
      <button type="button" class="btn btn-vert mt-6" data-fermer-dialogue>Fermer</button>
    </div>
  </div>
</dialog>
