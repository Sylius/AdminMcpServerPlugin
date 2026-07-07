<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Http;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final class AdminApiClient
{
    private ?string $token = null;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $baseUri,
        private readonly string $email,
        private readonly string $password,
    ) {
    }

    public function get(string $path, array $query = []): string
    {
        return $this->request('GET', $path, $query !== [] ? ['query' => $query] : []);
    }

    public function post(string $path, array $body = []): string
    {
        return $this->request('POST', $path, ['json' => $body]);
    }

    public function put(string $path, array $body = []): string
    {
        return $this->request('PUT', $path, ['json' => $body]);
    }

    public function patch(string $path, array $body = []): string
    {
        return $this->request('PATCH', $path, [
            'json' => $body,
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
            ],
        ]);
    }

    public function delete(string $path): string
    {
        return $this->request('DELETE', $path, []);
    }

    private function request(string $method, string $path, array $options = []): string
    {
        $options['headers']['Authorization'] = 'Bearer ' . $this->getToken();

        $response = $this->httpClient->request($method, $this->baseUri . $path, $options);

        return $response->getContent(false);
    }

    private function getToken(): string
    {
        if ($this->token === null) {
            $response = $this->httpClient->request('POST', $this->baseUri . 'administrators/token', [
                'json' => [
                    'email' => $this->email,
                    'password' => $this->password,
                ],
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'verify_peer' => false,
                'verify_host' => false,
            ]);

            $this->token = $response->toArray()['token'];
        }

        return $this->token;
    }
}
