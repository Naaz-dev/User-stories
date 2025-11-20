<?php

// Haal configuratie op voor de databaseverbinding
require_once __DIR__ . '/config.php';

/**
 * Bouw of hergebruik een PDO-verbinding naar de MariaDB-database.
 * De verbinding wordt in een statische variabele bewaard zodat alle functies dezelfde connectie delen.
 */
function get_pdo(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        // Maak de DSN-string op basis van de configuratie
        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);

        try {
            // Opties voor veilige PDO-verbindingen
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            // Maak een nieuwe PDO-verbinding met foutafhandeling en standaard fetch-modus
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $exception) {
            // Toon volledige fout in debugmodus, anders een veilige melding
            if (APP_DEBUG) {
                throw $exception;
            }

            http_response_code(500);
            exit('Databaseverbinding is mislukt. Controleer de instellingen.');
        }
    }

    return $pdo;
}
