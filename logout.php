<?php
session_start();

// OWASP SCP §3 – Session Termination
// Une déconnexion sécurisée doit :
//  1. Effacer toutes les variables de session en mémoire.
//  2. Invalider le cookie de session côté client (expiration dans le passé).
//  3. Détruire la session côté serveur.

// 1. Effacer les données de session
session_unset();

// 2. Invalider le cookie de session côté client
//    Sans cette étape, le cookie persiste dans le navigateur même après
//    la destruction de la session serveur, ce qui peut permettre
//    une réutilisation frauduleuse du token de session.
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000, // date dans le passé → suppression immédiate
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// 3. Détruire la session côté serveur
session_destroy();

header('Location: index.php');
exit;
