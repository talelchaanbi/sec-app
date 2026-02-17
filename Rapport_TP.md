# RAPPORT D'ANALYSE DE TRAFIC HTTP

## Application d'authentification PHP - Analyse Wireshark

**Date de l'analyse :** 17 février 2026  
**Auteur :** [Votre nom]  
**Contexte :** Projet Client/Serveur WEB - Application PHP d'authentification avec MySQL

## 1. INTRODUCTION

Ce rapport présente l'analyse du trafic réseau généré par une application web d'authentification développée en PHP. L'application permet aux utilisateurs de se connecter avec un login et mot de passe stockés dans une base MySQL. Les captures ont été réalisées avec Wireshark pour observer les échanges HTTP entre le client et le serveur sur deux machines distinctes.

## 2. ARCHITECTURE TECHNIQUE

### 2.1 Environnement matériel et logiciel

| Élément | Serveur | Client |
|---|---|---|
| Machine | HP Workstation | PC distant |
| OS | Linux (Ubuntu/Debian) | Windows 10 |
| IP | 192.168.3.7 | 192.168.3.100 |
| Serveur web | Apache/2.4.58 | - |
| Base de données | MySQL | - |
| PHP | Version 7.4/8.x | - |
| Navigateur | - | Chrome/91.0.4472.114 |
| Outil d'analyse | Wireshark | - |

### 2.2 Configuration réseau

Les deux machines sont connectées sur le même réseau local en 192.168.3.0/24 via une connexion Ethernet.

## 3. CAPTURES WIRESHARK

### 3.1 Filtre utilisé

```text
http && ip.addr == 192.168.3.100
```

Ce filtre permet d'afficher uniquement le trafic HTTP échangé avec le client (IP 192.168.3.100).

### 3.2 Vue d'ensemble des paquets capturés

- Nombre total de paquets : 5023
- Paquets affichés par le filtre : 12 (0.2%)

## 4. ANALYSE DÉTAILLÉE DES ÉCHANGES

### 4.1 Séquence complète des événements

| N° paquet | Temps (s) | Source | Destination | Méthode | URL | Code | Description |
|---:|---:|---|---|---|---|---|---|
| 293 | 5.142 | 192.168.3.100 | 192.168.3.7 | GET | /index.php | - | Demande page de connexion |
| 295 | 5.147 | 192.168.3.7 | 192.168.3.100 | - | - | 200 OK | Page de connexion renvoyée |
| 1541 | 47.667 | 192.168.3.100 | 192.168.3.7 | POST | /index.php | - | Envoi identifiants (admin/***) |
| 1547 | 47.668 | 192.168.3.7 | 192.168.3.100 | - | - | 302 Found | Redirection après connexion réussie |
| 1548 | 47.671 | 192.168.3.100 | 192.168.3.7 | GET | /dashboard.php | - | Demande page protégée |
| 1549 | 47.703 | 192.168.3.7 | 192.168.3.100 | - | - | 200 OK | Page dashboard renvoyée |
| 2593 | 70.959 | 192.168.3.100 | 192.168.3.7 | GET | /logout.php | - | Déconnexion |
| 2597 | 70.961 | 192.168.3.7 | 192.168.3.100 | - | - | 302 Found | Redirection après déconnexion |
| 2599 | 70.964 | 192.168.3.100 | 192.168.3.7 | GET | /index.php | - | Retour page connexion |
| 2600 | 70.965 | 192.168.3.7 | 192.168.3.100 | - | - | 200 OK | Page connexion renvoyée |
| 3047 | 84.551 | 192.168.3.100 | 192.168.3.7 | POST | /index.php | - | Tentative connexion (admin/124578) |
| 3052 | 84.553 | 192.168.3.7 | 192.168.3.100 | - | - | 200 OK | Échec authentification |

## 5. ANALYSE PAR PHASE

### 5.1 Chargement de la page de connexion (Paquets 293-295)

Requête GET (Paquet 293) :

```text
GET /index.php HTTP/1.1
Host: 192.168.3.7
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36
```

Réponse (Paquet 295) :

```text
HTTP/1.1 200 OK
Server: Apache/2.4.58 (Ubuntu)
Content-Type: text/html
```

Le serveur renvoie le formulaire de connexion HTML.

### 5.2 Connexion réussie (Paquets 1541-1549)

Requête POST (Paquet 1541) :

```text
POST /index.php HTTP/1.1
Content-Type: application/x-www-form-urlencoded

login=admin&password=******
```

Les identifiants sont transmis en clair dans le corps de la requête.

Réponse (Paquet 1547) :

```text
HTTP/1.1 302 Found
Location: dashboard.php
```

Le serveur valide les identifiants et redirige vers l'espace privé.

Accès au dashboard (Paquets 1548-1549) :

```text
GET /dashboard.php HTTP/1.1
Cookie: PHPSESSID=2rapnmsbdognegg6rnj1jcmv
```

Le cookie de session est utilisé pour maintenir l'authentification.

### 5.3 Déconnexion (Paquets 2593-2600)

Requête GET (Paquet 2593) :

```text
GET /logout.php HTTP/1.1
Referer: http://192.168.3.7/dashboard.php
Cookie: PHPSESSID=2rapnmsbdognegg6rnj1jcmv
```

Redirection (Paquet 2597) :

```text
HTTP/1.1 302 Found
Location: index.php
```

### 5.4 Tentative de connexion échouée (Paquets 3047-3052)

Requête POST (Paquet 3047) :

```text
POST /index.php HTTP/1.1
login=admin&password=124578
```

Réponse (Paquet 3052) :

```text
HTTP/1.1 200 OK
Content-Type: text/html
```

Le serveur renvoie la page index.php avec un message d'erreur :

"Authentification échouée : login ou mot de passe invalide."

## 6. ANALYSE DES EN-TÊTES HTTP

### 6.1 En-têtes de requête (client → serveur)

```text
Host: 192.168.3.7
Connection: keep-alive
Upgrade-Insecure-Requests: 1
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8
Referer: http://192.168.3.7/index.php
Accept-Encoding: gzip, deflate
Accept-Language: fr-FR,fr;q=0.9,en-US;q=0.8,en;q=0.7
Cookie: PHPSESSID=2rapnmsbdognegg6rnj1jcmv
```

### 6.2 En-têtes de réponse (serveur → client)

```text
HTTP/1.1 200 OK
Date: Tue, 17 Feb 2026 14:02:28 GMT
Server: Apache/2.4.58 (Ubuntu)
Cache-Control: max-age=0
Content-Type: text/html; charset=UTF-8
```

## 7. ANALYSE DE LA BASE DE DONNÉES

### 7.1 Structure de la table users

D'après les captures phpMyAdmin :

| Colonne | Type | Informations |
|---|---|---|
| login | VARCHAR | Nom d'utilisateur |
| pass | VARCHAR | Mot de passe (probablement en clair) |

État de la table :

- Moteur : InnoDB
- Collation : utf8mb4_unicode_ci
- Taille : 16.0 KiB
- Nombre d'enregistrements : 0 (table vide)

### 7.2 Problème identifié

La table users est vide, ce qui explique que seule la tentative avec le mot de passe 124578 a échoué. Pour fonctionner, il faut insérer au moins un utilisateur valide.

Commande SQL recommandée :

```sql
INSERT INTO users (login, pass) VALUES ('admin', 'admin123');
```

⚠️ Attention : En production, les mots de passe doivent être hashés (bcrypt/argon2).

## 8. ANALYSE DES CODES HTTP

| Code | Signification | Occurrence |
|---|---|---|
| 200 OK | Requête réussie | Page connexion, dashboard, erreur |
| 302 Found | Redirection après connexion/déconnexion | POST /index.php → dashboard, logout → index |

## 9. SÉCURITÉ - VULNÉRABILITÉS IDENTIFIÉES

### 9.1 Absence de chiffrement (HTTP)

- Les identifiants circulent en clair (paquets 1541, 3047)
- Capture possible avec Wireshark

### 9.2 Mots de passe en clair dans la base

La structure suggère un stockage non sécurisé.

### 9.3 Message d'erreur trop explicite

"Authentification échouée : login ou mot de passe invalide."

Facilite les attaques par brute-force.

### 9.4 Recommandations

- Migrer vers HTTPS avec TLS/SSL
- Hacher les mots de passe (`password_hash()` en PHP)
- Message d'erreur générique : "Identifiants incorrects"
- Limiter les tentatives de connexion

## 10. CAPTURE DU SERVEUR APACHE

Le serveur Apache était actif pendant toute la durée des tests :

```text
sudo systemctl status apache2
● apache2.service - The Apache HTTP Server
	Active: active (running) since Tue 2026-02-17 14:20:58 CET
	Main PID: 58892 (apache2)
	Tasks: 6 (limit: 18553)
	Memory: 14.9M
```

## 11. CONCLUSION

L'analyse Wireshark a permis d'observer le fonctionnement complet d'une application d'authentification PHP en environnement client/serveur réel :

- Communication établie entre deux machines distinctes (192.168.3.7 et 192.168.3.100)
- Mécanisme d'authentification fonctionnel avec sessions PHP
- Redirections HTTP 302 utilisées correctement
- Trafic HTTP entièrement analysable (identifiants visibles en clair)

Points positifs :

- L'application fonctionne comme attendu
- Les sessions PHP sont bien maintenues
- La séparation client/serveur est effective

Points à améliorer :

- Sécuriser les échanges avec HTTPS
- Hacher les mots de passe en base de données
- Remplir la table users avec des comptes valides

## 12. ANNEXES

### Annexe A : Page de connexion (index.php)

![Page de connexion](Screenshot%20from%202026-02-17%2012-42-16.png)

### Annexe B : Dashboard après connexion réussie

![Dashboard](Screenshot%20from%202026-02-17%2012-42-47.png)

### Annexe C : Message d'erreur après échec

![Erreur](Screenshot%20from%202026-02-17%2012-43-42.png)

### Annexe D : Capture Wireshark complète

![Wireshark](Screenshot%20from%202026-02-17%2012-45-30.png)

### Annexe E : Structure de la base de données

![phpMyAdmin](Screenshot%20from%202026-02-16%2022-30-58.png)

### Annexe F : État du serveur Apache

![Apache status](Screenshot%20from%202026-02-16%2022-31-16.png)

---

**Fin du rapport**

Document rédigé dans le cadre du projet Client/Serveur WEB - Application PHP d'authentification.
