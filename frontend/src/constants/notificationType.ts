export const NotificationType = {
  FRIEND_REQUEST: 'friend_request',
  INVITATION: 'invitation',
  OTHER: 'other',
} as const;

export type NotificationTypeValue = typeof NotificationType[keyof typeof NotificationType];
