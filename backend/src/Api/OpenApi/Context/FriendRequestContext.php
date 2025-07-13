<?php

namespace App\Api\OpenApi\Context;

class FriendRequestContext
{
    public static function getFriendRequestsSendContext(): array
    {
        return [
            'summary' => 'Send a friend request',
            'description' => 'Sends a friend request to another user.',
            'parameters' => [
                [
                    'name' => 'id',
                    'in' => 'path',
                    'required' => true,
                    'schema' => ['type' => 'string'],
                    'description' => 'The ID of the user to send the friend request to.'
                ]
            ],
            'responses' => [
                '201' => ['description' => 'Friend request sent'],
                '400' => ['description' => 'Invalid request'],
                '404' => ['description' => 'User not found'],
                '401' => ['description' => 'Unauthorized']
            ]
        ];
    }

    public static function getFriendRequestsAcceptContext(): array
    {
        return [
            'summary' => 'Accept a friend request',
            'description' => 'Accepts a friend request by its ID.',
            'responses' => [
                '200' => ['description' => 'Friend request accepted'],
                '400' => ['description' => 'Invalid request'],
                '404' => ['description' => 'Friend request not found'],
                '401' => ['description' => 'Unauthorized']
            ]
        ];
    }

    public static function getFriendRequestsDenyContext(): array
    {
        return [
            'summary' => 'Deny a friend request',
            'description' => 'Denies a friend request by its ID.',
            'responses' => [
                '200' => ['description' => 'Friend request denied'],
                '400' => ['description' => 'Invalid request'],
                '404' => ['description' => 'Friend request not found'],
                '401' => ['description' => 'Unauthorized']
            ]
        ];
    }

    public static function getFriendRequestsCancelContext(): array
    {
        return [
            'summary' => 'Cancel a sent friend request',
            'description' => 'Cancels a sent friend request by its ID.',
            'responses' => [
                '200' => ['description' => 'Friend request cancelled'],
                '400' => ['description' => 'Invalid request'],
                '404' => ['description' => 'Friend request not found'],
                '401' => ['description' => 'Unauthorized']
            ]
        ];
    }

    public static function getFriendRequestsSentContext(): array
    {
        return [
            'summary' => 'List sent friend requests',
            'description' => 'Lists all friend requests sent by the current user.',
            'responses' => [
                '200' => ['description' => 'List of sent friend requests'],
                '401' => ['description' => 'Unauthorized']
            ]
        ];
    }

    public static function getFriendRequestsReceivedContext(): array
    {
        return [
            'summary' => 'List received friend requests',
            'description' => 'Lists all friend requests received by the current user.',
            'responses' => [
                '200' => ['description' => 'List of received friend requests'],
                '401' => ['description' => 'Unauthorized']
            ]
        ];
    }
}