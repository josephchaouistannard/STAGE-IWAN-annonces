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
 * Verifie le nombre de pages nécessaire pour afficher les offres filtrées et crée les boutons si besoin
 */
function setupPagination() {
    jobListItems = document.querySelectorAll('.job-list-item');
    const totalOffres = jobListItems.length;
    nbPages = Math.ceil(totalOffres / offresParPage);

    const container = document.getElementById('pagination-controls');
    if (nbPages > 1) {
        const prevButton = document.createElement('button');
        prevButton.textContent = '<';
        prevButton.id = 'prevButton';
        prevButton.addEventListener('click', () => {
            if (page > 1) {
                page--;
                afficherPage();
            }
        })
        container.appendChild(prevButton);

        const numPage = document.createElement('p');
        numPage.id = 'page-count';
        numPage.textContent = 'Page ' + page + ' de ' + nbPages;
        container.appendChild(numPage);

        const nextButton = document.createElement('button');
        nextButton.textContent = '>';
        nextButton.id = 'nextButton';
        nextButton.addEventListener('click', () => {
            if (page < nbPages) {
                page++;
                afficherPage();
            }
        })
        container.appendChild(nextButton);

        afficherPage();
    }
}

/**
 * Change la page d'offres affichées et met à jour le compteur de page en bas
 */
function afficherPage() {
    const startIndex = (page - 1) * offresParPage;
    const endIndex = startIndex + offresParPage;
    const numPage = document.getElementById('page-count');
    numPage.textContent = 'Page ' + page + ' de ' + nbPages;

    jobListItems.forEach((item, index) => {
        if (index >= startIndex && index < endIndex) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
}

/**
 * Remplir formulaire selon parametres GET
 */
function remplirFormulaire() {
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
}

/**
 * Changer comportement de click sur bouton formulaire filtrage
 */
function changerSubmit() {
    const form = document.getElementById('filters-form');
    const submitLink = document.getElementById('submit-filters');

    if (form && submitLink) {
        submitLink.addEventListener('click', function (event) {
            event.preventDefault();
            submitFiltersForm(form);
        });
    }
}

// Initialisation des variables necessaires pour la pagination
let page = 1;
let nbPages = 1;
let jobListItems = [];
const offresParPage = 10;

/**
 * Après chargement de de la page, crée evenement de soumission de formulaire.
 * Rempli aussi le formulaire s'il exist des parametres dans URL
 */
document.addEventListener('DOMContentLoaded', function () {
    changerSubmit();
    remplirFormulaire();
    setupPagination();
})