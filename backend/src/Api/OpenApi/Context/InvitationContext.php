<?php

namespace App\Api\OpenApi\Context;

class InvitationContext
{
    public static function getInvitationSendContext(): array
    {
        return [
            'summary' => 'Send a room invitation',
            'description' => 'Send an invitation to another user to join your current room. Only possible if you are in a room, the user is not already in the room, and the room is not full.',
            'parameters' => [
                [
                    'name' => 'id',
                    'in' => 'path',
                    'required' => true,
                    'schema' => [ 'type' => 'string' ],
                    'description' => 'ID of the user to invite'
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
                                    'code' => ['type' => 'string', 'enum' => ['SUCCESS']],
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
                                    'code' => ['type' => 'string', 'enum' => ['MISSING_USER_ID', 'CANNOT_INVITE_SELF', 'NOT_IN_A_ROOM', 'USER_ALREADY_IN_ROOM', 'ROOM_FULL', 'INVITATION_ALREADY_SENT']],
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

    public static function getInvitationSentContext(): array
    {
        return [
            'summary' => 'List invitations sent by the current user',
            'description' => 'Returns invitations sent by the current user that are still pending (not accepted, denied, or revoked). Optionally filter by user_id as a query parameter.',
            'parameters' => [
                [
                    'name' => 'user_id',
                    'in' => 'query',
                    'required' => false,
                    'schema' => [ 'type' => 'string' ],
                    'description' => 'Filter to invitations sent to this user (optional)'
                ]
            ],
            'responses' => [
                '200' => [
                    'description' => 'List of pending invitations sent by the user',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'code' => ['type' => 'string', 'enum' => ['SUCCESS']],
                                    'invitations' => [
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

    public static function getInvitationPendingContext(): array
    {
        return [
            'summary' => 'List invitations received by the current user',
            'description' => 'Returns invitations received by the current user that are still pending (not accepted, denied, or revoked, and not expired).',
            'responses' => [
                '200' => [
                    'description' => 'List of pending invitations received by the user',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'code' => ['type' => 'string', 'enum' => ['SUCCESS']],
                                    'invitations' => [
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

    public static function getInvitationAcceptContext(): array
    {
        return [
            'summary' => 'Accept an invitation',
            'description' => 'Accepts an invitation, checks for expiration and revocation, and joins the new room.',
            'parameters' => [
                [
                    'name' => 'id',
                    'in' => 'path',
                    'required' => true,
                    'schema' => [ 'type' => 'string' ],
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
                                    'code' => ['type' => 'string', 'enum' => ['SUCCESS']],
                                    'message' => ['type' => 'string']
                                ]
                            ]
                        ]
                    ]
                ],
                '400' => [
                    'description' => 'Business rule violation (expired, revoked, room deleted, etc.)',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'code' => ['type' => 'string', 'enum' => ['INVITATION_EXPIRED', 'INVITATION_REVOKED', 'ROOM_DELETED']],
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
                                    'code' => ['type' => 'string', 'enum' => ['INVITATION_NOT_FOUND']],
                                    'error' => ['type' => 'string']
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public static function getInvitationDenyContext(): array
    {
        return [
            'summary' => 'Deny an invitation',
            'description' => 'Denies an invitation by its id. Only the invited user can deny.',
            'parameters' => [
                [
                    'name' => 'id',
                    'in' => 'path',
                    'required' => true,
                    'schema' => [ 'type' => 'string' ],
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
                                    'code' => ['type' => 'string', 'enum' => ['SUCCESS']],
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
                                    'code' => ['type' => 'string', 'enum' => ['INVITATION_NOT_FOUND']],
                                    'error' => ['type' => 'string']
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public static function getInvitationCancelContext(): array
    {
        return [
            'summary' => 'Cancel an invitation the user sent',
            'description' => 'Revokes an invitation by its id. Only the user who sent the invitation can revoke it.',
            'parameters' => [
                [
                    'name' => 'id',
                    'in' => 'path',
                    'required' => true,
                    'schema' => [ 'type' => 'string' ],
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
                                    'code' => ['type' => 'string', 'enum' => ['SUCCESS']],
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
                                    'code' => ['type' => 'string', 'enum' => ['INVITATION_NOT_FOUND']],
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