<?php

// Start de sessie als deze nog niet actief is
if (session_status() === PHP_SESSION_NONE) {
    // Start de PHP-sessie zodat we via $_SESSION kunnen werken
    session_start();
}

/**
 * Sla een tijdelijk bericht op in de sessie zodat het na een redirect kan worden getoond.
 *
 * @param string $message De tekst die aan de gebruiker wordt getoond.
 * @param string $type    Het soort melding (bijvoorbeeld success, error, info).
 */
function set_flash(string $message, string $type = 'success'): void
{
    $_SESSION['flash'] = [
        'message' => $message,
        'type' => $type,
    ];
}

/**
 * Haal een eerder opgeslagen flashbericht op en verwijder het meteen weer.
 * Zo verschijnt het bericht slechts één keer voor de gebruiker.
 */
function get_flash(): ?array
{
    if (empty($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}
