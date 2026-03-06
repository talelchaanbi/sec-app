<?php
// ============================================================
//  Configuration – NE PAS exposer ce fichier publiquement.
//  Idéalement, placez-le EN DEHORS de la racine web (webroot).
// ============================================================

// --- Paramètres DB via variables d'environnement (OWASP SCP §2) ---
// Évite de stocker les identifiants en clair dans le code source.
// En production : définir DB_HOST, DB_USER, DB_PASS, DB_NAME dans
// le fichier .env ou la configuration du serveur.
$DB_HOST = getenv('DB_HOST') ?: 'localhost';
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: '123456';
$DB_NAME = getenv('DB_NAME') ?: 'authdb';

// --- Sécurisation des cookies de session (OWASP SCP §3) ---
// HttpOnly  : cookie inaccessible en JavaScript → protège contre le vol via XSS.
// SameSite  : bloque l'envoi inter-sites → renforce la protection CSRF.
// Strict    : rejette les IDs de session non initialisés par le serveur.
// Secure    : mettre à 1 en PRODUCTION (transport HTTPS uniquement).
ini_set('session.cookie_httponly',  1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode',  1);
ini_set('session.cookie_secure',    0); // ← passer à 1 en production (HTTPS)

// Désactive les rapports détaillés MySQLi
mysqli_report(MYSQLI_REPORT_OFF);

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    // OWASP SCP §7 – Error Handling :
    // Journaliser l'erreur côté serveur UNIQUEMENT ; ne jamais l'exposer à l'utilisateur.
    error_log('Erreur connexion MySQL : ' . $conn->connect_error);
    http_response_code(500);
    die('Service temporairement indisponible. Veuillez réessayer plus tard.');
}

$conn->set_charset('utf8mb4');