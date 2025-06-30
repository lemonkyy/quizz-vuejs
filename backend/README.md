# Symfony Backend API

## Available Routes

### User

- **POST /api/register**
  - Registers a new user.
  - Body: `{ "email": "string", "password": "string" }`
  - Response: `{ "status": "User created" }` or error message.

- **GET /api/user/info**
  - Returns the current authenticated user's username and email.
  - Requires: `Authorization: Bearer <JWT_TOKEN>` header.
  - Response: `{ "username": "string", "email": "string" }` or 401 if not authenticated.

- **PUT /api/user/username**
  - Updates the current authenticated user's username.
  - Requires: `Authorization: Bearer <JWT_TOKEN>` header.
  - Body: `{ "username": "string" }`
  - Response: `{ "status": "Username updated", "username": "string" }` or error message.

### Group

- **GET /api/user/group**
  - Gets the current group of the user.
  - Requires: `Authorization: Bearer <JWT_TOKEN>` header.
  - Response: `{ "id": "group_id", ... }` or 401 if not authenticated.

- **POST /api/group/create**
  - Creates a new group (user becomes owner).
  - Requires: `Authorization: Bearer <JWT_TOKEN>` header.
  - Body: `{ "isPublic": true|false }` (optional)
  - Response: `{ "status": "Group created", "group_id": "string" }` or error message.

- **DELETE /api/group/delete**
  - Soft-deletes the group where the user is the owner.
  - Requires: `Authorization: Bearer <JWT_TOKEN>` header.
  - Response: `{ "status": "Group deleted", "group_id": "string" }` or error message.

- **POST /api/group/kick**
  - Kicks a user from the group (owner only).
  - Requires: `Authorization: Bearer <JWT_TOKEN>` header.
  - Body: `{ "user_id": "string" }`
  - Response: `{ "status": "User kicked from group", "user_id": "string" }` or error message.

### Invitation

- **POST /api/invitation/send**
  - Send an invitation to another user to join your group.
  - Requires: `Authorization: Bearer <JWT_TOKEN>` header.
  - Body: `{ "user_id": "string" }`
  - Response: `{ "status": "Invitation sent", "invitation_id": "string" }` or error message.

- **GET /api/invitation/sent?user_id=...**
  - List invitations you sent that are still pending (optionally filter by user).
  - Requires: `Authorization: Bearer <JWT_TOKEN>` header.
  - Response: `[{ "id": "invitation_id", ... }]` or error message.

- **GET /api/invitation/pending**
  - Lists the current user's active invitations (pending, not expired, not revoked/denied/accepted).
  - Requires: `Authorization: Bearer <JWT_TOKEN>` header.
  - Response: `[{ "id": "invitation_id", ... }]` or error message.

- **POST /api/invitation/{id}/accept**
  - Accepts an invitation.
  - Requires: `Authorization: Bearer <JWT_TOKEN>` header.
  - Response: `{ "status": "Invitation accepted", "invitation_id": "string" }` or error message.

- **POST /api/invitation/{id}/deny**
  - Denies an invitation.
  - Requires: `Authorization: Bearer <JWT_TOKEN>` header.
  - Response: `{ "status": "Invitation denied", "invitation_id": "string" }` or error message.

- **POST /api/invitation/{id}/cancel**
  - Cancels (revokes) an invitation you sent.
  - Requires: `Authorization: Bearer <JWT_TOKEN>` header.
  - Response: `{ "status": "Invitation canceled", "invitation_id": "string" }` or error message.

## Service Parameters

Defined in `config/services.yaml`:

- `app.max_group_users`: **4** — Maximum number of users allowed per group
- `app.invite_expiration_threshold`: **-10 minutes** — Invitations older than this are considered expired

## Notes
- All `/api/*` routes except `/api/register` and `/api/login` require authentication (JWT Bearer token).
- Register and login are open to anonymous users.
- Some routes are only available to the current authenticated user ("Me" endpoints).
- Built with Symfony, API Platform, and LexikJWTAuthenticationBundle.
- See the API Platform documentation for more details on custom operations and serialization groups.

## How to Test
- Use curl, Postman, or Thunder Client to test the endpoints.
- Register, then login to get a token, then use the token to access protected endpoints.

---

For more details, see the source code in `src/Controller/` and your `security.yaml` configuration.
