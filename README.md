# Formularz Kontaktowy

Projekt "Formularz Kontaktowy" to aplikacja webowa, która umożliwia użytkownikom wysyłanie wiadomości do firmy usługowej. Aplikacja jest zbudowana w oparciu o PHP i wykorzystuje bibliotekę PHPMailer do obsługi wysyłania e-maili. Formularz jest zgodny z najnowszymi standardami kodowania, trendami webdesign oraz zasadami dostępności.

## Zawartość projektu

- **src/**
  - `FormHandler.php`: Obsługuje logikę przetwarzania formularza kontaktowego, w tym wysyłanie wiadomości e-mail i obsługę błędów.
  - `Validator.php`: Zawiera klasę Validator do walidacji danych formularza, w tym sprawdzanie poprawności adresu e-mail i wymaganych pól.
  - `config.php`: Plik konfiguracyjny dla PHPMailer oraz inne istotne ustawienia aplikacji.

- **public/**
  - `index.php`: Główny plik wejściowy aplikacji, który ładuje formularz kontaktowy i obsługuje żądania HTTP.
  - `js/`
    - `form-validation.js`: Skrypt JavaScript do walidacji danych formularza po stronie klienta.
    - `form-submit.js`: Skrypt JavaScript do asynchronicznego wysyłania formularza za pomocą fetch API.
  - `css/`
    - `style.css`: Plik CSS z stylami dla formularza kontaktowego.
  - `assets/`
    - `favicon.svg`: Ikona favicon dla aplikacji.

- **templates/**
  - `form.php`: Szablon HTML dla formularza kontaktowego, z odpowiednimi atrybutami dostępności.
  - `success.php`: Szablon HTML wyświetlany po pomyślnym wysłaniu formularza.
  - `error.php`: Szablon HTML wyświetlany w przypadku błędów podczas wysyłania formularza.

- **vendor/**: Katalog zainstalowanych zależności zarządzanych przez Composer.

- **composer.json**: Plik konfiguracyjny dla Composer, definiujący zależności projektu.

- **composer.lock**: Plik blokady z dokładnymi wersjami zainstalowanych zależności.

- **.htaccess**: Plik konfiguracyjny dla serwera Apache.

- **privacy-policy.php**: Strona z polityką prywatności, zgodna z przepisami o ochronie danych osobowych.

## Instalacja

1. Skopiuj repozytorium na swój lokalny serwer.
2. Uruchom `composer install`, aby zainstalować wszystkie zależności.
3. Skonfiguruj plik `src/config.php` z odpowiednimi danymi do wysyłania e-maili.
4. Otwórz `public/index.php` w przeglądarce, aby uzyskać dostęp do formularza kontaktowego.

## Użytkowanie

Użytkownicy mogą wypełnić formularz kontaktowy, a po jego wysłaniu, wiadomość zostanie przesłana na wskazany adres e-mail. W przypadku błędów, użytkownicy zostaną poinformowani o konieczności poprawienia danych.

## Zasady dostępności

Formularz został zaprojektowany z myślą o dostępności, aby zapewnić, że osoby z niepełnosprawnościami mogą z niego korzystać. Zawiera odpowiednie atrybuty ARIA oraz jest zgodny z WCAG.

## Licencja

Projekt jest dostępny na licencji MIT.