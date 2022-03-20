<?php declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => [
                ['accessDeniedException', 10],
            ],
        ];
    }

    public function accessDeniedException(ExceptionEvent $event): void
    {
        if (!$event->getThrowable() instanceof AccessDeniedException) {
            return;
        }

        if ($event->getRequest()->attributes->get('_route') === 'story_create') {
            return;
        }

        throw new NotFoundHttpException();
    }
}
