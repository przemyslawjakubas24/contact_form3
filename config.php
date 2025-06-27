<?php
/**
 * Konfiguracja email - dane logowania do serwera SMTP
 * WAŻNE: Ten plik musi być umieszczony POZA katalogiem publicznym (public_html)
 * lub zabezpieczony plikiem .htaccess
 */

// Konfiguracja SMTP dla seohost.pl
define('SMTP_HOST', 'h58.seohost.pl'); // lub smtp.seohost.pl - sprawdź w panelu hostingu
define('SMTP_PORT', 465); // Port SSL (465) lub TLS (587) - sprawdź w dokumentacji seohost.pl
define('SMTP_SECURE', 'ssl'); // 'ssl' dla portu 465 lub 'tls' dla portu 587
define('SMTP_USERNAME', 'form@testystrony.pl'); // ZMIEŃ NA SWÓJ EMAIL
define('SMTP_PASSWORD', 'Tmobile1'); // ZMIEŃ NA SWOJE HASŁO

// Ustawienia odbiorcy
define('RECIPIENT_EMAIL', 'kontakt@testystrony.pl'); // ZMIEŃ NA EMAIL ODBIORCY
define('RECIPIENT_NAME', 'Ewa Jakubas'); // ZMIEŃ NA NAZWĘ FIRMY

// Ustawienia nadawcy (formularz)
define('SENDER_NAME', 'Formularz Kontaktowy');
//define('REPLY_TO_EMAIL', SMTP_USERNAME); // Email do odpowiedzi

// Ustawienia bezpieczeństwa
define('ALLOWED_ORIGINS', [
    'https://testystrony.pl',
    'https://www.testystrony.pl'
]); // ZMIEŃ NA SWOJE DOMENY

// Konfiguracja reCAPTCHA (opcjonalnie)
define('RECAPTCHA_SECRET_KEY', ''); // Klucz prywatny reCAPTCHA
define('RECAPTCHA_SITE_KEY', ''); // Klucz publiczny reCAPTCHA

// Ustawienia debugowania (wyłącz w produkcji)
define('DEBUG_MODE', false);
?>
