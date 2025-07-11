<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthenticationListener
{
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event): void
    {
        $data = $event->getData();
        $user = $event->getUser();

        // Optionnel : ajouter des infos supplémentaires à la réponse (comme l'ID, prénom, etc.)
        if (method_exists($user, 'getId')) {
            $data['id'] = $user->getId();
        }
        if (method_exists($user, 'getFirstName')) {
            $data['firstName'] = $user->getFirstName();
        }
        if (method_exists($user, 'getLastName')) {
            $data['lastName'] = $user->getLastName();
        }

        $event->setData($data);
    }
}
