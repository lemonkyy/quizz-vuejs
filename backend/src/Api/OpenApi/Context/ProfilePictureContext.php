<?php

namespace App\Api\OpenApi\Context;

class ProfilePictureContext
{
    public static function getApiProfilePictureUploadContext(): array
    {
        return [
            'summary' => 'Upload a profile picture',
            'description' => 'Uploads a new profile picture for the current user.',
            'requestBody' => [
                'required' => true,
                'content' => [
                    'multipart/form-data' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'file' => [
                                    'type' => 'string',
                                    'format' => 'binary',
                                    'description' => 'The image file to upload.'
                                ]
                            ],
                            'required' => ['file']
                        ]
                    ]
                ]
            ],
            'responses' => [
                '200' => [
                    'description' => 'Profile picture uploaded successfully.',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'code' => ['type' => 'string', 'enum' => ['SUCCESS']],
                                    'message' => ['type' => 'string'],
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
                    'description' => 'Invalid file or upload error.',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'code' => ['type' => 'string', 'enum' => ['ERR_INVALID_FILE', 'ERR_UPLOAD_FAILED']],
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
}