## Comment démarrer l'application

1. **Prérequis :**
- Docker et Docker Compose doivent être installés.

2. **Créer un fichier `.env` (Développement) :**
À la racine du projet, créez un fichier nommé `.env` et copiez-y les variables d'environnement de .env.example. Adaptez les valeurs si besoin.

3. **Installer les dépendances :**
- Dans le dossier `backend`, exécutez :
```sh
composer install
```
- Dans le dossier `frontend`, exécutez :
```sh
npm install
```

4. **Démarrer l'application en mode développement :**
```sh
docker compose up -d --build
```
Ceci démarrera les services backend (API Symfony) et frontend (Vue.js), ainsi que d'autres dépendances de développement.

5. **Créer la base de données (Backend) :**
Après avoir démarré les services, exécutez la commande suivante pour appliquer les migrations et créer la base de données :
```sh
docker compose exec backend php bin/console doctrine:migration:migrate
```
Cette commande exécutera les migrations Doctrine et créera la structure de la base de données nécessaire au fonctionnement de l'application.

6. **Générer les clés SSL JWT (Backend pour le développement) :**
Pour que l'authentification JWT fonctionne en développement, mettez à jour la variable `SYMFONY_JWT_PASSPHRASE_DEV` avec un mot de passe de votre choix puis exécutez la commande suivante depuis la racine de votre projet :
```bash
docker compose exec backend php bin/console lexik:jwt:generate-keypair --overwrite
```
Cette commande générera les fichiers `private.pem` et `public.pem` dans `backend/config/jwt/`.


7. **Installer le modèle (Mistral) :**
le container Ollama a besoin d'un modèle pour génerer les quiz. Cette application utilise Mistral:
```bash
docker exec ollama_quiz ollama pull mistral
```
L'installation du modèle peut prendre quelques temps.

8. **Charger les fixtures de test :**
Exécutez :
```bash
docker compose exec backend php bin/console doctrine:fixtures:load --no-interaction
```
Attention : cela effacera les données existantes dans la base de données.

## Points d'accès (Développement)

- **Frontend :** `http://localhost:8888`
- **Documentation API (Swagger/OpenAPI) :** `http://localhost:8888/api/docs`
- **Matomo :** `http://localhost:8888/matomo` (il est possible d'être redirigé la première fois, réaccédez au lien dans ce cas)

## Actions possibles sur l'application

* **Créer un compte :** Utilisez le lien "register" dans le menu.
* **Se connecter :** Utilisez le lien "login" dans le menu.
* **Configurer son compte (via le profil) :**
* L'option "edit profile" permet de changer son pseudonyme et/ou son image de profile.
* L'option "friends" liste les amis de l'utilisateur. Il est ensuite possible d'envoyer une requête d'ami via le bouton "Send Friend Request".
* Il est également possible de supprimer des amis depuis cette même liste.
* L'option "enable Two-Factor Authentication" permet de configurer le 2FA sur son compte. Au prochain login, un code temporaire sera demandé à partir du secret généré. Il est possible de désactiver le 2FA en rappuyant sur le même bouton.
* Il est également possible de se déconnecter à partir de ce menu.
* **Lister ses notifications :** Appuyez sur le bouton "cloche" à côté de l'icône du profile. Les notifications de l'utilisateur sont listées (friend requests et invitations à des groupes). Il est possible de les accepter ou de les refuser. L'utilisateur est dynamiquement notifié de son nombre de notifications au-dessus de l'icône de cloche.
* **Créer un groupe et un quiz :** Utilisez l'option "Create".
* **Inviter des amis à son groupe :** Utilisez le bouton "Invite Friends" dans un groupe.
* **Rejoindre un groupe de plusieurs façons :**
* À partir de la homepage, en précisant le code d'un groupe.
* À partir du menu, en appuyant sur "join" puis en rejoignant un groupe public.
* À partir d'une invitation.
* **Jouer à un quiz:** Attendre la création du quiz (peut être lente en fonction de l'ordinateur).

## Exécution de l'application en production

Pour déployer et exécuter l'application dans un environnement de production à l'aide de Docker Compose :

1. **Créer un fichier d'environnement de production (`.env.prod`) :**
Assure vous d'avoir un fichier .env à la racine du projet. Ce fichier doit contenir toutes les variables d'environnement spécifiques à la production, en remplaçant les suffixes `_DEV` par `_PROD` et en fournissant des valeurs sécurisées et prêtes pour la production. Par exemple :
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
**Important :** Assurez-vous que les variables suivantes sont gardées secrètes et ne sont pas exposées publiquement : `POSTGRES_PASSWORD_PROD`, `MARIADB_PASSWORD_PROD`, `MARIADB_ROOT_PASSWORD_PROD`, `SYMFONY_DATABASE_URL_PROD` (si elle contient des identifiants), `APP_SECRET_PROD`, `JWT_PASSPHRASE_PROD`, `SPEC_SHAPER_ENCRYPT_KEY_PROD`, `MERCURE_JWT_KEY_PROD`, `SENTRY_AUTH_TOKEN_PROD`.

2. **Générer les clés SSL JWT (Backend) :**
Pour que l'authentification JWT fonctionne en production, vous devez générer des clés SSL pour le backend. Exécutez la commande suivante depuis la racine de votre projet :
```bash
docker compose exec backend php bin/console lexik:jwt:generate-keypair --overwrite
```
Cette commande générera les fichiers `private.pem` et `public.pem` dans `backend/config/jwt/`. Assurez-vous que ces fichiers sont correctement sécurisés et non exposés.

3. **Créer la base de données (Backend) :**
Après avoir démarré les services, exécutez la commande suivante pour appliquer les migrations et créer la base de données :
```sh
docker compose exec backend php bin/console doctrine:migration:migrate
```
Cette commande exécutera les migrations Doctrine et créera la structure de la base de données nécessaire au fonctionnement de l'application.

4. **Construire et démarrer les services de production :**
```bash
docker compose -f docker-compose.prod.yml up --build -d
```
Cette commande :
- Utilisera le fichier `docker-compose.prod.yml`.
- Construira les images Docker de production pour vos services (en utilisant `Dockerfile.prod` le cas échéant).
- Démarrera les services en mode détaché.

5. **Accéder à l'application :**
L'application sera accessible sur le(s) domaine(s) que vous avez configuré(s).

**Conseil pour le déploiement :** Pour une gestion plus robuste des environnements de production, il est recommandé d'utiliser [Docker Contexts](https://docs.docker.com/engine/context/working-with-contexts/) pour déployer vos applications sur des hôtes distants.
