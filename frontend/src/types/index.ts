export interface User {
  id: string;
  username: string;
  email: string;
  roles: string[];
}

export interface JWTUserPayload {
  jti: string;
  username: string;
  email: string;
  roles: string[];
  iat: number;
  exp: number;
}
