# Bezpieczny Formularz Kontaktowy z PHPMailer

## 📋 Opis

Kompletny, bezpieczny formularz kontaktowy napisany w czystym HTML, CSS i JavaScript z backend'em PHP wykorzystującym PHPMailer do wysyłania wiadomości email.

## 🔧 Wymagania

- PHP 7.4 lub nowszy
- Composer
- Serwer obsługujący pliki .htaccess (Apache)
- Konto email SMTP (np. na seohost.pl)

## 🚀 Instalacja

### 1. Przygotowanie plików

Przenieś wszystkie pliki na serwer do katalogu publicznego (public_html).

### 2. Instalacja PHPMailer

Uruchom w terminalu w katalogu projektu:

```bash
composer install
```

### 3. Konfiguracja email

Edytuj plik `config.php` i uzupełnij dane:

```php
// Konfiguracja SMTP dla seohost.pl
define('SMTP_HOST', 'mail.seohost.pl'); // Sprawdź w panelu hostingu
define('SMTP_PORT', 587); // lub 465 dla SSL
define('SMTP_SECURE', 'tls'); // lub 'ssl'
define('SMTP_USERNAME', 'twoj-email@twoja-domena.pl');
define('SMTP_PASSWORD', 'twoje-haslo-email');

// Email odbiorcy wiadomości
define('RECIPIENT_EMAIL', 'kontakt@twoja-domena.pl');
define('RECIPIENT_NAME', 'Twoja Firma');

// Dozwolone domeny (CORS)
define('ALLOWED_ORIGINS', [
    'https://twoja-domena.pl',
    'https://www.twoja-domena.pl'
]);
```

### 4. Bezpieczeństwo pliku konfiguracji

**WAŻNE!** Plik `config.php` zawiera wrażliwe dane. Zabezpiecz go na jeden z poniższych sposobów:

#### Opcja A: Przeniesienie poza katalog publiczny (ZALECANE)

1. Przenieś `config.php` do katalogu powyżej `public_html`
2. Zaktualizuj ścieżkę w `send_email.php`:

```php
require_once '../config.php'; // Zamiast 'config.php'
```

#### Opcja B: Dodatkowe zabezpieczenie .htaccess

Plik `.htaccess` już zawiera zabezpieczenie, ale możesz dodać dodatkowe:

```apache
<Files "config.php">
    Order allow,deny
    Deny from all
</Files>
```

### 5. Konfiguracja uprawnień

Ustaw odpowiednie uprawnienia:

```bash
chmod 755 .
chmod 644 *.php *.html *.css *.js
chmod 600 config.php
chmod 755 logs/
```

## 🔐 Funkcje bezpieczeństwa

### Zabezpieczenia zaimplementowane:

- ✅ Walidacja po stronie klienta i serwera
- ✅ Ochrona przed atakami XSS
- ✅ Ochrona przed wstrzyknięciem SQL
- ✅ Kontrola CORS (Cross-Origin Resource Sharing)
- ✅ Sanityzacja danych wejściowych
- ✅ Zabezpieczenie plików konfiguracyjnych
- ✅ Logowanie aktywności
- ✅ Nagłówki bezpieczeństwa
- ✅ Rate limiting (można rozszerzyć)

### Dodatkowe zabezpieczenia (opcjonalne):

#### Google reCAPTCHA

1. Zarejestruj się na https://www.google.com/recaptcha/
2. Dodaj klucze do `config.php`
3. Dodaj kod reCAPTCHA do formularza

#### Rate Limiting

Można dodać ograniczenie liczby wiadomości na IP:

```php
// W send_email.php - przykład prostego rate limiting
$ip = $_SERVER['REMOTE_ADDR'];
$file = "logs/rate_limit_$ip.txt";
$requests = file_exists($file) ? (int)file_get_contents($file) : 0;

if ($requests > 5) { // Maksymalnie 5 wiadomości na godzinę
    die(json_encode(['success' => false, 'message' => 'Zbyt wiele żądań']));
}
```

## 📧 Konfiguracja SMTP dla seohost.pl

### Typowe ustawienia dla seohost.pl:

```
SMTP Host: mail.seohost.pl lub smtp.seohost.pl
SMTP Port: 587 (TLS) lub 465 (SSL)
SMTP Security: TLS lub SSL
```

### Sprawdzenie ustawień:

1. Zaloguj się do panelu hostingu seohost.pl
2. Przejdź do sekcji "Poczta"
3. Sprawdź ustawienia SMTP w konfiguracji konta email

## 🎨 Personalizacja

### Zmiana wyglądu:

Edytuj plik `styles.css` aby dostosować:

- Kolory (zmienne CSS na początku pliku)
- Czcionki
- Layout i responsywność

### Dodanie nowych pól:

1. Dodaj pole w `index.html`
2. Dodaj walidację w `script.js`
3. Dodaj obsługę w `send_email.php`

### Dostosowanie wiadomości email:

Edytuj funkcje `prepareHtmlBody()` i `prepareTextBody()` w `send_email.php`

## 🐛 Debugowanie

### Włączenie trybu debug:

W `config.php` ustaw:

```php
define('DEBUG_MODE', true);
```

### Sprawdzanie logów:

Logi są zapisywane w `logs/contact_form.log`

### Typowe problemy:

#### 1. Błąd "SMTP Error: Could not authenticate"

- Sprawdź dane logowania w `config.php`
- Upewnij się, że konto email istnieje
- Sprawdź czy hasło jest poprawne

#### 2. Błąd "Connection failed"

- Sprawdź ustawienia SMTP_HOST i SMTP_PORT
- Upewnij się, że hosting obsługuje połączenia SMTP
- Sprawdź czy firewall nie blokuje połączeń

#### 3. Błąd CORS

- Dodaj swoją domenę do ALLOWED_ORIGINS w `config.php`
- Upewnij się że testujesz na właściwej domenie

## 📱 Funkcje formularza

### Walidacja po stronie klienta:

- Sprawdzanie wymaganych pól w czasie rzeczywistym
- Walidacja formatu email
- Sprawdzanie długości pól
- Walidacja numeru telefonu

### Doświadczenie użytkownika:

- Animacje i przejścia CSS
- Komunikaty sukcesu/błędu
- Wskaźnik ładowania podczas wysyłania
- Responsywny design
- Dostępność (a11y)

## 📄 Struktura plików

```
contact_form3/
├── index.html          # Główna strona z formularzem
├── styles.css          # Style CSS
├── script.js           # JavaScript (walidacja, AJAX)
├── send_email.php      # Backend - obsługa wysyłania
├── config.php          # Konfiguracja (UKRYJ!)
├── composer.json       # Zależności PHP
├── .htaccess           # Zabezpieczenia Apache
├── README.md           # Ta dokumentacja
├── vendor/             # Biblioteki PHP (tworzone przez Composer)
└── logs/               # Logi aplikacji
    └── contact_form.log
```

## 🔄 Aktualizacje

Aby zaktualizować PHPMailer:

```bash
composer update phpmailer/phpmailer
```

## 📞 Wsparcie

W przypadku problemów:

1. Sprawdź logi w `logs/contact_form.log`
2. Włącz tryb debug
3. Sprawdź dokumentację seohost.pl
4. Skontaktuj się z pomocą techniczną hostingu

## 📜 Licencja

Ten projekt jest udostępniony na licencji MIT. Możesz go swobodnie używać i modyfikować.

---

**⚠️ WAŻNE UWAGI BEZPIECZEŃSTWA:**

- Nigdy nie commituj pliku `config.php` do repozytorium
- Regularnie aktualizuj PHPMailer
- Monitoruj logi pod kątem podejrzanej aktywności
- Używaj silnych haseł do kont email
- Rozważ dodanie reCAPTCHA na stronach produkcyjnych
