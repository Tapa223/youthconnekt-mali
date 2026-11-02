<?php partiel('pages/entete_page', ['surtitre' => 'YouthConnekt Mali', 'titre_page' => 'Mentions légales']); ?>
<section class="conteneur py-14 sm:py-16">
  <div class="prose-ycm max-w-3xl">
    <h2>Éditeur du site</h2>
    <p>YouthConnekt Mali, chapitre national du réseau YouthConnekt Africa<?= $p['adresse'] ? ', ' . e($p['adresse']) : ', Bamako, Mali' ?>.</p>
    <?php if ($p['email_contact'] || $p['telephone']): ?><p>Contact : <?= e(implode(' · ', array_filter([$p['email_contact'], $p['telephone']]))) ?></p><?php endif ?>
    <h2>Hébergement</h2>
    <p>Les informations sur l'hébergeur sont disponibles sur demande auprès de l'équipe du chapitre.</p>
    <h2>Contenus et images</h2>
    <p>Les textes, logos et photos publiés sur ce site appartiennent à YouthConnekt Mali ou à leurs auteurs, et ne peuvent être réutilisés sans autorisation. Les témoignages et photos de personnes sont publiés avec leur accord.</p>
    <h2>Données personnelles</h2>
    <p>Voir la <a href="<?= url('/confidentialite') ?>" class="lien-texte">politique de confidentialité</a>.</p>
  </div>
</section>
