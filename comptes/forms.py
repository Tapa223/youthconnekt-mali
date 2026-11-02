from django import forms
from django.contrib.auth import authenticate, password_validation
from django.contrib.auth.forms import AuthenticationForm, PasswordChangeForm
from django.db import transaction

from core.models import AbonneNewsletter

from .models import REGIONS, ProfilIndividuel, ProfilOrganisation, Utilisateur

MESSAGES_COMMUNS = {
    "required": "Ce champ est obligatoire.",
}


class ChampsCompteMixin(forms.Form):
    """E-mail, mot de passe et consentements communs aux deux inscriptions."""

    email = forms.EmailField(
        label="Adresse e-mail",
        error_messages={"required": "Indiquez votre adresse e-mail.", "invalid": "Cette adresse e-mail n'est pas valide."},
    )
    password1 = forms.CharField(
        label="Mot de passe",
        strip=False,
        widget=forms.PasswordInput(attrs={"autocomplete": "new-password"}),
        help_text="8 caractères au minimum, pas uniquement des chiffres.",
        error_messages={"required": "Choisissez un mot de passe."},
    )
    password2 = forms.CharField(
        label="Confirmer le mot de passe",
        strip=False,
        widget=forms.PasswordInput(attrs={"autocomplete": "new-password"}),
        error_messages={"required": "Confirmez votre mot de passe."},
    )
    conditions = forms.BooleanField(
        label="J'accepte la politique de confidentialité et les conditions d'utilisation de la plateforme.",
        error_messages={"required": "Vous devez accepter la politique de confidentialité pour créer un compte."},
    )
    newsletter = forms.BooleanField(
        label="Je souhaite recevoir les opportunités et actualités par e-mail.", required=False
    )

    def clean_email(self):
        email = self.cleaned_data["email"].strip().lower()
        if Utilisateur.objects.filter(email__iexact=email).exists():
            raise forms.ValidationError(
                "Un compte existe déjà avec cette adresse. Connectez-vous ou utilisez « Mot de passe oublié »."
            )
        return email

    def clean(self):
        donnees = super().clean()
        p1, p2 = donnees.get("password1"), donnees.get("password2")
        if p1 and p2 and p1 != p2:
            self.add_error("password2", "Les deux mots de passe ne correspondent pas.")
        if p1 and not self.errors.get("password2"):
            utilisateur_test = Utilisateur(email=donnees.get("email", ""))
            try:
                password_validation.validate_password(p1, utilisateur_test)
            except forms.ValidationError as erreur:
                self.add_error("password1", erreur)
        return donnees

    def _creer_compte(self, type_compte):
        utilisateur = Utilisateur.objects.create_user(
            email=self.cleaned_data["email"],
            password=self.cleaned_data["password1"],
            type_compte=type_compte,
        )
        if self.cleaned_data.get("newsletter"):
            AbonneNewsletter.objects.update_or_create(email=utilisateur.email, defaults={"actif": True})
        return utilisateur


class InscriptionIndividuelleForm(ChampsCompteMixin):
    prenom = forms.CharField(label="Prénom", max_length=80, widget=forms.TextInput(attrs={"autocomplete": "given-name", "placeholder": "Aminata"}),
                             error_messages={"required": "Indiquez votre prénom."})
    nom = forms.CharField(label="Nom", max_length=80, widget=forms.TextInput(attrs={"autocomplete": "family-name", "placeholder": "Keïta"}),
                          error_messages={"required": "Indiquez votre nom."})
    categorie = forms.ChoiceField(label="Vous êtes", choices=ProfilIndividuel.Categorie.choices)
    region = forms.ChoiceField(label="Région", choices=REGIONS)
    telephone = forms.CharField(label="Téléphone", max_length=30, required=False,
                                widget=forms.TextInput(attrs={"autocomplete": "tel", "inputmode": "tel", "placeholder": "+223 …"}))

    field_order = ["prenom", "nom", "categorie", "region", "email", "telephone", "password1", "password2", "conditions", "newsletter"]

    @transaction.atomic
    def save(self):
        utilisateur = self._creer_compte(Utilisateur.TypeCompte.INDIVIDUEL)
        ProfilIndividuel.objects.create(
            utilisateur=utilisateur,
            prenom=self.cleaned_data["prenom"].strip(),
            nom=self.cleaned_data["nom"].strip(),
            categorie=self.cleaned_data["categorie"],
            region=self.cleaned_data["region"],
            telephone=self.cleaned_data.get("telephone", "").strip(),
        )
        return utilisateur


class InscriptionOrganisationForm(ChampsCompteMixin):
    nom_structure = forms.CharField(label="Nom de l'organisation", max_length=150,
                                    widget=forms.TextInput(attrs={"autocomplete": "organization", "placeholder": "Nom de la structure"}),
                                    error_messages={"required": "Indiquez le nom de l'organisation."})
    type_structure = forms.ChoiceField(label="Type", choices=ProfilOrganisation.TypeStructure.choices)
    personne_contact = forms.CharField(label="Personne de contact", max_length=120,
                                       widget=forms.TextInput(attrs={"autocomplete": "name", "placeholder": "Prénom et nom"}),
                                       error_messages={"required": "Indiquez la personne de contact."})
    region = forms.ChoiceField(label="Région", choices=REGIONS)
    site_web = forms.URLField(label="Site web", required=False, assume_scheme="https",
                              widget=forms.URLInput(attrs={"placeholder": "https://"}),
                              error_messages={"invalid": "Cette adresse web n'est pas valide."})

    field_order = ["nom_structure", "type_structure", "personne_contact", "region", "email", "site_web", "password1", "password2", "conditions", "newsletter"]

    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
        self.fields["email"].widget.attrs["placeholder"] = "contact@structure.org"

    @transaction.atomic
    def save(self):
        utilisateur = self._creer_compte(Utilisateur.TypeCompte.ORGANISATION)
        ProfilOrganisation.objects.create(
            utilisateur=utilisateur,
            nom_structure=self.cleaned_data["nom_structure"].strip(),
            type_structure=self.cleaned_data["type_structure"],
            personne_contact=self.cleaned_data["personne_contact"].strip(),
            region=self.cleaned_data["region"],
            site_web=self.cleaned_data.get("site_web", ""),
        )
        return utilisateur


class ConnexionForm(AuthenticationForm):
    username = forms.EmailField(
        label="Adresse e-mail",
        widget=forms.EmailInput(attrs={"autocomplete": "email", "autofocus": True, "placeholder": "vous@exemple.com"}),
    )
    password = forms.CharField(label="Mot de passe", strip=False, widget=forms.PasswordInput(attrs={"autocomplete": "current-password"}))
    se_souvenir = forms.BooleanField(label="Se souvenir de moi", required=False)

    error_messages = {
        "invalid_login": "Adresse e-mail ou mot de passe incorrect.",
        "inactive": "Ce compte a été désactivé. Contactez le chapitre pour en savoir plus.",
        "non_confirme": "Confirmez d'abord votre adresse e-mail grâce au lien reçu par e-mail.",
    }

    def clean(self):
        email = (self.cleaned_data.get("username") or "").strip().lower()
        password = self.cleaned_data.get("password")
        if email and password:
            self.user_cache = authenticate(self.request, username=email, password=password)
            if self.user_cache is None:
                raise forms.ValidationError(self.error_messages["invalid_login"], code="invalid_login")
            self.confirm_login_allowed(self.user_cache)
        return self.cleaned_data

    def confirm_login_allowed(self, user):
        super().confirm_login_allowed(user)
        if not user.email_confirme and not user.is_staff:
            raise forms.ValidationError(self.error_messages["non_confirme"], code="non_confirme")


class ProfilIndividuelForm(forms.ModelForm):
    class Meta:
        model = ProfilIndividuel
        fields = ["prenom", "nom", "photo", "categorie", "domaine", "region", "ville", "presentation",
                  "lien_linkedin", "site_web", "telephone", "visible"]
        labels = {"categorie": "Vous êtes", "visible": "Afficher mon profil dans la communauté"}
        widgets = {"presentation": forms.Textarea(attrs={"rows": 4, "maxlength": 1500}),
                   "photo": forms.ClearableFileInput(attrs={"accept": "image/jpeg,image/png,image/webp"})}


class ProfilOrganisationForm(forms.ModelForm):
    class Meta:
        model = ProfilOrganisation
        fields = ["nom_structure", "logo", "type_structure", "secteur", "region", "ville", "presentation",
                  "site_web", "lien_linkedin", "email_contact_public", "personne_contact", "visible"]
        labels = {"visible": "Afficher l'organisation dans la communauté"}
        widgets = {"presentation": forms.Textarea(attrs={"rows": 4, "maxlength": 1500}),
                   "logo": forms.ClearableFileInput(attrs={"accept": "image/jpeg,image/png,image/webp"})}


class ChangementMotDePasseForm(PasswordChangeForm):
    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
        self.fields["old_password"].label = "Mot de passe actuel"
        self.fields["new_password1"].label = "Nouveau mot de passe"
        self.fields["new_password1"].help_text = "8 caractères au minimum, pas uniquement des chiffres."
        self.fields["new_password2"].label = "Confirmer le nouveau mot de passe"


class SuppressionCompteForm(forms.Form):
    mot_de_passe = forms.CharField(label="Votre mot de passe", strip=False,
                                   widget=forms.PasswordInput(attrs={"autocomplete": "current-password"}),
                                   error_messages={"required": "Saisissez votre mot de passe pour confirmer."})

    def __init__(self, utilisateur, *args, **kwargs):
        self.utilisateur = utilisateur
        super().__init__(*args, **kwargs)

    def clean_mot_de_passe(self):
        mdp = self.cleaned_data["mot_de_passe"]
        if not self.utilisateur.check_password(mdp):
            raise forms.ValidationError("Mot de passe incorrect.")
        return mdp
