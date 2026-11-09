<?php /* Image d'un axe d'action : celle de l'administration, sinon la photo de la maquette. Paramètre : $axe */
if (!empty($axe['image'])): ?><img src="<?= e(media($axe['image'])) ?>" alt="" class="h-full w-full object-cover" loading="lazy"><?php
elseif ($photo_axe = ['gouvernance-participation' => 'engager', 'croissance-environnement' => 'accompagner', 'accompagner' => 'accompagner', 'engager' => 'engager', 'valoriser' => 'valoriser'][$axe['slug']] ?? null): ?><img src="<?= asset('img/axes/' . $photo_axe . '.jpg') ?>" alt="" class="h-full w-full object-cover" loading="lazy"><?php
else: partiel('partials/photo', ['image' => null]); endif;
