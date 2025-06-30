<?php

use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Delete;
use App\Controller\Group\MeShowActiveGroupController;
use App\Controller\Api\Group\MeKickUserController;
use App\Controller\Api\Group\MeCreateController;
use App\Controller\Api\Group\MeDeleteController;

return [
    new Get([
        'uriTemplate' => '/user/group',
        'controller' => MeShowActiveGroupController::class,
        'read' => false,
        'name' => 'api_user_group',
        'openapiContext' => [
            'summary' => 'Get the current group of the user',
            'description' => 'Show the group the user currently belongs to (code, users, owner, createdAt, isPublic).'
        ]
    ]),
    new Post([
        'uriTemplate' => '/group/kick',
        'controller' => MeKickUserController::class,
        'read' => false,
        'name' => 'api_group_kick_user',
        'openapiContext' => [
            'summary' => 'Kick a user from your group',
            'description' => 'Kicks a user from the current group. Only the group owner can kick other users. Cannot kick yourself.',
            'requestBody' => [
                'required' => true,
                'content' => [
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'user_id' => [
                                    'type' => 'integer',
                                    'description' => 'ID of the user to kick from the group'
                                ]
                            ],
                            'required' => ['user_id']
                        ]
                    ]
                ]
            ],
            'responses' => [
                '200' => [
                    'description' => 'User kicked from group',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
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
                                    'error' => ['type' => 'string']
                                ]
                            ]
                        ]
                    ]
                ],
                '403' => [
                    'description' => 'Only the group owner can kick users',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
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
                                    'error' => ['type' => 'string']
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]),
    new Post([
        'uriTemplate' => '/group/create',
        'controller' => MeCreateController::class,
        'read' => false,
        'name' => 'api_group_create',
        'openapiContext' => [
            'summary' => 'Create a new group',
            'description' => 'Creates a new group with the current user as owner and member. Optionally accepts a code and isPublic flag.',
            'requestBody' => [
                'required' => false,
                'content' => [
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'isPublic' => [
                                    'type' => 'boolean',
                                    'description' => 'Whether the group is public (optional)'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'responses' => [
                '201' => [
                    'description' => 'Group created',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'id' => ['type' => 'integer'],
                                    'code' => ['type' => 'string'],
                                    'createdAt' => ['type' => 'string', 'format' => 'date-time'],
                                    'isPublic' => ['type' => 'boolean'],
                                    'owner' => ['type' => 'integer'],
                                    'users' => ['type' => 'array', 'items' => ['type' => 'integer']]
                                }
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]),
    new Delete([
        'uriTemplate' => '/group/delete',
        'controller' => MeDeleteController::class,
        'read' => false,
        'name' => 'api_group_delete',
        'openapiContext' => [
            'summary' => 'Delete the group you created',
            'description' => 'Soft-deletes the group where the current user is the owner.',
            'responses' => [
                '200' => [
                    'description' => 'Group deleted',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'message' => ['type' => 'string']
                                ]
                            ]
                        ]
                    ]
                ],
                '404' => [
                    'description' => 'No active group found for user as owner',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'error' => ['type' => 'string']
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]),
];
