/**
 * Réinitialise le formulaire de filtrage en rechargeant la page sans aucun paramètre de filtre.
 * Doit être appelé avec l'objet 'event' pour empêcher la soumission par défaut du formulaire.
 *
 * @param {Event} event - L'événement de clic du bouton.
 */
function reinitialiserFiltersForm(event) {
    // ÉTAPE CRUCIALE : Empêche le formulaire de se soumettre de manière standard.
    if (event) {
        event.preventDefault();
    }

    // Obtenir l'URL de base (sans les paramètres GET)
    const baseUrl = window.location.href.split('?')[0];
    
    // Recharger la page à cette URL de base. Cela supprime tous les filtres.
    window.location.href = baseUrl;
}

/**
 * Gère la soumission du formulaire de filtrage
 */
function submitFiltersForm(form) {
    // Obtenir URL de base (sans parametres GET)
    const baseUrl = window.location.href.split('?')[0];

    const params = [];

    // Valeur par defaut pour select elements
    const defaultSelectValue = 'tous';

    // Selectionner les elements du formulaire
    const elements = form.querySelectorAll('select, input[type="text"], input[type="checkbox"]');

    elements.forEach(element => {
        const name = element.name;
        const value = element.value;

        // Verifies que chaque element a un attribut name
        if (name) {
            if (element.tagName === 'SELECT') {
                // Pour select, ajouter valuer si different du défaut
                if (value !== defaultSelectValue) {
                    params.push(`${name}=${encodeURIComponent(value)}`);
                }
            } else if (element.tagName === 'INPUT') {
                if (element.type === 'checkbox') {
                    // Pour cases à cocher, ajouter valeur si cochée
                    if (element.checked) {
                        params.push(`${name}=${encodeURIComponent(value)}`);
                    }
                } else if (element.type === 'text') {
                    // Pour text, ajouter si'ils ne sont pas vides
                    if (value.trim() !== '') {
                        params.push(`${name}=${encodeURIComponent(value)}`);
                    }
                }
            }
        }
    });
    // Creer un string avec les parametres obtenus
    const queryString = params.join('&');
    // Ajouter ce string seuelement s'il n'est pas vide
    const newUrl = baseUrl + (queryString ? '?' + queryString : '');
    // Aller à ce URL
    window.location.href = newUrl;
}

/**
 * Après chargement de de la page, crée evenement de soumission de formulaire.
 * Rempli aussi le formulaire s'il exist des parametres dans URL
 */
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('filters-form');
    const submitLink = document.getElementById('submit-filters');

    if (form && submitLink) {
        submitLink.addEventListener('click', function (event) {
            event.preventDefault();
            submitFiltersForm(form);
        });
    }

    // Remplir formulaire selon parametres GET
    const params = new URLSearchParams(window.location.search);

    const contratSelected = params.get('contrat');
    const contratElement = document.getElementById('contrat');
    if (contratSelected && contratElement) {
        contratElement.value = contratSelected;
    }

    const professionSelected = params.get('profession');
    const professionElement = document.getElementById('profession');
    if (professionSelected && professionElement) {
        professionElement.value = professionSelected;
    }

    const dureeSelected = params.get('duree');
    const dureeElement = document.getElementById('duree');
    if (dureeSelected && dureeElement) {
        dureeElement.value = dureeSelected;
    }

    const evenementSelected = params.get('evenement');
    const evenementElement = document.getElementById('evenement');
    if (evenementSelected && evenementElement) {
        evenementElement.value = evenementSelected;
    }

    const motcleSelected = params.get('mot-cle');
    const motcleElement = document.getElementById('mot-cle');
    if (motcleSelected && motcleElement) {
        motcleElement.value = motcleSelected;
    }

    const noirmoutierSelected = params.get('noirmoutier');
    const noirmoutierElement = document.getElementById('noirmoutier');
    if (noirmoutierSelected && noirmoutierElement) {
        noirmoutierElement.checked = noirmoutierSelected === noirmoutierElement.value;
    }

    const epineSelected = params.get('epine');
    const epineElement = document.getElementById('epine');
    if (epineSelected && epineElement) {
        epineElement.checked = epineSelected === epineElement.value;
    }

    const gueriniereSelected = params.get('gueriniere');
    const gueriniereElement = document.getElementById('gueriniere');
    if (gueriniereSelected && gueriniereElement) {
        gueriniereElement.checked = gueriniereSelected === gueriniereElement.value;
    }

    const barbatreSelected = params.get('barbatre');
    const barbatreElement = document.getElementById('barbatre');
    if (barbatreSelected && barbatreElement) {
        barbatreElement.checked = barbatreSelected === barbatreElement.value;
    }

    const hebergementSelected = params.get('hebergement');
    const hebergementElement = document.getElementById('hebergement');
    if (hebergementSelected && hebergementElement) {
        hebergementElement.checked = hebergementSelected === hebergementElement.value;
    }
});