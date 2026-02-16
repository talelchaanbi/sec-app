# Rapport TP — Application Web Client/Serveur (PHP + MySQL)

## 1. Objectif
Mettre en place une application web d’authentification simple en architecture client/serveur distante, puis analyser le trafic réseau avec Wireshark.

## 2. Environnement
- **Serveur** : Linux, Apache2, PHP, MySQL
- **Client** : machine distante (navigateur web)
- **Analyse réseau** : Wireshark (côté client ou serveur)

## 3. Installation côté serveur
### 3.1 Apache2, PHP, MySQL
(Insérer captures d’écran des commandes et statuts de services)

### 3.2 Déploiement des fichiers PHP
- `index.php` : formulaire login/password + vérification
- `config.php` : connexion MySQL
- `dashboard.php` : page succès
- `logout.php` : déconnexion

(Insérer captures d’écran des fichiers)

## 4. Base de données
- Base : `authdb`
- Table : `users`
- Colonnes : `login`, `password`

(Insérer capture de la table + contenu)

## 5. Tests d’authentification (depuis le client distant)
### 5.1 Cas réussi
- Login valide
- Mot de passe valide
- Résultat : accès à la page "Authentification réussie"

(Insérer capture d’écran)

### 5.2 Cas échoué
- Login valide + mot de passe invalide (ou inverse)
- Résultat : message d’erreur d’authentification

(Insérer capture d’écran)

## 6. Capture Wireshark
- Interface capturée : [à préciser]
- Filtre : `http || tcp.port == 80`
- Observations :
  - Requête HTTP vers `/index.php`
  - Envoi des paramètres d’authentification
  - Réponse du serveur (succès/échec)

(Insérer captures Wireshark annotées)

## 7. Analyse et remarques sécurité
- En HTTP, les données peuvent être interceptées.
- Pour un environnement réel : HTTPS obligatoire.
- Les mots de passe doivent être hachés (`password_hash`, `password_verify`).

## 8. Conclusion
Le TP valide la mise en place d’un service web d’authentification simple, l’accès distant client/serveur, et l’observation du trafic réseau généré.
