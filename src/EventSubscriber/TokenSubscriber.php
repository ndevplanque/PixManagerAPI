<?php

namespace App\EventSubscriber;

use App\Repository\AppUserRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TokenSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly AppUserRepository     $appUserRepository,
    )
    {
    }

    public function onKernelController(
        ControllerEvent $event,
    ): void
    {
        $token = $this->tokenStorage->getToken();

        if ($token === null) {
            return;
        }

        $email = $token->getUserIdentifier();
        $user = $this->appUserRepository->findOneBy(['email' => $email]);
        $event->getRequest()->attributes->set('requester', $user);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
