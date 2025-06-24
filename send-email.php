<?php
/**
 * Skrypt obsługujący wysyłanie wiadomości e-mail z formularza kontaktowego
 * Wykorzystuje bibliotekę PHPMailer
 */

// Ustawienia początkowe
header('Content-Type: application/json');

// Włącz raportowanie błędów tylko w trybie deweloperskim
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// Podłączenie Composera i PHPMailer
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Funkcja do walidacji danych
function validateInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Funkcja do sprawdzania czy wszystkie wymagane pola zostały wypełnione
function validateRequired($fields, $data) {
    foreach ($fields as $field) {
        if (empty($data[$field])) {
            return false;
        }
    }
    return true;
}

// Obsługa tylko zapytań POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Nieprawidłowa metoda zapytania.'
    ]);
    exit;
}

// Wymagane pola
$requiredFields = ['name', 'email', 'subject', 'message', 'consent'];

// Sprawdź czy wszystkie wymagane pola zostały wysłane
if (!validateRequired($requiredFields, $_POST)) {
    echo json_encode([
        'success' => false,
        'message' => 'Wszystkie wymagane pola muszą być wypełnione.'
    ]);
    exit;
}

// Pobierz i przetwórz dane z formularza
$name = validateInput($_POST['name']);
$email = validateInput($_POST['email']);
$phone = isset($_POST['phone']) ? validateInput($_POST['phone']) : 'Nie podano';
$subject = validateInput($_POST['subject']);
$message = validateInput($_POST['message']);
$consent = isset($_POST['consent']) ? true : false;

// Dodatkowa walidacja e-maila
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Podany adres e-mail jest nieprawidłowy.'
    ]);
    exit;
}

// Sprawdź zgodę na przetwarzanie danych
if (!$consent) {
    echo json_encode([
        'success' => false,
        'message' => 'Zgoda na przetwarzanie danych osobowych jest wymagana.'
    ]);
    exit;
}

try {
    // Konfiguracja PHPMailer
    $mail = new PHPMailer(true);

    // Ustawienia serwera
    // TODO: Zmień poniższe dane na właściwe dla swojego serwera pocztowego
    $mail->isSMTP();
    $mail->Host       = 'h58.seohost.pl';  // Serwer SMTP
    $mail->SMTPAuth   = true;
    $mail->Username   = 'form@testystrony.pl';  // Adres e-mail SMTP
    $mail->Password   = 'Tmobile1';          // Hasło SMTP
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Lub ENCRYPTION_SMTPS
    $mail->Port       = 465;                 // Port 587 dla TLS, 465 dla SSL

    // Ustawienia języka
    $mail->setLanguage('pl');

    // Odbiorcy
    $mail->setFrom('form@testystrony.pl', 'Formularz kontaktowy'); // Nadawca
    $mail->addAddress('kontakt@testystrony.pl', 'Dział obsługi klienta'); // Odbiorca
    $mail->addReplyTo($email, $name); // Odpowiedz do

    // Opcjonalnie - kopia do nadawcy
    // $mail->addCC($email);

    // Treść
    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Subject = 'Wiadomość z formularza kontaktowego: ' . $subject;
    
    // Przygotowanie treści wiadomości HTML
    $mail->Body = "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            h2 { color: #2e5bff; border-bottom: 1px solid #eee; padding-bottom: 10px; }
            .info { background: #f9f9f9; padding: 15px; border-left: 4px solid #2e5bff; margin-bottom: 20px; }
            .field { margin-bottom: 15px; }
            .label { font-weight: bold; }
            .message { background: #f5f5f5; padding: 15px; border-radius: 5px; margin-top: 20px; }
            .footer { margin-top: 30px; font-size: 12px; color: #777; border-top: 1px solid #eee; padding-top: 10px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <h2>Nowa wiadomość z formularza kontaktowego</h2>
            <div class='info'>
                <div class='field'>
                    <span class='label'>Imię i nazwisko:</span> $name
                </div>
                <div class='field'>
                    <span class='label'>E-mail:</span> $email
                </div>
                <div class='field'>
                    <span class='label'>Telefon:</span> $phone
                </div>
                <div class='field'>
                    <span class='label'>Temat:</span> $subject
                </div>
            </div>
            
            <div class='message'>
                <div class='label'>Treść wiadomości:</div>
                <p>" . nl2br($message) . "</p>
            </div>
            
            <div class='footer'>
                Wiadomość wysłana z formularza kontaktowego na stronie example.com<br>
                Data: " . date('Y-m-d H:i:s') . "
            </div>
        </div>
    </body>
    </html>
    ";

    // Alternatywna wersja tekstowa wiadomości
    $mail->AltBody = "
    Nowa wiadomość z formularza kontaktowego

    Imię i nazwisko: $name
    E-mail: $email
    Telefon: $phone
    Temat: $subject

    Treść wiadomości:
    $message

    ---
    Wiadomość wysłana z formularza kontaktowego na stronie example.com
    Data: " . date('Y-m-d H:i:s');

    // Wysłanie wiadomości
    $mail->send();
    
    // Zwróć sukces jako odpowiedź JSON
    echo json_encode([
        'success' => true,
        'message' => 'Wiadomość została wysłana!'
    ]);

} catch (Exception $e) {
    // Obsługa błędów
    echo json_encode([
        'success' => false,
        'message' => 'Wystąpił błąd podczas wysyłania wiadomości: ' . $mail->ErrorInfo
    ]);
    
    // Możesz tutaj dodać logowanie błędów do pliku
    // error_log('Błąd PHPMailer: ' . $mail->ErrorInfo);
}
