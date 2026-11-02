<?php
/*
 * Sur l'ordinateur de développement (mode e-mail « journal »), les e-mails ne partent pas :
 * le lien qu'ils contiennent s'affiche ici pour pouvoir tester l'inscription et le mot de passe oublié.
 */
$lien_test = $_SESSION['lien_email_test'] ?? '';
unset($_SESSION['lien_email_test']);
if ($lien_test === '' || !en_developpement()) {
    return;
}
?>
<div class="fixed inset-x-4 top-20 z-50 mx-auto max-w-md rounded-xl border-2 border-dashed border-orange bg-white p-4 text-sm text-encre shadow-xl sm:left-6 sm:right-auto" role="status">
  <p class="font-bold text-vert">Test en local : e-mail non envoyé</p>
  <p class="mt-1">En ligne, ce lien arrive par e-mail. Sur WAMP, ouvrez-le directement :</p>
  <a href="<?= e($lien_test) ?>" class="mt-2 block break-all font-bold text-orange"><?= e($lien_test) ?></a>
</div>
