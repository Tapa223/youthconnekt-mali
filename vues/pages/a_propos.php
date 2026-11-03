<?php /** Page À propos */ ?>
<section class="relative isolate overflow-hidden bg-vert text-white">
  <?php partiel('partials/reseau_deco') ?>
  <div class="conteneur relative grid items-center gap-6 pt-6 pb-8 sm:py-10 lg:grid-cols-2 lg:gap-10 lg:py-10">
    <div class="anim-entree">
      <h1 class="text-[2.1rem] leading-[1.05] font-bold text-white sm:text-5xl lg:text-6xl">Un chapitre, une jeunesse, un même élan</h1>
      <p class="mt-4 max-w-lg text-[15px] leading-relaxed text-white/90 sm:mt-5 sm:text-lg">YouthConnekt Mali est le chapitre national de YouthConnekt Africa. Il relie les jeunes du pays aux opportunités, aux décideurs et entre eux.</p>
    </div>
    <div class="flex -space-x-3 lg:hidden" aria-hidden="true">
      <?php foreach (['cercle-centre', 'cercle-1', 'cercle-2', 'cercle-3'] as $k => $photo): ?><span class="size-16 overflow-hidden rounded-full border-[3px] bg-white shadow-lg sm:size-20 <?= cycle(['border-orange', 'border-turquoise', 'border-white', 'border-turquoise'], $k) ?>"><img src="<?= asset('img/a-propos/' . $photo . '.jpg') ?>" alt="" class="h-full w-full object-cover"></span><?php endforeach ?>
    </div>
    <div class="relative mx-auto hidden aspect-[5/4] w-full max-w-sm lg:block" aria-hidden="true">
      <svg class="absolute inset-0 h-full w-full" viewBox="0 0 500 400"><g stroke="rgb(255 255 255 / .35)" stroke-width="1.3" fill="none"><path d="M90 90 L250 190 L400 80"/><path d="M250 190 L410 310"/><path d="M90 90 L400 80"/></g></svg>
      <div class="anim-soleil absolute top-[20%] left-[26%] aspect-square w-[48%] overflow-hidden rounded-full border-4 border-orange shadow-2xl"><img src="<?= asset('img/a-propos/cercle-centre.jpg') ?>" alt="" class="h-full w-full object-cover"></div>
      <div class="anim-flotte absolute top-[4%] left-[6%] aspect-square w-[22%] overflow-hidden rounded-full border-4 border-turquoise shadow-xl"><img src="<?= asset('img/a-propos/cercle-1.jpg') ?>" alt="" class="h-full w-full object-cover"></div>
      <div class="anim-flotte-2 absolute top-[2%] right-[4%] aspect-square w-[22%] overflow-hidden rounded-full border-4 border-white shadow-xl"><img src="<?= asset('img/a-propos/cercle-2.jpg') ?>" alt="" class="h-full w-full object-cover"></div>
      <div class="anim-flotte absolute right-[2%] bottom-[4%] aspect-square w-[23%] overflow-hidden rounded-full border-4 border-turquoise shadow-xl"><img src="<?= asset('img/a-propos/cercle-3.jpg') ?>" alt="" class="h-full w-full object-cover"></div>
      <span class="absolute bottom-[22%] left-[8%] size-4 rounded-full bg-orange"></span>
      <span class="absolute top-[6%] left-[46%] size-3 rounded-full bg-orange"></span>
    </div>
  </div>
  <?php partiel('partials/mention_photo', ['texte' => 'Photos provisoires · hors Mali']) ?>
</section>

<section id="pourquoi" class="scroll-mt-24 bg-white py-12 sm:py-20">
<div class="conteneur grid gap-6 sm:gap-10 lg:grid-cols-[minmax(0,5fr)_minmax(0,7fr)] lg:gap-14">
  <div>
    <p class="surtitre surtitre-trait">Pourquoi YouthConnekt Mali</p>
    <h2 class="titre-section">Une jeunesse pleine d'énergie, qui mérite d'être connectée</h2>
    <p class="mt-5 text-base leading-relaxed sm:text-lg">Le Mali est l'un des pays les plus jeunes du monde. Chaque année, des milliers de jeunes arrivent sur le marché du travail avec des idées, du talent et de l'ambition. Trois obstacles reviennent souvent :</p>
  </div>
  <div>
    <ol class="space-y-2.5 sm:space-y-3">
      <?php foreach ($obstacles as $k => [$t, $texte]): ?>
      <li class="survol-leve flex gap-3 rounded-xl border border-ligne bg-white p-3 sm:gap-4 sm:rounded-2xl sm:p-5">
        <span class="grid size-7 shrink-0 place-items-center rounded-full text-sm font-bold text-white sm:size-9 sm:text-base <?= cycle(['bg-orange', 'bg-turquoise', 'bg-bleu'], $k) ?>"><?= $k + 1 ?></span>
        <span><span class="block text-base font-bold text-vert sm:text-lg"><?= e($t) ?></span><span class="mt-0.5 block text-sm sm:mt-1 sm:text-[15px]"><?= e($texte) ?></span></span>
      </li>
      <?php endforeach ?>
    </ol>
    <p class="mt-5 font-bold text-vert">YouthConnekt Mali est né pour lever ces trois obstacles.</p>
  </div>
</div>
</section>

<section id="qui-sommes-nous" class="scroll-mt-24 bg-gris py-12 sm:py-20">
<div class="conteneur">
  <div class="grid items-center gap-6 md:gap-12 lg:grid-cols-[minmax(0,7fr)_minmax(0,5fr)] lg:gap-14">
    <div>
      <p class="surtitre surtitre-trait">Qui sommes-nous</p>
      <h2 class="titre-section">Une plateforme nationale au service de la jeunesse malienne</h2>
      <div class="repliable mt-4 space-y-3 text-[15px] leading-relaxed sm:mt-5 sm:space-y-4 sm:text-[17px]" id="texte-qui" data-repliable>
        <p>YouthConnekt Mali rassemble les jeunes, les étudiants, les entrepreneurs et les organisations qui veulent faire avancer le pays. Le chapitre relaie des opportunités, organise des rencontres et met en valeur les initiatives des jeunes, partout au Mali.</p>
        <p>Notre conviction est simple : les jeunes Maliens ont des idées, de l'énergie et du talent, mais l'information et les réseaux leur manquent souvent. Le chapitre est là pour combler ce manque, en mettant au même endroit les jeunes, les organisations qui les accompagnent et les institutions qui décident.</p>
        <p>Il s'appuie sur le réseau YouthConnekt Africa et travaille aux côtés du ministère chargé de la Jeunesse et de ses partenaires.</p>
      </div>
      <?php partiel('partials/voir_plus', ['cible' => 'texte-qui']) ?>
    </div>
    <div class="relative mx-auto hidden w-full max-w-md pr-6 pb-6 md:block">
      <div class="absolute top-6 right-0 bottom-0 left-10 rounded-3xl bg-orange" aria-hidden="true"></div>
      <div class="devoile survol-zoom relative aspect-square overflow-hidden rounded-3xl shadow-xl">
        <img src="<?= asset('img/a-propos/qui-sommes-nous.jpg') ?>" alt="" class="h-full w-full object-cover" loading="lazy">
        <?php partiel('partials/mention_photo', ['texte' => 'Photo provisoire', 'position' => 'right-3 top-3']) ?>
      </div>
      <span class="anim-flotte absolute top-8 -left-3 flex items-center gap-2 rounded-full bg-white px-4 py-2 text-sm shadow-lg"><span class="grid size-7 place-items-center rounded-full bg-orange text-white" aria-hidden="true"><svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><circle cx="12" cy="12" r="8"/><path d="M12 8v4l3 2"/></svg></span>Lancé en <strong class="text-vert">2024</strong></span>
      <span class="anim-flotte-2 absolute top-1/2 -right-2 flex items-center gap-2 rounded-full bg-white px-4 py-2 text-sm shadow-lg"><span class="grid size-7 place-items-center rounded-full bg-turquoise text-white" aria-hidden="true"><svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 21s7-6.2 7-12a7 7 0 0 0-14 0c0 5.8 7 12 7 12z"/></svg></span>Basé à <strong class="text-vert">Bamako</strong></span>
      <span class="anim-flotte absolute bottom-0 left-4 flex items-center gap-2 rounded-full bg-white px-4 py-2 text-sm shadow-lg"><span class="grid size-7 place-items-center rounded-full bg-bleu text-white" aria-hidden="true"><svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><circle cx="6" cy="12" r="2.5"/><circle cx="18" cy="6" r="2.5"/><circle cx="18" cy="18" r="2.5"/><path d="m8.2 11 7.6-3.8M8.2 13l7.6 3.8"/></svg></span>Réseau <strong class="text-vert">YouthConnekt Africa</strong></span>
    </div>
  </div>
  <ul class="mt-5 flex flex-wrap gap-2 md:hidden">
    <li class="rounded-full border-[1.5px] border-orange px-3 py-1.5 text-[13px]">Lancé en <strong class="text-vert">2024</strong></li>
    <li class="rounded-full border-[1.5px] border-turquoise px-3 py-1.5 text-[13px]">Basé à <strong class="text-vert">Bamako</strong></li>
    <li class="rounded-full border-[1.5px] border-bleu px-3 py-1.5 text-[13px]">Réseau <strong class="text-vert">YouthConnekt Africa</strong></li>
  </ul>
  <ul class="mt-5 space-y-3 md:hidden">
    <?php foreach ($publics as $k => [$t, $texte]): ?>
    <li class="flex gap-3"><span class="w-1.5 shrink-0 self-stretch rounded-full <?= cycle(['bg-orange', 'bg-turquoise', 'bg-bleu'], $k) ?>" aria-hidden="true"></span><span><strong class="block text-base text-vert"><?= e($t) ?></strong><span class="mt-0.5 block text-sm leading-relaxed"><?= e($texte) ?></span></span></li>
    <?php endforeach ?>
  </ul>
  <ul class="mt-14 hidden gap-5 md:grid md:grid-cols-3">
    <?php foreach ($publics as $k => [$t, $texte]): ?>
    <li class="survol-leve rounded-2xl border border-t-[5px] border-ligne bg-white p-6 shadow-sm <?= cycle(['border-t-orange', 'border-t-turquoise', 'border-t-bleu'], $k) ?>">
      <h3 class="text-xl font-bold"><?= e($t) ?></h3>
      <p class="mt-2 text-[15px] leading-relaxed"><?= e($texte) ?></p>
    </li>
    <?php endforeach ?>
  </ul>
</div>
</section>

<section id="mission" class="scroll-mt-24 bg-white py-12 sm:py-20">
<div class="conteneur">
  <p class="surtitre surtitre-trait">Mission et valeurs</p>
  <h2 class="titre-section">Ce qui nous guide</h2>
  <div class="mt-5 grid gap-2.5 sm:mt-8 md:grid-cols-3 md:items-start md:gap-5">
    <div class="survol-leve rounded-2xl bg-vert flex items-center gap-3 p-3.5 md:block md:p-7 text-white">
      <span class="grid size-10 shrink-0 place-items-center rounded-full md:size-11 bg-orange" aria-hidden="true"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2"><circle cx="12" cy="12" r="8"/><circle cx="12" cy="12" r="3"/></svg></span>
      <div class="min-w-0"><h3 class="text-lg font-bold md:mt-4 md:text-2xl text-white">Mission</h3>
      <p class="mt-0.5 text-sm leading-relaxed md:mt-2 md:text-[15px] text-white/90">Connecter les jeunes du Mali aux opportunités, aux savoirs et aux décideurs pour qu'ils prennent leur place.</p></div>
    </div>
    <div class="survol-leve rounded-2xl border-[1.5px] border-vert bg-white flex items-center gap-3 p-3.5 md:block md:p-7 md:mt-8">
      <span class="grid size-10 shrink-0 place-items-center rounded-full md:size-11 bg-vert" aria-hidden="true"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg></span>
      <div class="min-w-0"><h3 class="text-lg font-bold md:mt-4 md:text-2xl">Vision</h3>
      <p class="mt-0.5 text-sm leading-relaxed md:mt-2 md:text-[15px] text-vert">Une jeunesse malienne formée, engagée et visible, qui fait avancer le pays.</p></div>
    </div>
    <div class="survol-leve rounded-2xl border-[1.5px] border-orange bg-white flex items-center gap-3 p-3.5 md:block md:p-7">
      <span class="grid size-10 shrink-0 place-items-center rounded-full md:size-11 bg-orange" aria-hidden="true"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2"><path d="M12 20s-7-4.5-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.5-7 10-7 10z"/></svg></span>
      <div class="min-w-0"><h3 class="text-lg font-bold md:mt-4 md:text-2xl">Valeurs</h3>
      <p class="mt-0.5 text-sm leading-relaxed md:mt-2 md:text-[15px] text-vert">Engagement, ouverture, excellence et solidarité : ce sont elles qui guident chacune de nos actions.</p></div>
    </div>
  </div>
</div>
</section>

<section class="relative isolate overflow-hidden bg-vert py-12 sm:py-20 text-white">
  <?php partiel('partials/reseau_deco') ?>
  <div class="conteneur relative">
    <p class="surtitre surtitre-trait text-white/85">Notre approche</p>
    <h2 class="titre-section text-white">Comment nous travaillons</h2>
    <ol class="mt-6 grid grid-cols-2 gap-2.5 sm:mt-10 sm:gap-8 lg:grid-cols-4">
      <?php foreach ($approche as $k => [$t, $texte]): ?>
      <li class="rounded-xl border border-white/20 p-3 sm:rounded-none sm:border-0 sm:p-0">
        <div class="flex items-center gap-3">
          <span class="grid size-8 shrink-0 text-sm sm:size-11 sm:text-base place-items-center rounded-full font-bold <?= cycle(['bg-orange text-white', 'bg-turquoise text-encre', 'bg-white text-vert', 'bg-orange text-white'], $k) ?>"><?= $k + 1 ?></span>
          <?php if ($k < count($approche) - 1): ?><span class="hidden h-px flex-1 border-t-2 border-dashed border-white/40 lg:block" aria-hidden="true"></span><?php endif ?>
        </div>
        <h3 class="mt-2.5 text-base font-bold text-white sm:mt-4 sm:text-xl"><?= e($t) ?></h3>
        <p class="mt-1 text-[13px] leading-snug text-white/85 sm:mt-1.5 sm:text-[15px] sm:leading-normal"><?= e($texte) ?></p>
      </li>
      <?php endforeach ?>
    </ol>
  </div>
</section>

<?php if ($axes): ?>
<section id="axes" class="scroll-mt-24 bg-gris py-12 sm:py-20">
  <div class="conteneur">
    <p class="surtitre surtitre-trait">Nos axes d'action</p>
    <h2 class="titre-section"><?= count($axes) === 3 ? "Trois façons d'agir avec les jeunes" : "Nos façons d'agir avec les jeunes" ?></h2>
    <div class="mt-5 space-y-2.5 md:hidden">
      <?php foreach ($axes as $k => $axe): ?>
      <div class="flex gap-3 rounded-2xl bg-white p-2.5 shadow-sm">
        <span class="relative w-24 shrink-0 overflow-hidden rounded-xl border-b-4 <?= bordure($axe['couleur']) ?>">
          <span class="absolute inset-0"><?php partiel('partials/image_axe', ['axe' => $axe]) ?></span>
          <span class="absolute top-1.5 left-1.5 grid size-6 place-items-center rounded-full bg-white/90 text-[11px] font-bold text-vert"><?= sprintf('%02d', $k + 1) ?></span>
        </span>
        <span class="min-w-0 py-1"><strong class="block text-lg text-vert"><?= e($axe['nom']) ?></strong><span class="mt-0.5 block text-sm leading-snug"><?= e($axe['description']) ?></span></span>
      </div>
      <?php endforeach ?>
    </div>
    <div class="mt-10 hidden space-y-16 md:block">
      <?php foreach ($axes as $k => $axe): $elements = lignes_texte($axe['elements']); ?>
      <article class="grid items-center gap-6 md:grid-cols-2 md:gap-12">
        <div class="devoile survol-zoom relative aspect-[16/11] overflow-hidden rounded-3xl border-b-[6px] <?= bordure($axe['couleur']) ?> <?= $k % 2 === 1 ? 'md:order-2' : '' ?>">
          <?php partiel('partials/image_axe', ['axe' => $axe]) ?>
          <span class="absolute top-4 left-4 grid size-10 place-items-center rounded-full bg-white/90 text-sm font-bold text-vert"><?= sprintf('%02d', $k + 1) ?></span>
          <?php if (!$axe['image']) partiel('partials/mention_photo', ['texte' => 'Photo provisoire', 'position' => 'right-3 top-3']) ?>
        </div>
        <div class="md:px-4">
          <h3 class="text-3xl font-bold sm:text-4xl"><?= e($axe['nom']) ?></h3>
          <p class="mt-3 text-base leading-relaxed sm:text-lg"><?= e($axe['description']) ?></p>
          <?php if ($elements): ?>
          <ul class="mt-4 space-y-2">
            <?php foreach ($elements as $el): ?><li class="flex items-center gap-3 text-[15px] text-encre"><span class="size-2 shrink-0 rounded-full <?= fond($axe['couleur']) ?>" aria-hidden="true"></span><?= e($el) ?></li><?php endforeach ?>
          </ul>
          <?php endif ?>
        </div>
      </article>
      <?php endforeach ?>
    </div>
  </div>
</section>
<?php endif ?>

<section id="parcours" class="scroll-mt-24 bg-white py-12 sm:py-20">
  <div class="conteneur">
    <p class="surtitre surtitre-trait">Notre parcours</p>
    <h2 class="titre-section">De la naissance du chapitre à aujourd'hui</h2>
    <?php $couleurs_parcours = ['orange', 'turquoise', 'bleu', 'vert']; ?>
    <ol class="relative mt-6 space-y-5 border-l-2 border-ligne pl-6 sm:hidden">
      <?php foreach ($parcours as $k => [$annee, $t, $texte]): ?>
      <li class="relative">
        <span class="absolute top-1 -left-[33px] size-4 rounded-full border-[3px] border-white <?= fond(cycle($couleurs_parcours, $k)) ?> shadow" aria-hidden="true"></span>
        <p class="text-lg leading-none font-bold text-vert"><?= e($annee) ?> <span class="ml-1 text-sm font-bold text-encre"><?= e($t) ?></span></p>
        <p class="mt-1 text-[13px] leading-snug"><?= e($texte) ?></p>
      </li>
      <?php endforeach ?>
    </ol>
    <ol class="mt-10 hidden gap-8 sm:grid sm:grid-cols-2 lg:grid-cols-4">
      <?php foreach ($parcours as $k => [$annee, $t, $texte]): ?>
      <li>
        <div class="flex items-center gap-3">
          <span class="grid size-8 shrink-0 place-items-center rounded-full border-2 <?= cycle(['border-orange', 'border-turquoise', 'border-bleu', 'border-vert'], $k) ?>" aria-hidden="true"><span class="size-4 rounded-full <?= fond(cycle($couleurs_parcours, $k)) ?>"></span></span>
          <?php if ($k < count($parcours) - 1): ?><span class="hidden h-px flex-1 border-t-2 border-dashed border-ligne lg:block" aria-hidden="true"></span><?php endif ?>
        </div>
        <p class="mt-4 text-3xl font-bold text-vert"><?= e($annee) ?></p>
        <h3 class="mt-1 text-base font-bold text-encre"><?= e($t) ?></h3>
        <p class="mt-1 text-sm"><?= e($texte) ?></p>
      </li>
      <?php endforeach ?>
    </ol>
  </div>
</section>

<?php /* Le réseau en chiffres : calculés à partir des comptes et des communautés */ ?>
<section class="bg-gris py-12 sm:py-20" aria-labelledby="titre-reseau">
  <div class="conteneur">
    <p class="surtitre surtitre-trait">Le réseau</p>
    <h2 id="titre-reseau" class="titre-section">Un chapitre qui grandit chaque jour</h2>
    <dl class="mt-6 grid grid-cols-3 gap-2.5 sm:mt-10 sm:gap-5">
      <?php foreach ([
          [$reseau['membres'], 'membre', 'membres', 'border-orange'],
          [$reseau['organisations'], 'organisation', 'organisations', 'border-turquoise'],
          [$reseau['communautes'], 'communauté', 'communautés', 'border-bleu'],
      ] as [$nombre, $singulier, $pluriel_mot, $bord]): ?>
      <div class="rounded-2xl border-t-4 bg-white p-3 text-center shadow-sm sm:p-7 <?= $bord ?>">
        <dt class="sr-only"><?= e($pluriel_mot) ?></dt>
        <dd class="text-3xl font-bold text-vert tabular-nums sm:text-5xl" data-compteur data-duree="1400"><?= $nombre ?></dd>
        <dd class="mt-1 text-xs text-encre sm:text-base"><?= $nombre > 1 ? $pluriel_mot : $singulier ?></dd>
      </div>
      <?php endforeach ?>
    </dl>
    <p class="mt-5 text-center text-[15px] sm:text-base">Et chaque nouvelle inscription agrandit le réseau. <a href="<?= url('/compte/inscription') ?>" class="btn-lien ml-1 mt-2 sm:mt-0">Rejoindre le chapitre</a></p>
  </div>
</section>

<?php partiel('partials/partenaires', ['partenaires' => $partenaires, 'fond' => 'bg-white']) ?>

<?php if ($mot): ?>
<section id="mot-du-ministre" class="scroll-mt-24 bg-gris py-12 sm:py-20">
  <div class="conteneur grid gap-8 md:grid-cols-[minmax(0,4fr)_minmax(0,8fr)] md:gap-14">
    <div class="mx-auto w-full max-w-[240px] md:max-w-xs">
      <div class="aspect-[4/5] overflow-hidden rounded-2xl border-t-[6px] border-orange bg-white shadow-xl">
        <?php partiel('partials/photo', ['image' => $mot['photo'], 'alt' => $mot['nom']]) ?>
      </div>
    </div>
    <div>
      <p class="surtitre surtitre-trait">Le mot du ministre</p>
      <div class="repliable prose-ycm mt-5" id="texte-ministre" data-repliable><?= paragraphes($mot['message']) ?></div>
      <?php partiel('partials/voir_plus', ['cible' => 'texte-ministre']) ?>
      <p class="mt-6 border-l-[3px] border-orange pl-4"><strong class="block text-encre"><?= e($mot['nom']) ?></strong><span class="text-sm"><?= e($mot['titre_officiel']) ?></span></p>
    </div>
  </div>
</section>
<?php endif ?>

<?php partiel('partials/temoignages', ['temoignages' => $temoignages]) ?>
<?php partiel('partials/bandeau_lets_connekt') ?>
