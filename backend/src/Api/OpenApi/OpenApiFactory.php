<?php

namespace App\Api\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\OpenApi;
use ApiPlatform\OpenApi\Model;
use App\Api\OpenApi\Context\FriendRequestContext;
use App\Api\OpenApi\Context\InvitationContext;
use App\Api\OpenApi\Context\RoomContext;
use App\Api\OpenApi\Context\UserContext;
use App\Api\OpenApi\Context\ProfilePictureContext;

final class OpenApiFactory implements OpenApiFactoryInterface
{
    public function __construct(
        private OpenApiFactoryInterface $decorated
    ) {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);

        $paths = $openApi->getPaths();

        $contextClassMap = [
            'api_friend_requests' => FriendRequestContext::class,
            'api_invitation' => InvitationContext::class,
            'api_room' => RoomContext::class,
            'api_user' => UserContext::class,
            'api_profile_picture' => ProfilePictureContext::class,
        ];

        foreach ($paths->getPaths() as $path => $pathItem) {
            foreach (Model\PathItem::$methods as $method) {
                $operation = $pathItem->{'get'.ucfirst(strtolower($method))}();
                if (null === $operation) {
                    continue;
                }
                $operationId = $operation->getOperationId();

                if (null !== $operationId) {
                    $newContext = null;
                    foreach ($contextClassMap as $prefix => $contextClass) {
                        if (str_starts_with($operationId, $prefix)) {
                            $methodName = 'get' . str_replace('_', '', ucwords(str_replace('api_', '', $operationId), '_')) . 'Context';

                            if (method_exists($contextClass, $methodName)) {
                                $newContext = $contextClass::$methodName();
                                break;
                            }
                        }
                    }

                    if ($newContext !== null) {
                        $newOperation = $operation;

                        if (isset($newContext['summary'])) {
                            $newOperation = $newOperation->withSummary($newContext['summary']);
                        }
                        if (isset($newContext['description'])) {
                            $newOperation = $newOperation->withDescription($newContext['description']);
                        }
                        if (isset($newContext['parameters'])) {
                            foreach ($newContext['parameters'] as $paramContext) {
                                $newOperation = $newOperation->withParameter(new Model\Parameter(
                                    name: $paramContext['name'],
                                    in: $paramContext['in'],
                                    description: $paramContext['description'] ?? null,
                                    required: $paramContext['required'] ?? false,
                                    schema: $paramContext['schema'] ?? null,
                                ));
                            }
                        }
                        if (isset($newContext['requestBody'])) {
                            $newOperation = $newOperation->withRequestBody(new Model\RequestBody(
                                content: new \ArrayObject($newContext['requestBody']['content']),
                                required: $newContext['requestBody']['required'] ?? false
                            ));
                        }
                        if (isset($newContext['responses'])) {
                            foreach ($newContext['responses'] as $statusCode => $responseContext) {
                                $content = $responseContext['content'] ?? null;
                                $newOperation = $newOperation->withResponse($statusCode, new Model\Response(
                                    description: $responseContext['description'] ?? '',
                                    content: isset($content) ? new \ArrayObject($content) : null
                                ));
                            }
                        }

                        $pathItem = $pathItem->{'with'.ucfirst(strtolower($method))}($newOperation);
                    }
                }
            }
            $paths->addPath($path, $pathItem);
        }

        return $openApi;
    }
}
