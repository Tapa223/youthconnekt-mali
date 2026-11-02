<?php /* Image d'un axe d'action : celle de l'administration, sinon la photo de la maquette. Paramètre : $axe */
if (!empty($axe['image'])): ?><img src="<?= e(media($axe['image'])) ?>" alt="" class="h-full w-full object-cover" loading="lazy"><?php
elseif (in_array($axe['slug'], ['accompagner', 'engager', 'valoriser'], true)): ?><img src="<?= asset('img/axes/' . $axe['slug'] . '.jpg') ?>" alt="" class="h-full w-full object-cover" loading="lazy"><?php
else: partiel('partials/photo', ['image' => null]); endif;
