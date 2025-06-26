<?php
require_once '../vendor/autoload.php';
require_once '../src/config.php';
require_once '../src/FormHandler.php';
require_once '../src/Validator.php';

$formHandler = new FormHandler();
$validator = new Validator();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = $validator->validate($_POST);
    
    if (empty($errors)) {
        $result = $formHandler->sendEmail($_POST);
        if ($result) {
            header('Location: ../templates/success.php');
            exit;
        } else {
            header('Location: ../templates/error.php');
            exit;
        }
    }
}

include '../templates/form.php';
?>