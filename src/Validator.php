<?php

class Validator {
    private $errors = [];

    public function validateEmail($email) {
        if (empty($email)) {
            $this->errors['email'] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = 'Invalid email format.';
        }
    }

    public function validateRequired($field, $fieldName) {
        if (empty($field)) {
            $this->errors[$fieldName] = "$fieldName is required.";
        }
    }

    public function getErrors() {
        return $this->errors;
    }

    public function hasErrors() {
        return !empty($this->errors);
    }
}