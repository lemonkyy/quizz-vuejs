<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsEventListener(event: LogoutEvent::class)]
class LogoutListener
{
    public string $path;
    public ?string $domain;
    public bool $secure;
    public bool $httpOnlyHp;
    public bool $httpOnlyS;
    public string $sameSite;

    public function __construct(ParameterBagInterface $params)
    {
        $this->path = $params->get('jwt_cookie_path');
        $this->domain = $params->get('jwt_cookie_domain');
        $this->secure = $params->get('jwt_cookie_secure');
        $this->httpOnlyHp = $params->get('jwt_cookie_http_only_hp');
        $this->httpOnlyS = $params->get('jwt_cookie_http_only_s');
        $this->sameSite = $params->get('jwt_cookie_same_site');
    }

    public function __invoke(LogoutEvent $logoutEvent): void
    {
        $response = new JsonResponse(['message' => 'Logged out successfully']);

        $response->headers->clearCookie(
            'jwt_hp',
            $this->path,
            $this->domain,
            $this->secure,
            $this->httpOnlyHp,
            false,
            $this->sameSite
        );

        $response->headers->clearCookie(
            'jwt_s',
            $this->path,
            $this->domain,
            $this->secure,
            $this->httpOnlyS,
            false,
            $this->sameSite
        );

        $logoutEvent->setResponse($response);
    }
}
