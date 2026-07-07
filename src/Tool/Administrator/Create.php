<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\Administrator;

use Acme\SyliusExamplePlugin\Http\AdminApiClient;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'create_administrator',
    description: 'create_administrator(email, username, plainPassword, firstName?, lastName?, localeCode?, enabled?) → JSON object of the newly created Sylius administrator.',
)]
final readonly class Create
{
    public function __construct(
        private AdminApiClient $client,
    ) {
    }

    /**
     * @param string $email         Administrator email address.
     * @param string $username      Administrator username.
     * @param string $plainPassword Plain text password for the new administrator.
     * @param string $firstName     First name. Default = "".
     * @param string $lastName      Last name. Default = "".
     * @param string $localeCode    Locale code, e.g. "en_US". Default = "en_US".
     * @param bool   $enabled       Whether the administrator is active. Default = true.
     */
    public function __invoke(
        string $email,
        string $username,
        string $plainPassword,
        string $firstName = '',
        string $lastName = '',
        string $localeCode = 'en_US',
        bool $enabled = true,
    ): string {
        $body = [
            'email' => $email,
            'username' => $username,
            'plainPassword' => $plainPassword,
            'localeCode' => $localeCode,
            'enabled' => $enabled,
        ];

        if ($firstName !== '') {
            $body['firstName'] = $firstName;
        }

        if ($lastName !== '') {
            $body['lastName'] = $lastName;
        }

        return $this->client->post('administrators', $body);
    }
}
