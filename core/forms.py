from django import forms

from .models import AbonneNewsletter, MessageContact


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


class ContactForm(forms.ModelForm):
    sujet = forms.ChoiceField(label="Sujet", choices=MessageContact.Sujet.choices)
    consentement = forms.BooleanField(
        label="J'accepte que mes données soient utilisées pour répondre à ma demande, conformément à la politique de confidentialité.",
        error_messages={"required": "Cochez cette case pour que nous puissions vous répondre."},
    )
    site_web = forms.CharField(required=False)

    class Meta:
        model = MessageContact
        fields = ["nom", "email", "profil", "message"]
        labels = {"nom": "Nom complet", "email": "Adresse e-mail", "profil": "Vous êtes", "message": "Message"}
        error_messages = {
            "nom": {"required": "Indiquez votre nom."},
            "email": {"required": "Indiquez votre adresse e-mail.", "invalid": "Cette adresse e-mail n'est pas valide."},
            "message": {"required": "Écrivez votre message."},
        }
        widgets = {
            "nom": forms.TextInput(attrs={"autocomplete": "name", "placeholder": "Votre nom"}),
            "email": forms.EmailInput(attrs={"autocomplete": "email", "placeholder": "vous@exemple.com"}),
            "message": forms.Textarea(attrs={"rows": 6, "placeholder": "Écrivez votre message…", "maxlength": 5000}),
        }

    def clean_message(self):
        message = self.cleaned_data["message"].strip()
        if len(message) < 10:
            raise forms.ValidationError("Votre message est trop court.")
        return message

    def save(self, commit=True):
        objet = super().save(commit=False)
        objet.objet = dict(MessageContact.Sujet.choices)[self.cleaned_data["sujet"]]
        if commit:
            objet.save()
        return objet
