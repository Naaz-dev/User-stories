<?php

/**
 * Controleer de invoer van het loginformulier en geef foutmeldingen terug.
 *
 * @param array $data Ruwe formuliergegevens uit $_POST.
 *
 * @return array Associatieve array met veld => foutmelding.
 */
function validate_login(array $data): array
{
    $errors = [];

    if (empty(trim($data['username'] ?? ''))) {
        $errors['username'] = 'Vul je gebruikersnaam in.';
    }

    if (empty(trim($data['password'] ?? ''))) {
        $errors['password'] = 'Vul je wachtwoord in.';
    }

    return $errors;
}

/**
 * Controleer de gegevens voor het gebruikersformulier (toevoegen én bewerken).
 *
 * @param array $data    Alle ingevoerde velden uit het formulier.
 * @param bool  $is_edit Geeft aan of het om een update gaat (wachtwoord is dan optioneel).
 */
function validate_user(array $data, bool $is_edit = false): array
{
    $errors = [];

    $firstName = trim($data['first_name'] ?? '');
    $lastName = trim($data['last_name'] ?? '');
    $email = trim($data['email'] ?? '');
    $username = trim($data['username'] ?? '');
    $password = $data['password'] ?? '';
    $role = $data['role'] ?? '';
    $birthDate = $data['birth_date'] ?? '';

    if ($firstName === '' || strlen($firstName) < 2) {
        $errors['first_name'] = 'Voornaam moet minimaal 2 letters bevatten.';
    }

    if ($lastName === '' || strlen($lastName) < 2) {
        $errors['last_name'] = 'Achternaam moet minimaal 2 letters bevatten.';
    }

    if ($birthDate === '') {
        $errors['birth_date'] = 'Selecteer een geboortedatum.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Vul een geldig e-mailadres in.';
    }

    if ($username === '' || strlen($username) < 5) {
        $errors['username'] = 'Gebruikersnaam moet minimaal 5 tekens hebben.';
    }

    if (!$is_edit || $password !== '') {
        if (strlen($password) < 8) {
            $errors['password'] = 'Wachtwoord moet minimaal 8 tekens bevatten.';
        }
    }

    // Alleen de rollen die in de applicatie ondersteund worden, zijn toegestaan
    $allowedRoles = ['admin', 'user'];
    if (!in_array($role, $allowedRoles, true)) {
        $errors['role'] = 'Kies een geldige rol.';
    }

    return $errors;
}
