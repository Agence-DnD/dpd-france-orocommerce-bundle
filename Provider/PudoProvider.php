<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Provider;

use Dnd\Bundle\DpdFranceShippingBundle\Exception\PudoException;
use SimpleXMLElement;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class PudoProvider
{
    /**
     * MyPUDO webservice endpoint to retrieve PUDO details
     */
    public const ENDPOINT_DETAILS = '/mypudo/mypudo.asmx/GetPudoDetails';
    /**
     * MyPUDO webservice endpoint to retrieve PUDO list
     */
    public const ENDPOINT_LIST = '/mypudo/mypudo.asmx/GetPudoList';
    /**
     * MyPUDO webservice host URL
     */
    public const HOST_URL = 'mypudo.pickup-services.com';
    /**
     * Iso2 country code for France
     */
    public const FR_COUNTRY_CODE = 'FR';
    /**
     * Access identifier
     */
    public const CARRIER = 'EXA';
    /**
     * MyPUDO webservice security key
     */
    public const SECURITY_KEY = 'deecd7bc81b71fcc0e292b53e826c48f';
    /**
     * Timeout for pudo retrieval
     */
    public const TIMEOUT = 30;

    public function __construct(
        private readonly HttpClientInterface $client
    ) {
    }

    private static function getUrl(?string $mode = 'list'): string
    {
        $endpoint = self::ENDPOINT_LIST;
        if ($mode === 'details') {
            $endpoint = self::ENDPOINT_DETAILS;
        }

        return implode('', [
            'https://',
            self::HOST_URL,
            $endpoint,
        ]);
    }

    /**
     * Builds the list of params which have to be wrapped in the request body
     */
    private function getParams(string $checkoutId, ?string $city, ?string $postalCode, ?string $address): array
    {
        $now = new \DateTime();

        return [
            'carrier' => self::CARRIER,
            'key' => self::SECURITY_KEY,
            'address' => $address ?? '',
            'zipCode' => $postalCode ?? '',
            'city' => $city ?? '',
            'countrycode' => self::FR_COUNTRY_CODE,
            'requestID' => $checkoutId,
            'date_from' => $now->add(new \DateInterval('P1D'))->format('d/m/Y'),
            'max_distance_search' => '',
            'category' => '',
            'max_pudo_number' => '',
            'weight' => '',
            'holiday_tolerant' => '',
        ];
    }

    /**
     * Fetches the list of relay points for a given set of parameters
     *
     * @throws PudoException
     * @throws TransportExceptionInterface
     * @throws \JsonException
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function getPudoList(string $checkoutId, ?string $city, ?string $postalCode, ?string $address): array
    {
        $response = $this->client->request(Request::METHOD_POST, self::getUrl(), [
            'http_version' => 1.1,
            'headers' => [
                'Content-Type' => "application/x-www-form-urlencoded",

            ],
            'body' => $this->getParams($checkoutId, $city, $postalCode, $address),
            'timeout' => self::TIMEOUT,
        ]);

        return $this->handleResponse($response);
    }

    /**
     * Handles WS Response, parses it in case of success, throws an error in case of failure
     *
     * @throws PudoException
     * @throws TransportExceptionInterface
     * @throws \JsonException
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    private function handleResponse(ResponseInterface $response, bool $parseXML = true)
    {
        if (Response::HTTP_OK !== $response->getStatusCode()) {
            throw new PudoException(
                sprintf(
                    'MyPudo WS returned an error response [%d] - %s',
                    $response->getStatusCode(),
                    $response->getContent()
                )
            );
        }

        return $parseXML ? $this->parseXML($response->getContent()) : simplexml_load_string($response->getContent());
    }

    /**
     * Fetches the details for a given pudo
     *
     * @return SimpleXMLElement
     * @throws PudoException
     * @throws TransportExceptionInterface
     * @throws JsonException
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function getPudoDetails(string $pudoID): SimpleXMLElement
    {
        $response = $this->client->request(Request::METHOD_GET, self::getUrl('details'), [
            'http_version' => 1.1,
            'headers' => [
                'Content-Type' => "application/x-www-form-urlencoded",

            ],
            'query' => [
                'pudo_id' => $pudoID,
                'carrier' => self::CARRIER,
                'key' => self::SECURITY_KEY,
            ],
            'timeout' => self::TIMEOUT,
        ]);

        return $this->handleResponse($response, false);
    }

    /**
     * Turns a xml string into an associative array
     *
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
