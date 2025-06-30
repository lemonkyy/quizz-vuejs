<?php

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\User\MeReadController;
use App\Controller\User\RegisterController;
use App\Controller\User\MeUpdateUsernameController;

return [
    new Get([
        'uriTemplate' => '/user/info',
        'controller' => MeReadController::class,
        'read' => false,
        'name' => 'api_user_info',
        'openapiContext' => [
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
                                ]
                            ]
                        ]
                    ]
                ],
                '401' => [
                    'description' => 'Unauthorized'
                ]
            ]
        ]
    ]),
    new Post([
        'uriTemplate' => '/register',
        'controller' => RegisterController::class,
        'read' => false,
        'name' => 'api_register',
        'openapiContext' => [
            'summary' => 'Register a new user',
            'description' => 'Registers a new user with email and password.',
            'requestBody' => [
                'content' => [
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'email' => ['type' => 'string'],
                                'password' => ['type' => 'string'],
                            ],
                            'required' => ['email', 'password']
                        ]
                    ]
                ]
            ],
            'responses' => [
                '201' => [
                    'description' => 'User created.'
                ],
                '400' => [
                    'description' => 'Invalid input or email already in use.'
                ]
            ]
        ]
    ]),
    new Put([
        'uriTemplate' => '/user/username',
        'controller' => MeUpdateUsernameController::class,
        'read' => false,
        'name' => 'api_update_username',
        'openapiContext' => [
            'summary' => 'Update current user username',
            'description' => 'Updates the username of the current authenticated user.',
            'requestBody' => [
                'content' => [
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'username' => ['type' => 'string'],
                            ],
                            'required' => ['username']
                        ]
                    ]
                ]
            ],
            'responses' => [
                '200' => [
                    'description' => 'Username updated.'
                ],
                '400' => [
                    'description' => 'Invalid username or already in use.'
                ],
                '401' => [
                    'description' => 'Unauthorized.'
                ]
            ]
        ]
    ]),
];
