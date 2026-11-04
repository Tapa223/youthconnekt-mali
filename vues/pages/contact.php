<?php /** Page Contact */ $admin = est_admin(); $reseaux = reseaux_sociaux(); ?>
<?php
// Coordonnées (saisies dans les paramètres du site) : [icône, libellé, valeur, lien]
$coordonnees = array_filter([
    $p['email_contact'] ? ['mail', 'E-mail', $p['email_contact'], 'mailto:' . $p['email_contact']] : null,
    $p['telephone'] ? ['tel', 'Téléphone', $p['telephone'], 'tel:' . str_replace(' ', '', $p['telephone'])] : null,
    $p['adresse'] ? ['lieu', 'Adresse', $p['adresse'], ''] : null,
    $p['horaires'] ? ['heure', 'Horaires', $p['horaires'], ''] : null,
]);
$traces = ICONES_CONTACT;
?>
<section class="bg-gris pb-8 sm:py-6 lg:py-5">
  <div class="conteneur anim-entree grid gap-4 max-sm:px-0 sm:gap-6 lg:grid-cols-[minmax(0,5fr)_minmax(0,7fr)]">
    <div class="relative isolate overflow-hidden bg-vert px-4 pt-5 pb-6 text-white sm:rounded-3xl sm:p-8">
      <img src="<?= asset('img/bandeaux/lets-connekt.jpg') ?>" alt="" class="absolute inset-0 -z-10 h-full w-full object-cover opacity-15">
      <?php partiel('partials/reseau_deco', ['opacite' => 'opacity-50']) ?>
      <div class="relative">
        <h1 class="text-[2rem] leading-none font-bold text-white sm:text-5xl">Parlons-en</h1>
        <p class="mt-2.5 text-[15px] text-white/90 sm:mt-3 sm:text-base">Une question, un projet, un partenariat ? L'équipe du chapitre vous répond.</p>
        <ul class="mt-4 grid gap-1.5 sm:mt-7 sm:gap-3.5">
          <?php foreach ($coordonnees as [$icone, $libelle, $valeur, $lien]): ?>
          <li class="flex items-center gap-2 rounded-2xl bg-white/10 p-2 sm:gap-3 sm:bg-transparent sm:p-0">
            <span class="grid size-8 shrink-0 place-items-center rounded-full bg-white/15 sm:size-10" aria-hidden="true"><svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><?= $traces[$icone] ?></svg></span>
            <span class="min-w-0"><span class="block text-[11px] text-white/75 sm:text-xs"><?= $libelle ?></span>
              <?php if ($lien): ?><a href="<?= e($lien) ?>" class="block truncate text-[13px] font-bold hover:text-orange sm:text-[15px]"><?= e($valeur) ?></a>
              <?php else: ?><span class="block text-[13px] leading-snug font-bold sm:text-[15px]"><?= e($valeur) ?></span><?php endif ?>
            </span>
          </li>
          <?php endforeach ?>
        </ul>
        <?php if ($reseaux): ?>
        <p class="mt-6 text-xs font-bold tracking-[0.14em] uppercase">Suivez le chapitre</p>
        <div class="[&_a]:border-white [&_a]:bg-white [&_a]:text-vert"><?php partiel('partials/reseaux') ?></div>
        <?php endif ?>
      </div>
    </div>

    <div class="mx-4 rounded-3xl bg-white p-5 shadow-xl shadow-vert/5 sm:mx-0 sm:p-8">
      <h2 class="text-xl font-bold sm:text-2xl">Envoyer un message</h2>
      <form method="post" class="mt-4 grid gap-4 sm:mt-5 sm:grid-cols-2 sm:gap-x-5" novalidate>
        <?= csrf() ?>
        <div class="hidden" aria-hidden="true"><label>Ne pas remplir <input type="text" name="site_web" tabindex="-1" autocomplete="off"></label></div>
        <?php partiel('partials/champ', ['nom' => 'nom', 'libelle' => 'Nom complet', 'valeur' => $valeurs['nom'], 'erreur' => $erreurs['nom'] ?? '', 'attributs' => ['autocomplete' => 'name', 'placeholder' => 'Votre nom', 'maxlength' => 120]]) ?>
        <?php partiel('partials/champ', ['nom' => 'email', 'libelle' => 'Adresse e-mail', 'type' => 'email', 'valeur' => $valeurs['email'], 'erreur' => $erreurs['email'] ?? '', 'attributs' => ['autocomplete' => 'email', 'placeholder' => 'vous@exemple.com', 'maxlength' => 190]]) ?>
        <?php partiel('partials/champ', ['nom' => 'profil', 'libelle' => 'Vous êtes', 'type' => 'select', 'options' => PROFILS_CONTACT, 'valeur' => $valeurs['profil'], 'erreur' => $erreurs['profil'] ?? '']) ?>
        <?php partiel('partials/champ', ['nom' => 'sujet', 'libelle' => 'Sujet', 'type' => 'select', 'options' => ['' => 'Choisir un sujet'] + SUJETS_CONTACT, 'valeur' => $valeurs['sujet'], 'erreur' => $erreurs['sujet'] ?? '']) ?>
        <?php partiel('partials/champ', ['nom' => 'message', 'libelle' => 'Message', 'type' => 'textarea', 'valeur' => $valeurs['message'], 'erreur' => $erreurs['message'] ?? '', 'classes_bloc' => 'sm:col-span-2', 'attributs' => ['rows' => 4, 'placeholder' => 'Écrivez votre message…', 'maxlength' => 5000]]) ?>
        <?php partiel('partials/case', ['nom' => 'consentement', 'libelle_html' => 'J\'accepte que mes données soient utilisées pour répondre à ma demande, conformément à la <a href="' . url('/confidentialite') . '" class="lien-texte">politique de confidentialité</a>.', 'coche' => $valeurs['consentement'] !== '', 'erreur' => $erreurs['consentement'] ?? '', 'classes_bloc' => 'sm:col-span-2']) ?>
        <div class="sm:col-span-2"><button type="submit" class="btn btn-orange min-h-12 w-full px-8 text-base sm:w-auto">Envoyer le message</button></div>
      </form>
    </div>
  </div>
</section>
