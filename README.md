# ChefOp - Portfolio & CMS System

Portfolio créatif et système de gestion de contenu construit avec Laravel 12 + React 19.

## 🚀 Installation & Setup

### Prérequis
- PHP 8.2+
- Composer
- Node.js 18+ & npm
- SQLite

### Installation initiale

```bash
# 1. Cloner le repo et installer les dépendances
composer install
npm install

# 2. Configuration de l'environnement
cp .env.example .env
php artisan key:generate

# 3. Base de données (SQLite)
touch database/database.sqlite
php artisan migrate --seed

# 4. Lier le stockage public
php artisan storage:link

# 5. Build des assets
npm run build
```

## 🏃‍♂️ Commandes de Développement

### Démarrage rapide

```bash
# Lancer l'environnement complet (recommandé)
composer dev
# Lance : Laravel dev server (8000) + Queue worker + Logs + Vite

# Lancer avec SSR
composer dev:ssr
```

### Commandes individuelles

```bash
# Backend
php artisan serve                    # Serveur Laravel (http://localhost:8000)

# Frontend
npm run dev                          # Vite dev server avec HMR
npm run build                        # Build de production
npm run build:ssr                    # Build SSR

# Code Quality
npm run lint                         # ESLint avec auto-fix
npm run format                       # Prettier formatting
npm run format:check                 # Vérifier le formatage
npm run types                        # TypeScript type checking
vendor/bin/pint                      # PHP CS Fixer (Laravel Pint)
```

## 🗄️ Base de Données

### Migrations

```bash
# Exécuter les migrations
php artisan migrate

# Créer une nouvelle migration
php artisan make:migration create_exemple_table

# Reset complet + seed
php artisan migrate:fresh --seed

# Rollback dernière migration
php artisan migrate:rollback

# Reset et relancer
php artisan migrate:refresh --seed
```

### Seeders

```bash
# Lancer tous les seeders
php artisan db:seed

# Lancer un seeder spécifique
php artisan db:seed --class=CategorySeeder

# Créer un nouveau seeder
php artisan make:seeder ExempleSeeder
```

## 🧪 Tests

```bash
# Lancer tous les tests
php artisan test

# Tests par type
php artisan test --filter=Feature
php artisan test --filter=Unit

# Test spécifique
php artisan test --filter=TestName
php artisan test tests/Feature/DashboardTest.php

# Avec couverture
php artisan test --coverage
```

## 📦 Commandes Artisan Utiles

```bash
# Cache
php artisan cache:clear              # Vider le cache
php artisan config:clear             # Vider config cache
php artisan view:clear               # Vider view cache
php artisan route:clear              # Vider route cache

# Optimisation (production)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Générer des classes
php artisan make:model NomModele -m         # Modèle + migration
php artisan make:controller NomController   # Contrôleur
php artisan make:request NomRequest         # Form request
php artisan make:middleware NomMiddleware   # Middleware

# Routes
php artisan route:list                      # Liste toutes les routes
php artisan route:list --except-vendor      # Sans vendors
```

## 📁 Structure du Projet

### Backend (Laravel)

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Dashboard/              # Contrôleurs admin
│   │   │   ├── PageController      # Gestion des pages
│   │   │   ├── ProjectController   # Gestion des projets
│   │   │   ├── MediaController     # Gestion des médias
│   │   │   ├── CategoryController  # Gestion des catégories
│   │   │   ├── MenuController      # Gestion du menu
│   │   │   └── FolderController    # Gestion des dossiers médias
│   │   └── Settings/               # Paramètres utilisateur
│   └── Middleware/
│       ├── AdminMiddleware         # Vérification admin (is_admin)
│       ├── HandleInertiaRequests   # Données partagées Inertia
│       └── HandleAppearance        # Thème (light/dark/system)
├── Models/
│   ├── User                        # is_admin field
│   ├── Project                     # is_selected_work, is_locked
│   ├── Page                        # project_id XOR category_id
│   ├── Media                       # folder_id (nullable)
│   ├── Folder                      # parent_id (auto-référence)
│   ├── Category
│   └── MenuLink                    # page_id OR category_id
└── ...

database/
├── migrations/
└── seeders/
```

### Frontend (React + Blade)

```
resources/
├── views/                          # Templates Blade
│   ├── dashboard/                  # Pages admin (Blade)
│   │   ├── media/
│   │   └── projects/
│   ├── layouts/                    # Layouts de base
│   └── components/                 # Composants Blade réutilisables
├── js/
│   ├── pages/                      # Pages React (Inertia)
│   │   ├── settings/               # UNIQUEMENT les settings utilisent React
│   │   └── auth/
│   ├── components/                 # Composants React
│   │   ├── ui/                     # shadcn/ui + Radix UI
│   │   └── ...
│   ├── layouts/                    # Layouts React
│   └── hooks/                      # Custom hooks
└── css/
    └── app.css                     # Tailwind CSS v4
```

## 🎯 Fonctionnalités Clés

### 1. Système de Projets
- **Projets standards** : Avec catégorie, médias, pages auto-générées
- **Projet "Stills"** : Projet spécial verrouillé (`is_locked=true`, `slug='stills'`)
- **Selected Work** : Projects marqués apparaissent sur la homepage
- **Auto-page création** : Une page est créée automatiquement avec chaque projet

### 2. Gestion des Médias avec Dossiers
- **Upload** : Images & vidéos (max 120 Mo, 10+ formats)
- **Dossiers** : Organisation hiérarchique (parent/enfant)
- **Drag & Drop** : Déplacer médias entre dossiers
- **Click to expand** : Voir le contenu des dossiers

### 3. Système de Menu Dynamique
- **MenuLink** : Liens vers pages OU catégories
- **Ordre personnalisable** : Champ `order` pour le tri
- **Gestion visuelle** : Interface de réorganisation

### 4. Responsive Masonry Grid
- **Algorithme intelligent** : Calcul des ratios d'images
- **3 breakpoints** : Mobile (2-3), Tablet (3-4), Desktop (4-5)
- **Rebalancing** : Évite les éléments seuls sur dernière ligne

### 5. Thème Light/Dark
- **3 modes** : Light, Dark, System
- **Cookie-based** : Préférence persistée
- **Middleware** : `HandleAppearance` distribue la préférence
- **Hook React** : `use-appearance` pour les composants

## 🔑 Comptes & Permissions

### Admin par défaut (seed)
```
Email: admin@example.com
Password: password
```

### Système de permissions
- **Admin** : `is_admin = 1` dans la table users
- **Middleware** : `AdminMiddleware` protège `/dashboard/*`
- **Vérification** : Email vérifié + Admin requis

## 📝 Rappels Importants

### ⚠️ À faire après chaque pull

```bash
composer install              # Si composer.json a changé
npm install                   # Si package.json a changé
php artisan migrate           # Si nouvelles migrations
npm run build                 # Si assets modifiés
php artisan config:clear      # Si .env modifié
```

### ⚠️ Avant de push

```bash
npm run types                 # Vérifier TypeScript
npm run format                # Formater le code
vendor/bin/pint               # Formater PHP
php artisan test              # Lancer les tests
```

### ⚠️ Permissions fichiers (Production)

```bash
# SQLite database
chmod 664 database/database.sqlite
chown www-data:www-data database/database.sqlite

# Storage
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### ⚠️ Uploads volumineux

Ajuster dans `php.ini` :
```ini
upload_max_filesize = 120M
post_max_size = 120M
memory_limit = 256M
max_execution_time = 300
```

Apache `.htaccess` :
```apache
LimitRequestBody 125829120  # 120 MB en bytes
```

## 🎨 Intégrations

### TinyMCE
- **Version** : 8
- **Build** : Copy skins automatique (`npm run copy-tinymce`)
- **Utilisation** : Rich text editor pour contenu

### Ziggy
- **Routes Laravel** disponibles en JavaScript
- **Usage** : `route('dashboard.media.index')`

### Inertia.js
- **Utilisé pour** : Settings pages uniquement
- **SSR** : Support disponible via `resources/js/ssr.tsx`

## 🐛 Debugging

### Logs Laravel
```bash
tail -f storage/logs/laravel.log

# Avec composer dev, les logs s'affichent en temps réel
```

### Vite HMR
- Port par défaut : `5173`
- Si erreurs : Vérifier que le serveur Vite tourne (`npm run dev`)

### Database
```bash
# Inspecter avec sqlite3
sqlite3 database/database.sqlite
.tables
.schema media
SELECT * FROM folders;
```

## 🚢 Déploiement

```bash
# 1. Build production
npm run build

# 2. Optimiser Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 3. Migrations
php artisan migrate --force

# 4. Permissions
chmod -R 775 storage bootstrap/cache
```

## 📚 Documentation

- **Laravel** : https://laravel.com/docs/11.x
- **React** : https://react.dev
- **Inertia.js** : https://inertiajs.com
- **Tailwind CSS** : https://tailwindcss.com
- **Radix UI** : https://www.radix-ui.com

## 🤝 Contribution

1. Créer une branche depuis `main`
2. Faire vos modifications
3. Tester (`php artisan test`, `npm run types`)
4. Formater (`npm run format`, `vendor/bin/pint`)
5. Créer une Pull Request vers `main`

## 📄 Licence

Propriétaire - Tous droits réservés.
