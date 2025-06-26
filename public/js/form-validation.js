document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('contact-form');
    const emailField = document.getElementById('email');
    const nameField = document.getElementById('name');
    const messageField = document.getElementById('message');
    const errorMessage = document.getElementById('error-message');

    form.addEventListener('submit', function(event) {
        event.preventDefault();
        errorMessage.textContent = '';

        if (validateForm()) {
            // If validation passes, proceed to submit the form
            submitForm();
        }
    });

    function validateForm() {
        let valid = true;

        if (!nameField.value.trim()) {
            valid = false;
            errorMessage.textContent += 'Name is required.\n';
        }

        if (!emailField.value.trim() || !isValidEmail(emailField.value)) {
            valid = false;
            errorMessage.textContent += 'A valid email is required.\n';
        }

        if (!messageField.value.trim()) {
            valid = false;
            errorMessage.textContent += 'Message is required.\n';
        }

        return valid;
    }

    function isValidEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }

    function submitForm() {
        const formData = new FormData(form);

        fetch('form-submit.js', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Handle success (e.g., show success message or redirect)
                alert('Form submitted successfully!');
                form.reset();
            } else {
                // Handle error (e.g., show error message)
                errorMessage.textContent = data.message;
            }
        })
        .catch(error => {
            errorMessage.textContent = 'An error occurred. Please try again later.';
        });
    }
});