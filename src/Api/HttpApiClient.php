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
        private IriConverterInterface $iriConverter,
    ) {
    }

    public function iri(string $path): string
    {
        return $this->iriConverter->iri($path);
    }

    public function get(string $path, array $query = []): string
    {
        return $this->request($this->apiClient, 'GET', $path, $query !== [] ? ['query' => $query] : []);
    }

    public function post(string $path, array $body = []): string
    {
        return $this->request($this->apiClient, 'POST', $path, ['body' => json_encode($body, \JSON_THROW_ON_ERROR)]);
    }

    public function put(string $path, array $body = []): string
    {
        return $this->request($this->apiClient, 'PUT', $path, ['body' => json_encode($body, \JSON_THROW_ON_ERROR)]);
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

            $content    = $response->getContent(false);
            $statusCode = $response->getStatusCode();

            if ($statusCode >= 400) {
                throw new ToolCallException($content);
            }

            return $content;
        } catch (NotAuthenticatedException | AuthenticationFailedException $e) {
            throw new ToolCallException($e->getMessage(), 0, $e);
        }
    }
}
