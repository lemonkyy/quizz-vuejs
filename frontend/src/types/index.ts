export interface User {
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
