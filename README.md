# Configuration du Projet et Variables d'Environnement


## Comment démarrer l'application

1.  **Prérequis :**
    -   Docker et Docker Compose doivent être installés.


2.  **Créer un fichier `.env` (Développement) :**
    À la racine du projet, créez un fichier nommé `.env` et copiez-y les variables d'environnement de .env.example. Adaptez les valeurs si besoin.

3.  **Installer les dépendances :**
    - Dans le dossier `backend`, exécutez :
      ```sh
      composer install
      ```
    - Dans le dossier `frontend`, exécutez :
      ```sh
      npm install
      ```

4.  **Démarrer l'application en mode développement :**
    ```sh
    docker compose up -d --build
    ```
    Ceci démarrera les services backend (API Symfony) et frontend (Vue.js), ainsi que d'autres dépendances de développement.


5.  **Générer les clés SSL JWT (Backend pour le développement) :**
    Pour que l'authentification JWT fonctionne en développement, mettez à jour la variable `SYMFONY_JWT_PASSPHRASE_DEV` avec un mot de passe de votre choix puis exécutez la commande suivante depuis le racine de votre projet :
    ```bash
    docker compose exec backend php bin/console lexik:jwt:generate-keypair --overwrite
    ```
    Cette commande générera les fichiers `private.pem` et `public.pem` dans `backend/config/jwt/`.

6.  *(Optionnel)* **Charger les fixtures de test :**
    Si vous souhaitez peupler la base de données avec des données de test, exécutez :
    ```bash
    docker compose exec backend php bin/console doctrine:fixtures:load --no-interaction
    ```
    Attention : cela effacera les données existantes dans la base de données.

## Points d'accès (Développement)

-   **Frontend :** `http://localhost:8888`
-   **Documentation API (Swagger/OpenAPI) :** `http://localhost:8888/api/docs`
-   **Matomo :** `http://localhost:8888/matomo`

## Variables d'environnement de développement

Pour exécuter l'application en développement, vous devez configurer les variables d'environnement. Celles-ci sont généralement définies dans un fichier `.env` à la racine du projet. Voici les variables utilisées dans votre configuration de développement :

```
# Base de données Postgres pour le Backend Symfony
POSTGRES_DB_DEV="pgdbdev"
POSTGRES_USER_DEV="user"
POSTGRES_PASSWORD_DEV="user"

# MariaDB pour Matomo
MARIADB_DEV="matomodev"
MARIADB_USER_DEV="user"
MARIADB_PASSWORD_DEV="user"
MARIADB_ROOT_PASSWORD_DEV="root"

# URL de la base de données pour Symfony
SYMFONY_DATABASE_URL_DEV="postgresql://user:user@backend-database:5432/pgdbdev?serverVersion=17.4"

# Configuration Symfony & URL Mercure
APP_SECRET_DEV="NdXG2v7L8Jj0a2H2MEBQeJMC0FShpyRuxwXOxFYHQ"
SYMFONY_JWT_PASSPHRASE_DEV="123A"
SPEC_SHAPER_ENCRYPT_KEY_DEV="XR1kBhpusf1TAHgWptdrHW2smuwXOxFYHQ"
SYMFONY_CORS_ALLOW_ORIGIN_DEV="^http://localhost:8888"
MERCURE_URL_DEV="http://mercure:80/.well-known/mercure"
MERCURE_PUBLIC_URL_DEV="http://localhost:8888/mercure/.well-known/mercure"
MERCURE_JWT_KEY_DEV="kq9T8o2+9g6XwLr3Yz3AHzP4YjVxUq9v7H1fj9wXrZQ="
# DSN Sentry pour le Backend (actif uniquement en production)
SENTRY_DSN_DEV=""

# URLs publiques Vite (pour le Frontend)
VITE_PUBLIC_API_URL_DEV=http://localhost:8888/api
VITE_PUBLIC_IMAGES_URL_DEV=http://localhost:8888/api/images
VITE_PUBLIC_PFP_URL_DEV=http://localhost:8888/images/profile_pictures
VITE_MERCURE_PUBLIC_URL_DEV=http://localhost:8888/mercure/.well-known/mercure
VITE_MATOMO_HOST_DEV=http://localhost:8888/matomo
VITE_MATOMO_SITE_ID_DEV=1
# DSN Sentry pour le Frontend
VITE_SENTRY_DSN="YOUR_FRONTEND_SENTRY_DSN"
# Jeton d'authentification Sentry pour le plugin de build Frontend
SENTRY_AUTH_TOKEN="YOUR_FRONTEND_SENTRY_AUTH_TOKEN"
```
*Remplacez `YOUR_FRONTEND_SENTRY_DSN` et `YOUR_FRONTEND_SENTRY_AUTH_TOKEN` par votre DSN Sentry et votre jeton d'authentification réels pour le frontend.*

## Exécution de l'application en production

Pour déployer et exécuter l'application dans un environnement de production à l'aide de Docker Compose :

1.  **Créer un fichier d'environnement de production :**
    Créez un fichier `.env.prod` à la racine du projet. Ce fichier doit contenir toutes les variables d'environnement spécifiques à la production, en remplaçant les suffixes `_DEV` par `_PROD` et en fournissant des valeurs sécurisées et prêtes pour la production. Par exemple :
    ```
    # Base de données Postgres pour le Backend Symfony
    POSTGRES_DB_PROD="your_prod_db_name"
    POSTGRES_USER_PROD="your_prod_db_user"
    POSTGRES_PASSWORD_PROD="your_strong_prod_db_password"

    # MariaDB pour Matomo
    MARIADB_PROD="your_prod_matomo_db_name"
    MARIADB_USER_PROD="your_prod_matomo_user"
    MARIADB_PASSWORD_PROD="your_strong_prod_matomo_password"
    MARIADB_ROOT_PASSWORD_PROD="your_strong_prod_matomo_root_password"

    # URL de la base de données pour Symfony
    SYMFONY_DATABASE_URL_PROD="postgresql://your_prod_db_user:your_strong_prod_db_password@backend-database:5432/your_prod_db_name?serverVersion=17.4"

    # Configuration Symfony & URL Mercure
    APP_SECRET_PROD="a_very_long_and_random_secret_for_prod"
    SYMFONY_CORS_ALLOW_ORIGIN_PROD="https://your-frontend-domain.com" # Votre domaine frontend de production réel
    JWT_PASSPHRASE_PROD="a_very_strong_jwt_passphrase"
    SPEC_SHAPER_ENCRYPT_KEY_PROD="a_very_strong_encryption_key"
    MERCURE_URL_PROD="https://your-mercure-domain.com/.well-known/mercure" # Votre URL Mercure de production réelle
MERCURE_PUBLIC_URL_PROD="https://your-mercure-domain.com/.well-known/mercure" # Votre URL publique Mercure de production réelle
    MERCURE_JWT_KEY_PROD="a_very_strong_mercure_jwt_key"
    SENTRY_DSN_PROD="YOUR_PRODUCTION_BACKEND_SENTRY_DSN"

    # URLs publiques Vite (pour le Frontend)
    VITE_PUBLIC_API_URL_PROD=https://your-api-domain.com/api
    VITE_PUBLIC_IMAGES_URL_PROD=https://your-api-domain.com/api/images
    VITE_PUBLIC_PFP_URL_PROD=https://your-api-domain.com/images/profile_pictures
    VITE_MERCURE_PUBLIC_URL_PROD=https://your-mercure-domain.com/.well-known/mercure
    VITE_MATOMO_HOST_PROD=https://your-matomo-domain.com/matomo
    VITE_MATOMO_SITE_ID_PROD=1
    VITE_SENTRY_DSN_PROD="YOUR_PRODUCTION_FRONTEND_SENTRY_DSN"
    SENTRY_AUTH_TOKEN_PROD="YOUR_PRODUCTION_FRONTEND_SENTRY_AUTH_TOKEN"
    ```
    **Important :** Ne jamais commettre votre fichier `.env.prod` dans le contrôle de version.


2.  **Générer les clés SSL JWT (Backend) :**
    Pour que l'authentification JWT fonctionne en production, vous devez générer des clés SSL pour le backend. Exécutez la commande suivante depuis la racine de votre projet :
    ```bash
    docker compose exec backend php bin/console lexik:jwt:generate-keypair --overwrite
    ```
    Cette commande générera les fichiers `private.pem` et `public.pem` dans `backend/config/jwt/`. Assurez-vous que ces fichiers sont correctement sécurisés et non exposés.

3.  *(Optionnel)* **Charger les fixtures de test :**
    Si vous souhaitez peupler la base de données de production avec des  utilisateurs de test, exécutez :
    ```bash
    docker compose exec backend php bin/console doctrine:fixtures:load --no-interaction
    ```
    Attention : cela effacera les données existantes dans la base de données.
    
4.  **Construire et démarrer les services de production :**
    ```bash
    docker compose -f docker-compose.prod.yml --env-file .env.prod up --build -d
    ```
    Cette commande :
    -   Utilisera le fichier `docker-compose.prod.yml`.
    -   Chargera les variables d'environnement depuis `.env.prod`.
    -   Construira les images Docker de production pour vos services (en utilisant `Dockerfile.prod` le cas échéant).
    -   Démarrera les services en mode détaché.

5.  **Accéder à l'application :**
    L'application sera accessible via votre proxy inverse configuré (par exemple, Nginx) sur le(s) domaine(s) que vous avez configuré(s).
