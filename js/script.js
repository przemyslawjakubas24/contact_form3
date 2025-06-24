/**
 * Nowoczesny formularz kontaktowy - skrypt JavaScript
 * Zapewnia walidację po stronie klienta i przesyłanie formularza za pomocą AJAX
 */

document.addEventListener('DOMContentLoaded', () => {
	const contactForm = document.getElementById('contact-form')
	const submitBtn = document.getElementById('submit-btn')
	const spinner = document.getElementById('spinner')
	const successMessage = document.getElementById('success-message')
	const errorMessage = document.getElementById('error-message')

	// Mapowanie pól formularza i ich komunikatów o błędach
	const formFields = [
		{ input: 'name', error: 'name-error', validate: validateName },
		{ input: 'email', error: 'email-error', validate: validateEmail },
		{ input: 'phone', error: 'phone-error', validate: validatePhone },
		{ input: 'subject', error: 'subject-error', validate: validateSubject },
		{ input: 'message', error: 'message-error', validate: validateMessage },
		{ input: 'consent', error: 'consent-error', validate: validateConsent },
	]

	// Dodaj obsługę zdarzeń do pól formularza (walidacja podczas wpisywania)
	formFields.forEach(field => {
		const inputElement = document.getElementById(field.input)
		inputElement.addEventListener('input', () => {
			validateField(field)
		})

		// Dodatkowe zdarzenie dla checkbox'a
		if (field.input === 'consent') {
			inputElement.addEventListener('change', () => {
				validateField(field)
			})
		}
	})

	// Obsługa przesyłania formularza
	contactForm.addEventListener('submit', async e => {
		e.preventDefault()

		// Ukryj wcześniejsze komunikaty
		hideMessages()

		// Sprawdź czy wszystkie pola są poprawne
		let isFormValid = true
		formFields.forEach(field => {
			if (!validateField(field)) {
				isFormValid = false
			}
		})

		if (!isFormValid) {
			// Przewiń do pierwszego błędu
			const firstErrorField = document.querySelector('.error')
			if (firstErrorField) {
				firstErrorField.focus()
			}
			return
		}

		// Formularz jest poprawny, przygotuj się do wysłania
		setSubmitting(true)

		// Przygotuj dane formularza
		const formData = new FormData(contactForm)

		try {
			// Wyślij dane poprzez AJAX
			const response = await fetch('send-email.php', {
				method: 'POST',
				body: formData,
			})

			const result = await response.json()

			if (result.success) {
				// Pokaż komunikat o powodzeniu
				showSuccess()
				contactForm.reset()
			} else {
				// Pokaż komunikat o błędzie
				showError(result.message || 'Wystąpił nieznany błąd. Spróbuj ponownie.')
			}
		} catch (err) {
			// Pokaż ogólny komunikat o błędzie
			showError('Wystąpił problem z połączeniem. Sprawdź połączenie internetowe i spróbuj ponownie.')
			console.error('Error submitting form:', err)
		} finally {
			setSubmitting(false)
		}
	})

	// Funkcje pomocnicze do walidacji
	function validateName(value) {
		if (!value.trim()) {
			return 'Imię i nazwisko jest wymagane'
		}
		if (value.trim().length < 3) {
			return 'Imię i nazwisko musi mieć co najmniej 3 znaki'
		}
		return ''
	}

	function validateEmail(value) {
		if (!value.trim()) {
			return 'Adres e-mail jest wymagany'
		}

		// Podstawowa walidacja e-mail
		const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
		if (!emailRegex.test(value)) {
			return 'Podaj poprawny adres e-mail'
		}
		return ''
	}

	function validatePhone(value) {
		if (!value.trim()) {
			return '' // Telefon nie jest wymagany
		}

		// Walidacja formatu telefonu (podstawowy regex dla polskich numerów)
		const phoneRegex = /^[+]?[(]?[0-9]{3}[)]?[-\s.]?[0-9]{3}[-\s.]?[0-9]{3,6}$/
		if (!phoneRegex.test(value.replace(/\s/g, ''))) {
			return 'Podaj poprawny numer telefonu'
		}
		return ''
	}

	function validateSubject(value) {
		if (!value.trim()) {
			return 'Temat wiadomości jest wymagany'
		}
		if (value.trim().length < 5) {
			return 'Temat musi mieć co najmniej 5 znaków'
		}
		return ''
	}

	function validateMessage(value) {
		if (!value.trim()) {
			return 'Treść wiadomości jest wymagana'
		}
		if (value.trim().length < 10) {
			return 'Wiadomość musi mieć co najmniej 10 znaków'
		}
		return ''
	}

	function validateConsent(value) {
		if (!value) {
			return 'Musisz wyrazić zgodę na przetwarzanie danych osobowych'
		}
		return ''
	}

	// Funkcja do walidacji pojedynczego pola
	function validateField(field) {
		const inputElement = document.getElementById(field.input)
		const errorElement = document.getElementById(field.error)
		const validationResult = field.validate(field.input === 'consent' ? inputElement.checked : inputElement.value)

		if (validationResult) {
			errorElement.textContent = validationResult
			inputElement.classList.add('error')
			inputElement.setAttribute('aria-invalid', 'true')
			return false
		} else {
			errorElement.textContent = ''
			inputElement.classList.remove('error')
			inputElement.setAttribute('aria-invalid', 'false')
			return true
		}
	}

	// Funkcje do zarządzania stanem formularza
	function setSubmitting(isSubmitting) {
		if (isSubmitting) {
			submitBtn.disabled = true
			submitBtn.style.opacity = '0.7'
			spinner.hidden = false
			spinner.setAttribute('aria-hidden', 'false')
		} else {
			submitBtn.disabled = false
			submitBtn.style.opacity = '1'
			spinner.hidden = true
			spinner.setAttribute('aria-hidden', 'true')
		}
	}

	function hideMessages() {
		successMessage.hidden = true
		errorMessage.hidden = true
		successMessage.setAttribute('aria-hidden', 'true')
		errorMessage.setAttribute('aria-hidden', 'true')
	}

	function showSuccess() {
		successMessage.hidden = false
		successMessage.setAttribute('aria-hidden', 'false')
		successMessage.focus()
		scrollToTop()
	}

	function showError(message = null) {
		if (message) {
			const errorParagraph = errorMessage.querySelector('p')
			errorParagraph.textContent = message
		}
		errorMessage.hidden = false
		errorMessage.setAttribute('aria-hidden', 'false')
		errorMessage.focus()
		scrollToTop()
	}

	function scrollToTop() {
		window.scrollTo({
			top: 0,
			behavior: 'smooth',
		})
	}
})
