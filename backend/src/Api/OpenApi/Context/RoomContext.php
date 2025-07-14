<?php

namespace App\Api\OpenApi\Context;

class RoomContext
{
    public static function getUserRoomContext(): array
    {
        return [
            'summary' => 'Get the current room of the user',
            'description' => 'Show the room the user currently belongs to (code, users, owner, createdAt, isPublic).',
            'responses' => [
                '200' => [
                    'description' => 'Current room details',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'code' => ['type' => 'string', 'enum' => ['SUCCESS']],
                                    'room' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'id' => ['type' => 'string'],
                                            'code' => ['type' => 'string'],
                                            'createdAt' => ['type' => 'string', 'format' => 'date-time'],
                                            'isPublic' => ['type' => 'boolean'],
                                            'owner' => ['type' => 'string'],
                                            'users' => ['type' => 'array', 'items' => ['type' => 'string']]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public static function getRoomListPublicContext(): array
    {
        return [
            'summary' => 'List public joinable rooms',
            'description' => 'Lists all public rooms that have available slots.',
            'responses' => [
                '200' => [
                    'description' => 'List of public rooms',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'code' => ['type' => 'string', 'enum' => ['SUCCESS']],
                                    'rooms' => [
                                        'type' => 'array',
                                        'items' => [ 'type' => 'object' ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public static function getRoomKickContext(): array
    {
        return [
            'summary' => 'Kick another user from the user\'s room',
            'description' => 'Kicks a user from the current room. Only the room owner can kick other users. Cannot kick yourself.',
            'parameters' => [
                [
                    'name' => 'id',
                    'in' => 'path',
                    'required' => true,
                    'schema' => [ 'type' => 'string' ],
                    'description' => 'ID of the user to kick from the room'
                ]
            ],
            'responses' => [
                '200' => [
                    'description' => 'User kicked from room',
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
                    'description' => 'Business rule violation or invalid input',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'code' => ['type' => 'string', 'enum' => ['MISSING_USER_ID', 'CANNOT_KICK_SELF', 'NOT_IN_A_ROOM', 'USER_NOT_IN_ROOM']],
                                    'error' => ['type' => 'string']
                                ]
                            ]
                        ]
                    ]
                ],
                '403' => [
                    'description' => 'Only the room owner can kick users',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'code' => ['type' => 'string', 'enum' => ['NOT_ROOM_OWNER']],
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
                                    'code' => ['type' => 'string', 'enum' => ['USER_NOT_FOUND']],
                                    'error' => ['type' => 'string']
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public static function getRoomCreateContext(): array
    {
        return [
            'summary' => 'Create a new room',
            'description' => 'Creates a new room with the current user as owner and member. Optionally accepts a code and isPublic flag.',
            'requestBody' => [
                'required' => false,
                'content' => [
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'isPublic' => [
                                    'type' => 'boolean',
                                    'description' => 'Whether the room is public (optional)'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'responses' => [
                '201' => [
                    'description' => 'Room created',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'code' => ['type' => 'string', 'enum' => ['SUCCESS']],
                                    'room' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'id' => ['type' => 'string'],
                                            'code' => ['type' => 'string'],
                                            'createdAt' => ['type' => 'string', 'format' => 'date-time'],
                                            'isPublic' => ['type' => 'boolean'],
                                            'owner' => ['type' => 'string'],
                                            'users' => ['type' => 'array', 'items' => ['type' => 'string']]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public static function getRoomDeleteContext(): array
    {
        return [
            'summary' => 'Delete the room the user owns',
            'description' => 'Soft-deletes the room where the current user is the owner.',
            'responses' => [
                '200' => [
                    'description' => 'Room deleted',
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
                '404' => [
                    'description' => 'No active room found for user as owner',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'code' => ['type' => 'string', 'enum' => ['ROOM_NOT_FOUND']],
                                    'error' => ['type' => 'string']
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public static function getRoomJoinContext(): array
    {
        return [
            'summary' => 'Join a room by code',
            'description' => 'Current user joins a room by its code.',
            'parameters' => [
                [
                    'name' => 'id',
                    'in' => 'path',
                    'required' => true,
                    'schema' => [ 'type' => 'string' ],
                    'description' => 'Id of the room to join'
                ]
            ],
            'responses' => [
                '200' => [
                    'description' => 'Joined room',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'code' => ['type' => 'string', 'enum' => ['SUCCESS']],
                                    'room' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'id' => ['type' => 'string'],
                                            'code' => ['type' => 'string'],
                                            'createdAt' => ['type' => 'string', 'format' => 'date-time'],
                                            'isPublic' => ['type' => 'boolean'],
                                            'owner' => ['type' => 'string'],
                                            'users' => ['type' => 'array', 'items' => ['type' => 'string']]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                '400' => [
                    'description' => 'Business rule violation or invalid input',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'code' => ['type' => 'string', 'enum' => ['ROOM_DELETED', 'USER_ALREADY_IN_ROOM']],
                                    'error' => ['type' => 'string']
                                ]
                            ]
                        ]
                    ]
                ],
                '404' => [
                    'description' => 'Room not found',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'code' => ['type' => 'string', 'enum' => ['ROOM_NOT_FOUND']],
                                    'error' => ['type' => 'string']
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public static function getRoomLeaveContext(): array
    {
        return [
            'summary' => 'Leave the current room',
            'description' => 'Current user leaves their active room.',
            'responses' => [
                '200' => [
                    'description' => 'Left room successfully',
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
                    'description' => 'User is not in an active room',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'code' => ['type' => 'string', 'enum' => ['NOT_IN_A_ROOM']],
                                    'error' => ['type' => 'string']
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}