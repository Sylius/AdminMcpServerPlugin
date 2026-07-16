<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Api;

use Mcp\Exception\ToolCallException;
use Sylius\AdminMcpServerPlugin\Exception\NotAuthenticatedException;
use Sylius\AdminMcpServerPlugin\Provider\TokenProviderInterface;
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
    private function request(HttpClientInterface $client, string $method, string $path, array $options): string
    {
        try {
            $options['headers'] = ['Authorization' => 'Bearer ' . $this->tokenProvider->getToken()];

            $response = $client->request($method, $path, $options);

            return $response->getContent(false);
        } catch (NotAuthenticatedException $e) {
            throw new ToolCallException($e->getMessage(), 0, $e);
        }
    }
}
