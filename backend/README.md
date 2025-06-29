# Symfony Backend API

## Available Routes

### Authentication & User

- **POST /api/register**
  - Registers a new user.
  - Body: `{ "email": "string", "password": "string" }`
  - Response: `{ "status": "User created" }` or error message.

- **POST /api/login**
  - Authenticates a user and returns a JWT token.
  - Body: `{ "email": "string", "password": "string" }`
  - Response: `{ "token": "JWT_TOKEN" }` on success.

- **GET /api/user/info**
  - Returns the current authenticated user's username and email.
  - Requires: `Authorization: Bearer <JWT_TOKEN>` header.
  - Response: `{ "username": "string", "email": "string" }` or 401 if not authenticated.

- **PUT /api/username**
  - Updates the current authenticated user's username.
  - Requires: `Authorization: Bearer <JWT_TOKEN>` header.
  - Body: `{ "username": "string" }`
  - Response: `{ "status": "Username updated", "username": "string" }` or error message.

## Notes
- All `/api/*` routes except `/api/register` and `/api/login` require authentication (JWT Bearer token).
- Register and login are open to anonymous users.
- Built with Symfony, API Platform, and LexikJWTAuthenticationBundle.

## How to Test
- Use curl, Postman, or Thunder Client to test the endpoints.
- Register, then login to get a token, then use the token to access protected endpoints.

---

For more details, see the source code in `src/Controller/` and your `security.yaml` configuration.
