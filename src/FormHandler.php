<?php

require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class FormHandler {
    private $validator;

    public function __construct() {
        $this->validator = new Validator();
    }

    public function handleFormSubmission($data) {
        $errors = $this->validator->validate($data);

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.example.com'; // Set the SMTP server to send through
            $mail->SMTPAuth = true;
            $mail->Username = 'your_email@example.com'; // SMTP username
            $mail->Password = 'your_password'; // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('from@example.com', 'Mailer');
            $mail->addAddress('recipient@example.com', 'Recipient Name');

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'New Contact Form Submission';
            $mail->Body    = $this->createEmailBody($data);

            $mail->send();
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'errors' => ['Mail could not be sent. Mailer Error: ' . $mail->ErrorInfo]];
        }
    }

    private function createEmailBody($data) {
        return "<h1>Contact Form Submission</h1>
                <p><strong>Name:</strong> {$data['name']}</p>
                <p><strong>Email:</strong> {$data['email']}</p>
                <p><strong>Message:</strong><br>{$data['message']}</p>";
    }
}