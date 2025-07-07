export interface User {
  username: string;
  email: string;
  roles: string[];
  hasTotp: boolean;
}

export interface JWTUserPayload {
  jti: string;
  username: string;
  email: string;
  hasTotp: boolean;
  roles: string[];
  iat: number;
  exp: number;
}
