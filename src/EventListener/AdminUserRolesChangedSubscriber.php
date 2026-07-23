<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\EventListener;

use Sylius\Component\Core\Model\AdminUserInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class AdminUserRolesChangedSubscriber
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function __invoke(GenericEvent $event): void
    {
        $adminUser = $event->getSubject();
        if (!$adminUser instanceof AdminUserInterface) {
            return;
        }

        $token = $this->tokenStorage->getToken();
        if ($token === null) {
            return;
        }

        $currentUser = $token->getUser();
        if (!$currentUser instanceof AdminUserInterface) {
            return;
        }

        if ($currentUser->getId() !== $adminUser->getId()) {
            return;
        }

        $this->tokenStorage->setToken(null);

        $request = $this->requestStack->getCurrentRequest();
        if ($request !== null && $request->hasSession()) {
            $request->getSession()->invalidate();
        }
    }
}
