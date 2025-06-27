<?php
/**
 * Skrypt wysyłania emaili z wykorzystaniem PHPMailer
 * Bezpieczna implementacja z walidacją i ochroną przed CSRF
 */

// Zabezpieczenie przed bezpośrednim dostępem
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['success' => false, 'message' => 'Metoda niedozwolona']));
}

// Ustawienia nagłówków
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Sprawdzenie origin (CORS)
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
require_once '../config.php';

// Zezwalaj na lokalne testy (tryb debugowania)
$isLocalTest = DEBUG_MODE && (empty($origin) || 
                              strpos($origin, 'localhost') !== false || 
                              strpos($origin, '127.0.0.1') !== false ||
                              strpos($origin, 'file://') !== false);

if (!$isLocalTest && !in_array($origin, ALLOWED_ORIGINS)) {
    http_response_code(403);
    die(json_encode(['success' => false, 'message' => 'Nieautoryzowane źródło żądania']));
}

if (!empty($origin)) {
    header('Access-Control-Allow-Origin: ' . $origin);
}
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Załadowanie PHPMailer
require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Klasa do obsługi formularza kontaktowego
 */
class ContactFormHandler {
    private $data;
    private $errors = [];
    
    public function __construct() {
        $this->parseInput();
    }
    
    /**
     * Parsowanie danych wejściowych
     */
    private function parseInput() {
        $input = file_get_contents('php://input');
        $this->data = json_decode($input, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Nieprawidłowe dane JSON');
        }
    }
    
    /**
     * Walidacja wszystkich pól
     */
    public function validate() {
        $this->validateName();
        $this->validateEmail();
        $this->validatePhone();
        $this->validateSubject();
        $this->validateMessage();
        $this->validatePrivacy();
        
        return empty($this->errors);
    }
    
    /**
     * Walidacja imienia
     */
    private function validateName() {
        $name = trim($this->data['name'] ?? '');
        
        if (empty($name)) {
            $this->errors['name'] = 'Imię i nazwisko jest wymagane';
            return;
        }
        
        if (strlen($name) < 2) {
            $this->errors['name'] = 'Imię i nazwisko musi mieć co najmniej 2 znaki';
            return;
        }
        
        if (strlen($name) > 100) {
            $this->errors['name'] = 'Imię i nazwisko nie może być dłuższe niż 100 znaków';
            return;
        }
        
        // Sprawdzenie na potencjalnie niebezpieczne znaki
        if (preg_match('/[<>"\']/', $name)) {
            $this->errors['name'] = 'Imię zawiera niedozwolone znaki';
            return;
        }
        
        $this->data['name'] = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Walidacja emaila
     */
    private function validateEmail() {
        $email = trim($this->data['email'] ?? '');
        
        if (empty($email)) {
            $this->errors['email'] = 'Email jest wymagany';
            return;
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = 'Podaj prawidłowy adres email';
            return;
        }
        
        if (strlen($email) > 320) {
            $this->errors['email'] = 'Adres email jest za długi';
            return;
        }
        
        $this->data['email'] = strtolower($email);
    }
    
    /**
     * Walidacja telefonu
     */
    private function validatePhone() {
        $phone = trim($this->data['phone'] ?? '');
        
        if (!empty($phone)) {
            // Usuń wszystkie znaki oprócz cyfr, spacji, myślników, nawiasów i znaku +
            $cleanPhone = preg_replace('/[^\d\s\-\(\)\+]/', '', $phone);
            
            if (strlen($cleanPhone) < 9 || strlen($cleanPhone) > 15) {
                $this->errors['phone'] = 'Numer telefonu powinien mieć od 9 do 15 cyfr';
                return;
            }
            
            $this->data['phone'] = htmlspecialchars($cleanPhone, ENT_QUOTES, 'UTF-8');
        }
    }
    
    /**
     * Walidacja tematu
     */
    private function validateSubject() {
        $subject = trim($this->data['subject'] ?? '');
        
        if (empty($subject)) {
            $this->errors['subject'] = 'Temat jest wymagany';
            return;
        }
        
        if (strlen($subject) < 3) {
            $this->errors['subject'] = 'Temat musi mieć co najmniej 3 znaki';
            return;
        }
        
        if (strlen($subject) > 200) {
            $this->errors['subject'] = 'Temat nie może być dłuższy niż 200 znaków';
            return;
        }
        
        $this->data['subject'] = htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Walidacja wiadomości
     */
    private function validateMessage() {
        $message = trim($this->data['message'] ?? '');
        
        if (empty($message)) {
            $this->errors['message'] = 'Wiadomość jest wymagana';
            return;
        }
        
        if (strlen($message) < 10) {
            $this->errors['message'] = 'Wiadomość musi mieć co najmniej 10 znaków';
            return;
        }
        
        if (strlen($message) > 2000) {
            $this->errors['message'] = 'Wiadomość nie może być dłuższa niż 2000 znaków';
            return;
        }
        
        $this->data['message'] = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Walidacja zgody na przetwarzanie danych
     */
    private function validatePrivacy() {
        if (!isset($this->data['privacy']) || $this->data['privacy'] !== true) {
            $this->errors['privacy'] = 'Zgoda na przetwarzanie danych jest wymagana';
        }
    }
    
    /**
     * Wysłanie emaila
     */
    public function sendEmail() {
        $mail = new PHPMailer(true);
        
        try {
            // Konfiguracja serwera SMTP
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPSecure = SMTP_SECURE;
            $mail->Port = SMTP_PORT;
            $mail->CharSet = 'UTF-8';
            
            // Debugowanie (tylko w trybie dev)
            if (DEBUG_MODE) {
                $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            }
            
            // Ustawienia wiadomości
            $mail->setFrom(SMTP_USERNAME, SENDER_NAME);
            $mail->addAddress(RECIPIENT_EMAIL, RECIPIENT_NAME);
            $mail->addReplyTo($this->data['email'], $this->data['name']);
            
            // Temat i treść
            $mail->Subject = 'Formularz kontaktowy: ' . $this->data['subject'];
            $mail->isHTML(true);
            
            // Przygotowanie treści HTML
            $htmlBody = $this->prepareHtmlBody();
            $textBody = $this->prepareTextBody();
            
            $mail->Body = $htmlBody;
            $mail->AltBody = $textBody;
            
            // Wysłanie
            $mail->send();
            
            // Logowanie sukcesu (opcjonalnie)
            $this->logActivity('Email wysłany pomyślnie', 'success');
            
            return true;
            
        } catch (Exception $e) {
            // Logowanie błędu
            $this->logActivity('Błąd wysyłania: ' . $e->getMessage(), 'error');
            
            if (DEBUG_MODE) {
                throw new Exception('Błąd wysyłania: ' . $e->getMessage());
            } else {
                throw new Exception('Wystąpił błąd podczas wysyłania wiadomości');
            }
        }
    }
    
    /**
     * Przygotowanie treści HTML
     */
    private function prepareHtmlBody() {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #667eea; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background-color: #f9f9f9; }
                .field { margin-bottom: 15px; }
                .label { font-weight: bold; color: #4a5568; }
                .value { margin-top: 5px; padding: 10px; background-color: white; border-left: 3px solid #667eea; }
                .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>Nowa wiadomość z formularza kontaktowego</h2>
                </div>
                <div class="content">
                    <div class="field">
                        <div class="label">Imię i nazwisko:</div>
                        <div class="value">' . $this->data['name'] . '</div>
                    </div>
                    <div class="field">
                        <div class="label">Email:</div>
                        <div class="value">' . $this->data['email'] . '</div>
                    </div>';
        
        if (!empty($this->data['phone'])) {
            $html .= '
                    <div class="field">
                        <div class="label">Telefon:</div>
                        <div class="value">' . $this->data['phone'] . '</div>
                    </div>';
        }
        
        $html .= '
                    <div class="field">
                        <div class="label">Temat:</div>
                        <div class="value">' . $this->data['subject'] . '</div>
                    </div>
                    <div class="field">
                        <div class="label">Wiadomość:</div>
                        <div class="value">' . nl2br($this->data['message']) . '</div>
                    </div>
                </div>
                <div class="footer">
                    <p>Wiadomość wysłana: ' . date('Y-m-d H:i:s') . '</p>
                    <p>IP nadawcy: ' . $_SERVER['REMOTE_ADDR'] . '</p>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    /**
     * Przygotowanie treści tekstowej
     */
    private function prepareTextBody() {
        $text = "NOWA WIADOMOŚĆ Z FORMULARZA KONTAKTOWEGO\n\n";
        $text .= "Imię i nazwisko: " . $this->data['name'] . "\n";
        $text .= "Email: " . $this->data['email'] . "\n";
        
        if (!empty($this->data['phone'])) {
            $text .= "Telefon: " . $this->data['phone'] . "\n";
        }
        
        $text .= "Temat: " . $this->data['subject'] . "\n\n";
        $text .= "Wiadomość:\n" . $this->data['message'] . "\n\n";
        $text .= "---\n";
        $text .= "Wysłano: " . date('Y-m-d H:i:s') . "\n";
        $text .= "IP: " . $_SERVER['REMOTE_ADDR'];
        
        return $text;
    }
    
    /**
     * Logowanie aktywności
     */
    private function logActivity($message, $type = 'info') {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'type' => $type,
            'message' => $message,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        ];
        
        $logFile = 'logs/contact_form.log';
        $logLine = json_encode($logEntry) . "\n";
        
        // Utwórz katalog logs jeśli nie istnieje
        if (!file_exists('logs')) {
            mkdir('logs', 0755, true);
        }
        
        file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Pobranie błędów walidacji
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * Pobranie przetworzonych danych
     */
    public function getData() {
        return $this->data;
    }
}

// Główna logika przetwarzania
try {
    $handler = new ContactFormHandler();
    
    // Walidacja
    if (!$handler->validate()) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Dane formularza są nieprawidłowe',
            'errors' => $handler->getErrors()
        ]);
        exit;
    }
    
    // Wysłanie emaila
    $handler->sendEmail();
    
    // Odpowiedź sukcesu
    echo json_encode([
        'success' => true,
        'message' => 'Wiadomość została wysłana pomyślnie'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
