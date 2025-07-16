export interface User {
  id: string;
  username: string;
  profilePicture: ProfilePicture;
  roles: string[];
  hasTotp?: boolean;
  email?: string;
}

//normal user is used for authenticated user, PublicUser is for other users (no need to store roles totp email etc)
export interface PublicUser {
  id: string,
  username: string,
  profilePicture: ProfilePicture
}

export interface ProfilePicture {
  id: string,
  fileName: string
}

export interface JWTUserPayload {
  jti: string;
  id: string;
  username: string;
  profilePictureId: string;
  profilePictureFileName: string;
  email: string;
  hasTotp: boolean;
  roles: string[];
  iat: number;
  exp: number;
}

export interface Notification {
  id: string;
  type: string;
  sentAt: string;
  data: {
    sender?: {
      id?: string;
      username?: string;
      profilePicture?: string;
    };
    message?: string;
  };
}
