document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filters-form');

    if (form) { // Make sure the form element exists
        form.addEventListener('submit', function(event) {
            // Prevent the default form submission (which would include all elements)
            //event.preventDefault();

            // Get the base URL for the submission (usually the current page if action is empty)
            const baseUrl = form.action || window.location.href.split('?')[0];

            const params = [];
            // Define the default value for your select elements
            const defaultSelectValue = 'tous';

            // Iterate over all elements within the form
            for (let i = 0; i < form.elements.length; i++) {
                const element = form.elements[i];
                const name = element.name;
                const value = element.value;

                // Only process elements that have a name and value (ignore buttons, etc.)
                if (name && value !== undefined && element.type !== 'submit' && element.type !== 'button' && element.type !== 'reset') {

                    if (element.tagName === 'SELECT') {
                        // For select elements, ONLY add if the value is NOT the default
                        if (value !== defaultSelectValue) {
                            params.push(`${name}=${encodeURIComponent(value)}`);
                        }
                    } else if (element.tagName === 'INPUT') {
                        if (element.type === 'checkbox') {
                            // For checkboxes, ONLY add if they are checked
                            if (element.checked) {
                                params.push(`${name}=${encodeURIComponent(value)}`);
                            }
                        } else if (element.type === 'text') {
                            // For text inputs, ONLY add if they are not empty
                            if (value.trim() !== '') {
                                params.push(`${name}=${encodeURIComponent(value)}`);
                            }
                        }
                        // You can add other input types (radio, etc.) here if needed
                    }
                    // You can add other element types (textarea, etc.) here if needed
                }
            }

            // Join the collected parameters into a query string
            const queryString = params.join('&');

            // Construct the new URL
            // Add the query string only if there are parameters
            const newUrl = baseUrl + (queryString ? '?' + queryString : '');

            // Redirect the browser to the new URL
            window.location.href = newUrl;
        });
    }
});