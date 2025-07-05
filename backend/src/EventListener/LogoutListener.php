<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Event\LogoutEvent;

#[AsEventListener(event: LogoutEvent::class)]
class LogoutListener
{
    public function __invoke(LogoutEvent $logoutEvent): void
    {
        setcookie('jwt_hp', '', -1, '/');
        setcookie('jwt_s', '', -1, '/');
        $logoutEvent->setResponse(new JsonResponse([]));
    }
}
