<?php declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Bundle\SecurityBundle\Security;

readonly class RequestSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Security $security,
        private TokenStorageInterface $tokenStorage,
    ){}

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if ($this->security->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $session = $event->getRequest()->getSession();
            /** @var User $user */
            $user = $this->security->getUser();
            if ($user->isBanned()) {
                $this->tokenStorage->setToken(null);
                $session->invalidate();
            }
        }
    }
}
