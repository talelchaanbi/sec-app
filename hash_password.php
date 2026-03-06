<?php
/**
 * UTILITAIRE DE MIGRATION – Hachage des mots de passe existants
 * =============================================================
 * OWASP SCP §2 – Secure Password Storage
 *
 * Exécutez ce script UNE SEULE FOIS en ligne de commande pour migrer
 * les mots de passe en clair vers des hash bcrypt :
 *
 *   php hash_password.php
 *
 * SUPPRIMEZ ce fichier du serveur dès la migration terminée !
 */

require_once __DIR__ . '/config.php';

// --- Démonstration : générer le hash d'un mot de passe donné ---
$plainPassword = 'VotreMotDePasse123!'; // ← remplacez par le vrai mot de passe

// password_hash() utilise bcrypt (PASSWORD_BCRYPT) avec un coût adaptatif.
// PASSWORD_DEFAULT choisit automatiquement le meilleur algorithme disponible.
$hash = password_hash($plainPassword, PASSWORD_DEFAULT);

echo "Mot de passe clair  : {$plainPassword}\n";
echo "Hash bcrypt généré  : {$hash}\n\n";

// --- Migration automatique de tous les utilisateurs ---
// Récupère les utilisateurs dont le mot de passe n'est pas encore haché
// (heuristique : les hash bcrypt commencent toujours par '$2y$')
$result = $conn->query("SELECT id, login, pass FROM users");

if (!$result) {
    die("Erreur : " . $conn->error . "\n");
}

$migrated = 0;
while ($row = $result->fetch_assoc()) {
    // Si le mot de passe ne ressemble pas déjà à un hash bcrypt, on le hache
    if (strpos($row['pass'], '$2y$') !== 0 && strpos($row['pass'], '$2b$') !== 0) {
        $newHash = password_hash($row['pass'], PASSWORD_DEFAULT);
        $stmt    = $conn->prepare("UPDATE users SET pass = ? WHERE id = ?");
        $stmt->bind_param('si', $newHash, $row['id']);
        $stmt->execute();
        $stmt->close();
        echo "✔ Utilisateur '{$row['login']}' migré.\n";
        $migrated++;
    } else {
        echo "⏭  Utilisateur '{$row['login']}' déjà haché, ignoré.\n";
    }
}

echo "\nMigration terminée : {$migrated} mot(s) de passe mis à jour.\n";
echo "\n⚠  SUPPRIMEZ CE FICHIER DU SERVEUR MAINTENANT !\n";
