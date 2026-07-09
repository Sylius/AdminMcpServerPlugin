<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Session;

interface TokenStorageInterface
{
    public function get(): ?string;

    public function store(string $token): void;

    public function clear(): void;
}
