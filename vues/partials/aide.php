<?php
/* Bulle d'aide en bas à droite : réponses rapides et lien vers la page Contact. */
$connecte = est_connecte();
$questions = [
    ['Comment rejoindre YouthConnekt Mali ?', 'Créez votre compte, en tant que membre individuel ou en tant qu\'organisation. Votre compte fait de vous un membre du chapitre.'],
    ['Comment intégrer une communauté ?', 'Ouvrez la communauté qui vous intéresse, cliquez sur « Intégrer la communauté » et remplissez le petit formulaire. L\'équipe étudie ensuite votre demande.'],
    ['Où trouver les opportunités ?', 'Toutes les opportunités ouvertes sont sur la page Opportunités. Celles de vos communautés s\'affichent aussi dans votre espace.'],
    ['Je n\'ai pas reçu l\'e-mail de confirmation', 'Regardez dans vos courriers indésirables. Sinon, connectez-vous : le site vous propose de recevoir un nouvel e-mail.'],
];
$plus_haut = str_starts_with(chemin_actuel(), '/opportunites/'); // laisse la place à la barre « Postuler » sur téléphone
?>
<div class="aide fixed right-4 z-40 sm:right-6 <?= $plus_haut ? 'bottom-24 lg:bottom-6' : 'bottom-4 sm:bottom-6' ?>" data-aide>
  <div id="panneau-aide" class="aide-panneau absolute right-0 bottom-[calc(100%+12px)] w-[min(22rem,calc(100vw-2rem))] overflow-hidden rounded-2xl border border-ligne bg-white shadow-2xl shadow-vert/20" hidden>
    <div class="bg-vert px-5 py-4 text-white">
      <p class="text-lg font-bold">Besoin d'aide ?</p>
      <p class="text-sm text-white/85">Les réponses aux questions les plus fréquentes</p>
    </div>
    <div class="max-h-[50vh] divide-y divide-ligne overflow-y-auto px-2">
      <?php foreach ($questions as [$question, $reponse]): ?>
      <details class="group px-3 py-1">
        <summary class="flex min-h-11 cursor-pointer list-none items-center justify-between gap-3 text-[15px] font-bold text-encre hover:text-orange [&::-webkit-details-marker]:hidden">
          <?= e($question) ?>
          <svg class="size-4 shrink-0 text-vert transition-transform group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
        </summary>
        <p class="pb-3 text-sm leading-relaxed"><?= e($reponse) ?></p>
      </details>
      <?php endforeach ?>
    </div>
    <div class="flex gap-2 border-t border-ligne p-3">
      <a href="<?= url('/contact') ?>" class="btn btn-vert min-h-11 flex-1 px-3 text-[15px]">Nous écrire</a>
      <?php if (!$connecte): ?><a href="<?= url('/compte/inscription') ?>" class="btn btn-orange min-h-11 flex-1 px-3 text-[15px]">Créer mon compte</a><?php endif ?>
    </div>
  </div>
  <button type="button" class="aide-bouton grid size-14 place-items-center rounded-full bg-orange text-white shadow-lg shadow-orange/40 transition-colors hover:bg-vert" aria-expanded="false" aria-controls="panneau-aide" data-ouvrir-aide>
    <span class="sr-only">Besoin d'aide ?</span>
    <span class="aide-icone text-[28px] leading-none font-bold" aria-hidden="true">?</span>
    <svg class="aide-croix hidden size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
  </button>
</div>
