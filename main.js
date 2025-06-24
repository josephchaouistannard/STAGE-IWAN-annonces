function resetForm() {
    const contratElement = document.getElementById('contrat');
    if (contratElement) contratElement.value = "tous";
    const professionElement = document.getElementById('profession');
    if (professionElement) professionElement.value = "tous";
    const dureeElement = document.getElementById('duree');
    if (dureeElement) dureeElement.value = "tous";
    const evenementElement = document.getElementById('evenement');
    if (evenementElement) evenementElement.value = "tous";
    const motcleElement = document.getElementById('mot-cle');
    if (motcleElement) motcleElement.value = ""
    const noirmoutierElement = document.getElementById('noirmoutier');
    if (noirmoutierElement) noirmoutierElement.checked = false;
    const epineElement = document.getElementById('epine');
    if (epineElement) epineElement.checked = false;
    const gueriniereElement = document.getElementById('gueriniere');
    if (gueriniereElement) gueriniereElement.checked = false;
    const barbatreElement = document.getElementById('barbatre');
    if (barbatreElement) barbatreElement.checked = false;
    const hebergementElement = document.getElementById('hebergement');
    if (hebergementElement) hebergementElement.checked = false;

    // Call the function that handles submission logic
    const form = document.getElementById("filters-form");
    if (form) {
        handleFormSubmission(form);
    }
}

// Function to handle the form submission logic
function handleFormSubmission(form) {
    // Get the base URL for the submission (current page path without query string)
    const baseUrl = window.location.href.split('?')[0];

    const params = [];
    // Define the default value for your select elements
    const defaultSelectValue = 'tous';

    // Select relevant form elements
    const elements = form.querySelectorAll('select, input[type="text"], input[type="checkbox"]');

    elements.forEach(element => {
        const name = element.name;
        const value = element.value;

        // Always check for name, it's essential for a valid parameter
        if (name) {
            if (element.tagName === 'SELECT') {
                // For select elements, ONLY add if the value is NOT the default 'tous'
                if (value !== defaultSelectValue) {
                    params.push(`${name}=${encodeURIComponent(value)}`);
                }
            } else if (element.tagName === 'INPUT') {
                if (element.type === 'checkbox') {
                    // For checkboxes, ONLY add if they are checked
                    if (element.checked) {
                        // Ensure the value matches what your PHP expects (e.g., 'oui')
                        params.push(`${name}=${encodeURIComponent(value)}`);
                    }
                } else if (element.type === 'text') {
                    // For text inputs, ONLY add if they are not empty after trimming whitespace
                    if (value.trim() !== '') {
                        params.push(`${name}=${encodeURIComponent(value)}`);
                    }
                }
            }
            // Add logic for other input types if needed (e.g., radio buttons)
        }
    });

    // Join the collected parameters into a query string
    const queryString = params.join('&');

    // Construct the new URL
    // Add the query string only if there are parameters
    const newUrl = baseUrl + (queryString ? '?' + queryString : '');

    // Redirect the browser to the new URL
    window.location.href = newUrl;
}


document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('filters-form');
    const submitLink = document.getElementById('submit-filters'); // Get the new link

    if (form && submitLink) { // Make sure both elements exist
        // Remove the form submit listener as the link will trigger the logic
        // form.addEventListener('submit', function (event) {
        //     event.preventDefault();
        //     handleFormSubmission(form);
        // });

        // Add a click listener to the new link
        submitLink.addEventListener('click', function(event) {
            event.preventDefault(); // Prevent the default link navigation
            handleFormSubmission(form); // Trigger the submission logic
        });
    }

    // FILL FORM ACCORDING TO GET PARAMETERS
    // Get the search parameters from the URL
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