<?php
// Plik konfiguracyjny dla PHPMailer oraz inne istotne konfiguracje aplikacji

// Ustawienia PHPMailer
define('MAIL_HOST', 'smtp.example.com'); // Adres serwera SMTP
define('MAIL_USERNAME', 'your-email@example.com'); // Adres e-mail nadawcy
define('MAIL_PASSWORD', 'your-email-password'); // Hasło do konta e-mail
define('MAIL_PORT', 587); // Port SMTP
define('MAIL_ENCRYPTION', 'tls'); // Typ szyfrowania (tls lub ssl)

// Inne ustawienia aplikacji
define('APP_NAME', 'Twoja Firma'); // Nazwa aplikacji
define('APP_EMAIL', 'info@example.com'); // E-mail kontaktowy
define('APP_URL', 'https://yourdomain.com'); // URL aplikacji

// Ustawienia dotyczące ochrony danych osobowych
define('GDPR_COMPLIANCE', true); // Czy aplikacja jest zgodna z RODO
?>