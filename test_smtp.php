<?php
/**
 * Test konfiguracji SMTP i połączenia z serwerem
 * WAŻNE: Usuń ten plik po testach!
 */

require_once '../config.php';
require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Włącz wyświetlanie błędów
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Test konfiguracji SMTP</h1>\n";

// Test 1: Sprawdź stałe konfiguracyjne
echo "<h2>1. Sprawdzenie konfiguracji</h2>\n";
echo "SMTP_HOST: " . SMTP_HOST . "<br>\n";
echo "SMTP_PORT: " . SMTP_PORT . "<br>\n";
echo "SMTP_SECURE: " . SMTP_SECURE . "<br>\n";
echo "SMTP_USERNAME: " . SMTP_USERNAME . "<br>\n";
echo "SMTP_PASSWORD: " . (SMTP_PASSWORD ? str_repeat('*', strlen(SMTP_PASSWORD)) : 'BRAK') . "<br>\n";
echo "RECIPIENT_EMAIL: " . RECIPIENT_EMAIL . "<br>\n";
echo "DEBUG_MODE: " . (DEBUG_MODE ? 'TAK' : 'NIE') . "<br>\n";

// Test 2: Sprawdź PHPMailer
echo "<h2>2. Test połączenia SMTP</h2>\n";

try {
    $mail = new PHPMailer(true);
    
    // Konfiguracja SMTP
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USERNAME;
    $mail->Password = SMTP_PASSWORD;
    $mail->SMTPSecure = SMTP_SECURE;
    $mail->Port = SMTP_PORT;
    $mail->CharSet = 'UTF-8';
    
    // Włącz debug
    $mail->SMTPDebug = SMTP::DEBUG_CONNECTION;
    
    // Test połączenia
    if ($mail->smtpConnect()) {
        echo "<p style='color: green;'><strong>✅ Połączenie SMTP udane!</strong></p>\n";
        $mail->smtpClose();
    } else {
        echo "<p style='color: red;'><strong>❌ Błąd połączenia SMTP</strong></p>\n";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>❌ Błąd: " . $e->getMessage() . "</strong></p>\n";
}

// Test 3: Test wysłania przykładowego emaila
if (isset($_GET['send_test']) && $_GET['send_test'] == '1') {
    echo "<h2>3. Test wysłania emaila</h2>\n";
    
    try {
        $mail = new PHPMailer(true);
        
        // Konfiguracja SMTP
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port = SMTP_PORT;
        $mail->CharSet = 'UTF-8';
        
        // Ustawienia wiadomości
        $mail->setFrom(SMTP_USERNAME, 'Test konfiguracji');
        $mail->addAddress(RECIPIENT_EMAIL, RECIPIENT_NAME);
        
        // Treść
        $mail->isHTML(true);
        $mail->Subject = 'Test konfiguracji SMTP - ' . date('Y-m-d H:i:s');
        $mail->Body = '
        <h2>Test konfiguracji</h2>
        <p>To jest testowa wiadomość wysłana w celu sprawdzenia konfiguracji SMTP.</p>
        <p><strong>Data wysłania:</strong> ' . date('Y-m-d H:i:s') . '</p>
        <p><strong>Serwer:</strong> ' . SMTP_HOST . ':' . SMTP_PORT . '</p>
        <p><strong>Zabezpieczenie:</strong> ' . SMTP_SECURE . '</p>
        ';
        
        // Wyślij
        $mail->send();
        echo "<p style='color: green;'><strong>✅ Email testowy wysłany pomyślnie!</strong></p>\n";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'><strong>❌ Błąd wysyłania: " . $e->getMessage() . "</strong></p>\n";
    }
} else {
    echo "<h2>3. Test wysłania emaila</h2>\n";
    echo "<p><a href='?send_test=1' style='background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Wyślij testowy email</a></p>\n";
    echo "<p><small>Kliknij przycisk powyżej, aby wysłać testowy email</small></p>\n";
}

// Test 4: Informacje o serwerze
echo "<h2>4. Informacje o serwerze</h2>\n";
echo "Wersja PHP: " . phpversion() . "<br>\n";
echo "Wersja PHPMailer: " . PHPMailer::VERSION . "<br>\n";
echo "OpenSSL: " . (extension_loaded('openssl') ? 'Dostępne' : 'Niedostępne') . "<br>\n";
echo "Socket: " . (function_exists('fsockopen') ? 'Dostępne' : 'Niedostępne') . "<br>\n";

echo "<hr>\n";
echo "<p style='color: red;'><strong>WAŻNE: Usuń ten plik po zakończeniu testów!</strong></p>\n";
?>
