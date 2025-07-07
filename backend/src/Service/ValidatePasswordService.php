<?php

namespace App\Service;

class ValidatePasswordService
{
    public function validate(string $password): ?array
    {
        if (strlen($password) < 12) {
            return ['code' => 'ERR_PASSWORD_WEAK', 'message' => 'Le mot de passe doit contenir au moins 12 caractères.'];
        }

        if (!preg_match('/[A-Z]/', $password)) {
            return ['code' => 'ERR_PASSWORD_WEAK', 'message' => 'Le mot de passe doit contenir au moins une lettre majuscule.'];
        }

        if (!preg_match('/[a-z]/', $password)) {
            return ['code' => 'ERR_PASSWORD_WEAK', 'message' => 'Le mot de passe doit contenir au moins une lettre minuscule.'];
        }

        if (!preg_match('/[0-9]/', $password)) {
            return ['code' => 'ERR_PASSWORD_WEAK', 'message' => 'Le mot de passe doit contenir au moins un chiffre.'];
        }

        if (!preg_match('/[^a-zA-Z0-9\s]/', $password)) {
            return ['code' => 'ERR_PASSWORD_WEAK', 'message' => 'Le mot de passe doit contenir au moins un caractère spécial.'];
        }

        return null;
    }
}
