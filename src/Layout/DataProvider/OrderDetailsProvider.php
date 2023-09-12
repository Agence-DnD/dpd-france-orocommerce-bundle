<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Layout\DataProvider;

use Dnd\Bundle\DpdFranceShippingBundle\Entity\DpdFranceTransportSettings;
use Dnd\Bundle\DpdFranceShippingBundle\Exception\PudoException;
use Dnd\Bundle\DpdFranceShippingBundle\Provider\PudoProvider;
use Oro\Bundle\OrderBundle\Entity\Order;
use SimpleXMLElement;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class OrderDetailsProvider
{
    private array $pudoDetails = [];

    public function __construct(
        private readonly PudoProvider $pudoProvider
    ) {
    }

    public function isPickupDetailsVisible(?Order $order): ?bool
    {
        if ($order->getShippingMethodType() !== DpdFranceTransportSettings::IDENTIFIER_PICKUP) {
            return false;
        }
        $pudoID = $order->getDpdFrRelayId();

        return ($pudoID !== null && $pudoID !== '' && $pudoID !== '-1');
    }

    public function isPredictDetailsVisible(?Order $order): ?bool
    {
        return $order?->getShippingMethodType() === DpdFranceTransportSettings::IDENTIFIER_PREDICT;
    }

    public function getPredictPhone(?Order $order): ?string
    {
        return !empty($order?->getDeliveryPhone()) ? $order?->getDeliveryPhone() : 'N/A';
    }

    public function getPudoDetails(string $pudoID): ?SimpleXMLElement
    {
        if (!isset($this->pudoDetails[$pudoID])) {
            try {
                $this->pudoDetails[$pudoID] = $this->pudoProvider->getPudoDetails($pudoID);
            } catch (PudoException | \JsonException | TransportExceptionInterface $e) {
                $this->pudoDetails[$pudoID] = null;
            }
        }

        return $this->pudoDetails[$pudoID];
    }

    /**
     * Returns the SimpleXMLElement corresponding to the pudo object
     */
    private function getPudoObject(string $pudoID): ?SimpleXMLElement
    {
        $pudoDetails = $this->getPudoDetails($pudoID);

        return $pudoDetails !== null ? $pudoDetails->PUDO_ITEMS->PUDO_ITEM : null;
    }

    /**
     * Returns the name of the pudo with id = $pudoID
     */
    public function getPudoName(string $pudoID): string
    {
        $pudoObject = $this->getPudoObject($pudoID);
        if ($pudoObject === null) {
            return '';
        }

        return $pudoObject->NAME->__toString() ?? '';
    }

    /**
     * Returns the address of the pudo with id = $pudoID
     */
    public function getPudoAddress(string $pudoID): string
    {
        /** @var SimpleXMLElement|null $pudoObject */
        $pudoObject = $this->getPudoObject($pudoID);

        if ($pudoObject === null) {
            return '';
        }

        return implode(
            PHP_EOL,
            [
                $pudoObject->ADDRESS1->__toString() ?? '',
                $pudoObject->ZIPCODE->__toString() ?? '',
                $pudoObject->CITY->__toString() ?? '',
            ]
        );
    }
}
