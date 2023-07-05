<?php

namespace App\EventSubscriber;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class MaintenanceSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly RequestStack $stack,
        private readonly Environment $twig,
        #[Autowire('%env(bool:IS_MAINTENANCE)%')]
        private readonly bool $isMaintenance,
    ) {}

    public function onKernelRequest(RequestEvent $event): void
    {
        if ($this->isMaintenance && $this->stack->getMainRequest() === $event->getRequest()) {
            $response = new Response($this->twig->render('maintenance.html.twig'), 500);
            $event->setResponse($response);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 9000],
        ];
    }
}
