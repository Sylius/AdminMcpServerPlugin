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

use Mcp\Event\RequestEvent;
use Sylius\AdminMcpServerPlugin\Session\CurrentSession;

final readonly class BindSessionListener
{
    public function __construct(
        private CurrentSession $currentSession,
    ) {
    }

    public function __invoke(RequestEvent $event): void
    {
        $this->currentSession->bind($event->getSession());
    }
}
