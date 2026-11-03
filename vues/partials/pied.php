<?php $p = parametres(); $u = utilisateur(); $reseaux = reseaux_sociaux(); ?>
<section class="bg-gris py-12 sm:py-16" aria-labelledby="titre-newsletter">
  <div id="newsletter" class="conteneur sans-apparition scroll-mt-24">
    <div class="grid gap-5 rounded-3xl border-t-[6px] border-orange bg-white p-6 shadow-xl shadow-vert/10 sm:p-9 md:grid-cols-[minmax(0,1fr)_minmax(0,1.1fr)] md:items-center md:gap-10">
      <div>
        <h2 id="titre-newsletter" class="text-2xl font-bold text-vert sm:text-3xl">Abonnez-vous à la newsletter</h2>
        <p class="mt-2 text-[15px] sm:text-base">Opportunités, initiatives et événements de YouthConnekt Mali, directement par e-mail.</p>
      </div>
      <form action="<?= url('/newsletter') ?>" method="post" class="flex flex-col gap-2.5 sm:flex-row" novalidate>
        <?= csrf() ?>
        <input type="hidden" name="suivant" value="<?= e(adresse_actuelle()) ?>">
        <div class="hidden" aria-hidden="true"><label>Ne pas remplir <input type="text" name="site_web" tabindex="-1" autocomplete="off"></label></div>
        <label for="newsletter-email" class="sr-only">Votre adresse e-mail</label>
        <input id="newsletter-email" name="email" type="email" required autocomplete="email" inputmode="email" placeholder="Votre adresse e-mail" maxlength="190"
               class="min-h-12 w-full min-w-0 flex-1 rounded-lg border-2 border-ligne bg-white px-4 text-base text-encre placeholder:text-texte/70 focus:border-orange focus:ring-[3px] focus:ring-orange/40 focus:outline-none">
        <button type="submit" class="btn btn-orange min-h-12 shrink-0 px-7">S'abonner</button>
      </form>
    </div>
  </div>
</section>

<footer class="relative isolate overflow-hidden bg-vert text-white" aria-labelledby="titre-pied">
  <h2 id="titre-pied" class="sr-only">Informations sur YouthConnekt Mali</h2>
  <?php partiel('partials/reseau_deco', ['opacite' => 'opacity-40']) ?>

  <div class="conteneur sans-apparition relative grid grid-cols-2 gap-x-6 gap-y-9 py-10 sm:gap-10 lg:grid-cols-12 lg:gap-8 lg:py-14">
    <div class="col-span-2 lg:col-span-4">
      <?php
      /* Institutions (ministère, puis PNUD Mali) : leurs logos s'affichent dès que les fichiers
         sont déposés dans assets/img/appui/ (ministere, pnud) ; sinon un petit repère de couleur. */
      $logos_appui = [];
      foreach (['ministere' => 'Ministère de la Jeunesse et des Sports', 'pnud' => 'PNUD Mali'] as $fichier => $nom_appui) {
          foreach (['svg', 'png', 'webp', 'jpg'] as $ext) {
              if (is_file(RACINE . "/assets/img/appui/$fichier.$ext")) { $logos_appui[] = [asset("img/appui/$fichier.$ext"), $nom_appui]; break; }
          }
      }
      ?>
      <?php
      $logo_de = [];
      foreach ($logos_appui as [$src, $nom_appui]) { $logo_de[$nom_appui] = $src; }
      // Réseaux sociaux du chapitre : un lien s'active dès qu'il est saisi dans Administration > Paramètres
      $icones_reseaux = [
          'lien_facebook' => ['Facebook', '<path d="M14 8.5V6.8c0-.8.5-1.3 1.3-1.3H17V2.3h-2.6C11.9 2.3 10.5 3.9 10.5 6.3v2.2H8v3.3h2.5v9.9H14v-9.9h2.6l.5-3.3H14z" fill="currentColor" stroke="none"/>'],
          'lien_instagram' => ['Instagram', '<rect x="3.5" y="3.5" width="17" height="17" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.2" cy="6.8" r=".9" fill="currentColor" stroke="none"/>'],
          'lien_linkedin' => ['LinkedIn', '<path d="M6.9 9.3H3.6V20h3.3V9.3zM5.2 4a1.9 1.9 0 1 0 0 3.8 1.9 1.9 0 0 0 0-3.8zM20.4 13.9c0-3-1.6-4.9-4.2-4.9-1.4 0-2.4.8-2.8 1.5V9.3h-3.2V20h3.3v-5.6c0-1.4.6-2.4 1.9-2.4 1.2 0 1.7.9 1.7 2.4V20h3.3v-6.1z" fill="currentColor" stroke="none"/>'],
          'lien_x' => ['X', '<path d="M4 4l16 16M20 4L4 20"/>'],
          'lien_youtube' => ['YouTube', '<rect x="2.5" y="5.5" width="19" height="13" rx="4"/><path d="M10 9.3v5.4l4.6-2.7z" fill="currentColor" stroke="none"/>'],
          'lien_tiktok' => ['TikTok', '<path d="M14 3.5v11.2a3.3 3.3 0 1 1-3.3-3.3M14 3.5c.5 2.6 2.3 4.2 4.8 4.4"/>'],
      ];
      ?>
      <ul class="max-w-sm space-y-3 border-b border-white/15 pb-5 text-[13px] leading-snug text-white/80">
        <li class="flex items-center gap-3">
          <?php if (isset($logo_de['Ministère de la Jeunesse et des Sports'])): ?><img src="<?= e($logo_de['Ministère de la Jeunesse et des Sports']) ?>" alt="" class="size-9 shrink-0 rounded-full bg-white object-contain p-0.5" loading="lazy">
          <?php else: ?><span class="flex h-9 w-1.5 shrink-0 flex-col overflow-hidden rounded-full" aria-hidden="true"><span class="flex-1 bg-[#14B53A]"></span><span class="flex-1 bg-[#FCD116]"></span><span class="flex-1 bg-[#CE1126]"></span></span><?php endif ?>
          <span><strong class="font-bold text-white">Ministère de la Jeunesse et des Sports</strong>, chargé de l'Instruction civique et de la Construction citoyenne</span>
        </li>
        <li class="flex items-center gap-3">
          <?php if (isset($logo_de['PNUD Mali'])): ?><img src="<?= e($logo_de['PNUD Mali']) ?>" alt="" class="h-9 w-auto shrink-0 rounded bg-white object-contain p-0.5" loading="lazy">
          <?php else: ?><span class="h-9 w-1.5 shrink-0 rounded-full bg-[#0468B1]" aria-hidden="true"></span><?php endif ?>
          <strong class="font-bold text-white">PNUD Mali</strong>
        </li>
      </ul>
      <a href="<?= url('/') ?>" class="mt-6 inline-flex rounded-xl bg-white px-3 py-2" aria-label="YouthConnekt Mali, retour à l'accueil">
        <?php partiel('partials/logo', ['classes' => 'h-11 w-auto']) ?>
      </a>
      <p class="mt-4 max-w-sm text-sm leading-relaxed text-white/85 sm:mt-5 sm:text-[15px]">Chapitre national de YouthConnekt Africa, YouthConnekt Mali relie les jeunes du pays aux opportunités, aux initiatives et aux organisations qui les accompagnent.</p>


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
      <p class="mt-7 text-xs font-bold tracking-[0.16em] text-white uppercase">Suivez-nous</p>
      <ul class="mt-3 flex flex-wrap gap-2" aria-label="Réseaux sociaux de YouthConnekt Mali">
        <?php foreach ($icones_reseaux as $cle => [$nom_reseau, $trace]):
            if ($cle === 'lien_tiktok' && empty($p[$cle])) continue;
            $classe_icone = 'grid size-10 place-items-center rounded-full border border-white/30 text-white transition-colors'; ?>
        <li>
          <?php if (!empty($p[$cle])): ?>
          <a href="<?= e($p[$cle]) ?>" target="_blank" rel="noopener" class="<?= $classe_icone ?> hover:border-orange hover:bg-orange" aria-label="<?= e($nom_reseau) ?>"><svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?= $trace ?></svg></a>
          <?php else: ?>
          <span class="<?= $classe_icone ?>" title="<?= e($nom_reseau) ?>"><svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?= $trace ?></svg><span class="sr-only"><?= e($nom_reseau) ?></span></span>
          <?php endif ?>
        </li>
        <?php endforeach ?>
      </ul>
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
