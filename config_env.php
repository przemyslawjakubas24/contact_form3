<?php
/**
 * Alternatywna konfiguracja z obsługą pliku .env
 * Ta wersja jest bezpieczniejsza - można użyć zamiast config.php
 */

// Funkcja do wczytywania pliku .env
function loadEnv($path) {
    if (!file_exists($path)) {
        return;
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue; // Pomiń komentarze
        }
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        // Usuń cudzysłowy jeśli istnieją
        if (preg_match('/^"(.*)"$/', $value, $matches)) {
            $value = $matches[1];
        } elseif (preg_match("/^'(.*)'$/", $value, $matches)) {
            $value = $matches[1];
        }
        
        $_ENV[$name] = $value;
    }
}

// Wczytaj zmienne środowiskowe
loadEnv(__DIR__ . '/.env');

// Funkcja pomocnicza do pobierania zmiennych środowiskowych
function env($key, $default = null) {
    return $_ENV[$key] ?? getenv($key) ?: $default;
}

// Konfiguracja SMTP
define('SMTP_HOST', env('SMTP_HOST', 'mail.seohost.pl'));
define('SMTP_PORT', (int)env('SMTP_PORT', 587));
define('SMTP_SECURE', env('SMTP_SECURE', 'tls'));
define('SMTP_USERNAME', env('SMTP_USERNAME', ''));
define('SMTP_PASSWORD', env('SMTP_PASSWORD', ''));

// Ustawienia odbiorcy
define('RECIPIENT_EMAIL', env('RECIPIENT_EMAIL', ''));
define('RECIPIENT_NAME', env('RECIPIENT_NAME', ''));

// Ustawienia nadawcy
define('SENDER_NAME', env('SENDER_NAME', 'Formularz Kontaktowy'));
define('REPLY_TO_EMAIL', SMTP_USERNAME);

// Ustawienia bezpieczeństwa
$allowedOrigins = env('ALLOWED_ORIGINS', '');
define('ALLOWED_ORIGINS', $allowedOrigins ? explode(',', $allowedOrigins) : []);

// Konfiguracja reCAPTCHA
define('RECAPTCHA_SECRET_KEY', env('RECAPTCHA_SECRET_KEY', ''));
define('RECAPTCHA_SITE_KEY', env('RECAPTCHA_SITE_KEY', ''));

// Ustawienia debugowania
define('DEBUG_MODE', filter_var(env('DEBUG_MODE', 'false'), FILTER_VALIDATE_BOOLEAN));

// Sprawdzenie czy wszystkie wymagane zmienne są ustawione
$requiredVars = ['SMTP_USERNAME', 'SMTP_PASSWORD', 'RECIPIENT_EMAIL'];
foreach ($requiredVars as $var) {
    if (empty(constant($var))) {
        die('Błąd konfiguracji: Brak wymaganej zmiennej ' . $var);
    }
}
?>
