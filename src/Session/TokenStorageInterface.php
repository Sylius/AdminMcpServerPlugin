<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Session;

interface TokenStorageInterface
{
    public function get(): ?string;

    public function store(string $token): void;

    public function clear(): void;
}
