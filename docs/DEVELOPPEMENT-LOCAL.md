# Développement local

Le produit peut être développé **entièrement en local** avant mise en production (cf. discussion projet).

## Prérequis

- PHP **8.3+** (idéalement avec l’extension **intl** activée — requise par Filament ; `brew install shivammathur/php/php@8.4` avec intl ou activer `extension=intl` dans `php.ini`).
- Composer, **Node.js 20.19+ ou 22.12+** et npm (requis par Vite 8 — voir `package.json` / `engines`).
- SQLite (défaut `.env`) ou MySQL si vous préférez aligner la prod.

Si `npm run build` échoue sur **rolldown / binding darwin-arm64**, supprimez `node_modules` et `package-lock.json`, puis réinstallez avec une version Node conforme :

```bash
rm -rf node_modules package-lock.json
npx -p node@22 npm install
npx -p node@22 npm run build
```

## Démarrage rapide

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

- **Site public** : [http://127.0.0.1:8000](http://127.0.0.1:8000)  
- **Filament (admin)** : [http://127.0.0.1:8000/admin](http://127.0.0.1:8000/admin)

Compte seed : **[admin@example.test](mailto:admin@example.test)** / **password**

## Lancer le projet en développement

Après le premier démarrage, utilisez deux terminaux :

```bash
php artisan serve
```

```bash
npm run dev
```

Pour lancer serveur Laravel, queue, logs et Vite ensemble, vous pouvez aussi utiliser :

```bash
composer run dev
```

## E-mail

En local, utiliser `MAIL_MAILER=log` (ou Mailpit / Mailhog) au lieu de SMTP Google. La prod utilisera **SMTP Google** (CDC §6.1).

## Multi-tenant

Tant que `public_host` ne correspond pas au `Host` de la requête, l’application utilise le **premier** restaurant (ordre `id`) — pratique pour `localhost` / `127.0.0.1`.

## Composer sans intl (temporaire)

Si `composer install` échoue sur `ext-intl` :

```bash
composer install --ignore-platform-req=ext-intl
```

Installez intl dès que possible pour exécuter Filament correctement.

## Pousser sur GitHub

Le dépôt est connecté au remote `origin`. Vérifiez-le avec :

```bash
git remote -v
```

Workflow recommandé :

```bash
git checkout -b feature/ma-fonctionnalite
git status
git add .
git commit -m "Décrire le changement"
git push -u origin feature/ma-fonctionnalite
```

Ouvrez ensuite une pull request GitHub vers `main`.

Ne versionnez pas `.env`, `vendor/`, `node_modules/` ou `public/build/` : ils sont exclus par le `.gitignore` et se reconstruisent avec `composer install`, `npm install` et `npm run build`.