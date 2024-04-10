<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ResponseSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();
        $status = $response->getStatusCode();

        $request = $event->getRequest();
        $method = $request->getMethod();
        $uri = $request->getRequestUri();
        $agent = $request->headers->get('User-Agent', 'Unknown');

        $this->logger->notice("Sent $status for $method $uri ($agent)");
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }
}
