<?php partiel('pages/entete_page', ['surtitre' => 'YouthConnekt Mali', 'titre_page' => 'Politique de confidentialité', 'intro' => 'Ce que le site recueille, pourquoi, et comment vous gardez la main sur vos informations.']); ?>
<section class="conteneur py-14 sm:py-16">
  <div class="prose-ycm max-w-3xl">
    <h2>Les informations recueillies</h2>
    <p>Le site ne recueille que les informations nécessaires à son fonctionnement :</p>
    <ul>
      <li>à la création d'un compte : votre adresse e-mail, votre mot de passe (enregistré sous une forme chiffrée, que personne ne peut lire), votre type de compte et les informations de votre profil ;</li>
      <li>avec le formulaire de contact : votre nom, votre adresse e-mail, votre profil et votre message ;</li>
      <li>avec l'inscription à la newsletter : votre adresse e-mail.</li>
    </ul>
    <h2>Ce qui est visible sur le site</h2>
    <p>Votre adresse e-mail de connexion, votre mot de passe et votre numéro de téléphone ne sont jamais affichés. Les informations de votre profil public (nom, profil, région, présentation) ne sont visibles que des membres connectés, et vous pouvez masquer votre profil à tout moment depuis votre espace membre.</p>
    <h2>À quoi servent ces informations</h2>
    <p>Elles servent à gérer votre compte et vos demandes d'intégration aux communautés, à vous proposer les opportunités qui vous concernent, à répondre à vos messages et, si vous l'avez demandé, à vous envoyer la newsletter. Elles ne sont ni vendues ni cédées.</p>
    <h2>Vos droits</h2>
    <p>Vous pouvez modifier les informations de votre profil depuis votre espace membre, et supprimer votre compte à tout moment (Mon espace, puis Paramètres) : votre profil et vos demandes sont alors effacés. Pour toute question ou pour vous désabonner de la newsletter, écrivez à l'équipe du chapitre<?php if ($p['email_contact']): ?> à l'adresse <a href="mailto:<?= e($p['email_contact']) ?>" class="font-bold text-vert underline"><?= e($p['email_contact']) ?></a><?php else: ?> depuis la page <a href="<?= url('/contact') ?>" class="font-bold text-vert underline">Contact</a><?php endif ?>.</p>
    <h2>Sécurité</h2>
    <p>Les échanges avec le site sont chiffrés (HTTPS), les accès à l'administration sont réservés à l'équipe selon son rôle, et les données sont sauvegardées régulièrement.</p>
    <h2>Cookies</h2>
    <p>Le site n'utilise qu'un cookie technique, nécessaire pour rester connecté et protéger les formulaires. Aucun cookie publicitaire ni de suivi n'est utilisé.</p>
  </div>
</section>
