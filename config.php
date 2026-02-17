<?php
// Configuration base de données
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '123456';
$DB_NAME = 'authdb';

// Évite les exceptions fatales MySQLi (HTTP 500) et permet une gestion propre des erreurs
mysqli_report(MYSQLI_REPORT_OFF);

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    http_response_code(500);
    die('Erreur connexion MySQL : vérifiez DB_USER / DB_PASS dans config.php. Détail: ' . $conn->connect_error);
}

$conn->set_charset('utf8mb4');