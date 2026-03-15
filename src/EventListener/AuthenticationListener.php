<?php

namespace App\EventListener;

use App\Service\ActivityLogService;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class AuthenticationListener
{
    public function __construct(private ActivityLogService $activityLogService)
    {
    }

    #[AsEventListener(event: InteractiveLoginEvent::class)]
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();
        
        if ($user && method_exists($user, 'getId')) {
            $this->activityLogService->logLogin($user);
        }
    }

    #[AsEventListener(event: LogoutEvent::class)]
    public function onLogout(LogoutEvent $event): void
    {
        $token = $event->getToken();
        
        if ($token && $user = $token->getUser()) {
            if (method_exists($user, 'getId')) {
                $this->activityLogService->logLogout($user);
            }
        }
    }
}
