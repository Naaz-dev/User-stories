<?php
// Dit bestand handelt het uitlogproces af
require_once __DIR__ . '/../includes/init.php';

// Verwijder alle sessiegegevens van de huidige gebruiker
logout();

// Laat een succesmelding zien op de loginpagina
set_flash('Je bent uitgelogd.', 'success');

// Stuur de gebruiker terug naar de loginpagina
header('Location: index.php');
exit; // Zorg dat er geen extra output meer volgt
