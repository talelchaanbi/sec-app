<?php
session_start();
require_once __DIR__ . '/config.php';

// ---------------------------------------------------------------
// OWASP SCP §2 – Authentication : protection contre la force brute
// Blocage temporaire après MAX_FAILED_ATTEMPTS tentatives échouées.
// ---------------------------------------------------------------
define('MAX_FAILED_ATTEMPTS', 5);
define('LOCKOUT_DURATION',    300); // secondes (5 minutes)

$message = '';

// --- Génération du jeton CSRF (OWASP SCP §5 – CSRF Protection) ---
// Un jeton aléatoire unique par session est créé et glissé dans le
// formulaire ; le serveur le vérifie à chaque soumission POST.
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Validation du jeton CSRF (OWASP SCP §5)
    //    hash_equals() évite les attaques de timing sur la comparaison.
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $csrfToken)) {
        $message = 'Requête invalide (jeton CSRF incorrect).';

    // 2. Vérification du verrouillage de compte (OWASP SCP §2 – Brute Force)
    } elseif (
        isset($_SESSION['failed_attempts'], $_SESSION['lockout_time']) &&
        $_SESSION['failed_attempts'] >= MAX_FAILED_ATTEMPTS &&
        (time() - $_SESSION['lockout_time']) < LOCKOUT_DURATION
    ) {
        $remaining = LOCKOUT_DURATION - (time() - $_SESSION['lockout_time']);
        $message   = "Compte temporairement bloqué. Réessayez dans {$remaining} s.";

    } else {
        $login    = trim($_POST['login']    ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($login === '' || $password === '') {
            $message = 'Veuillez remplir le login et le mot de passe.';
        } else {
            // 3. On récupère UNIQUEMENT par login ; le hash est comparé en PHP
            //    (OWASP SCP §2 – ne jamais passer le mot de passe dans la requête SQL)
            $sql  = 'SELECT login, pass FROM users WHERE login = ? LIMIT 1';
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $stmt->bind_param('s', $login);
                $stmt->execute();
                $result = $stmt->get_result();
                $user   = $result ? $result->fetch_assoc() : null;
                $stmt->close();

                // 4. Vérification du hash bcrypt (OWASP SCP §2 – Stockage sécurisé)
                //    password_verify() compare le mot de passe clair au hash stocké.
                //    Ne jamais stocker ni comparer les mots de passe en clair.
                $valid = $user && password_verify($password, $user['pass']);

                if ($valid) {
                    // 5. Réinitialisation du compteur d'échecs
                    unset($_SESSION['failed_attempts'], $_SESSION['lockout_time']);

                    // 6. Prévention de la fixation de session (OWASP SCP §3)
                    //    Génère un nouvel ID de session après une authentification
                    //    réussie pour invalider tout ID préalablement fixé.
                    session_regenerate_id(true);

                    // 7. Renouvellement du jeton CSRF après connexion
                    $_SESSION['csrf_token']     = bin2hex(random_bytes(32));

                    $_SESSION['login']          = $login;
                    $_SESSION['last_activity']  = time(); // utilisé pour le timeout

                    header('Location: dashboard.php');
                    exit;
                } else {
                    // 8. Message d'erreur générique (OWASP SCP §2)
                    //    Ne pas indiquer si c'est le login OU le mot de passe qui est faux.
                    $message = 'Authentification échouée : login ou mot de passe invalide.';

                    // 9. Incrément du compteur d'échecs (OWASP SCP §2 – Brute Force)
                    $_SESSION['failed_attempts'] = ($_SESSION['failed_attempts'] ?? 0) + 1;
                    if ($_SESSION['failed_attempts'] >= MAX_FAILED_ATTEMPTS) {
                        $_SESSION['lockout_time'] = time();
                    }
                }
            } else {
                // OWASP SCP §7 : journaliser l'erreur, ne pas l'exposer
                error_log('Erreur préparation requête : ' . $conn->error);
                $message = 'Erreur serveur. Veuillez réessayer plus tard.';
            }
        }
    }

    // 10. Renouvellement du jeton CSRF après chaque soumission (même échouée)
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
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
        <!-- Jeton CSRF caché (OWASP SCP §5 – CSRF Protection) -->
        <input type="hidden" name="csrf_token"
               value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

        <label for="login">Login</label>
        <!-- autocomplete="username" aide le gestionnaire de mots de passe du navigateur -->
        <input id="login" name="login" type="text" required autocomplete="username">

        <label for="password">Mot de passe</label>
        <input id="password" name="password" type="password" required autocomplete="current-password">

        <button type="submit">Se connecter</button>
    </form>

    <?php if ($message !== ''): ?>
        <div class="msg"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
</div>
</body>
</html>
