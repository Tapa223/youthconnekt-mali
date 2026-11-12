<?php $p = parametres(); $u = utilisateur(); $reseaux = reseaux_sociaux();
// Espace membre : pied de page réduit (les trois logos, puis les mentions)
$pied_compact = str_starts_with(chemin_actuel(), '/compte/mon-espace') || str_starts_with(chemin_actuel(), '/compte/membres');
?>
<footer class="relative isolate overflow-hidden bg-vert text-white" aria-labelledby="titre-pied">
  <h2 id="titre-pied" class="sr-only">Informations sur YouthConnekt Mali</h2>
  <?php
  /* Les trois logos (comme YouthConnekt Cameroon) : ministère à gauche, YouthConnekt Mali au centre, PNUD à droite.
     Les logos officiels s'affichent dès qu'ils sont déposés dans assets/img/appui/ (ministere.png, pnud.png…). */
  $logo_appui = function (string $fichier): string {
      foreach (['svg', 'png', 'webp', 'jpg'] as $ext) {
          if (is_file(RACINE . "/assets/img/appui/$fichier.$ext")) return asset("img/appui/$fichier.$ext");
      }
      return '';
  };
  // Logos déposés dans Administration > Paramètres, sinon fichiers du dossier assets/img/appui/
  $logo_ministere = !empty($p['logo_ministere']) ? media($p['logo_ministere']) : $logo_appui('ministere');
  $logo_pnud = !empty($p['logo_pnud']) ? media($p['logo_pnud']) : $logo_appui('pnud');
  ?>
  <div class="relative bg-white text-encre">
    <div class="conteneur sans-apparition grid grid-cols-[1fr_auto_1fr] items-center gap-3 py-4 sm:gap-6 sm:py-5">
      <div class="flex min-w-0 items-center gap-2.5 sm:gap-3">
        <?php if ($logo_ministere): ?><img src="<?= e($logo_ministere) ?>" alt="Sceau de la République du Mali" class="h-12 w-auto shrink-0 sm:h-14" loading="lazy">
        <?php else: ?><span class="flex h-10 w-7 shrink-0 overflow-hidden rounded-sm sm:h-12 sm:w-9" aria-hidden="true"><span class="flex-1 bg-[#14B53A]"></span><span class="flex-1 bg-[#FCD116]"></span><span class="flex-1 bg-[#CE1126]"></span></span><?php endif ?>
        <span class="hidden min-w-0 leading-tight sm:block"><span class="block text-sm font-bold tracking-wide text-vert uppercase">République du Mali</span><span class="hidden max-w-[19rem] text-xs text-texte sm:block">Ministère de la Jeunesse et des Sports, chargé de l'Instruction civique et de la Construction citoyenne</span></span>
      </div>
      <a href="<?= url('/') ?>" class="justify-self-center" aria-label="YouthConnekt Mali, retour à l'accueil"><?php partiel('partials/logo', ['classes' => 'h-10 w-auto sm:h-14']) ?></a>
      <div class="flex min-w-0 items-center justify-end gap-2.5 text-right sm:gap-3">
        <span class="hidden min-w-0 leading-tight sm:block"><span class="block text-sm font-bold tracking-wide text-vert uppercase">PNUD Mali</span><span class="hidden text-xs text-texte sm:block">Programme des Nations unies pour le développement</span></span>
        <?php if ($logo_pnud): ?><img src="<?= e($logo_pnud) ?>" alt="PNUD" class="h-12 w-auto shrink-0 sm:h-14" loading="lazy">
        <?php else: ?><span class="grid h-10 w-8 shrink-0 place-items-center rounded-sm bg-[#0468B1] text-[9px] font-bold text-white sm:h-12 sm:w-10 sm:text-[10px]" aria-hidden="true">PNUD</span><?php endif ?>
      </div>
    </div>
  </div>

<?php if (!$pied_compact):
  // Réseaux sociaux du chapitre : un lien s'active dès qu'il est saisi dans Administration > Paramètres
  $icones_reseaux = [
      'lien_facebook' => ['Facebook', '<path d="M14 8.5V6.8c0-.8.5-1.3 1.3-1.3H17V2.3h-2.6C11.9 2.3 10.5 3.9 10.5 6.3v2.2H8v3.3h2.5v9.9H14v-9.9h2.6l.5-3.3H14z" fill="currentColor" stroke="none"/>'],
      'lien_instagram' => ['Instagram', '<rect x="3.5" y="3.5" width="17" height="17" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.2" cy="6.8" r=".9" fill="currentColor" stroke="none"/>'],
      'lien_linkedin' => ['LinkedIn', '<path d="M6.9 9.3H3.6V20h3.3V9.3zM5.2 4a1.9 1.9 0 1 0 0 3.8 1.9 1.9 0 0 0 0-3.8zM20.4 13.9c0-3-1.6-4.9-4.2-4.9-1.4 0-2.4.8-2.8 1.5V9.3h-3.2V20h3.3v-5.6c0-1.4.6-2.4 1.9-2.4 1.2 0 1.7.9 1.7 2.4V20h3.3v-6.1z" fill="currentColor" stroke="none"/>'],
      'lien_x' => ['X', '<path d="M4 4l16 16M20 4L4 20"/>'],
      'lien_youtube' => ['YouTube', '<rect x="2.5" y="5.5" width="19" height="13" rx="4"/><path d="M10 9.3v5.4l4.6-2.7z" fill="currentColor" stroke="none"/>'],
      'lien_tiktok' => ['TikTok', '<path d="M14 3.5v11.2a3.3 3.3 0 1 1-3.3-3.3M14 3.5c.5 2.6 2.3 4.2 4.8 4.4"/>'],
  ];
  /* Pied allégé : à gauche la présentation et les réseaux ; à droite la newsletter puis trois courtes colonnes de liens */
  ?>
  <div class="conteneur sans-apparition relative grid gap-8 py-9 sm:py-12 lg:grid-cols-[minmax(0,4fr)_minmax(0,8fr)] lg:gap-12">
    <div>
      <p class="text-sm font-bold text-white">Suivez-nous</p>
      <ul class="mt-3 flex flex-wrap gap-2" aria-label="Réseaux sociaux de YouthConnekt Mali">
        <?php foreach ($icones_reseaux as $cle => [$nom_reseau, $trace]):
            if ($cle === 'lien_tiktok' && empty($p[$cle])) continue;
            $classe_icone = 'icone-reseau grid size-10 cursor-pointer place-items-center rounded-full border border-white/30 text-white'; ?>
        <li>
          <?php if (!empty($p[$cle])): ?>
          <a href="<?= e($p[$cle]) ?>" target="_blank" rel="noopener" class="<?= $classe_icone ?>" aria-label="<?= e($nom_reseau) ?>"><svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?= $trace ?></svg></a>
          <?php else: ?>
          <span class="<?= $classe_icone ?>" title="<?= e($nom_reseau) ?> : bientôt disponible"><svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?= $trace ?></svg><span class="sr-only"><?= e($nom_reseau) ?></span></span>
          <?php endif ?>
        </li>
        <?php endforeach ?>
      </ul>
    </div>

    <div>
      <div id="newsletter" class="grid scroll-mt-24 gap-4 border-b border-white/15 pb-7 md:grid-cols-[minmax(0,5fr)_minmax(0,7fr)] md:items-center md:gap-8">
        <p class="text-xl leading-tight font-bold text-white sm:text-2xl">Abonnez-vous à la newsletter</p>
        <form action="<?= url('/newsletter') ?>" method="post" novalidate data-form-newsletter>
          <?= csrf() ?>
          <input type="hidden" name="suivant" value="<?= e(adresse_actuelle()) ?>">
          <div class="hidden" aria-hidden="true"><label>Ne pas remplir <input type="text" name="site_web" tabindex="-1" autocomplete="off"></label></div>
          <div class="flex overflow-hidden rounded-full bg-white p-1 focus-within:ring-[3px] focus-within:ring-orange/50">
            <label for="newsletter-email" class="sr-only">Votre adresse e-mail</label>
            <input id="newsletter-email" name="email" type="email" required autocomplete="email" inputmode="email" placeholder="Votre adresse e-mail" maxlength="190"
                   class="min-h-11 min-w-0 flex-1 border-0 bg-transparent pl-4 text-[15px] text-encre placeholder:text-texte/70 focus:outline-none">
            <button type="submit" class="grid h-11 w-14 shrink-0 place-items-center rounded-full bg-orange text-white transition-colors hover:bg-vert sm:w-16" aria-label="S'abonner">
              <svg class="size-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M4 4.5 21 12 4 19.5l2.2-7.5L4 4.5zm2.2 7.5h7"/></svg>
            </button>
          </div>
          <label class="mt-3 flex cursor-pointer items-start gap-2.5 text-[13px] leading-snug text-white/85">
            <input type="checkbox" name="accord" value="1" required class="mt-0.5 size-4 shrink-0 rounded accent-orange">
            <span>J'accepte de recevoir la newsletter et la <a href="<?= url('/confidentialite') ?>" class="font-bold text-white hover:text-orange">politique de confidentialité</a>.</span>
          </label>
          <p class="mt-3 hidden items-start gap-2 rounded-xl px-3.5 py-2.5 text-sm font-medium" data-retour-newsletter role="status" aria-live="polite"></p>
        </form>
      </div>
      <div class="grid grid-cols-2 gap-x-6 gap-y-7 pt-7 md:grid-cols-[minmax(0,2fr)_minmax(0,2fr)_minmax(0,3fr)] md:gap-8">
        <nav aria-label="Le chapitre">
          <p class="text-sm font-bold text-white">Le chapitre</p>
          <ul class="mt-3 space-y-1 text-[15px]">
            <li><a href="<?= url('/a-propos') ?>" class="lien-pied">À propos</a></li>
            <li><a href="<?= url('/initiatives') ?>" class="lien-pied">Initiatives</a></li>
            <li><a href="<?= url('/actualites') ?>" class="lien-pied">Actualités</a></li>
          </ul>
        </nav>
        <nav aria-label="Participer">
          <p class="text-sm font-bold text-white">Participer</p>
          <ul class="mt-3 space-y-1 text-[15px]">
            <li><a href="<?= url('/opportunites') ?>" class="lien-pied">Opportunités</a></li>
            <li><a href="<?= url('/communaute') ?>" class="lien-pied">Communautés</a></li>
            <?php if ($u): ?>
            <li><a href="<?= url(est_membre($u) ? '/compte/mon-espace' : '/gestion') ?>" class="lien-pied"><?= est_membre($u) ? 'Mon espace' : 'Administration' ?></a></li>
            <?php else: ?>
            <li><a href="<?= url('/compte/inscription') ?>" class="lien-pied">Rejoindre le chapitre</a></li>
            <?php endif ?>
          </ul>
        </nav>
        <div class="col-span-2 md:col-span-1">
          <p class="text-sm font-bold text-white">Nous joindre</p>
          <ul class="mt-3 space-y-1 text-[15px] text-white/85 [&>li]:flex [&>li]:min-h-10 [&>li]:items-center">
            <li><?= e($p['adresse'] ?: 'Bamako, Mali') ?></li>
            <?php if ($p['email_contact']): ?><li><a href="mailto:<?= e($p['email_contact']) ?>" class="lien-pied [overflow-wrap:anywhere]"><?= e($p['email_contact']) ?></a></li><?php endif ?>
            <?php if ($p['telephone']): ?><li><a href="tel:<?= e(str_replace(' ', '', $p['telephone'])) ?>" class="lien-pied"><?= e($p['telephone']) ?></a></li><?php endif ?>
          </ul>
        </div>
      </div>
    </div>
  </div>

<?php endif ?>
  <div class="relative <?= $pied_compact ? '' : 'border-t border-white/15' ?>">
    <div class="conteneur sans-apparition flex flex-col gap-3 py-5 pb-[max(1.25rem,env(safe-area-inset-bottom))] text-[13px] text-white/75 sm:py-6 sm:text-sm md:flex-row md:items-center md:justify-between">
      <p>&copy; <?= date('Y') ?> YouthConnekt Mali</p>
      <ul class="flex flex-wrap gap-x-5 gap-y-2">
        <li><a href="<?= url('/mentions-legales') ?>" class="lien-pied">Mentions légales</a></li>
        <li><a href="<?= url('/confidentialite') ?>" class="lien-pied">Politique de confidentialité</a></li>
        <li><a href="<?= url('/contact') ?>" class="lien-pied">Contact</a></li>
      </ul>
    </div>
  </div>
</footer>
