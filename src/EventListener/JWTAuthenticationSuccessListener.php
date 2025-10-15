<?php

namespace App\EventListener;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(
    event: 'lexik_jwt_authentication.on_authentication_success',
    method: 'onAuthenticationSuccessResponse',
    priority: -1
)]
class JWTAuthenticationSuccessListener
{
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event): void
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof User) {
            return;
        }

        $data['email_crypted'] = $user->getEmailCrypted();

        $event->setData($data);
    }
}
