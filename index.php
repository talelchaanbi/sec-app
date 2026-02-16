<?php
session_start();
require_once __DIR__ . '/config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($login === '' || $password === '') {
        $message = 'Veuillez remplir le login et le mot de passe.';
    } else {
        $sql = 'SELECT login FROM users WHERE login = ? AND pass = ? LIMIT 1';
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param('ss', $login, $password);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows === 1) {
                $_SESSION['login'] = $login;
                header('Location: dashboard.php');
                exit;
            } else {
                $message = 'Authentification échouée : login ou mot de passe invalide.';
            }
            $stmt->close();
        } else {
            $message = 'Erreur serveur lors de la préparation de la requête.';
        }
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Authentification PHP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: #f5f7fb;
        }
        .card {
            width: 360px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,.08);
            padding: 24px;
        }
        h1 { font-size: 1.2rem; margin: 0 0 16px; }
        label { display: block; margin: 10px 0 6px; font-size: .92rem; }
        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #d0d7de;
            border-radius: 8px;
            box-sizing: border-box;
        }
        button {
            margin-top: 14px;
            width: 100%;
            border: 0;
            background: #2563eb;
            color: #fff;
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
        }
        .msg {
            margin-top: 12px;
            color: #b00020;
            font-size: .9rem;
        }
    </style>
</head>
<body>
<div class="card">
    <h1>Connexion</h1>
    <form method="post" action="">
        <label for="login">Login</label>
        <input id="login" name="login" type="text" required>

        <label for="password">Mot de passe</label>
        <input id="password" name="password" type="password" required>

        <button type="submit">Se connecter</button>
    </form>

    <?php if ($message !== ''): ?>
        <div class="msg"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
</div>
</body>
</html>
