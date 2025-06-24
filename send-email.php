<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Załóżmy, że PHPMailer jest zainstalowany przez Composer

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Nieprawidłowa metoda żądania']);
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$message) {
    echo json_encode(['success' => false, 'error' => 'Niepoprawne dane formularza']);
    exit;
}

$mail = new PHPMailer(true);

try {
    // Konfiguracja SMTP (dostosuj do swojego serwera)
    $mail->isSMTP();
    $mail->Host = 'h58.seohost.pl';
    $mail->SMTPAuth = true;
    $mail->Username = 'form@testystrony.pl';
    $mail->Password = 'Tmobile1';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;

    $mail->setFrom('form@testystrony.pl', 'Formularz Kontaktowy');
    $mail->addAddress('kontakt@testystrony.pl', 'Ewa Jakubas');
    $mail->addReplyTo($email, $name);

    $mail->isHTML(true);
    $mail->Subject = 'Nowa wiadomość z formularza kontaktowego';
    $mail->Body = "<p><strong>Od:</strong> {$name} ({$email})</p><p><strong>Wiadomość:</strong><br>" . nl2br(htmlspecialchars($message)) . "</p>";
    $mail->AltBody = "Od: {$name} ({$email})\n\nWiadomość:\n{$message}";

    $mail->send();

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Błąd wysyłki: ' . $mail->ErrorInfo]);
}
?>
