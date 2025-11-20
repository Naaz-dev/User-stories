<?php

/**
 * Centrale configuratie voor de applicatie.
 * Waarden worden zoveel mogelijk uit environment-variabelen gehaald zodat Docker deze kan injecteren.
 */

// Basisinstellingen voor de applicatie
if (!defined('APP_NAME')) {
    define('APP_NAME', 'Simple User Manager');
}

if (!defined('APP_DEBUG')) {
    define('APP_DEBUG', getenv('APP_DEBUG') === 'true');
}

// Databasegegevens, bij voorkeur via environment-variabelen voor Docker
if (!defined('DB_HOST')) {
    define('DB_HOST', getenv('MYSQL_HOST') ?: 'db');
}

if (!defined('DB_NAME')) {
    define('DB_NAME', getenv('MYSQL_DATABASE') ?: 'user_management');
}

if (!defined('DB_USER')) {
    define('DB_USER', getenv('MYSQL_USER') ?: 'user_app');
}

if (!defined('DB_PASS')) {
    define('DB_PASS', getenv('MYSQL_PASSWORD') ?: 'user_secret');
}

if (!defined('DB_CHARSET')) {
    define('DB_CHARSET', 'utf8mb4');
}

if (!defined('APP_URL')) {
    define('APP_URL', getenv('APP_URL') ?: 'http://localhost:8080');
}

// Stel een standaardtijdzone in zodat datums voorspelbaar werken
if (!ini_get('date.timezone')) {
    date_default_timezone_set('Europe/Amsterdam');
}
