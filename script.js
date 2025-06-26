// Klasa walidatora formularza
class FormValidator {
    constructor() {
        this.errors = {};
    }

    // Walidacja pola imię
    validateName(name) {
        if (!name || name.trim().length < 2) {
            return 'Imię i nazwisko musi mieć co najmniej 2 znaki';
        }
        if (name.trim().length > 100) {
            return 'Imię i nazwisko nie może być dłuższe niż 100 znaków';
        }
        return null;
    }

    // Walidacja emaila
    validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email || !emailRegex.test(email)) {
            return 'Podaj prawidłowy adres email';
        }
        if (email.length > 320) {
            return 'Adres email jest za długi';
        }
        return null;
    }

    // Walidacja telefonu (opcjonalna)
    validatePhone(phone) {
        if (phone && phone.trim().length > 0) {
            const phoneRegex = /^[\+]?[0-9\s\-\(\)]{9,15}$/;
            if (!phoneRegex.test(phone.replace(/\s/g, ''))) {
                return 'Podaj prawidłowy numer telefonu';
            }
        }
        return null;
    }

    // Walidacja tematu
    validateSubject(subject) {
        if (!subject || subject.trim().length < 3) {
            return 'Temat musi mieć co najmniej 3 znaki';
        }
        if (subject.trim().length > 200) {
            return 'Temat nie może być dłuższy niż 200 znaków';
        }
        return null;
    }

    // Walidacja wiadomości
    validateMessage(message) {
        if (!message || message.trim().length < 10) {
            return 'Wiadomość musi mieć co najmniej 10 znaków';
        }
        if (message.trim().length > 2000) {
            return 'Wiadomość nie może być dłuższa niż 2000 znaków';
        }
        return null;
    }

    // Walidacja zgody na przetwarzanie danych
    validatePrivacy(privacy) {
        if (!privacy) {
            return 'Musisz wyrazić zgodę na przetwarzanie danych osobowych';
        }
        return null;
    }

    // Główna metoda walidacji
    validate(formData) {
        this.errors = {};
        
        const nameError = this.validateName(formData.name);
        if (nameError) this.errors.name = nameError;

        const emailError = this.validateEmail(formData.email);
        if (emailError) this.errors.email = emailError;

        const phoneError = this.validatePhone(formData.phone);
        if (phoneError) this.errors.phone = phoneError;

        const subjectError = this.validateSubject(formData.subject);
        if (subjectError) this.errors.subject = subjectError;

        const messageError = this.validateMessage(formData.message);
        if (messageError) this.errors.message = messageError;

        const privacyError = this.validatePrivacy(formData.privacy);
        if (privacyError) this.errors.privacy = privacyError;

        return Object.keys(this.errors).length === 0;
    }

    getErrors() {
        return this.errors;
    }
}

// Klasa do zarządzania formularzem
class ContactForm {
    constructor() {
        this.form = document.getElementById('contactForm');
        this.validator = new FormValidator();
        this.submitBtn = document.getElementById('submitBtn');
        this.btnText = this.submitBtn.querySelector('.btn-text');
        this.spinner = this.submitBtn.querySelector('.spinner');
        this.messageDiv = document.getElementById('message');
        
        this.init();
    }

    init() {
        this.form.addEventListener('submit', this.handleSubmit.bind(this));
        this.addRealTimeValidation();
    }

    // Walidacja w czasie rzeczywistym
    addRealTimeValidation() {
        const fields = ['name', 'email', 'phone', 'subject', 'message-text', 'privacy'];
        
        fields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.addEventListener('blur', () => this.validateField(fieldId));
                field.addEventListener('input', () => this.clearFieldError(fieldId));
            }
        });
    }

    validateField(fieldId) {
        const field = document.getElementById(fieldId);
        const fieldName = fieldId === 'message-text' ? 'message' : fieldId;
        let value = field.type === 'checkbox' ? field.checked : field.value;
        
        const formData = { [fieldName]: value };
        
        let error = null;
        switch(fieldName) {
            case 'name':
                error = this.validator.validateName(value);
                break;
            case 'email':
                error = this.validator.validateEmail(value);
                break;
            case 'phone':
                error = this.validator.validatePhone(value);
                break;
            case 'subject':
                error = this.validator.validateSubject(value);
                break;
            case 'message':
                error = this.validator.validateMessage(value);
                break;
            case 'privacy':
                error = this.validator.validatePrivacy(value);
                break;
        }
        
        this.displayFieldError(fieldId, error);
    }

    clearFieldError(fieldId) {
        const field = document.getElementById(fieldId);
        const errorElement = document.getElementById(`${fieldId === 'message-text' ? 'message' : fieldId}-error`);
        
        field.classList.remove('error');
        if (errorElement) {
            errorElement.textContent = '';
        }
    }

    displayFieldError(fieldId, error) {
        const field = document.getElementById(fieldId);
        const fieldName = fieldId === 'message-text' ? 'message' : fieldId;
        const errorElement = document.getElementById(`${fieldName}-error`);
        
        if (error) {
            field.classList.add('error');
            if (errorElement) {
                errorElement.textContent = error;
            }
        } else {
            field.classList.remove('error');
            if (errorElement) {
                errorElement.textContent = '';
            }
        }
    }

    getFormData() {
        const formData = new FormData(this.form);
        return {
            name: formData.get('name'),
            email: formData.get('email'),
            phone: formData.get('phone'),
            subject: formData.get('subject'),
            message: formData.get('message'),
            privacy: formData.get('privacy') === 'on'
        };
    }

    showMessage(message, type) {
        this.messageDiv.textContent = message;
        this.messageDiv.className = `message ${type}`;
        this.messageDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        // Automatyczne ukrycie komunikatu po 5 sekundach dla sukcesu
        if (type === 'success') {
            setTimeout(() => {
                this.hideMessage();
            }, 5000);
        }
    }

    hideMessage() {
        this.messageDiv.classList.add('hidden');
    }

    setLoading(loading) {
        if (loading) {
            this.submitBtn.disabled = true;
            this.btnText.textContent = 'Wysyłanie...';
            this.spinner.classList.remove('hidden');
        } else {
            this.submitBtn.disabled = false;
            this.btnText.textContent = 'Wyślij wiadomość';
            this.spinner.classList.add('hidden');
        }
    }

    clearForm() {
        this.form.reset();
        // Wyczyść wszystkie błędy
        const errorElements = this.form.querySelectorAll('.error-message');
        errorElements.forEach(el => el.textContent = '');
        const fields = this.form.querySelectorAll('.error');
        fields.forEach(field => field.classList.remove('error'));
    }

    async handleSubmit(e) {
        e.preventDefault();
        
        this.hideMessage();
        
        const formData = this.getFormData();
        
        // Walidacja po stronie klienta
        if (!this.validator.validate(formData)) {
            const errors = this.validator.getErrors();
            
            // Wyświetl błędy dla wszystkich pól
            Object.keys(errors).forEach(fieldName => {
                const fieldId = fieldName === 'message' ? 'message-text' : fieldName;
                this.displayFieldError(fieldId, errors[fieldName]);
            });
            
            this.showMessage('Proszę poprawić błędy w formularzu', 'error');
            return;
        }

        // Wyślij formularz
        this.setLoading(true);
        
        try {
            const response = await fetch('send_email.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showMessage('Dziękujemy! Wiadomość została wysłana pomyślnie.', 'success');
                this.clearForm();
            } else {
                this.showMessage(result.message || 'Wystąpił błąd podczas wysyłania wiadomości.', 'error');
            }
            
        } catch (error) {
            console.error('Błąd:', error);
            this.showMessage('Wystąpił błąd połączenia. Spróbuj ponownie później.', 'error');
        } finally {
            this.setLoading(false);
        }
    }
}

// Inicjalizacja formularza po załadowaniu strony
document.addEventListener('DOMContentLoaded', () => {
    new ContactForm();
});
