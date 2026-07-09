<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Address;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_address',
    description: 'update_address(id, firstName, lastName, street, city, postcode, countryCode, company?, phoneNumber?, provinceCode?, provinceName?) → JSON object of the updated Sylius address. firstName, lastName, street, city, postcode and countryCode are required by the API.',
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int    $id            Address ID.
     * @param string $firstName     First name (required).
     * @param string $lastName      Last name (required).
     * @param string $street        Street address (required).
     * @param string $city          City (required).
     * @param string $postcode      Postal code (required).
     * @param string $countryCode   ISO 3166-1 alpha-2 country code, e.g. "US", "FR" (required).
     * @param string $company       Company name. Default = "".
     * @param string $phoneNumber   Phone number. Default = "".
     * @param string $provinceCode  Province/state code. Default = "".
     * @param string $provinceName  Province/state name. Default = "".
     */
    public function __invoke(
        int $id,
        string $firstName,
        string $lastName,
        string $street,
        string $city,
        string $postcode,
        string $countryCode,
        string $company = '',
        string $phoneNumber = '',
        string $provinceCode = '',
        string $provinceName = '',
    ): string {
        $body = [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'street' => $street,
            'city' => $city,
            'postcode' => $postcode,
            'countryCode' => $countryCode,
        ];

        if ($company !== '') {
            $body['company'] = $company;
        }
        if ($phoneNumber !== '') {
            $body['phoneNumber'] = $phoneNumber;
        }
        if ($provinceCode !== '') {
            $body['provinceCode'] = $provinceCode;
        }
        if ($provinceName !== '') {
            $body['provinceName'] = $provinceName;
        }

        return $this->client->put(sprintf('addresses/%d', $id), $body);
    }
}
