<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Layout\DataProvider;

use Dnd\Bundle\DpdFranceShippingBundle\Exception\PudoException;
use Dnd\Bundle\DpdFranceShippingBundle\Provider\PudoProvider;
use SimpleXMLElement;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class OrderDetailsProvider
 *
 * @package   Dnd\Bundle\DpdFranceShippingBundle\Layout\Dataprovider
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class OrderDetailsProvider
{
    /**
     * Description $pudoProvider field
     *
     * @var PudoProvider $pudoProvider
     */
    protected PudoProvider $pudoProvider;
    /**
     * Description $pudoDetails field
     *
     * @var array $pudoDetails
     */
    private array $pudoDetails;
    /**
     * OrderDetailsProvider constructor
     *
     * @param PudoProvider $pudoProvider
     */
    public function __construct(PudoProvider $pudoProvider)
    {
        $this->pudoProvider = $pudoProvider;
    }

    /**
     * Description getPudoDetails function
     *
     * @param string $pudoID
     *
     * @return SimpleXMLElement|null
     */
    public function getPudoDetails(string $pudoID): ?SimpleXMLElement
    {
        if (!isset($this->pudoDetails[$pudoID])) {
            try {
                $this->pudoDetails[$pudoID] = $this->pudoProvider->getPudoDetails($pudoID);
            } catch (PudoException|\JsonException|TransportExceptionInterface $e) {
                $this->pudoDetails[$pudoID] = null;
            }
        }

        return $this->pudoDetails[$pudoID];
    }

    /**
     * Returns the SimpleXMLElement corresponding to the pudo object
     *
     * @param string $pudoID
     *
     * @return SimpleXMLElement|null
     */
    private function getPudoObject(string $pudoID): ?SimpleXMLElement
    {
        /** @var SimpleXMLElement|null $pudoDetails */
        $pudoDetails = $this->getPudoDetails($pudoID);

        return $pudoDetails !== null ? $pudoDetails->PUDO_ITEMS->PUDO_ITEM : null;
    }

    /**
     * Returns the name of the pudo with id = $pudoID
     *
     * @param string $pudoID
     *
     * @return string
     */
    public function getPudoName(string $pudoID): string
    {
        /** @var SimpleXMLElement|null $pudoObject */
        $pudoObject = $this->getPudoObject($pudoID);
        if ($pudoObject === null) {
            return '';
        }
        return $pudoObject->NAME->__toString() ?? '';
    }


    /**
     * Returns the address of the pudo with id = $pudoID
     *
     * @param string $pudoID
     *
     * @return string
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
                $pudoObject->CITY->__toString() ?? ''
            ]
        );
    }
}
