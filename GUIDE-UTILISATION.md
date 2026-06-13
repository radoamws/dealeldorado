# Guide d'utilisation - DealElDorado

**Site comparateur de prix** | Domaine final : [dealeldorado.com](https://dealeldorado.com)

---

## Table des matières

1. [Vue d'ensemble](#vue-densemble)
2. [Architecture technique](#architecture-technique)
3. [Installation et démarrage](#installation-et-démarrage)
4. [Configuration des APIs affiliées](#configuration-des-apis-affiliées)
5. [Créer un article produit](#créer-un-article-produit)
6. [Shortcodes disponibles](#shortcodes-disponibles)
7. [Modules affiliés](#modules-affiliés)
8. [Gestion du thème](#gestion-du-thème)
9. [Fonctionnement des produits](#fonctionnement-des-produits)
10. [Mise en production](#mise-en-production)

---

## Vue d'ensemble

DealElDorado est un **comparateur de prix** similaire à idealo.fr. Il permet de :

- Comparer les prix d'un produit chez plusieurs marchands (CJ, Clickbank, Sovrn, etc.)
- Afficher l'historique des prix
- Créer des alertes prix par email
- Monétiser via des liens affiliés

### Technologies utilisées

| Composant | Technologie |
|-----------|-------------|
| CMS | WordPress 6.x |
| E-commerce | WooCommerce |
| Comparateur | Content Egg Pro v18.7 |
| Thème | DealElDorado (custom) |
| Plugin principal | DealElDorado Core |
| Frontend | Bootstrap 5.3 + Inter Font |
| APIs affiliées | CJ Products, Clickbank, Sovrn/VigLink |

---

## Architecture technique

```
dealeldorado/ (racine WordPress)
├── .env                        ← Clés API (CJ, Clickbank, Sovrn, OpenAI)
├── content-egg/                ← Source Content Egg Pro (liée via junction)
├── wp-content/
│   ├── plugins/
│   │   ├── content-egg/        ← Junction vers /content-egg
│   │   └── dealeldorado-core/  ← Plugin principal DealElDorado
│   └── themes/
│       └── dealeldorado/       ← Thème custom
└── GUIDE-UTILISATION.md        ← Ce fichier
```

---

## Installation et démarrage

### 1. Vérifier que XAMPP est démarré

- Apache doit être actif
- MySQL doit être actif
- Accéder à : `http://localhost/dealeldorado`

### 2. Se connecter au Back Office WordPress

URL : `http://localhost/dealeldorado/wp-admin`
- **Utilisateur** : votre compte admin WordPress
- **Mot de passe** : votre mot de passe

### 3. Activer les plugins requis

Dans **Extensions → Extensions installées**, activer dans cet ordre :
1. **WooCommerce** (si ce n'est pas fait)
2. **Content Egg Pro** (obligatoire pour les comparaisons)
3. **DealElDorado Core** (plugin principal)

### 4. Activer le thème DealElDorado

Dans **Apparence → Thèmes**, activer **DealElDorado**.

### 5. Vérifier la configuration automatique

Aller dans **DealElDorado → Tableau de bord**. Les modules CJ Products, Clickbank et Sovrn doivent afficher ✅ Actif (configurés automatiquement depuis le `.env`).

---

## Configuration des APIs affiliées

Les clés sont lues automatiquement depuis le fichier `.env` à la racine du projet. Pour modifier manuellement :

**DealElDorado → Configuration API**

### CJ Products (Commission Junction)

| Paramètre | Valeur dans .env |
|-----------|-----------------|
| Personal Access Token | `PERSONAL_ACCESS_TOKEN` |
| Company ID | `COMPANY_ID` (ex: CJ4476141 → 4476141) |
| Website ID | `WEBSITE_ID` |

**Obtenir vos clés :** [developers.cj.com](https://developers.cj.com/account/personal-access-tokens)

### Clickbank

| Paramètre | Valeur dans .env |
|-----------|-----------------|
| Nickname | `NICKNAME` |
| API Key | `API_Key` |

**Obtenir vos clés :** [accounts.clickbank.com](https://accounts.clickbank.com)

### Sovrn / VigLink

| Paramètre | Valeur dans .env |
|-----------|-----------------|
| API Key | `API_KEY` (de86ae09...) |
| Secret Key | `SECRET_KEY` (ab8a43d7...) |

**Obtenir vos clés :** [platform.sovrn.com](https://platform.sovrn.com/commerce/settings/site)

---

## Créer un article produit

### Méthode 1 : Importer depuis un module affilié

1. **Articles → Ajouter**
2. Donner un titre précis : ex. _"iPhone 15 Pro 256Go Noir"_
3. Ajouter une description dans l'éditeur
4. Descendre jusqu'à la **métabox Content Egg** (en bas de page)
5. Sélectionner le module : **CjProducts**, **Clickbank**, ou **Viglink**
6. Entrer le mot-clé : _"iPhone 15 Pro"_
7. Cliquer **Update** → les offres affiliées s'importent automatiquement
8. Ajouter une image à la une (photo du produit)
9. **Publier**

Le tableau de comparaison apparaît automatiquement sur la page du produit.

### Méthode 2 : Shortcode dans l'article

Insérer directement dans le contenu :
```
[ded_compare keyword="iPhone 15 Pro 256Go" module="CjProducts"]
```

### Méthode 3 : Autoblog (automatisation)

Dans **Content Egg → Autoblog**, configurer une liste de mots-clés. Content Egg crée automatiquement des articles avec les offres importées.

---

## Shortcodes disponibles

### `[ded_compare]` - Tableau de comparaison

```
[ded_compare keyword="Samsung Galaxy S24" module="CjProducts" limit="10"]
```

| Paramètre | Défaut | Description |
|-----------|--------|-------------|
| `keyword` | Titre de l'article | Terme de recherche |
| `module` | `CjProducts` | Module à utiliser |
| `template` | `block_price_comparison` | Template Content Egg |
| `limit` | `10` | Nombre de résultats |

---

### `[ded_search_bar]` - Barre de recherche

```
[ded_search_bar placeholder="Chercher un produit..." button_text="Comparer"]
```

---

### `[ded_top_deals]` - Grille de deals

```
[ded_top_deals count="6"]
```

---

### `[ded_price_box]` - Boîte de prix manuelle

```
[ded_price_box merchant="Amazon" price="1199" url="https://amzn.to/xxx" badge="-15%" shipping="Livraison gratuite"]
```

---

### `[ded_affiliate_disclaimer]` - Mention affilié

```
[ded_affiliate_disclaimer]
```
Affiche : _"Ce site contient des liens affiliés. Nous percevons une commission..."_

---

### Shortcodes Content Egg natifs

Content Egg Pro ajoute aussi ses propres shortcodes :

```
[content-egg module=CjProducts]
[content-egg module=Clickbank]
[content-egg-block template=offers_list]
[content-egg-block template=price_comparison]
[content-egg-block template=price_history]
[content-egg-block template=block_offers_tile]
[cegg_price_alert]
```

---

## Modules affiliés

### Fonctionnement d'un module

1. Chaque module se connecte à un réseau affilié via API
2. Il récupère les offres (prix, descriptions, liens) pour un mot-clé donné
3. Il génère des liens affiliés trackés pour les clics
4. Vous percevez une commission à chaque achat généré

### Modules configurés automatiquement

| Module | Réseau | Commission type |
|--------|--------|-----------------|
| CjProducts | CJ.com | 2-15% par vente |
| Clickbank | Clickbank.com | 30-75% (produits digitaux) |
| Viglink | Sovrn | Variable par lien |

### Ajouter d'autres modules

Via **Content Egg Pro → Settings → Modules** (si CJ, Amazon, etc. ont des clés API supplémentaires).

---

## Gestion du thème

### Structure du thème

```
wp-content/themes/dealeldorado/
├── style.css               ← Métadonnées thème
├── functions.php           ← Configuration + hooks
├── header.php              ← En-tête (logo, recherche, nav)
├── footer.php              ← Pied de page
├── home.php                ← Page d'accueil
├── single.php              ← Article produit
├── index.php               ← Archive / liste
├── search.php              ← Résultats de recherche
├── page.php                ← Page statique
├── 404.php                 ← Page d'erreur
└── assets/
    ├── css/custom.css      ← Styles personnalisés
    ├── js/main.js          ← JavaScript (recherche live, etc.)
    └── images/
        ├── logo.svg        ← Logo couleur
        └── logo-white.svg  ← Logo blanc (footer)
```

### Personnalisation des couleurs

Modifier dans `assets/css/custom.css` :
```css
:root {
  --ded-primary: #e85d04;    /* Orange principal */
  --ded-dark: #1a1a2e;       /* Bleu foncé */
  --ded-gold: #f4a261;       /* Or/accent */
}
```

### Menus à configurer

Dans **Apparence → Menus**, créer :
- **Menu Principal** : Accueil, Catégories, Blog, Contact
- **Menu Footer** : Mentions légales, CGU, Contact

### Widgets

Dans **Apparence → Widgets**, configurer :
- **Barre Latérale** : widgets pour les pages produits
- **Footer Zone 1-3** : liens et informations pied de page

---

## Fonctionnement des produits

### Cycle de vie d'un article produit

```
1. Création article → 2. Import offres via Content Egg → 3. Publication
        ↓
4. Visiteur arrive sur la page → 5. Tableau de prix affiché → 6. Clic sur "Voir l'offre"
        ↓
7. Redirection vers marchand → 8. Achat → 9. Commission versée
```

### Mise à jour automatique des prix

Content Egg Pro peut mettre à jour les prix automatiquement :
- **Content Egg → Autoupdate** : configurer la fréquence (horaire, quotidienne, etc.)
- Les prix sont mis à jour sans intervention manuelle

### Alertes prix

Les visiteurs peuvent créer une alerte prix :
1. Sur une page produit, cliquer "Créer une alerte"
2. Entrer son email
3. Recevoir un email automatique quand le prix baisse

Les alertes sont stockées dans **DealElDorado → Tableau de bord**.

### Historique des prix

Ajouter dans un article :
```
[content-egg-block template=price_history]
```
Affiche un graphique d'évolution du prix sur 30/90 jours.

---

## Mise en production

### Avant de passer sur dealeldorado.com

1. **Réglages → Général** : changer l'URL du site vers `https://dealeldorado.com`
2. **Réglages → Permaliens** : sélectionner "Nom de l'article"
3. Exporter la base de données MySQL
4. Uploader les fichiers sur le serveur
5. Importer la base de données
6. Mettre à jour le `wp-config.php` avec les credentials de production
7. Vérifier le `.env` est présent sur le serveur
8. Activer HTTPS (SSL gratuit Let's Encrypt)

### Fichier .env en production

⚠️ **Important** : Ajouter `/wp-content/uploads/` et `.env` dans `.gitignore` si vous utilisez Git.

Le `.env` doit être présent à la racine du WordPress en production pour que les APIs fonctionnent.

### SEO

- Installer **Yoast SEO** ou **Rank Math**
- Créer un sitemap XML
- Configurer les métas titre/description pour chaque article

### Performance

- Installer **WP Super Cache** ou **W3 Total Cache**
- Utiliser un CDN (Cloudflare gratuit)
- Optimiser les images avec **Smush** ou **ShortPixel**

---

## Dépannage

### Les modules Content Egg ne fonctionnent pas

1. Vérifier que Content Egg Pro est bien activé (Extensions)
2. Aller dans **DealElDorado → Configuration API** et vérifier les clés
3. Cliquer "Reconfigurer depuis .env" dans le tableau de bord
4. Vérifier les logs PHP dans `wp-content/debug.log` (activer WP_DEBUG)

### Le thème ne s'affiche pas correctement

1. Vider le cache navigateur (Ctrl+F5)
2. Vérifier que Bootstrap 5 est chargé (F12 → Console)
3. Désactiver temporairement les autres plugins pour tester les conflits

### Les prix ne s'affichent pas

1. Vérifier que les clés API CJ sont valides
2. Dans un article, utiliser la métabox Content Egg pour tester manuellement
3. Vérifier les quotas API (CJ : 1000 req/heure, Clickbank : illimité)

---

## Support

- **Documentation Content Egg Pro** : [ce-docs.keywordrush.com](https://ce-docs.keywordrush.com)
- **CJ Affiliate** : [help.cj.com](https://help.cj.com)
- **Sovrn Commerce** : [support.sovrn.com](https://support.sovrn.com)
- **Clickbank** : [support.clickbank.com](https://support.clickbank.com)

---

*Généré automatiquement pour DealElDorado v1.0.0 — 2026*
