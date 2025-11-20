<?php

// Bootstrap-bestand dat alle basisfunctionaliteit voor elke pagina inlaadt

// Start een sessie zodat alle helpers dezelfde sessie delen
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Laad alle kernbestanden voor configuratie, database en helperfuncties
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/flash.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/validation.php';
