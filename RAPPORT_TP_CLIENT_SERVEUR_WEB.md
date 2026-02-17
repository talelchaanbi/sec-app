# Rapport TP Client/Serveur Web (PHP + Apache2 + MySQL)

**Date :** 17/02/2026  
**Type de travail :** Individuel  
**Thème :** Authentification Web simple en architecture client/serveur

---

## 1) Objectif du TP

Mettre en place une application Web PHP côté serveur avec :
- un serveur HTTP Apache2,
- un moteur PHP,
- une base de données MySQL,
- une page d’authentification (login/mot de passe),
- des tests d’authentification réussie et échouée côté client,
- une capture réseau avec Wireshark.

---

## 2) Contexte et architecture

- **Serveur** : machine hébergeant Apache2, PHP, MySQL et les fichiers PHP.
- **Client** : machine distante qui accède à l’application via navigateur.
- **Communication** : HTTP entre client et serveur (port 80).

Flux logique :
1. Le client envoie une requête vers la page de connexion.
2. Le serveur renvoie le formulaire d’authentification.
3. Le client soumet `login` + `password`.
4. Le serveur vérifie les informations dans MySQL.
5. En cas de succès : création de session PHP puis redirection vers l’espace privé.
6. En cas d’échec : affichage d’un message d’erreur.

---

## 3) Fichiers du projet analysés

- [config.php](config.php) : configuration de connexion MySQL (`authdb`, utilisateur `root`), initialisation de la connexion et gestion d’erreur.
- [index.php](index.php) : page de connexion, traitement du formulaire et vérification SQL avec requête préparée.
- [dashboard.php](dashboard.php) : page privée affichée uniquement si la session est active.
- [logout.php](logout.php) : destruction de session et retour à la page d’authentification.

Captures présentes dans [assets](assets) :
- [assets/login_page_client.jpeg](assets/login_page_client.jpeg)
- [assets/success_login.jpeg](assets/success_login.jpeg)
- [assets/failed_login.jpeg](assets/failed_login.jpeg)
- [assets/annexe-a-connexion.png](assets/annexe-a-connexion.png)
- [assets/annexe-b-dashboard.png](assets/annexe-b-dashboard.png)
- [assets/annexe-c-erreur.png](assets/annexe-c-erreur.png)
- [assets/annexe-d-wireshark.png](assets/annexe-d-wireshark.png)
- [assets/annexe-f-apache-status.png](assets/annexe-f-apache-status.png)
- [assets/bd.png](assets/bd.png)

---

## 4) Mise en place serveur (Apache2, PHP, MySQL)

### 4.1 Installation des composants (référence)

Exemple Debian/Ubuntu :
- `apache2`
- `php`
- `libapache2-mod-php`
- `mysql-server`
- `php-mysql`

Après installation :
- démarrage/activation d’Apache2 et MySQL,
- copie des fichiers PHP dans le dossier Web serveur,
- vérification de l’état du service Apache (capture correspondante : annexe Apache).

### 4.2 Déploiement

Les fichiers PHP ont été déployés dans le répertoire Web du serveur.  
Commande observée dans l’historique terminal : copie des `.php` vers `/var/www/html/`.

---

## 5) Base de données MySQL

### 5.1 Schéma minimal demandé

Base : `authdb`  
Table : `users` avec deux colonnes :
- `login`
- `pass`

SQL type :

```sql
CREATE DATABASE IF NOT EXISTS authdb;
USE authdb;

CREATE TABLE IF NOT EXISTS users (
  login VARCHAR(50) PRIMARY KEY,
  pass VARCHAR(255) NOT NULL
);

INSERT INTO users (login, pass) VALUES ('admin', 'admin123');
```

La capture [assets/bd.png](assets/bd.png) correspond à la partie base de données.

---

## 6) Fonctionnement de l’authentification

### 6.1 Traitement applicatif

Dans [index.php](index.php) :
- récupération des champs du formulaire,
- validation des champs non vides,
- exécution d’une requête préparée :
  - `SELECT login FROM users WHERE login = ? AND pass = ? LIMIT 1`
- si trouvé :
  - création de session (`$_SESSION['login']`),
  - redirection vers la page privée.
- sinon : message d’échec.

Dans [dashboard.php](dashboard.php) :
- contrôle d’accès via session,
- affichage “Authentification réussie”.

Dans [logout.php](logout.php) :
- fermeture de session,
- redirection vers la page de connexion.

### 6.2 Résultats attendus et observés

- **Page de connexion affichée** : [assets/login_page_client.jpeg](assets/login_page_client.jpeg), [assets/annexe-a-connexion.png](assets/annexe-a-connexion.png)
- **Connexion réussie** (accès dashboard) : [assets/success_login.jpeg](assets/success_login.jpeg), [assets/annexe-b-dashboard.png](assets/annexe-b-dashboard.png)
- **Connexion échouée** (message d’erreur) : [assets/failed_login.jpeg](assets/failed_login.jpeg), [assets/annexe-c-erreur.png](assets/annexe-c-erreur.png)

---

## 7) Analyse réseau Wireshark

Capture : [assets/annexe-d-wireshark.png](assets/annexe-d-wireshark.png)

Constats typiques du TP en HTTP :
- on observe des échanges HTTP entre client et serveur,
- la soumission du formulaire se fait en requête `POST`,
- sans HTTPS, les données applicatives peuvent être visibles en clair dans le trafic,
- présence de réponses HTTP du serveur (succès/erreur/redirection).

Cela valide la génération effective de trafic Web pendant les tests d’authentification.

---

## 8) Bilan sécurité

Points positifs observés dans le code :
- usage de requête préparée SQL (réduction du risque d’injection SQL),
- protection `htmlspecialchars()` lors de l’affichage de texte dynamique,
- contrôle de session pour l’accès à la page privée.

Limites (dans le cadre pédagogique) :
- mot de passe stocké en clair dans la base (`pass`),
- identifiants de base en clair dans [config.php](config.php),
- trafic HTTP non chiffré (préférer HTTPS).

Améliorations recommandées :
- hachage des mots de passe (`password_hash()` / `password_verify()`),
- compte MySQL dédié avec privilèges minimaux,
- activation TLS/HTTPS,
- ajout de protections anti-bruteforce (temporisation, compteur, journalisation).

---

## 9) Conclusion

Le TP demandé est réalisé :
- environnement serveur Apache2/PHP/MySQL opérationnel,
- table MySQL contenant login/mot de passe valides,
- authentification réussie puis échouée vérifiée via navigateur client,
- capture Wireshark effectuée et exploitée,
- preuves jointes via captures d’écran dans [assets](assets).

L’objectif pédagogique client/serveur Web est atteint.

---

## 10) Annexes (captures)

- A — Connexion : [assets/annexe-a-connexion.png](assets/annexe-a-connexion.png)
- B — Dashboard : [assets/annexe-b-dashboard.png](assets/annexe-b-dashboard.png)
- C — Erreur : [assets/annexe-c-erreur.png](assets/annexe-c-erreur.png)
- D — Wireshark : [assets/annexe-d-wireshark.png](assets/annexe-d-wireshark.png)
- F — État Apache : [assets/annexe-f-apache-status.png](assets/annexe-f-apache-status.png)
- Base de données : [assets/bd.png](assets/bd.png)
- Captures client complémentaires :
  - [assets/login_page_client.jpeg](assets/login_page_client.jpeg)
  - [assets/success_login.jpeg](assets/success_login.jpeg)
  - [assets/failed_login.jpeg](assets/failed_login.jpeg)
