# Déploiement & CI/CD - DealElDorado

Documentation de l'infrastructure de production (VPS Infomaniak) et du pipeline de mise en prod automatisé.

---

## Table des matières

1. [Vue d'ensemble](#vue-densemble)
2. [Serveur](#serveur)
3. [Sécurité en place](#sécurité-en-place)
4. [Secrets & fichiers non versionnés](#secrets--fichiers-non-versionnés)
5. [CI/CD - comment ça marche](#cicd---comment-ça-marche)
6. [Déployer manuellement (secours)](#déployer-manuellement-secours)
7. [Rollback](#rollback)
8. [Sauvegardes](#sauvegardes)
9. [Ce qu'il te reste à faire](#ce-quil-te-reste-à-faire)
10. [Checklist pour une prochaine mise en prod](#checklist-pour-une-prochaine-mise-en-prod)

---

## Vue d'ensemble

```
Ton poste (git push origin main)
        │
        ▼
  GitHub Actions (.github/workflows/deploy.yml)
        │  SSH (clé dédiée, utilisateur "deploy", sans sudo)
        ▼
  VPS Infomaniak (Ubuntu 26.04)
  /var/www/dealeldorado.com
  ├── git reset --hard origin/main
  ├── permissions wp-content (www-data)
  ├── wp cache flush / wp rewrite flush
  └── smoke test HTTP
```

À chaque `git push` sur `main`, le site en prod se met à jour automatiquement en ~30-60 secondes.

## Serveur

| | |
|---|---|
| Hébergeur | Infomaniak (VPS Lite) |
| IP | `179.237.110.111` |
| OS | Ubuntu 26.04 LTS |
| Ressources | 1 vCPU / 1.9 Go RAM (+ 2 Go de swap) / 19 Go disque |
| Web | Nginx 1.28 + PHP 8.5-FPM |
| Base de données | MariaDB 11.8, base `dealeldorado`, utilisateur `ded_user` |
| Racine web | `/var/www/dealeldorado.com` |
| Nom de domaine cible | `dealeldorado.com` (DNS pas encore basculé, voir plus bas) |

### Utilisateurs SSH

| Utilisateur | Rôle | Clé privée (sur ton poste) | Sudo |
|---|---|---|---|
| `ubuntu` | Administration serveur (toi/moi) | `~/.ssh/dealeldorado_vps` | Oui (sans mot de passe) |
| `deploy` | CI/CD uniquement, propriétaire de `/var/www/dealeldorado.com` | `~/.ssh/dealeldorado_deploy` (aussi dans le secret GitHub `DEPLOY_SSH_KEY`) | Non |
| `rado` | Existe par défaut (compte Infomaniak), accessible seulement via la console web (KVM), pas en SSH | - | - |

> Le mot de passe `Deal-Eldorado2026` initialement dans `.env` est celui de la console web Infomaniak (accès `rado`), pas du SSH — le SSH n'accepte que des clés (`PasswordAuthentication no`).

Se connecter en admin :
```bash
ssh -i ~/.ssh/dealeldorado_vps ubuntu@179.237.110.111
```

## Sécurité en place

- **Pare-feu OS** : `ufw` — seuls 22 (SSH), 80 (HTTP), 443 (HTTPS) sont ouverts.
- **Pare-feu Infomaniak** (niveau infrastructure, séparé de ufw) : ouvert sur 80/443 dans le Manager.
- **fail2ban** : jails `sshd`, `nginx-http-auth`, `nginx-botsearch` actifs.
- **Anti brute-force wp-login** : `limit_req` nginx sur `/wp-login.php` et `/xmlrpc.php` (5 req/min, config dans `/etc/nginx/conf.d/wp-login-limit.conf`).
- **SSH** : mot de passe désactivé, `PermitRootLogin no`, seuls `ubuntu` et `deploy` autorisés (`/etc/ssh/sshd_config.d/99-dealeldorado-hardening.conf`).
- **Mises à jour de sécurité** : `unattended-upgrades` actif (patchs automatiques).
- **Utilisateur de déploiement dédié** (`deploy`) sans droits sudo, isolé du compte admin.

## Secrets & fichiers non versionnés

Ces fichiers existent **uniquement sur le serveur**, jamais dans git (`.gitignore`) :

| Fichier | Contenu | Où le modifier |
|---|---|---|
| `/var/www/dealeldorado.com/.env` | Clés API (OpenAI, ClickBank, CJ, Sovrn) lues par `dealeldorado-core` | Édition directe en SSH, pas de redéploiement nécessaire |
| `/var/www/dealeldorado.com/wp-config-local.php` | Identifiants MySQL de prod + `WP_HOME`/`WP_SITEURL` | Édition directe en SSH |

Secrets GitHub Actions (`Settings → Secrets and variables → Actions → Repository secrets`) :

| Secret | Valeur |
|---|---|
| `DEPLOY_HOST` | `179.237.110.111` |
| `DEPLOY_USER` | `deploy` |
| `DEPLOY_PATH` | `/var/www/dealeldorado.com` |
| `DEPLOY_SSH_KEY` | Contenu de `~/.ssh/dealeldorado_deploy` (clé privée) |

## CI/CD - comment ça marche

Fichier : [.github/workflows/deploy.yml](.github/workflows/deploy.yml)

- Déclenché sur chaque `push` vers `main` (ou manuellement via l'onglet **Actions** → **Déploiement production** → **Run workflow**).
- Se connecte en SSH avec l'utilisateur `deploy` (pas de sudo, accès restreint à `/var/www/dealeldorado.com`).
- `git fetch` + `git reset --hard origin/main` : le serveur reflète exactement ce qui est sur `main` (les fichiers non versionnés `.env`/`wp-config-local.php` ne sont jamais touchés).
- Remet les permissions correctes sur `wp-content` (écriture par `www-data` pour les uploads/cache).
- Vide le cache WordPress et les règles de réécriture.
- Fait un test HTTP local sur le serveur ; le job échoue si le site ne répond pas.

**Suivre une exécution** : onglet [Actions du repo](https://github.com/radoamws/dealeldorado/actions).

## Déployer manuellement (secours)

Si GitHub Actions est indisponible :

```bash
ssh -i ~/.ssh/dealeldorado_deploy deploy@179.237.110.111
cd /var/www/dealeldorado.com
git fetch origin main
git reset --hard origin/main
chgrp -R www-data wp-content
find wp-content -type d -exec chmod 2775 {} \;
find wp-content -type f -exec chmod 664 {} \;
wp cache flush
wp rewrite flush --hard
```

## Rollback

**Option 1 — revert (recommandé)** : depuis ton poste,
```bash
git revert <sha-du-commit-problématique>
git push origin main
```
Le push déclenche automatiquement un redéploiement avec le code précédent.

**Option 2 — retour direct à un commit** (urgence) :
```bash
ssh -i ~/.ssh/dealeldorado_deploy deploy@179.237.110.111
cd /var/www/dealeldorado.com
git reset --hard <sha-du-commit-stable>
wp cache flush
```
⚠️ Le prochain push sur `main` ré-appliquera l'historique normal — l'option 2 est temporaire, corrige la branche `main` ensuite.

**Restaurer la base de données** depuis une sauvegarde (voir section suivante) :
```bash
gunzip -c /var/backups/dealeldorado/db-AAAA-MM-JJ_HHMMSS.sql.gz | sudo mysql dealeldorado
```

## Sauvegardes

- Script : `/usr/local/bin/dealeldorado-backup.sh` (base MySQL + `wp-content/uploads`).
- Planifié tous les jours à **3h15** (cron root) dans `/var/backups/dealeldorado/`.
- Rétention : **7 jours** (purge automatique des fichiers plus anciens).
- Logs : `/var/log/dealeldorado-backup.log`.
- ⚠️ Sauvegarde locale uniquement (sur le même disque que le site) — à améliorer plus tard avec un envoi hors-site (S3/Object Storage Infomaniak) si le contenu devient critique.

## État de la bascule

- ✅ **DNS** : `dealeldorado.com` et `www` pointent vers `179.237.110.111`.
- ✅ **SSL** : certificat Let's Encrypt actif (`dealeldorado.com` + `www.dealeldorado.com`), expire le 2026-10-26, renouvellement automatique via `certbot.timer`. HTTP redirige vers HTTPS.
- ⚠️ Ancien hébergement (PlanetHoster) toujours en place — à résilier quand tu confirmes que tout fonctionne bien sur le VPS.

## Ce qu'il te reste à faire

1. **Faire tourner les clés API compromises** (elles ont été exposées publiquement sur GitHub avant le nettoyage de l'historique) :
   - **OpenAI** : révoquer `sk-proj-...` sur platform.openai.com et en générer une nouvelle.
   - **ClickBank** : changer le mot de passe du compte `mdevimada@gmail.com` et régénérer la clé API si possible.
   - **CJ Affiliate** : régénérer le `PERSONAL_ACCESS_TOKEN`.
   - **Sovrn** : régénérer `API_KEY`/`SECRET_KEY`.
   - Une fois faites, mets à jour `/var/www/dealeldorado.com/.env` sur le serveur (pas besoin de redéployer).
2. **Lever le noindex** quand le site est prêt pour Google : Réglages → Lecture → décocher *« Décourager les moteurs de recherche »* (actuellement coché intentionnellement).
3. **Bug pré-existant repéré pendant la migration** : dans `.env`, la clé `API_KEY` est utilisée à la fois pour CJ Products et pour Sovrn (même nom de variable) — la valeur de Sovrn écrase celle de CJ en mémoire (`DED_Env_Loader`). Vérifie si `class-ded-admin.php`/les modules Content Egg lisent bien la bonne valeur pour CJ ; si besoin, renommer une des deux clés dans `.env` et dans le code qui les lit.

## Checklist pour une prochaine mise en prod

Rien à faire manuellement — juste :
```bash
git add -A
git commit -m "..."
git push origin main
```
GitHub Actions déploie automatiquement. Vérifie ensuite l'onglet **Actions** (doit finir en vert) et que [https://dealeldorado.com](https://dealeldorado.com) répond.

Si tu ajoutes un nouveau plugin/thème avec des dépendances (Composer, npm build), il faudra étendre `.github/workflows/deploy.yml` avec une étape de build avant le déploiement — dis-le-moi le moment venu.
