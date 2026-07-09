<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Loader;

use Mcp\Capability\Discovery\Discoverer;
use Mcp\Capability\Registry\Loader\LoaderInterface;
use Mcp\Capability\RegistryInterface;
use Psr\Log\LoggerInterface;

final readonly class PluginDiscoveryLoader implements LoaderInterface
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function load(RegistryInterface $registry): void
    {
        $discoverer = new Discoverer($this->logger);
        $state = $discoverer->discover(
            basePath: dirname(__DIR__, 2),
            directories: ['src/Tool'],
        );

        foreach ($state->getTools() as $toolRef) {
            $registry->registerTool(
                $toolRef->tool,
                $toolRef->handler,
                isManual: true,
            );
        }
    }
}
