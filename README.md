# Project Setup & Environment Variables

## How to Start the App

1. **Requirements:**
   - Docker and Docker Compose must be installed.

2. **Start the application:**
   ```sh
   docker compose up -d --build
   ```
   This will start both the backend (Symfony API) and frontend (Vue.js) services.

## Environment Variables

The following variables are defined in your `.env` file:

- `POSTGRES_DB_DEV`: Name of the development PostgreSQL database
- `POSTGRES_USER_DEV`: Username for the development database
- `POSTGRES_PASSWORD_DEV`: Password for the development database
- `DATABASE_URL_DEV`: Connection string for the development database
- `APP_SECRET_DEV`: Symfony app secret (used for security, e.g., CSRF tokens)
- `JWT_PASSPHRASE`: Passphrase for JWT authentication
- `VUE_PUBLIC_API_URL`: Public URL for the API (used by the frontend)
- `VUE_SERVER_API_URL`: Internal URL for the API (used by the frontend server)
- `VUE_PUBLIC_IMAGES_URL`: Public URL for uploaded images
