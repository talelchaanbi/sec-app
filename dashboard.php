<?php
session_start();

if (!isset($_SESSION['login'])) {
    header('Location: index.php');
    exit;
}
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
