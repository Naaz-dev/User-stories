<?php

// Start een sessie als dat nog niet is gebeurd (nodig voor formulierhelpers)
if (session_status() === PHP_SESSION_NONE) {
    // Houd formuliergegevens bij in dezelfde sessie over meerdere pagina's heen
    session_start();
}

/**
 * Maak een string veilig voor HTML-uitvoer zodat scripts niet uitgevoerd worden.
 *
 * @param string|null $value De ruwe waarde die uit de database of het formulier komt.
 */
function esc(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Haal een eerder ingevulde formulierwaarde op zodat een gebruiker niet alles opnieuw hoeft in te vullen.
 *
 * @param string $key     Naam van het formulier-veld.
 * @param mixed  $default Waarde die gebruikt wordt als er niets opgeslagen is.
 */
function old(string $key, $default = ''): string
{
    return esc($_SESSION['old'][$key] ?? $default);
}

/**
 * Bewaar de volledige formulierinvoer tijdelijk in de sessie.
 * Handig bij een redirect nadat validatie is mislukt.
 */
function save_old_input(array $data): void
{
    $_SESSION['old'] = $data;
}

/**
 * Verwijder alle tijdelijk bewaarde formuliergegevens zodra ze niet meer nodig zijn.
 */
function clear_old_input(): void
{
    unset($_SESSION['old']);
}
