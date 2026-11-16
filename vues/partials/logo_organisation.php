<?php /* Logo d'une organisation, ou ses initiales. Paramètres : $o, $classes (taille) */ ?>
<?php if (!empty($o['logo'])): ?><img src="<?= e(media($o['logo'])) ?>" alt="" class="shrink-0 rounded-2xl bg-white object-contain p-1.5 ring-1 ring-ligne <?= e($classes ?? 'size-14') ?>" loading="lazy">
<?php else: ?><span class="grid shrink-0 place-items-center rounded-2xl bg-vert-clair font-bold text-vert <?= e($classes ?? 'size-14') ?>" aria-hidden="true"><?= e(initiales_organisation($o['nom_structure'])) ?></span><?php endif ?>
