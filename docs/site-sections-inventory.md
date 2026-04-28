# Inventaire des sections publiques

Ce document recense les pages publiques du site, les sections affichées et leur source de données actuelle.

## Tableau de synthèse

| Route | Vue Blade | Sections / blocs | Source actuelle |
|---|---|---|---|
| `/` | `site.home` | `hero`, `manifesto`, `carte_narrative`, `espaces`, `spotlight`, `reviews_widget`, `practical` | Majoritairement hardcodé dans `HomeController::$content`, avec éléments dynamiques (`Restaurant`, `openingHours`) |
| `/carte` | `site.carte` | Header + catégories + items | Données métier (`MenuCategory`, `MenuItem`) |
| `/galerie` | `site.galerie` | Header + grille galerie + lightbox | Données métier (`GalleryMedia`) |
| `/contact` | `site.contact` | Intro + formulaire contact | Formulaire + données `Restaurant` |
| `/reservation` | `site.reservation` | Sélecteur service/date/créneau + infos client | Données métier (`BookingService`, `BookingSetting`, disponibilités API) |
| `/reservation/manage/{token}` | `site.reservation-manage` | Détail réservation + annulation/reprogrammation | Données métier (`Reservation`) |

## Détail par page

### 1) Accueil (`/`)

- **Vue**: `resources/views/site/home.blade.php`
- **Sections incluses**:
  - `site.blocks.hero`
  - `site.blocks.manifesto`
  - `site.blocks.carte-narrative`
  - `site.blocks.espaces-links`
  - `site.blocks.spotlight`
  - bloc inline `reviews_widget`
  - bloc inline `practical` (contact + horaires)
- **Source**:
  - `app/Http/Controllers/Site/HomeController.php`
  - tableau `$content` (texte/CTA/liens) majoritairement statique
  - `Restaurant` + `openingHours` pour `practical`
- **Constat**: principal point à rendre CMS.

### 2) Carte (`/carte`)

- **Vue**: `resources/views/site/carte.blade.php`
- **Sections**: catégories + plats par catégorie.
- **Source**: déjà backoffice via `MenuCategory` / `MenuItem`.

### 3) Galerie (`/galerie`)

- **Vue**: `resources/views/site/galerie.blade.php`
- **Sections**: grille images + lightbox.
- **Source**: déjà backoffice via `GalleryMedia`.

### 4) Contact (`/contact`)

- **Vue**: `resources/views/site/contact.blade.php`
- **Sections**: intro + formulaire + feedback succès/erreur.
- **Source**:
  - `Restaurant` pour les coordonnées
  - texte de page en partie fixe (à rendre CMS).

### 5) Réservation (`/reservation`)

- **Vue**: `resources/views/site/reservation.blade.php`
- **Sections**: service, date/horaire/couverts, infos client.
- **Source**:
  - `BookingService`, `BookingSetting`
  - API disponibilités (`ReservationController@availability`)
  - textes d’interface encore majoritairement fixes.

### 6) Gestion réservation client (`/reservation/manage/{token}`)

- **Vue**: `resources/views/site/reservation-manage.blade.php`
- **Sections**: détail réservation + annulation/reprogrammation.
- **Source**: `ReservationController` + `Reservation` (token sécurisé).

## Déjà configurable en backoffice

- Identité, contact, apparence, publication, horaires et exceptions (`Manage*` pages Filament).
- Carte/menu, galerie, réservations, paramètres réservation (`Resources` Filament).

## Non configurable (ou partiellement) à ce stade

- Textes et CTA éditoriaux de la home (`$content` dans `HomeController`).
- Intro/contact/réservation (copys de page), hors données métier.
