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
        $basePath = dirname(__DIR__, 2);
        $discoverer = new Discoverer($this->logger);
        $state = $discoverer->discover(
            basePath: $basePath,
            directories: ['src/Tool', 'src/Mcp'],
        );

        $this->logger->info('PluginDiscoveryLoader: discovered', [
            'tools' => count($state->getTools()),
            'resources' => count($state->getResources()),
            'basePath' => $basePath,
        ]);

        foreach ($state->getTools() as $toolRef) {
            $registry->registerTool(
                $toolRef->tool,
                $toolRef->handler,
                isManual: true,
            );
        }

        foreach ($state->getResources() as $resourceRef) {
            $registry->registerResource(
                $resourceRef->resource,
                $resourceRef->handler,
                isManual: true,
            );
        }
    }
}
