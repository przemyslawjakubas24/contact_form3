document.getElementById('contactForm').addEventListener('submit', async function(e) {
  e.preventDefault();

  const form = e.target;
  const name = form.name;
  const email = form.email;
  const message = form.message;
  const formStatus = document.getElementById('formStatus');

  // Reset error messages
  ['nameError', 'emailError', 'messageError'].forEach(id => {
    document.getElementById(id).textContent = '';
  });
  formStatus.textContent = '';

  let valid = true;

  // Prosta walidacja
  if (!name.value.trim()) {
    document.getElementById('nameError').textContent = 'Proszę podać imię i nazwisko.';
    valid = false;
  }
  if (!email.value.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
    document.getElementById('emailError').textContent = 'Proszę podać poprawny adres email.';
    valid = false;
  }
  if (!message.value.trim()) {
    document.getElementById('messageError').textContent = 'Proszę wpisać wiadomość.';
    valid = false;
  }

  if (!valid) return;

  // Blokuj przycisk i pokaż ładowanie
  const submitBtn = form.querySelector('button[type="submit"]');
  submitBtn.disabled = true;
  submitBtn.textContent = 'Wysyłanie...';

  try {
    const formData = new FormData(form);
    const response = await fetch('send-email.php', {
      method: 'POST',
      body: formData
    });

    const result = await response.json();

    if (response.ok && result.success) {
      formStatus.style.color = 'green';
      formStatus.textContent = 'Wiadomość została wysłana. Dziękujemy!';
      form.reset();
    } else {
      formStatus.style.color = 'red';
      formStatus.textContent = result.error || 'Wystąpił błąd podczas wysyłania.';
    }
  } catch (error) {
    formStatus.style.color = 'red';
    formStatus.textContent = 'Błąd sieci. Spróbuj ponownie później.';
  } finally {
    submitBtn.disabled = false;
    submitBtn.textContent = 'Wyślij';
  }
});
