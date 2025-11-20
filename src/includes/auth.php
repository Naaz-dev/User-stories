<?php

// Laad hulpfuncties voor database en meldingen
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/flash.php';

/**
 * Start de sessie als dat nog niet is gebeurd zodat alle auth-functies veilig met $_SESSION werken.
 */
function ensure_session_started(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Probeer een gebruiker in te loggen met gebruikersnaam en wachtwoord.
 *
 * @param string $username Ingevoerde gebruikersnaam.
 * @param string $password Ingevoerde wachtwoord in platte tekst.
 *
 * @return string|null Foutmelding of null wanneer inloggen gelukt is.
 */
function attempt_login(string $username, string $password): ?string
{
    ensure_session_started();

    $pdo = get_pdo();
    $statement = $pdo->prepare('SELECT id, username, password_hash, first_name, last_name, role, is_active FROM users WHERE username = :username');
    $statement->execute(['username' => $username]);
    $user = $statement->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        return 'Onjuiste gebruikersnaam of wachtwoord.';
    }

    if ((int) $user['is_active'] === 0) {
        return 'Dit account is gedeactiveerd.';
    }

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = trim($user['first_name'] . ' ' . $user['last_name']);
    $_SESSION['user_role'] = $user['role'];

    return null;
}

/**
 * Log de huidige gebruiker uit door alle sessiegegevens op te ruimen.
 */
function logout(): void
{
    ensure_session_started();
    $_SESSION = [];
    session_destroy();
}

/**
 * Controleer of er iemand is ingelogd op basis van de sessie.
 */
function is_logged_in(): bool
{
    ensure_session_started();
    return isset($_SESSION['user_id']);
}

/**
 * Bescherm pagina's door te controleren of de bezoeker is ingelogd.
 * Zo niet, stuur naar de loginpagina met een melding.
 */
function require_login(): void
{// Controleer of de gebruiker is ingelogd
    if (!is_logged_in()) {
        set_flash('Log eerst in om deze pagina te bekijken.', 'error');
        header('Location: index.php');
        exit;
    }
}

/**
 * Geef de basisgegevens terug van de ingelogde gebruiker.
 *
 * @return array|null Associatieve array met id, naam en rol of null wanneer niemand ingelogd is.
 */
function current_user(): ?array
{
    if (!is_logged_in()) {
        return null;
    }

    return [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'],
        'role' => $_SESSION['user_role'] ?? 'user',
    ];
}

/**
 * Controleer of de huidige gebruiker de admin-rol bezit.
 */
function is_admin(): bool
{
    ensure_session_started();
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Zorg dat alleen admins verder mogen gaan, anders terug naar het dashboard met melding.
 */
function require_admin(): void
{
    require_login();

    if (!is_admin()) {
        set_flash('Alleen beheerders kunnen deze actie uitvoeren.', 'error');
        header('Location: dashboard.php');
        exit;
    }
}
