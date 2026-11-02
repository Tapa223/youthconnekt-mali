<?php partiel('comptes/etapes', ['etape' => $etape]) ?>
<div class="rounded-2xl border border-ligne p-6 text-center sm:p-10">
  <span class="mx-auto grid size-16 place-items-center rounded-full bg-vert-clair text-vert" aria-hidden="true"><svg class="size-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg></span>
  <h1 class="mt-5 text-3xl font-bold">Vérifiez votre boîte e-mail</h1>
  <p class="mt-3 text-base">Nous venons d'envoyer un lien de confirmation à<br><strong class="text-encre"><?= e($email) ?></strong></p>
  <p class="mt-3 text-sm">Ouvrez ce lien pour activer votre compte et accéder à votre espace membre. Pensez à regarder dans les courriers indésirables.</p>
  <form method="post" action="<?= url('/compte/inscription/renvoyer') ?>" class="mt-6"><?= csrf() ?>
    <button type="submit" class="btn btn-contour">Renvoyer l'e-mail</button>
  </form>
</div>
<p class="mt-5 text-center text-sm">Mauvaise adresse ? <a href="<?= url('/contact', ['sujet' => 'compte']) ?>" class="font-bold text-vert underline underline-offset-4">Contactez-nous</a></p>
