from django import forms

from .models import AbonneNewsletter


class NewsletterForm(forms.Form):
    email = forms.EmailField(
        label="Votre adresse e-mail",
        max_length=254,
        error_messages={
            "required": "Indiquez votre adresse e-mail.",
            "invalid": "Cette adresse e-mail n'est pas valide.",
        },
        widget=forms.EmailInput(
            attrs={"autocomplete": "email", "placeholder": "Votre adresse e-mail", "inputmode": "email"}
        ),
    )
    # Champ piège invisible : un humain le laisse vide, un robot le remplit.
    site_web = forms.CharField(required=False, widget=forms.TextInput(attrs={"tabindex": "-1", "autocomplete": "off"}))

    def est_un_robot(self):
        return bool(self.cleaned_data.get("site_web"))

    def enregistrer(self):
        email = self.cleaned_data["email"].strip().lower()
        abonne, cree = AbonneNewsletter.objects.get_or_create(email=email)
        if not cree and not abonne.actif:
            abonne.actif = True
            abonne.save(update_fields=["actif"])
        return abonne
