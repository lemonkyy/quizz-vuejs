<?php

use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use App\Controller\Api\Invitation\MeSendController;
use App\Controller\Api\Invitation\MeListSentController;
use App\Controller\Api\Invitation\MeListPendingController;
use App\Controller\Api\Invitation\MeAcceptController;
use App\Controller\Api\Invitation\MeDenyController;
use App\Controller\Api\Invitation\MeCancelController;

return [
    new Post([
        'uriTemplate' => '/invitation/send',
        'controller' => MeSendController::class,
        'read' => false,
        'name' => 'api_invitation_send',
        'openapiContext' => [
            'summary' => 'Send a group invitation',
            'description' => 'Send an invitation to another user to join your current group. Only possible if you are in a group, the user is not already in the group, and the group is not full.',
            'requestBody' => [
                'required' => true,
                'content' => [
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'user_id' => [
                                    'type' => 'integer',
                                    'description' => 'ID of the user to invite'
                                ]
                            ],
                            'required' => ['user_id']
                        ]
                    ]
                ]
            ],
            'responses' => [
                '201' => [
                    'description' => 'Invitation sent',
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
                    'description' => 'Business rule violation',
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
    new Get([
        'uriTemplate' => '/invitation/sent',
        'controller' => MeListSentController::class,
        'read' => false,
        'name' => 'api_invitation_sent',
        'openapiContext' => [
            'summary' => 'List invitations sent by the current user',
            'description' => 'Returns invitations sent by the current user that are still pending (not accepted, denied, or revoked). Optionally filter by user_id as a query parameter.',
            'parameters' => [
                [
                    'name' => 'user_id',
                    'in' => 'query',
                    'required' => false,
                    'schema' => [ 'type' => 'integer' ],
                    'description' => 'Filter to invitations sent to this user (optional)'
                ]
            ],
            'responses' => [
                '200' => [
                    'description' => 'List of pending invitations sent by the user',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'array',
                                'items' => [ 'type' => 'object' ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]),
    new Get([
        'uriTemplate' => '/invitation/pending',
        'controller' => MeListPendingController::class,
        'read' => false,
        'name' => 'api_invitation_pending',
        'openapiContext' => [
            'summary' => 'List invitations received by the current user',
            'description' => 'Returns invitations received by the current user that are still pending (not accepted, denied, or revoked, and not expired).',
            'responses' => [
                '200' => [
                    'description' => 'List of pending invitations received by the user',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'array',
                                'items' => [ 'type' => 'object' ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]),
    new Post([
        'uriTemplate' => '/invitation/{id}/accept',
        'controller' => MeAcceptController::class,
        'read' => false,
        'name' => 'api_invitation_accept',
        'openapiContext' => [
            'summary' => 'Accept an invitation',
            'description' => 'Accepts an invitation, checks for expiration and revocation, and joins the new group.',
            'parameters' => [
                [
                    'name' => 'id',
                    'in' => 'path',
                    'required' => true,
                    'schema' => [ 'type' => 'integer' ],
                    'description' => 'ID of the invitation to accept'
                ]
            ],
            'responses' => [
                '200' => [
                    'description' => 'Invitation accepted',
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
                    'description' => 'Business rule violation (expired, revoked, group deleted, etc.)',
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
                    'description' => 'Invitation not found',
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
        'uriTemplate' => '/invitation/{id}/deny',
        'controller' => MeDenyController::class,
        'read' => false,
        'name' => 'api_invitation_deny',
        'openapiContext' => [
            'summary' => 'Deny an invitation',
            'description' => 'Denies an invitation by its id. Only the invited user can deny.',
            'parameters' => [
                [
                    'name' => 'id',
                    'in' => 'path',
                    'required' => true,
                    'schema' => [ 'type' => 'integer' ],
                    'description' => 'ID of the invitation to deny'
                ]
            ],
            'responses' => [
                '200' => [
                    'description' => 'Invitation denied',
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
                    'description' => 'Invitation not found',
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
        'uriTemplate' => '/invitation/{id}/cancel',
        'controller' => MeCancelController::class,
        'read' => false,
        'name' => 'api_invitation_cancel',
        'openapiContext' => [
            'summary' => 'Cancel (revoke) an invitation',
            'description' => 'Revokes an invitation by its id. Only the user who sent the invitation can revoke it.',
            'parameters' => [
                [
                    'name' => 'id',
                    'in' => 'path',
                    'required' => true,
                    'schema' => [ 'type' => 'integer' ],
                    'description' => 'ID of the invitation to cancel'
                ]
            ],
            'responses' => [
                '200' => [
                    'description' => 'Invitation revoked',
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
                    'description' => 'Invitation not found or not allowed',
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
