<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;

#[AsEventListener(event: LoginSuccessEvent::class)]
#[AsEventListener(event: LogoutEvent::class)]
class LoginListener
{
    public function __construct(
        private LoggerInterface $importantLogger,
    ) {}

    public function __invoke(LoginSuccessEvent|LogoutEvent $event): void
    {
        if ($event instanceof LoginSuccessEvent) {
            $user = $event->getUser();
            $userId = $user->getId() ?? 'unknown';

            $this->importantLogger->info('User logged in', [
                'user_id' => $userId,
                'username' => $user->getUserIdentifier(),
                'ip' => $event->getRequest()->getClientIp(),
            ]);
        } elseif ($event instanceof LogoutEvent) {
            $token = $event->getToken();
            if ($token && $token->getUser()) {
                $user = $token->getUser();
                $userId = $user->getId() ?? 'unknown';

                $this->importantLogger->info('User logged out', [
                    'user_id' => $userId,
                    'username' => $user->getUserIdentifier(),
                ]);
            }
        }
    }
}