# Mini projet Client/Serveur Web (PHP + MySQL)

## 1) Pré-requis côté serveur

```bash
sudo apt update
sudo apt install -y apache2 php libapache2-mod-php php-mysql mysql-server wireshark
sudo systemctl enable --now apache2 mysql
```

> Vérifier les services :

```bash
sudo systemctl status apache2
sudo systemctl status mysql
```

## 2) Déployer l'application

Copier les fichiers `index.php`, `config.php`, `dashboard.php`, `logout.php` vers :

```bash
/var/www/html/
```

Exemple :

```bash
sudo cp *.php /var/www/html/
sudo chown -R www-data:www-data /var/www/html/
```

## 3) Base de données MySQL

Si la base et la table existent déjà chez vous, garder votre structure.

Exemple SQL minimal :

```sql
CREATE DATABASE IF NOT EXISTS auth_db;
USE auth_db;

CREATE TABLE IF NOT EXISTS users (
  login VARCHAR(100) NOT NULL PRIMARY KEY,
  password VARCHAR(255) NOT NULL
);

INSERT INTO users (login, password)
VALUES ('admin', 'admin123');
```

Adapter `config.php` avec vos paramètres :
- `$DB_HOST`
- `$DB_USER`
- `$DB_PASS`
- `$DB_NAME`

## 4) Test depuis le client (machine distante)

Dans le navigateur du client :

```text
http://IP_SERVEUR/index.php
```

### Test 1 (réussi)
- Login correct
- Mot de passe correct
- Résultat attendu : page "Authentification réussie"

### Test 2 (échoué)
- Login correct + mot de passe faux (ou inverse)
- Résultat attendu : message d'échec

## 5) Capture réseau Wireshark

Installer Wireshark côté client **ou** serveur, puis capturer sur l'interface réseau active.

Filtre utile :

```text
http || tcp.port == 80
```

Actions à réaliser pendant la capture :
1. Ouvrir la page de login
2. Faire une authentification réussie
3. Faire une authentification échouée
4. Arrêter la capture et faire des captures d'écran

## 6) Contenu conseillé du PDF à rendre

1. Objectif
2. Architecture (client distant / serveur distant)
3. Installation Apache, PHP, MySQL
4. Schéma de la base + table `users`
5. Code PHP (captures)
6. Test réussi (capture écran)
7. Test échoué (capture écran)
8. Analyse Wireshark (requêtes HTTP observées)
9. Conclusion

---

⚠️ Remarque sécurité (pour le cours) :
- Ce TP fonctionne en HTTP (non chiffré), donc les identifiants peuvent être visibles dans le trafic.
- En production, utiliser HTTPS + mots de passe hachés (`password_hash` / `password_verify`).
