<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Api;

use Sylius\AdminMcpServerPlugin\Exception\AuthenticationFailedException;
use Sylius\AdminMcpServerPlugin\Exception\NotAuthenticatedException;
use Sylius\AdminMcpServerPlugin\Provider\TokenProviderInterface;
use Mcp\Exception\ToolCallException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class HttpApiClient implements ApiClientInterface
{
    public function __construct(
        private HttpClientInterface $apiClient,
        private HttpClientInterface $mergePatchClient,
        private TokenProviderInterface $tokenProvider,
    ) {
    }

    public function get(string $path, array $query = []): string
    {
        return $this->request($this->apiClient, 'GET', $path, $query !== [] ? ['query' => $query] : []);
    }

    public function post(string $path, array $body = []): string
    {
        return $this->request($this->apiClient, 'POST', $path, ['json' => $body]);
    }

    public function put(string $path, array $body = []): string
    {
        return $this->request($this->apiClient, 'PUT', $path, ['json' => $body]);
    }

    public function putLd(string $path, array $body = []): string
    {
        return $this->request($this->apiClient, 'PUT', $path, [
            'body'    => json_encode($body, \JSON_THROW_ON_ERROR),
            'headers' => ['Content-Type' => 'application/ld+json'],
        ]);
    }

    public function patch(string $path, array $body = []): string
    {
        return $this->request($this->mergePatchClient, 'PATCH', $path, [
            'body' => json_encode($body, \JSON_THROW_ON_ERROR),
        ]);
    }

    public function delete(string $path): string
    {
        return $this->request($this->apiClient, 'DELETE', $path, []);
    }

    /**
     * @param array<string, mixed> $options
     */
    private function request(HttpClientInterface $client, string $method, string $path, array $options, bool $forceRefresh = false): string
    {
        try {
            $options['headers'] = ['Authorization' => 'Bearer ' . $this->tokenProvider->getToken($forceRefresh)];

            $response = $client->request($method, $path, $options);

            if (401 === $response->getStatusCode() && !$forceRefresh) {
                return $this->request($client, $method, $path, $options, forceRefresh: true);
            }

            return $response->getContent(false);
        } catch (NotAuthenticatedException | AuthenticationFailedException $e) {
            throw new ToolCallException($e->getMessage(), 0, $e);
        }
    }
}
