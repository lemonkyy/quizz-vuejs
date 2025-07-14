<?php

namespace App\Api\OpenApi\Context;

class UserContext
{
    public static function getUserGetByUsernameContext(): array
    {
        return [
            'summary' => 'Get user by username',
            'description' => 'Returns a user based on their username.',
            'parameters' => [
                [
                    'name' => 'username',
                    'in' => 'query',
                    'required' => true,
                    'schema' => ['type' => 'string'],
                    'description' => 'The username of the user to retrieve.'
                ]
            ],
            'responses' => [
                '200' => [
                    'description' => 'User info',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'code' => ['type' => 'string', 'enum' => ['SUCCESS']],
                                    'user' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'id' => ['type' => 'string'],
                                            'username' => ['type' => 'string'],
                                            'email' => ['type' => 'string'],
                                            'profilePicture' => ['type' => 'string'],
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '400' => [
                        'description' => 'Missing username',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'code' => ['type' => 'string', 'enum' => ['ERR_MISSING_USERNAME']],
                                        'error' => ['type' => 'string']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '404' => [
                        'description' => 'User not found',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'code' => ['type' => 'string', 'enum' => ['ERR_USER_NOT_FOUND']],
                                        'error' => ['type' => 'string']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public static function getUserInfoContext(): array
    {
        return [
            'summary' => 'Get current user info',
            'description' => 'Returns the username and email of the current authenticated user.',
            'responses' => [
                '200' => [
                    'description' => 'User info',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'username' => ['type' => 'string'],
                                    'email' => ['type' => 'string'],
                                    'profilePicture' => ['type' => 'string'],
                                ]
                            ]
                        ]
                    ]
                ],
                '401' => [
                    'description' => 'Unauthorized'
                ]
            ]
        ];
    }

    public static function getUserSearchContext(): array
    {
        return [
            'summary' => 'Search for users by username',
            'description' => 'Returns a list of users matching the provided username, with pagination.',
            'parameters' => [
                [
                    'name' => 'username',
                    'in' => 'query',
                    'required' => true,
                    'schema' => ['type' => 'string'],
                    'description' => 'The username to search for'
                ],
                [
                    'name' => 'page',
                    'in' => 'query',
                    'required' => false,
                    'schema' => ['type' => 'integer', 'default' => 1],
                    'description' => 'The page number for pagination'
                ],
                [
                    'name' => 'limit',
                    'in' => 'query',
                    'required' => false,
                    'schema' => ['type' => 'integer', 'default' => 10],
                    'description' => 'The number of results per page'
                ]
            ],
            'responses' => [
                '200' => [
                    'description' => 'List of users',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'code' => ['type' => 'string', 'enum' => ['SUCCESS']],
                                    'users' => ['type' => 'array',
                                        'items' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'id' => ['type' => 'string'],
                                                'username' => ['type' => 'string'],
                                                'profilePicture' => ['type' => 'string']
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                '401' => [
                    'description' => 'Unauthorized'
                ]
            ]
        ];
    }

    public static function getUserRegisterContext(): array
    {
        return [
            'summary' => 'Register a new user',
            'description' => 'Registers a new user with email and password.',
            'requestBody' => [
                'content' => [
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'email' => ['type' => 'string'],
                                'username' => ['type' => 'string'],
                                'password' => ['type' => 'string'],
                                'passwordConfirmation' => ['type' => 'string'],
                                'tosAgreedTo' => ['type' => 'boolean']
                            ],
                            'required' => ['email', 'password', 'tosAgreedTo']
                        ]
                    ]
                ]
            ],
            'responses' => [
                '201' => [
                    'description' => 'User created.',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'code' => ['type' => 'string', 'enum' => ['SUCCESS']],
                                    'message' => ['type' => 'string']
                                ]
                            ]
                        ]
                    ]
                ],
                '400' => [
                    'description' => 'Invalid input or email already in use.',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'code' => ['type' => 'string', 'enum' => ['MISSING_CREDENTIALS', 'INVALID_EMAIL', 'EMAIL_ALREADY_IN_USE', 'USERNAME_VALIDATION_FAILED', 'USERNAME_GENERATION_FAILED', 'ERR_PASSWORD_WEAK', 'ERR_TOS_REFUSED']],
                                    'error' => ['type' => 'string']
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public static function getUserUpdateContext(): array
    {
        return [
            'summary' => 'Update current user',
            'description' => 'Updates the authenticated user and refreshes his cookies.',
            'requestBody' => [
                'content' => [
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'newUsername' => ['type' => 'string'],
                                'newProfilePictureId' => ['type' => 'string'],
                                'clearTotpSecret' => ['type' => 'boolean', 'default' => 'false']
                            ]
                        ]
                    ]
                ]
            ],
            'responses' => [
                '200' => [
                    'description' => 'Username updated.',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'code' => ['type' => 'string', 'enum' => ['SUCCESS']],
                                    'message' => ['type' => 'string'],
                                    'username' => ['type' => 'string'],
                                    'profilePicture' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'id' => ['type' => 'string', 'format' => 'uuid'],
                                            'fileName' => ['type' => 'string']
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                '400' => [
                    'description' => 'Invalid username or already in use.',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'code' => ['type' => 'string', 'enum' => ['INVALID_USERNAME', 'ERR_USERNAME_INVALID_TYPE', 'ERR_USERNAME_CONTAINS_SPACES', 'ERR_USERNAME_LENGTH', 'ERR_USERNAME_INAPPROPRIATE', 'ERR_USERNAME_TAKEN', 'ERR_NULL_PROFILE_PICTURE', 'ERR_PROFILE_PICTURE_NOT_FOUND']],
                                    'error' => ['type' => 'string']
                                ]
                            ]
                        ]
                    ]
                ],
                '401' => [
                    'description' => 'Unauthorized.'
                ]
            ]
        ];
    }

    public static function getUserTotpSecretGenerateContext(): array
    {
        return [
            'summary' => 'Generate TOTP secret for the current user and refreshes his cookies',
            'description' => 'Generates a TOTP secret for the current authenticated user.',
            'responses' => [
                '200' => [
                    'description' => 'TOTP secret generated successfully.',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'code' => ['type' => 'string', 'enum' => ['SUCCESS']],
                                    'totpSecret' => ['type' => 'string'],
                                ]
                            ]
                        ]
                    ]
                ],
                '400' => [
                    'description' => 'Failed to generate TOTP key.',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'code' => ['type' => 'string', 'enum' => ['TOTP_GENERATION_FAILED']],
                                    'error' => ['type' => 'string']
                                ]
                            ]
                        ]
                    ]
                ],
                '401' => [
                    'description' => 'Unauthorized'
                ]
            ]
        ];
    }

    public static function getUserTotpVerifyContext(): array
    {
        return [
            'summary' => 'Verify TOTP code for the current user',
            'description' => 'Verifies the TOTP code submitted by the user.',
            'responses' => [
                '200' => [
                    'description' => 'TOTP code verified and JWT returned.',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'code' => ['type' => 'string', 'enum' => ['SUCCESS']],
                                    'token' => ['type' => 'string']
                                ]
                            ]
                        ]
                    ]
                ],
                '400' => [
                    'description' => 'Invalid input.',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'code' => ['type' => 'string', 'enum' => ['MISSING_TOTP_CREDENTIALS']],
                                    'error' => ['type' => 'string']
                                ]
                            ]
                        ]
                    ]
                ],
                '401' => [
                    'description' => 'Unauthorized or invalid TOTP code.',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'code' => ['type' => 'string', 'enum' => ['INVALID_TEMP_TOKEN', 'INVALID_TOTP_CODE']],
                                    'error' => ['type' => 'string']
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'requestBody' => [
                'content' => [
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'totpCode' => ['type' => 'string'],
                                'tempToken' => ['type' => 'string']
                            ],
                            'required' => ['totpCode', 'tempToken']
                        ]
                    ]
                ]
            ]
        ];
    }

    public static function getUserRemoveFriendContext(): array
    {
        return [
            'summary' => 'Remove a friend',
            'description' => 'Removes a friend from the current user\'s friend list.',
            'responses' => [
                '200' => ['description' => 'Friend removed successfully'],
                '400' => ['description' => 'Invalid request'],
                '404' => ['description' => 'User not found or not a friend'],
                '401' => ['description' => 'Unauthorized']
            ]
        ];
    }

    public static function getUserListFriendsContext(): array
    {
        return [
            'summary' => 'List current user\'s friends',
            'description' => 'Lists all friends of the current authenticated user with pagination and filtering.',
            'parameters' => [
                [
                    'name' => 'page',
                    'in' => 'query',
                    'required' => false,
                    'schema' => ['type' => 'integer', 'default' => 1],
                    'description' => 'The page number for pagination'
                ],
                [
                    'name' => 'limit',
                    'in' => 'query',
                    'required' => false,
                    'schema' => ['type' => 'integer', 'default' => 10],
                    'description' => 'The number of results per page'
                ],
                [
                    'name' => 'username',
                    'in' => 'query',
                    'required' => false,
                    'schema' => ['type' => 'string'],
                    'description' => 'Filter friends by username'
                ]
            ],
            'responses' => [
                '200' => [
                    'description' => 'List of friends',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'code' => ['type' => 'string', 'enum' => ['SUCCESS']],
                                    'friends' => ['type' => 'array',
                                        'items' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'id' => ['type' => 'string'],
                                                'username' => ['type' => 'string'],
                                                'profilePicture' => ['type' => 'string']
                                            ]
                                        ]
                                    ],
                                    'hasMore' => ['type' => 'boolean']
                                ]
                            ]
                        ]
                    ]
                ],
                '401' => ['description' => 'Unauthorized']
            ]
        ];
    }

    public static function getUserNotificationCountContext(): array
    {
        return [
            'summary' => 'Get notification count for the current user',
            'description' => 'Returns the total number of unread friend requests and invitations for the authenticated user.',
            'responses' => [
                '200' => [
                    'description' => 'Notification count',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'notificationCount' => ['type' => 'integer'],
                                ]
                            ]
                        ]
                    ]
                ],
                '401' => [
                    'description' => 'Unauthorized'
                ]
            ]
        ];
    }

    public static function getUserListNotificationsContext(): array
    {
        return [
            'summary' => 'List all active notifications for the current user',
            'description' => 'Returns a list of active friend requests and invitations, ordered by creation date.',
            'responses' => [
                '200' => [
                    'description' => 'List of notifications',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'array',
                                'items' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'type' => ['type' => 'string', 'enum' => ['friend_request', 'invitation']],
                                        'id' => ['type' => 'string', 'format' => 'uuid'],
                                        'createdAt' => ['type' => 'string', 'format' => 'date-time'],
                                        'sender' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'id' => ['type' => 'string', 'format' => 'uuid'],
                                                'username' => ['type' => 'string'],
                                            ],
                                            'nullable' => true,
                                        ],
                                        'invitedBy' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'id' => ['type' => 'string', 'format' => 'uuid'],
                                                'username' => ['type' => 'string'],
                                            ],
                                            'nullable' => true,
                                        ],
                                        'room' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'id' => ['type' => 'string', 'format' => 'uuid'],
                                                'name' => ['type' => 'string'],
                                            ],
                                            'nullable' => true,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                '401' => [
                    'description' => 'Unauthorized'
                ],
            ],
        ];
    }
}