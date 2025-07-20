# Vue 3 + TypeScript + Vite

This template should help get you started developing with Vue 3 and TypeScript in Vite. The template uses Vue 3 `<script setup>` SFCs, check out the [script setup docs](https://v3.vuejs.org/api/sfc-script-setup.html#sfc-script-setup) to learn more.

Learn more about the recommended Project Setup and IDE Support in the [Vue Docs TypeScript Guide](https://vuejs.org/guide/typescript/overview.html#project-setup).

## Development Environment Variables

To run the frontend in development, ensure you have a `.env` file in the root of your project (or within the `frontend` directory, depending on your setup) with the following variables defined. These are typically sourced from your `docker-compose.yml` for development.

```
VITE_PUBLIC_API_URL=http://localhost:8888/api
VITE_PUBLIC_IMAGES_URL=http://localhost:8888/api/images
VITE_PUBLIC_PFP_URL=http://localhost:8888/images/profile_pictures
VITE_MERCURE_PUBLIC_URL=http://localhost:8888/mercure/.well-known/mercure
VITE_MATOMO_HOST=http://localhost:8888/matomo
VITE_MATOMO_SITE_ID=1
VITE_SENTRY_DSN="YOUR_SENTRY_DSN_HERE"
SENTRY_AUTH_TOKEN="YOUR_SENTRY_AUTH_TOKEN_HERE"
```
*Replace `"YOUR_SENTRY_DSN_HERE"` and `"YOUR_SENTRY_AUTH_TOKEN_HERE"` with your actual Sentry DSN and auth token.*

## Running the Application in Production

To run the application in a production-like environment using Docker Compose:

1.  **Ensure your production environment variables are set:** Create a `.env.prod` file (or similar, as configured in your `docker-compose.prod.yml`) with all the `_PROD` suffixed variables (e.g., `POSTGRES_USER_PROD`, `VITE_SENTRY_DSN_PROD`, etc.) and their secure production values.
2.  **Build and start the services:**
    ```bash
    docker-compose -f docker-compose.prod.yml up --build -d
    ```
    This command will build the production Docker images for your services and start them in detached mode.
3.  **Access the application:** The application should be accessible via the `reverse-proxy` service, typically on port `8888` as configured in your `docker-compose.prod.yml`.