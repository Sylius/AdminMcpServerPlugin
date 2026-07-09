<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\EventListener;

use Acme\SyliusExamplePlugin\Session\CurrentSession;
use Mcp\Event\RequestEvent;

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
