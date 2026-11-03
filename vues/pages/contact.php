<?php /** Page Contact */ $admin = est_admin(); $reseaux = reseaux_sociaux(); ?>
<section class="bg-gris py-10 sm:py-14">
  <div class="conteneur anim-entree grid gap-6 lg:grid-cols-[minmax(0,5fr)_minmax(0,7fr)] lg:gap-7">
    <div class="relative isolate overflow-hidden rounded-3xl bg-vert p-7 text-white sm:p-10">
      <img src="<?= asset('img/bandeaux/lets-connekt.jpg') ?>" alt="" class="absolute inset-0 -z-10 h-full w-full object-cover opacity-15">
      <?php partiel('partials/reseau_deco', ['opacite' => 'opacity-50']) ?>
      <div class="relative">
        <p class="surtitre surtitre-trait text-white/85">Contact</p>
        <h1 class="mt-3 text-4xl font-bold text-white sm:text-5xl">Parlons-en</h1>
        <p class="mt-4 text-base text-white/90">Une question, un projet, un partenariat ? L'équipe du chapitre vous répond.</p>
        <ul class="mt-8 space-y-4">
          <?php if ($p['email_contact']): ?>
          <li class="flex items-center gap-4"><span class="grid size-11 shrink-0 place-items-center rounded-full bg-white/15" aria-hidden="true"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg></span>
            <span><span class="block text-xs text-white/75">E-mail</span><a href="mailto:<?= e($p['email_contact']) ?>" class="font-bold break-all hover:text-orange"><?= e($p['email_contact']) ?></a></span></li>
          <?php endif ?>
          <?php if ($p['telephone']): ?>
          <li class="flex items-center gap-4"><span class="grid size-11 shrink-0 place-items-center rounded-full bg-white/15" aria-hidden="true"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2"/></svg></span>
            <span><span class="block text-xs text-white/75">Téléphone</span><a href="tel:<?= e(str_replace(' ', '', $p['telephone'])) ?>" class="font-bold hover:text-orange"><?= e($p['telephone']) ?></a></span></li>
          <?php endif ?>
          <?php if ($p['adresse']): ?>
          <li class="flex items-center gap-4"><span class="grid size-11 shrink-0 place-items-center rounded-full bg-white/15" aria-hidden="true"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s7-6.2 7-12a7 7 0 0 0-14 0c0 5.8 7 12 7 12z"/><circle cx="12" cy="9" r="2.5"/></svg></span>
            <span><span class="block text-xs text-white/75">Adresse</span><span class="font-bold"><?= e($p['adresse']) ?></span></span></li>
          <?php endif ?>
          <?php if ($p['horaires']): ?>
          <li class="flex items-center gap-4"><span class="grid size-11 shrink-0 place-items-center rounded-full bg-white/15" aria-hidden="true"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></span>
            <span><span class="block text-xs text-white/75">Horaires</span><span class="font-bold"><?= e($p['horaires']) ?></span></span></li>
          <?php endif ?>
        </ul>
        <?php if ($reseaux): ?>
        <p class="mt-8 text-xs font-bold tracking-[0.14em] uppercase">Suivez le chapitre</p>
        <div class="[&_a]:border-white [&_a]:bg-white [&_a]:text-vert"><?php partiel('partials/reseaux') ?></div>
        <?php endif ?>
      </div>
    </div>

    <div class="rounded-3xl bg-white p-6 shadow-xl shadow-vert/5 sm:p-10">
      <h2 class="text-2xl font-bold sm:text-3xl">Envoyer un message</h2>
      <form method="post" class="mt-6 grid gap-5 sm:grid-cols-2" novalidate>
        <?= csrf() ?>
        <div class="hidden" aria-hidden="true"><label>Ne pas remplir <input type="text" name="site_web" tabindex="-1" autocomplete="off"></label></div>
        <?php partiel('partials/champ', ['nom' => 'nom', 'libelle' => 'Nom complet', 'valeur' => $valeurs['nom'], 'erreur' => $erreurs['nom'] ?? '', 'attributs' => ['autocomplete' => 'name', 'placeholder' => 'Votre nom', 'maxlength' => 120]]) ?>
        <?php partiel('partials/champ', ['nom' => 'email', 'libelle' => 'Adresse e-mail', 'type' => 'email', 'valeur' => $valeurs['email'], 'erreur' => $erreurs['email'] ?? '', 'attributs' => ['autocomplete' => 'email', 'placeholder' => 'vous@exemple.com', 'maxlength' => 190]]) ?>
        <?php partiel('partials/champ', ['nom' => 'profil', 'libelle' => 'Vous êtes', 'type' => 'select', 'options' => PROFILS_CONTACT, 'valeur' => $valeurs['profil'], 'erreur' => $erreurs['profil'] ?? '']) ?>
        <?php partiel('partials/champ', ['nom' => 'sujet', 'libelle' => 'Sujet', 'type' => 'select', 'options' => ['' => 'Choisir un sujet'] + SUJETS_CONTACT, 'valeur' => $valeurs['sujet'], 'erreur' => $erreurs['sujet'] ?? '']) ?>
        <?php partiel('partials/champ', ['nom' => 'message', 'libelle' => 'Message', 'type' => 'textarea', 'valeur' => $valeurs['message'], 'erreur' => $erreurs['message'] ?? '', 'classes_bloc' => 'sm:col-span-2', 'attributs' => ['rows' => 6, 'placeholder' => 'Écrivez votre message…', 'maxlength' => 5000]]) ?>
        <?php partiel('partials/case', ['nom' => 'consentement', 'libelle_html' => 'J\'accepte que mes données soient utilisées pour répondre à ma demande, conformément à la <a href="' . url('/confidentialite') . '" class="lien-texte">politique de confidentialité</a>.', 'coche' => $valeurs['consentement'] !== '', 'erreur' => $erreurs['consentement'] ?? '', 'classes_bloc' => 'sm:col-span-2']) ?>
        <div class="sm:col-span-2"><button type="submit" class="btn btn-orange min-h-13 w-full px-8 text-base sm:w-auto">Envoyer le message</button></div>
      </form>
    </div>
  </div>
</section>
