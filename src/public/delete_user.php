<?php
// Laad alle helpers en controleer admin-rechten
require_once __DIR__ . '/../includes/init.php';

require_admin();

// Alleen POST-verzoeken zijn toegestaan voor verwijderen
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

// Controleer of het aangeleverde ID geldig is
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    set_flash('Ongeldige aanvraag.', 'error');
    header('Location: dashboard.php');
    exit;
}

$current = current_user();

// Voorkom dat iemand zijn eigen account verwijdert
if ($current && (int)$current['id'] === $id) {
    set_flash('Je kunt je eigen account niet verwijderen.', 'error');
    header('Location: dashboard.php');
    exit;
}

// Voer de daadwerkelijke verwijderactie uit met een prepared statement
$pdo = get_pdo();
$statement = $pdo->prepare('DELETE FROM users WHERE id = :id');
$statement->execute(['id' => $id]);

// Laat weten dat de actie gelukt is en keer terug naar het overzicht
set_flash('Gebruiker verwijderd.');
header('Location: dashboard.php');
exit;
