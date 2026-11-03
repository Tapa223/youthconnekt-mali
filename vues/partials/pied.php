<?php $p = parametres(); $u = utilisateur(); $reseaux = reseaux_sociaux(); ?>
<footer class="relative isolate overflow-hidden bg-vert text-white" aria-labelledby="titre-pied">
  <h2 id="titre-pied" class="sr-only">Informations sur YouthConnekt Mali</h2>
  <?php partiel('partials/reseau_deco', ['opacite' => 'opacity-40']) ?>

  <div class="relative border-b border-white/15">
    <div id="newsletter" class="conteneur sans-apparition grid scroll-mt-24 gap-4 py-8 sm:gap-5 sm:py-9 md:grid-cols-[minmax(0,1fr)_minmax(0,1.1fr)] md:items-center md:gap-10 lg:py-10">
      <div>
        <p class="text-xl font-bold text-white sm:text-2xl">Abonnez-vous à la newsletter</p>
        <p class="mt-1 text-sm text-white/80 sm:text-base">Opportunités, initiatives et événements de YouthConnekt Mali, directement par e-mail.</p>
      </div>
      <form action="<?= url('/newsletter') ?>" method="post" class="flex flex-col gap-2.5 sm:flex-row" novalidate>
        <?= csrf() ?>
        <input type="hidden" name="suivant" value="<?= e(adresse_actuelle()) ?>">
        <div class="hidden" aria-hidden="true"><label>Ne pas remplir <input type="text" name="site_web" tabindex="-1" autocomplete="off"></label></div>
        <label for="newsletter-email" class="sr-only">Votre adresse e-mail</label>
        <input id="newsletter-email" name="email" type="email" required autocomplete="email" inputmode="email" placeholder="Votre adresse e-mail" maxlength="190"
               class="min-h-12 w-full min-w-0 flex-1 rounded-lg border-2 border-white/25 bg-white px-4 text-base text-encre placeholder:text-texte/70 focus:border-orange focus:ring-[3px] focus:ring-orange/40 focus:outline-none">
        <button type="submit" class="btn btn-orange min-h-12 shrink-0 px-7">S'abonner</button>
      </form>
    </div>
  </div>

  <div class="conteneur sans-apparition relative grid grid-cols-2 gap-x-6 gap-y-9 py-10 sm:gap-10 lg:grid-cols-12 lg:gap-8 lg:py-14">
    <div class="col-span-2 lg:col-span-4">
      <?php
      /* Institutions d'appui, en petites capitales comme sur le site du Palais des Pionniers.
         Les logos s'affichent dès que leurs fichiers sont déposés dans assets/img/appui/ (pnud, ministere). */
      $logos_appui = [];
      foreach (['ministere' => 'Ministère de la Jeunesse et des Sports', 'pnud' => 'PNUD Mali'] as $fichier => $nom_appui) {
          foreach (['svg', 'png', 'webp', 'jpg'] as $ext) {
              if (is_file(RACINE . "/assets/img/appui/$fichier.$ext")) { $logos_appui[] = [asset("img/appui/$fichier.$ext"), $nom_appui]; break; }
          }
      }
      ?>
      <?php if ($logos_appui): ?>
      <div class="mb-4 flex items-center gap-3"><?php foreach ($logos_appui as [$src, $nom_appui]): ?><img src="<?= e($src) ?>" alt="<?= e($nom_appui) ?>" class="h-11 w-auto rounded-full bg-white object-contain p-1" loading="lazy"><?php endforeach ?></div>
      <?php endif ?>
      <p class="mb-4 max-w-md text-[11px] leading-relaxed font-bold tracking-widest text-white/60 uppercase">Ministère de la Jeunesse et des Sports, chargé de l'Instruction civique et de la Construction citoyenne · Avec l'appui du PNUD Mali</p>
      <a href="<?= url('/') ?>" class="inline-flex rounded-xl bg-white px-3 py-2" aria-label="YouthConnekt Mali, retour à l'accueil">
        <?php partiel('partials/logo', ['classes' => 'h-11 w-auto']) ?>
      </a>
      <p class="mt-4 max-w-sm text-sm leading-relaxed text-white/85 sm:mt-5 sm:text-[15px]">Chapitre national de YouthConnekt Africa, YouthConnekt Mali relie les jeunes du pays aux opportunités, aux initiatives et aux organisations qui les accompagnent.</p>
      <?php if ($reseaux): ?>
      <div class="mt-5 [&_a]:border-white/40 [&_a]:text-white"><?php partiel('partials/reseaux') ?></div>
      <?php endif ?>
    </div>

    <nav class="lg:col-span-2" aria-label="Le chapitre">
      <p class="text-xs font-bold tracking-[0.16em] text-white uppercase"><span class="mb-3 block h-[3px] w-8 rounded-full bg-orange" aria-hidden="true"></span>Le chapitre</p>
      <ul class="mt-4 space-y-1 text-[15px]">
        <li><a href="<?= url('/a-propos') ?>" class="lien-pied">À propos</a></li>
        <li><a href="<?= url('/a-propos') ?>#axes" class="lien-pied">Nos axes d'action</a></li>
        <li><a href="<?= url('/initiatives') ?>" class="lien-pied">Initiatives</a></li>
        <li><a href="<?= url('/actualites') ?>" class="lien-pied">Actualités</a></li>
      </ul>
    </nav>

    <nav class="lg:col-span-3" aria-label="Participer">
      <p class="text-xs font-bold tracking-[0.16em] text-white uppercase"><span class="mb-3 block h-[3px] w-8 rounded-full bg-turquoise" aria-hidden="true"></span>Participer</p>
      <ul class="mt-4 space-y-1 text-[15px]">
        <li><a href="<?= url('/opportunites') ?>" class="lien-pied">Opportunités</a></li>
        <li><a href="<?= url('/communaute') ?>" class="lien-pied">Communautés</a></li>
        <?php if ($u): ?>
        <li><a href="<?= url(est_membre($u) ? '/compte/mon-espace' : '/gestion') ?>" class="lien-pied"><?= est_membre($u) ? 'Mon espace' : 'Administration' ?></a></li>
        <?php else: ?>
        <li><a href="<?= url('/compte/inscription') ?>" class="lien-pied">Rejoindre le chapitre</a></li>
        <li><a href="<?= url('/compte/connexion') ?>" class="lien-pied">Se connecter</a></li>
        <?php endif ?>
      </ul>
    </nav>

    <div class="col-span-2 lg:col-span-3">
      <p class="text-xs font-bold tracking-[0.16em] text-white uppercase"><span class="mb-3 block h-[3px] w-8 rounded-full bg-bleu" aria-hidden="true"></span>Nous joindre</p>
      <ul class="mt-4 space-y-2.5 text-[15px] text-white/85">
        <li><?= e($p['adresse'] ?: 'Bamako, Mali') ?></li>
        <?php if ($p['email_contact']): ?><li><a href="mailto:<?= e($p['email_contact']) ?>" class="lien-pied break-all"><?= e($p['email_contact']) ?></a></li><?php endif ?>
        <?php if ($p['telephone']): ?><li><a href="tel:<?= e(str_replace(' ', '', $p['telephone'])) ?>" class="lien-pied"><?= e($p['telephone']) ?></a></li><?php endif ?>
        <?php if ($p['horaires']): ?><li><?= e($p['horaires']) ?></li><?php endif ?>
      </ul>
      <a href="<?= url('/contact') ?>" class="btn btn-blanc mt-5 min-h-11">Nous écrire</a>
    </div>
  </div>

  <div class="relative border-t border-white/15">
    <div class="conteneur sans-apparition flex flex-col gap-3 py-5 pb-[max(1.25rem,env(safe-area-inset-bottom))] text-[13px] text-white/75 sm:py-6 sm:text-sm md:flex-row md:items-center md:justify-between">
      <p>&copy; <?= date('Y') ?> YouthConnekt Mali · Un chapitre du réseau YouthConnekt Africa</p>
      <ul class="flex flex-wrap gap-x-5 gap-y-2">
        <li><a href="<?= url('/mentions-legales') ?>" class="lien-pied">Mentions légales</a></li>
        <li><a href="<?= url('/confidentialite') ?>" class="lien-pied">Politique de confidentialité</a></li>
        <li><a href="<?= url('/contact') ?>" class="lien-pied">Contact</a></li>
      </ul>
    </div>
  </div>
</footer>
