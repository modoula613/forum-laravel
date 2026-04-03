# Deployment Guide

Ce guide part du principe que le projet est deploye sur un VPS Linux avec Nginx, PHP-FPM et MySQL ou PostgreSQL.

## 1. Prerequis serveur

Installe sur le serveur :

- PHP 8.2 ou plus recent
- Composer
- Nginx
- MySQL ou PostgreSQL
- Node.js 20+ et npm
- Supervisor
- Git

## 2. Recuperer le projet

Clone le depot dans un dossier du type :

```bash
/var/www/forum-app
```

Puis :

```bash
cd /var/www/forum-app
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

## 3. Fichier `.env`

Cree le fichier `.env` a partir de `.env.example`, puis adapte au minimum :

```env
APP_NAME=Sphere
APP_ENV=production
APP_DEBUG=false
APP_URL=https://ton-domaine.com

APP_LOCALE=fr
APP_FALLBACK_LOCALE=fr
APP_FAKER_LOCALE=fr_FR

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=forum_app
DB_USERNAME=forum_user
DB_PASSWORD=mot_de_passe_solide

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database

MAIL_MAILER=smtp
MAIL_HOST=smtp.ton-fournisseur.com
MAIL_PORT=587
MAIL_USERNAME=ton_login
MAIL_PASSWORD=ton_mot_de_passe
MAIL_FROM_ADDRESS=no-reply@ton-domaine.com
MAIL_FROM_NAME="Sphere"

GNEWS_API_KEY=ta_cle_api
GNEWS_ENDPOINT=https://gnews.io/api/v4
GNEWS_LANG=fr
GNEWS_COUNTRY=fr
GNEWS_MAX=25
```

## 4. Initialiser Laravel

Une fois `.env` pret :

```bash
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan optimize:clear
php artisan optimize
```

## 5. Droits d'ecriture

Le serveur web doit pouvoir ecrire dans :

- `storage/`
- `bootstrap/cache/`

Exemple :

```bash
sudo chown -R www-data:www-data /var/www/forum-app/storage /var/www/forum-app/bootstrap/cache
sudo chmod -R 775 /var/www/forum-app/storage /var/www/forum-app/bootstrap/cache
```

## 6. Nginx

Utilise le fichier :

[`deploy/nginx/sphere.conf.example`](./nginx/sphere.conf.example)

Important :

- le `root` doit pointer vers `public/`
- adapte le nom de domaine
- adapte la version PHP-FPM si necessaire

## 7. Scheduler Laravel

Ajoute cette ligne cron :

```cron
* * * * * cd /var/www/forum-app && php artisan schedule:run >> /dev/null 2>&1
```

Le scheduler est necessaire notamment pour :

- la synchronisation des actualites `news:sync`

## 8. Queue worker

Utilise Supervisor avec :

[`deploy/supervisor/laravel-worker.conf.example`](./supervisor/laravel-worker.conf.example)

Puis :

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start forum-app-worker:*
```

## 9. Commandes utiles en mise a jour

Quand tu deploies une nouvelle version :

```bash
cd /var/www/forum-app
git pull
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan migrate --force
php artisan optimize:clear
php artisan optimize
sudo supervisorctl restart forum-app-worker:*
```

## 10. Ce que je ne peux pas faire a ta place

Ces actions demandent un acces serveur ou DNS que je n’ai pas ici :

- acheter ou connecter le nom de domaine
- creer la base de donnees de production
- installer Nginx / PHP / MySQL sur le serveur
- configurer le certificat SSL
- ouvrir les ports et regler le firewall
- creer le cron systeme
- activer Supervisor

## 11. Checklist finale

Avant d’ouvrir le site publiquement, verifie :

- `APP_ENV=production`
- `APP_DEBUG=false`
- le domaine pointe vers le serveur
- Nginx sert bien `public/`
- `php artisan migrate --force` est passe
- `php artisan storage:link` est fait
- le cron Laravel tourne
- le worker de queue tourne
- le mail fonctionne
- `GNEWS_API_KEY` est configuree
