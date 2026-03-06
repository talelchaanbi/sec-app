<?php
session_start();

// OWASP SCP §3 – Session Management : timeout d'inactivité
// Si l'utilisateur reste inactif plus de SESSION_TIMEOUT secondes,
// sa session est détruite et il est renvoyé à la page de connexion.
define('SESSION_TIMEOUT', 900); // 15 minutes

if (!isset($_SESSION['login'])) {
    header('Location: index.php');
    exit;
}

// Vérification du timeout d'inactivité (OWASP SCP §3)
if (isset($_SESSION['last_activity']) &&
    (time() - $_SESSION['last_activity']) > SESSION_TIMEOUT) {
    // Session expirée : destruction propre côté serveur
    session_unset();
    session_destroy();
    header('Location: index.php?timeout=1');
    exit;
}

// Mise à jour du timestamp d'activité à chaque requête authentifiée
$_SESSION['last_activity'] = time();
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Espace privé</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: #f5f7fb;
        }
        .box {
            background: #fff;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,.08);
            text-align: center;
        }
        a {
            display: inline-block;
            margin-top: 10px;
            text-decoration: none;
            color: #2563eb;
        }
    </style>
</head>
<body>
    <div class="box">
        <h2>Authentification réussie</h2>
        <p>Bienvenue <?php echo htmlspecialchars($_SESSION['login'], ENT_QUOTES, 'UTF-8'); ?>.</p>
        <a href="logout.php">Se déconnecter</a>
    </div>
</body>
</html>
