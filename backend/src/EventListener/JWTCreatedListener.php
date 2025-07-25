<?php

namespace App\EventListener;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

//this is where you can add custom data to the jwt token
#[AsEventListener(event: 'lexik_jwt_authentication.on_jwt_created')]
class JWTCreatedListener
{
    public function __invoke(JWTCreatedEvent $event): void
    {
        //$request = $this->requestStack->getCurrentRequest();
        $user = $event->getUser();

        if (!$user instanceof User) {
            return;
        }

        $payload = $event->getData();
        $payload['username'] = $user->getUsername();
        $payload['id'] = $user->getId()->toString();
        $payload['hasTotp'] = $user->getTotpSecret() ? true : false;
        $payload['profilePictureFileName'] = $user->getprofilePicture()->getFileName();
        $payload['profilePictureId'] = $user->getProfilePicture()->getId();
        $payload['roles'] = $user->getRoles();

        // if ($request) {
        //     $payload['ip'] = $request->getClientIp();
        // }

        $event->setData($payload);
    }
}
