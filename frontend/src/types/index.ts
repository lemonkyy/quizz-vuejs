export interface User {
  id: string;
  username: string;
  profilePicture: string;
  email: string;
  roles: string[];
  hasTotp: boolean;
}

export interface JWTUserPayload {
  jti: string;
  id: string;
  username: string;
  profilePicture: string;
  email: string;
  hasTotp: boolean;
  roles: string[];
  iat: number;
  exp: number;
}
