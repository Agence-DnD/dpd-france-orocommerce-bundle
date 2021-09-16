<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Provider;

use Dnd\Bundle\DpdFranceShippingBundle\Exception\PudoException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class PudoProvider
 *
 * @package   Dnd\Bundle\DpdFranceShippingBundle\Provider
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class PudoProvider
{
    /**
     * MyPUDO webservice endpoint to retrieve PUDO list
     *
     * @var string ENDPOINT
     */
    public const ENDPOINT = '/mypudo/mypudo.asmx/GetPudoList';
    /**
     * MyPUDO webservice host URL
     *
     * @var string HOST_URL
     */
    public const HOST_URL = 'mypudo.pickup-services.com';
    /**
     * Iso2 country code for France
     *
     * @var string FR_COUNTRY_CODE
     */
    public const FR_COUNTRY_CODE = 'FR';
    /**
     * Access identifier
     *
     * @var string CARRIER
     */
    public const CARRIER = 'EXA';
    /**
     * MyPUDO webservice security key
     *
     * @var string SECURITY_KEY
     */
    public const SECURITY_KEY = 'deecd7bc81b71fcc0e292b53e826c48f';
    /**
     * Timeout for pudo retrieval
     *
     * @var string TIMEOUT
     */
    public const TIMEOUT = 30;
    /**
     * Description $client field
     *
     * @var HttpClientInterface $client
     */
    protected HttpClientInterface $client;

    /**
     * PudoProvider constructor
     *
     * @param HttpClientInterface $client
     */
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Description getUrl function
     *
     * @return string
     */
    private static function getUrl(): string
    {
        return implode('', [
            'https://',
            self::HOST_URL,
            self::ENDPOINT,
        ]);
    }

    /**
     * Builds the list of params which have to be wrapped in the request body
     *
     * @param string      $checkoutId
     * @param string|null $city
     * @param string|null $postalCode
     * @param string|null $address
     *
     * @return array
     */
    private function getParams(string $checkoutId, ?string $city, ?string $postalCode, ?string $address): array
    {
        /** @var \DateTime $now */
        $now = new \DateTime();

        return [
            'carrier'             => self::CARRIER,
            'key'                 => self::SECURITY_KEY,
            'address'             => $address ?? '',
            'zipCode'             => $postalCode ?? '',
            'city'                => $city ?? '',
            'countrycode'         => self::FR_COUNTRY_CODE,
            'requestID'           => $checkoutId,
            'date_from'           => $now->add(new \DateInterval('P1D'))->format('d/m/Y'),
            'max_distance_search' => '',
            'category'            => '',
            'max_pudo_number'     => '',
            'weight'              => '',
            'holiday_tolerant'    => '',
        ];
    }

    /**
     * Description getPudoList function
     *
     * @param string      $checkoutId
     * @param string|null $city
     * @param string|null $postalCode
     * @param string|null $address
     *
     * @return mixed[]
     * @throws PudoException
     * @throws TransportExceptionInterface
     * @throws \JsonException
     */
    public function getPudoList(string $checkoutId, ?string $city, ?string $postalCode, ?string $address): array
    {
        /** @var Response $response */
        $response = $this->client->request(Request::METHOD_POST, self::getUrl(), [
            'http_version' => 1.1,
            'headers'      => [
                'Content-Type' => "application/x-www-form-urlencoded",

            ],
            'body'         => $this->getParams($checkoutId, $city, $postalCode, $address),
            'timeout'      => self::TIMEOUT,
        ]);

        if (Response::HTTP_OK !== $response->getStatusCode()) {
            throw new PudoException(
                sprintf(
                    'MyPudo WS returned an error response [%d] - %s',
                    $response->getStatusCode(),
                    $response->getContent()
                )
            );
        }

        return $this->parseXML($response->getContent());
    }

    /**
     * Turns a xml string into an associative array
     *
     * @param string $xml
     *
     * @return mixed[]
     * @throws \JsonException
     */
    private function parseXML(string $xml): array
    {
        return json_decode(
            json_encode((array)simplexml_load_string($xml), JSON_THROW_ON_ERROR),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }
}
