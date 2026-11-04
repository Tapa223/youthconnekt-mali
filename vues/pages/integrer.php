<?php
/* Formulaire « Intégrer la communauté ». Les informations du profil sont reprises automatiquement. */
$p = $u['profil'] ?? [];
$organisation = $u['type_compte'] === 'organisation';
$infos = array_filter([
    $organisation ? 'Organisation' : 'Nom' => $u['nom_affiche'],
    'Profil' => libelle_profil($p, $u['type_compte']),
    'Région' => trim(($p['ville'] ?? '') . ((($p['ville'] ?? '') !== '' && isset(REGIONS[$p['region'] ?? ''])) ? ', ' : '') . (REGIONS[$p['region'] ?? ''] ?? '')),
    $organisation ? 'Secteur' : 'Domaine' => $organisation ? ($p['secteur'] ?? '') : ($p['domaine'] ?? ''),
    'E-mail' => $u['email'],
]);
?>
<section class="relative isolate overflow-hidden bg-vert text-white">
  <?php partiel('partials/reseau_deco') ?>
  <div class="conteneur relative pt-2 pb-8 sm:py-14">
    <?php partiel('partials/retour', ['url' => $c['url'], 'clair' => true]) ?>
    <p class="surtitre surtitre-trait text-white/85">Intégrer la communauté</p>
    <h1 class="mt-3 text-4xl leading-tight font-bold text-white sm:text-5xl"><?= e($c['nom']) ?></h1>
    <p class="mt-3 max-w-xl text-white/90"><strong>Pour :</strong> <?= e($c['public_vise']) ?></p>
  </div>
</section>

<section class="bg-gris py-10 sm:py-14">
  <div class="conteneur grid gap-6 lg:grid-cols-[minmax(0,7fr)_minmax(0,5fr)]">
    <form method="post" class="rounded-3xl bg-white p-6 sm:p-9" novalidate>
      <?= csrf() ?>
      <h2 class="text-2xl font-bold">Votre demande</h2>
      <p class="mt-1 text-[15px]"><?= $refusee ? 'Votre précédente demande n\'a pas été retenue : vous pouvez en envoyer une nouvelle.' : 'Quelques mots suffisent. Réponse sous 24 heures : sans réponse de l\'équipe, vous êtes intégré automatiquement.' ?></p>
      <?php if ($erreurs): ?><p class="mt-5 rounded-xl border border-rouge/30 bg-rouge/5 px-4 py-3 text-sm text-rouge" role="alert">Certaines informations sont à compléter.</p><?php endif ?>
      <div class="mt-6 grid gap-5">
        <?php partiel('partials/champ', ['nom' => 'motivation', 'libelle' => "Qu'est-ce qui vous amène dans cette communauté ?", 'type' => 'textarea', 'valeur' => $valeurs['motivation'], 'erreur' => $erreurs['motivation'] ?? '', 'attributs' => ['rows' => 4, 'maxlength' => 1000, 'placeholder' => 'Ce qui vous intéresse, ce que vous espérez y trouver ou y apprendre…']]) ?>
        <?php partiel('partials/champ', ['nom' => 'projet', 'libelle' => 'Portez-vous déjà une initiative ou un projet dans ce domaine ?', 'type' => 'textarea', 'requis' => false, 'aide' => 'Si oui, présentez-le en une ou deux phrases. Sinon, laissez vide.', 'valeur' => $valeurs['projet'], 'erreur' => $erreurs['projet'] ?? '', 'attributs' => ['rows' => 3, 'maxlength' => 1000, 'placeholder' => 'Exemple : un club de code au lycée de Kati, une petite exploitation maraîchère…']]) ?>
        <?php partiel('partials/champ', ['nom' => 'apport', 'libelle' => 'Que pourriez-vous partager avec les autres membres ?', 'type' => 'textarea', 'requis' => false, 'valeur' => $valeurs['apport'], 'erreur' => $erreurs['apport'] ?? '', 'attributs' => ['rows' => 3, 'maxlength' => 1000, 'placeholder' => 'Une compétence, une expérience, du temps, un contact…']]) ?>
        <?php partiel('partials/case', ['nom' => 'accord', 'libelle' => "J'accepte que les informations de mon profil soient transmises à l'équipe qui anime la communauté.", 'coche' => $valeurs['accord'] !== '', 'erreur' => $erreurs['accord'] ?? '']) ?>
      </div>
      <div class="mt-7 flex flex-wrap items-center gap-3">
        <button type="submit" class="btn btn-orange min-h-13 px-8 text-base">Envoyer ma demande</button>
        <a href="<?= e($c['url']) ?>" class="btn btn-contour min-h-13">Annuler</a>
      </div>
    </form>

    <aside class="h-fit rounded-3xl bg-white p-6 sm:p-7">
      <h2 class="text-lg font-bold">Informations transmises avec votre demande</h2>
      <p class="mt-1 text-sm">Elles viennent de votre profil.</p>
      <dl class="mt-4 divide-y divide-ligne text-sm">
        <?php foreach ($infos as $libelle => $valeur): ?>
        <div class="flex justify-between gap-4 py-2.5"><dt><?= e($libelle) ?></dt><dd class="text-right font-bold break-all text-encre"><?= e($valeur) ?></dd></div>
        <?php endforeach ?>
      </dl>
      <a href="<?= url('/compte/mon-espace/profil') ?>" class="btn-lien mt-5">Compléter mon profil</a>
    </aside>
  </div>
</section>
