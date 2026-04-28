# Restaurant SaaS — plateforme vitrine + Filament

Application **Laravel 13** + **Filament 5** pour la gestion de sites restaurants (thème **Bistro**), conforme au cahier des charges du dépôt (`Cahiers-des-Charges.md`).

## Phase J1 (livré)

- Projet Laravel avec **Filament** (`/admin`).
- **Multi-tenant minimal** : table `restaurants`, pivot `restaurant_user`, middleware `tenant` (résolution par `public_host` ou premier établissement en local).
- **Manifeste Bistro** : `themes/bistro/manifest.php` + helper `App\Theme\BistroManifest`.
- **Page d’accueil publique** : blocs Hero, Manifeste, Grille « Nos … », Mise en avant (structure §2.6 CDC).
- **Seed** : restaurant « Bistro démo » + utilisateur admin rattaché.

## Démarrage

Voir **[docs/DEVELOPPEMENT-LOCAL.md](docs/DEVELOPPEMENT-LOCAL.md)**.

Résumé :

```bash
composer install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed
npm install && npm run build
php artisan serve
```

- Public : http://127.0.0.1:8000  
- Admin : http://127.0.0.1:8000/admin — `admin@example.test` / `password`

## Tests

```bash
php artisan test
```
