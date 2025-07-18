# Project Setup & Environment Variables

## How to Start the App

1.  **Requirements:**
    -   Docker and Docker Compose must be installed.

2.  **Generate JWT SSL Keys (Backend for Development):**
    For JWT authentication to work in development, you need to generate SSL keys for the backend. Run the following command from your project root:
    ```bash
    docker compose exec backend php bin/console lexik:jwt:generate-keypair --overwrite
    ```
    This command will generate `private.pem` and `public.pem` files in `backend/config/jwt/`. Ensure these files are properly secured and not exposed.

3.  **Update JWT Passphrase (Development):**
    After generating the keys, update the `SYMFONY_JWT_PASSPHRASE_DEV` variable in your `.env` file with the passphrase you used during key generation.

4.  **Start the application in development mode:**
    ```sh
    docker compose up -d --build
    ```
    This will start both the backend (Symfony API) and frontend (Vue.js) services, along with other development dependencies.

## Development Environment Variables

To run the application in development, you need to set up environment variables. These are typically defined in a `.env` file in the project root. Here are the variables used in your development setup:

```
# Postgres DB for Symfony Backend
POSTGRES_DB_DEV="pgdbdev"
POSTGRES_USER_DEV="user"
POSTGRES_PASSWORD_DEV="user"

# MariaDB for Matomo
MARIADB_DEV="matomodev"
MARIADB_USER_DEV="user"
MARIADB_PASSWORD_DEV="user"
MARIADB_ROOT_PASSWORD_DEV="root"

# Database URL for Symfony
SYMFONY_DATABASE_URL_DEV="postgresql://user:user@backend-database:5432/pgdbdev?serverVersion=17.4"

# Symfony Setup & Mercure URL
APP_SECRET_DEV="NdXG2v7L8Jj0a2H2MEBQeJMC0FShpyRuxwXOxFYHQ"
SYMFONY_JWT_PASSPHRASE_DEV="123A"
SPEC_SHAPER_ENCRYPT_KEY_DEV="XR1kBhpusf1TAHgWptdrHW2smuwXOxFYHQ"
SYMFONY_CORS_ALLOW_ORIGIN_DEV="^http://localhost:8888"
MERCURE_URL_DEV="http://mercure:80/.well-known/mercure"
MERCURE_PUBLIC_URL_DEV="http://localhost:8888/mercure/.well-known/mercure"
MERCURE_JWT_KEY_DEV="kq9T8o2+9g6XwLr3Yz3AHzP4YjVxUq9v7H1fj9wXrZQ="
# Sentry DSN for Backend (only active in prod)
SENTRY_DSN_DEV=""

# Vite Public URLs (for Frontend)
VITE_PUBLIC_API_URL_DEV=http://localhost:8888/api
VITE_PUBLIC_IMAGES_URL_DEV=http://localhost:8888/api/images
VITE_PUBLIC_PFP_URL_DEV=http://localhost:8888/images/profile_pictures
VITE_MERCURE_PUBLIC_URL_DEV=http://localhost:8888/mercure/.well-known/mercure
VITE_MATOMO_HOST_DEV=http://localhost:8888/matomo
VITE_MATOMO_SITE_ID_DEV=1
# Sentry DSN for Frontend
VITE_SENTRY_DSN="YOUR_FRONTEND_SENTRY_DSN"
# Sentry Auth Token for Frontend build plugin
SENTRY_AUTH_TOKEN="YOUR_FRONTEND_SENTRY_AUTH_TOKEN"
```
*Replace `YOUR_FRONTEND_SENTRY_DSN` and `YOUR_FRONTEND_SENTRY_AUTH_TOKEN` with your actual Sentry DSN and auth token.*

## Access Points (Development)

-   **Frontend:** `http://localhost:8888`
-   **API Documentation (Swagger/OpenAPI):** `http://localhost:8888/api/docs`
-   **Matomo:** `http://localhost:8888/matomo`

## Running the Application in Production

To deploy and run the application in a production environment using Docker Compose:

1.  **Create a Production Environment File:**
    Create a `.env.prod` file in the project root. This file should contain all the production-specific environment variables, replacing the `_DEV` suffixes with `_PROD` and providing secure, production-ready values. For example:
    ```
    # Postgres DB for Symfony Backend
    POSTGRES_DB_PROD="your_prod_db_name"
    POSTGRES_USER_PROD="your_prod_db_user"
    POSTGRES_PASSWORD_PROD="your_strong_prod_db_password"

    # MariaDB for Matomo
    MARIADB_PROD="your_prod_matomo_db_name"
    MARIADB_USER_PROD="your_prod_matomo_user"
    MARIADB_PASSWORD_PROD="your_strong_prod_matomo_password"
    MARIADB_ROOT_PASSWORD_PROD="your_strong_prod_matomo_root_password"

    # Database URL for Symfony
    SYMFONY_DATABASE_URL_PROD="postgresql://your_prod_db_user:your_strong_prod_db_password@backend-database:5432/your_prod_db_name?serverVersion=17.4"

    # Symfony Setup & Mercure URL
    APP_SECRET_PROD="a_very_long_and_random_secret_for_prod"
    SYMFONY_CORS_ALLOW_ORIGIN_PROD="https://your-frontend-domain.com" # Your actual production frontend domain
    JWT_PASSPHRASE_PROD="a_very_strong_jwt_passphrase"
    SPEC_SHAPER_ENCRYPT_KEY_PROD="a_very_strong_encryption_key"
    MERCURE_URL_PROD="https://your-mercure-domain.com/.well-known/mercure" # Your actual production Mercure URL
    MERCURE_PUBLIC_URL_PROD="https://your-mercure-domain.com/.well-known/mercure" # Your actual production Mercure public URL
    MERCURE_JWT_KEY_PROD="a_very_strong_mercure_jwt_key"
    SENTRY_DSN_PROD="YOUR_PRODUCTION_BACKEND_SENTRY_DSN"

    # Vite Public URLs (for Frontend)
    VITE_PUBLIC_API_URL_PROD=https://your-api-domain.com/api
    VITE_PUBLIC_IMAGES_URL_PROD=https://your-api-domain.com/api/images
    VITE_PUBLIC_PFP_URL_PROD=https://your-api-domain.com/images/profile_pictures
    VITE_MERCURE_PUBLIC_URL_PROD=https://your-mercure-domain.com/.well-known/mercure
    VITE_MATOMO_HOST_PROD=https://your-matomo-domain.com/matomo
    VITE_MATOMO_SITE_ID_PROD=1
    VITE_SENTRY_DSN_PROD="YOUR_PRODUCTION_FRONTEND_SENTRY_DSN"
    SENTRY_AUTH_TOKEN_PROD="YOUR_PRODUCTION_FRONTEND_SENTRY_AUTH_TOKEN"
    ```
    **Important:** Never commit your `.env.prod` file to version control.

2.  **Generate JWT SSL Keys (Backend):**
    For JWT authentication to work in production, you need to generate SSL keys for the backend. Run the following command from your project root:
    ```bash
    docker compose exec backend php bin/console lexik:jwt:generate-keypair --overwrite
    ```
    This command will generate `private.pem` and `public.pem` files in `backend/config/jwt/`. Ensure these files are properly secured and not exposed.
    
4.  **Build and Start Production Services:**
    ```bash
    docker compose -f docker-compose.prod.yml --env-file .env.prod up --build -d
    ```
    This command will:
    -   Use the `docker-compose.prod.yml` file.
    -   Load environment variables from `.env.prod`.
    -   Build the production Docker images for your services (using `Dockerfile.prod` where applicable).
    -   Start the services in detached mode.

5.  **Access the Application:**
    The application will be accessible via your configured reverse proxy (e.g., Nginx) on the domain(s) you set up.