<?php

namespace Src\Validators;

class AuthValidator {
    public static function validateEmail($email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function validatePassword($password): bool {
        return strlen($password) >= 6 && 
               preg_match('/[A-Za-z]/', $password) && 
               preg_match('/[0-9]/', $password);
    }

    public static function validateLoginData($data): array {
        $errors = [];
        
        if (!isset($data['email']) || !isset($data['password'])) {
            $errors[] = 'Email and password are required';
        } elseif (!self::validateEmail($data['email'])) {
            $errors[] = 'Invalid email format';
        }

        return $errors;
    }

    public static function validateRegisterData($data): array {
        $errors = [];
        
        if (!isset($data['email']) || !isset($data['password'])) {
            $errors[] = 'Email and password are required';
        } else {
            if (!self::validateEmail($data['email'])) {
                $errors[] = 'Invalid email format';
            }
            if (!self::validatePassword($data['password'])) {
                $errors[] = 'Password must be at least 6 characters long and contain at least one letter and one number';
            }
        }

        return $errors;
    }
} 