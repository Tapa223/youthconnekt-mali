<?php
/* Fenêtre « Proposer une opportunité ». Un membre connecté remplit le formulaire ; l'opportunité est enregistrée
   « à valider » et l'équipe la publie depuis l'administration. Paramètre : $types */
$u = utilisateur();
$champ = 'min-h-12 w-full rounded-lg border border-ligne bg-white px-4 text-[15px] text-encre placeholder:text-texte/60 focus:border-vert focus:ring-[3px] focus:ring-orange/35 focus:outline-none';
$suivant = url('/opportunites') . '?proposer=1';
?>
<dialog id="dialogue-opportunite" class="dialogue m-auto w-[min(44rem,calc(100vw-1.5rem))] max-h-[calc(100dvh-1.5rem)] overflow-hidden rounded-3xl border-0 bg-white p-0 shadow-2xl" aria-labelledby="titre-dialogue-opportunite">
  <div class="flex max-h-[calc(100dvh-1.5rem)] flex-col">
    <div class="flex items-start justify-between gap-4 bg-orange px-6 py-5 text-white sm:px-8 sm:py-6">
      <div>
        <h2 id="titre-dialogue-opportunite" class="text-2xl leading-tight font-bold text-white sm:text-3xl">Proposer une opportunité</h2>
        <p class="mt-1.5 text-[15px] text-white/95">Elle sera publiée sur le site après validation par l'équipe du chapitre.</p>
      </div>
      <button type="button" class="-mt-1 -mr-2 grid size-11 shrink-0 place-items-center rounded-full text-white transition-colors hover:bg-white hover:text-orange" data-fermer-dialogue aria-label="Fermer">
        <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
      </button>
    </div>

    <?php if ($u && est_membre($u)): ?>
    <form action="<?= url('/opportunites/proposer') ?>" method="post" enctype="multipart/form-data" class="grid gap-4 overflow-y-auto px-6 py-6 sm:grid-cols-2 sm:px-8" novalidate data-form-dialogue>
      <?= csrf() ?>
      <p class="rounded-xl bg-gris px-4 py-3 text-sm sm:col-span-2">Proposée par <strong class="text-encre"><?= e($u['nom_affiche']) ?></strong>. L'équipe vous écrira à <strong class="break-all text-encre"><?= e($u['email']) ?></strong> si besoin.</p>
      <div class="sm:col-span-2"><label for="opp-titre" class="libelle">Titre de l'opportunité</label><input id="opp-titre" name="titre" type="text" maxlength="200" placeholder="Ex. : Bourse de formation en marketing digital" class="<?= $champ ?>"></div>
      <div><label for="opp-type" class="libelle">Type</label>
        <select id="opp-type" name="type" class="<?= $champ ?> pr-10"><option value="">Choisir…</option><?php foreach ($types as $slug_type => $t): ?><option value="<?= e($slug_type) ?>"><?= e($t['nom']) ?></option><?php endforeach ?></select></div>
      <div><label for="opp-organisme" class="libelle">Organisme <span class="font-normal text-texte">(facultatif)</span></label><input id="opp-organisme" name="organisme" type="text" maxlength="150" value="<?= $u['type_compte'] === 'organisation' ? e($u['nom_affiche']) : '' ?>" class="<?= $champ ?>"></div>
      <div><label for="opp-date" class="libelle">Date limite <span class="font-normal text-texte">(facultatif)</span></label><input id="opp-date" name="date_limite" type="date" min="<?= date('Y-m-d') ?>" class="<?= $champ ?>"></div>
      <div><label for="opp-lieu" class="libelle">Lieu <span class="font-normal text-texte">(facultatif)</span></label><input id="opp-lieu" name="lieu" type="text" maxlength="120" placeholder="Ex. : Bamako, en ligne" class="<?= $champ ?>"></div>
      <div class="sm:col-span-2"><label for="opp-lien" class="libelle">Lien pour candidater <span class="font-normal text-texte">(facultatif)</span></label><input id="opp-lien" name="lien_candidature" type="url" maxlength="255" placeholder="https://" class="<?= $champ ?>"></div>
      <div class="sm:col-span-2"><label for="opp-description" class="libelle">Description</label><textarea id="opp-description" name="description" rows="5" maxlength="8000" placeholder="À qui elle s'adresse, ce qu'elle apporte, les conditions et comment postuler…" class="<?= $champ ?> min-h-36 py-3"></textarea></div>
      <div class="sm:col-span-2"><label for="opp-image" class="libelle">Image <span class="font-normal text-texte">(facultatif)</span></label>
        <input id="opp-image" name="image" type="file" accept="image/jpeg,image/png,image/webp" class="block w-full rounded-lg border border-dashed border-ligne bg-gris px-3 py-3 text-sm file:mr-3 file:rounded-full file:border-0 file:bg-vert file:px-4 file:py-2 file:text-sm file:font-bold file:text-white">
        <p class="mt-1 text-xs text-texte">JPG, PNG ou WebP, 3 Mo maximum (affiche, logo ou photo).</p></div>
      <p class="hidden text-sm font-medium text-rouge sm:col-span-2" data-erreur-dialogue role="alert"></p>
      <div class="flex flex-wrap gap-3 sm:col-span-2">
        <button type="submit" class="btn btn-orange min-h-12 flex-1 sm:flex-none sm:px-9">Envoyer l'opportunité</button>
        <button type="button" class="btn btn-contour min-h-12" data-fermer-dialogue>Annuler</button>
      </div>
    </form>
    <div class="hidden px-6 py-10 text-center sm:px-8" data-merci-dialogue role="status">
      <span class="mx-auto grid size-16 place-items-center rounded-full bg-vert text-white" aria-hidden="true"><svg class="size-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg></span>
      <p class="mt-5 text-lg font-bold text-vert">Merci, votre opportunité a bien été envoyée</p>
      <p class="mt-1 text-[15px]">Elle sera publiée après validation par l'équipe du chapitre.</p>
      <button type="button" class="btn btn-vert mt-6" data-fermer-dialogue>Fermer</button>
    </div>

    <?php elseif ($u): ?>
    <div class="px-6 py-8 sm:px-8">
      <p class="text-[15px]">Vous êtes connecté avec un compte d'administration : ajoutez l'opportunité directement dans l'administration.</p>
      <a href="<?= url('/gestion/opportunites/ajouter') ?>" class="btn btn-vert mt-5">Ajouter une opportunité</a>
    </div>

    <?php else: ?>
    <div class="px-6 py-8 sm:px-8">
      <p class="text-[15px] leading-relaxed">Pour proposer une opportunité, connectez-vous à votre compte membre, individuel ou organisation. Vous n'avez pas encore de compte ? Rejoignez le chapitre en quelques minutes.</p>
      <div class="mt-6 flex flex-wrap gap-3">
        <a href="<?= url('/compte/connexion', ['suivant' => $suivant]) ?>" class="btn btn-vert min-h-12 px-7">Se connecter</a>
        <a href="<?= url('/compte/inscription') ?>" class="btn btn-contour min-h-12 px-7">Rejoindre le chapitre</a>
      </div>
    </div>
    <?php endif ?>
  </div>
</dialog>
