<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\Administrator;

use Acme\SyliusExamplePlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_administrator',
    description: 'update_administrator(id, email?, username?, firstName?, lastName?, localeCode?, enabled?, plainPassword?) → JSON object of the updated Sylius administrator. Only provided fields are updated.',
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int    $id            Administrator ID.
     * @param string $email         New email address.
     * @param string $username      New username.
     * @param string $firstName     New first name.
     * @param string $lastName      New last name.
     * @param string $localeCode    New locale code, e.g. "en_US".
     * @param bool   $enabled       Whether the administrator is active.
     * @param string $plainPassword New plain text password (leave empty to keep current).
     */
    public function __invoke(
        int $id,
        string $email = '',
        string $username = '',
        string $firstName = '',
        string $lastName = '',
        string $localeCode = '',
        bool $enabled = true,
        string $plainPassword = '',
    ): string {
        $body = ['enabled' => $enabled];

        if ($email !== '') {
            $body['email'] = $email;
        }
        if ($username !== '') {
            $body['username'] = $username;
        }
        if ($firstName !== '') {
            $body['firstName'] = $firstName;
        }
        if ($lastName !== '') {
            $body['lastName'] = $lastName;
        }
        if ($localeCode !== '') {
            $body['localeCode'] = $localeCode;
        }
        if ($plainPassword !== '') {
            $body['plainPassword'] = $plainPassword;
        }

        return $this->client->put(sprintf('administrators/%d', $id), $body);
    }
}
