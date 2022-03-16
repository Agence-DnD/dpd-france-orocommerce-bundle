<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Normalizer;

use Dnd\Bundle\DpdFranceShippingBundle\Exception\NormalizerException;
use Dnd\Bundle\DpdFranceShippingBundle\Model\DpdShippingPackageOptionsInterface;
use Oro\Bundle\OrderBundle\Converter\OrderShippingLineItemConverterInterface;
use Oro\Bundle\OrderBundle\Entity\Order;
use Oro\Bundle\OrderBundle\Entity\OrderAddress;
use Oro\Bundle\ShippingBundle\Model\ShippingOrigin;
use Oro\Bundle\ShippingBundle\Provider\ShippingOriginProvider;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class OrderNormalizer
 *
 * @package   Dnd\Commerce\Bundle\OrderBundle\Normalizer
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class OrderNormalizer implements NormalizerInterface
{
    /**
     * Required value
     *
     * @var string STATUS_MANDATORY
     */
    public const STATUS_MANDATORY = 'O';
    /**
     * Optional value
     *
     * @var string STATUS_OPTIONAL
     */
    public const STATUS_OPTIONAL = 'F';
    /**
     * Always empty
     *
     * @var string STATUS_EMPTY
     */
    public const STATUS_EMPTY = 'V';
    /**
     * Columns containing alphanumeric values
     *
     * @var string TYPE_ALPHANUMERIC
     */
    public const TYPE_ALPHANUMERIC = 'AN';
    /**
     * Columns containing numeric values
     *
     * @var string TYPE_NUMERIC
     */
    public const TYPE_NUMERIC = 'N';
    /**
     * Columns filled with air
     *
     * @var string TYPE_FILLER
     */
    public const TYPE_FILLER = 'F';
    /**
     * Grouping type
     *
     * @var int CONSOLIDATION_TYPE_DECLARATIVE
     */
    public const CONSOLIDATION_TYPE_DECLARATIVE = 38;
    /**
     * Grouping type
     *
     * @var int CONSOLIDATION_TYPE_DELIVERY_GROUPING
     */
    public const CONSOLIDATION_TYPE_DELIVERY_GROUPING = 1;
    /**
     * Description $shippingOriginProvider field
     *
     * @var ShippingOriginProvider $shippingOriginProvider
     */
    protected ShippingOriginProvider $shippingOriginProvider;
    /**
     * Description $shippingLineItemConverter field
     *
     * @var OrderShippingLineItemConverterInterface $shippingLineItemConverter
     */
    protected OrderShippingLineItemConverterInterface $shippingLineItemConverter;
    /**
     * Description $fillerCount field
     *
     * @var int $fillerCount
     */
    private int $fillerCount = 0;
    /**
     * Description $packageCount field
     *
     * @var int $packageCount
     */
    private int $packageCount = 0;

    /**
     * OrderNormalizer constructor
     *
     * @param ShippingOriginProvider                  $shippingOriginProvider
     */
    public function __construct(
        ShippingOriginProvider $shippingOriginProvider
    ) {
        $this->shippingOriginProvider    = $shippingOriginProvider;
    }

    /**
     * Ensures that vital elements are set in the context
     *
     * @param array $context
     *
     * @return void
     * @throws NormalizerException
     */
    private function checkContext(array $context): void
    {
        /** @var string[] $mandatoryKeys */
        $mandatoryKeys = ['shipping_service', 'settings', 'packages'];
        /** @var string $mandatoryKey */
        foreach ($mandatoryKeys as $mandatoryKey) {
            if (!isset($context[$mandatoryKey])) {
                throw new NormalizerException(sprintf('Could not fetch %s from context.', $mandatoryKey));
            }
        }
    }

    /**
     * {@inheritDoc}
     *
     * @param Order   $order
     * @param string  $format
     * @param mixed[] $context
     *
     * @return mixed[]
     * @throws NormalizerException
     */
    public function normalize($order, $format = null, array $context = []): array
    {
        /** @var mixed[] $data */
        $data = [];
        $this->checkContext($context);

        $this->packageCount = count($context['packages']);
        /** @var DpdShippingPackageOptionsInterface $package */
        foreach ($context['packages'] as $package) {
            $data[] = $this->getGeneralFields($order, $package);
            $data[] = $this->getRecipientFields($order);
            $data[] = $this->getSenderFields($order);
            $data[] = $this->getShipmentFields($order, $package, $context['settings']);
            $data[] = $this->getReturnFields();
            $data[] = $this->getLineEnd();
        }
        return array_merge([], ...$data);
    }

    /**
     * Builds an array of general elements
     *
     * @param Order                              $order
     * @param DpdShippingPackageOptionsInterface $package
     * @param ParameterBag                       $settings
     *
     * @return array
     * @throws NormalizerException
     */
    private function getGeneralFields(
        Order $order,
        DpdShippingPackageOptionsInterface $package
    ): array {
        return [
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_MANDATORY,
                1,
                35,
                'Référence client N°1',
                'BL' . $order->getId()
            ),
            $this->makeFiller(36, 2),
            $this->getElement(
                self::TYPE_NUMERIC,
                self::STATUS_MANDATORY,
                38,
                8,
                'Poids en décagramme',
                (string)(round($package->getWeight(), 2) * 100)
            ),
            $this->makeFiller(46, 15),
        ];
    }

    /**
     * Builds an array of recipient address related elements
     *
     * @param Order $order
     *
     * @return array
     * @throws NormalizerException
     */
    private function getRecipientFields(Order $order): array
    {
        /** @var OrderAddress $shippingAddress */
        $shippingAddress = $order->getShippingAddress();
        if (null === $shippingAddress) {
            throw new NormalizerException('The order has no shipping address');
        }

        return [
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_MANDATORY,
                61,
                35,
                'Nom destinataire',
                $shippingAddress->getLastName(),
            ),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                96,
                35,
                "Complément d’adresse 1",
                $shippingAddress->getFirstName(),
            ),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                131,
                35,
                "Complément d’adresse 2",
                $shippingAddress->getStreet2(),
            ),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                166,
                35,
                "Complément d’adresse 3",
                '',
            ),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                201,
                35,
                "Complément d’adresse 4",
                '',
            ),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                236,
                35,
                "Complément d’adresse 5",
                '',
            ),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_MANDATORY,
                271,
                10,
                "Code postal",
                $shippingAddress->getPostalCode(),
            ),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_MANDATORY,
                281,
                35,
                "Ville",
                $shippingAddress->getCity(),
            ),
            $this->makeFiller(316, 10),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_MANDATORY,
                326,
                35,
                "Rue",
                $shippingAddress->getStreet(),
            ),
            $this->makeFiller(361, 10),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_MANDATORY,
                371,
                3,
                "Code Pays",
                $shippingAddress->getCountryIso2(),
            ),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                374,
                20,
                "Téléphone",
                $order->getDeliveryPhone()
            ),
            $this->makeFiller(394, 25),
        ];
    }

    /**
     * Builds an array of recipient address related elements
     *
     * @param Order $order
     *
     * @return array
     * @throws NormalizerException
     */
    private function getSenderFields(Order $order): array
    {
        /** @var ShippingOrigin $shippingOrigin */
        $shippingOrigin = $this->shippingOriginProvider->getSystemShippingOrigin();

        /** @var string[] $multiLineCustomerNotes */
        $multiLineCustomerNotes = explode("\n", wordwrap($order->getCustomerNotes() ?? '', 35));

        return [
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                419,
                35,
                'Nom expéditeur',
                $shippingOrigin->getLastName(),
            ),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                454,
                35,
                "Complément d’adresse 1",
                $shippingOrigin->getFirstName(),
            ),
            $this->makeFiller(489, 35),
            $this->makeFiller(524, 35),
            $this->makeFiller(559, 35),
            $this->makeFiller(594, 35),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                629,
                10,
                "CP",
                $shippingOrigin->getPostalCode(),
            ),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                639,
                35,
                "Ville",
                $shippingOrigin->getCity(),
            ),
            $this->makeFiller(674, 10),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                684,
                35,
                "Rue",
                $shippingOrigin->getStreet(),
            ),
            $this->makeFiller(719, 10),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                729,
                3,
                "Code Pays",
                $shippingOrigin->getCountryIso2(),
            ),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                732,
                20,
                "Téléphone",
                '' //@TODO set phone in integration settings & fetch it here
            ),
            $this->makeFiller(752, 10),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                762,
                35,
                "Commentaire 1",
                $multiLineCustomerNotes[0] ?? ''
            ),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                797,
                35,
                "Commentaire 2",
                $multiLineCustomerNotes[1] ?? ''
            ),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                832,
                35,
                "Commentaire 3",
                $multiLineCustomerNotes[2] ?? ''
            ),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                867,
                35,
                "Commentaire 4",
                $multiLineCustomerNotes[3] ?? ''
            ),
        ];
    }

    /**
     * Builds an array of elements related with the shipment
     *
     * @param Order                              $order
     * @param DpdShippingPackageOptionsInterface $package
     * @param ParameterBag                       $settings
     *
     * @return array
     * @throws NormalizerException
     */
    private function getShipmentFields(
        Order $order,
        DpdShippingPackageOptionsInterface $package,
        ParameterBag $settings
    ): array {
        /** @var OrderAddress $shippingAddress */
        $shippingAddress = $order->getShippingAddress();

        if (null === $shippingAddress) {
            throw new NormalizerException('The order has no shipping address');
        }

        return [
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                902,
                10,
                "Date d'expédition",
                date('')
            ),
            $this->getElement(
                self::TYPE_NUMERIC,
                self::STATUS_OPTIONAL,
                912,
                8,
                "Numéro de compte",
                $settings->get('dpd_fr_contract_number')
            ),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                920,
                35,
                'Code à barres',
                ''
            ),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                955,
                35,
                'Référence client N°2',
                ''
            ),
            $this->makeFiller(990, 29),
            $this->getElement(
                self::TYPE_NUMERIC,
                self::STATUS_OPTIONAL,
                1019,
                9,
                'Montant de la valeur déclarée',
                $package->getPrice()->getValue()
            ),
            $this->makeFiller(1028, 8),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                1036,
                35,
                'Référence client N°3',
                ''
            ),
            $this->makeFiller(1071, 1),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                1072,
                35,
                'Numéro de consolidation',
                $this->packageCount > 1 ? 'BL' . $order->getId() : ''
            ),
            $this->makeFiller(1107, 10),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                1117,
                80,
                'Email expéditeur',
                '' //@TODO set contact email in integration settings & fetch it here
            ),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                1197,
                35,
                'GSM expéditeur',
                '' //@TODO set GSM phone in integration settings & fetch it here
            ),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                1232,
                80,
                'Email destinataire',
                $order->getEmail()
            ),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                $order->getShippingMethodType() !== 'predict' ? self::STATUS_OPTIONAL : self::STATUS_MANDATORY,
                1312,
                35,
                'GSM destinataire',
                $order->getDeliveryPhone()
            ),
            $this->makeFiller(1347, 96),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                1443,
                8,
                'Identifiant du point relais',
                $order->getDpdFrRelayId()
            ),
            $this->makeFiller(1451, 113),
            $this->getElement(
                self::TYPE_NUMERIC,
                self::STATUS_OPTIONAL,
                1564,
                2,
                'Consolidation /type',
                self::CONSOLIDATION_TYPE_DECLARATIVE
            ),
            $this->getElement(
                self::TYPE_NUMERIC,
                self::STATUS_OPTIONAL,
                1566,
                2,
                'Consolidation /type',
                $this->packageCount > 1 ? 1 : 0
            ),
            $this->makeFiller(1568, 1),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                $order->getShippingMethodType() !== 'predict' ? self::STATUS_OPTIONAL : self::STATUS_MANDATORY,
                1569,
                1,
                'Predict',
                $order->getShippingMethodType() === 'predict' ? '+' : '0' // so much for a numeric value
            ),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                1570,
                35,
                'Nom du contact',
                $shippingAddress->getLastName()
            ),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                1605,
                10,
                'DigiCode1',
                ''
            ),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                1615,
                10,
                'DigiCode2',
                ''
            ),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                1625,
                10,
                'Intercom',
                ''
            ),
            $this->makeFiller(1635, 200),
        ];
    }

    /**
     * Builds an array of elements related with the return procedure
     * Return policy not implemented yet, returns only empty values
     *
     * @return array
     * @throws NormalizerException
     */
    private function getReturnFields(): array
    {
        return [
            $this->getElement(
                self::TYPE_NUMERIC,
                self::STATUS_OPTIONAL,
                1835,
                1,
                'Retour',
                0
            ),
            $this->makeFiller(1836, 15),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                1851,
                35,
                'Nom destinataire retour',
                ''
            ),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                1886,
                35,
                "Complément d’adresse 1",
                '',
            ),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                1921,
                35,
                "Complément d’adresse 2",
                '',
            ),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                1956,
                35,
                "Complément d’adresse 3",
                '',
            ),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                1991,
                35,
                "Complément d’adresse 4",
                '',
            ),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                2026,
                35,
                "Complément d’adresse 5",
                '',
            ),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                2061,
                10,
                "CP",
                '',
            ),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                2071,
                35,
                "Ville",
                '',
            ),
            $this->makeFiller(2106, 10),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                2116,
                35,
                "Rue",
                '',
            ),
            $this->makeFiller(2151, 10),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                2161,
                3,
                "Code Pays",
                '',
            ),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                2164,
                30,
                "Téléphone",
                '',
            ),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                2194,
                18,
                "Cargo ID",
                '',
            ),
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_OPTIONAL,
                2212,
                35,
                "Référence client N°4",
                '',
            ),
        ];
    }

    /**
     * Returns a line closing element
     *
     * @return mixed[][]
     * @throws NormalizerException
     */
    private function getLineEnd(): array
    {
        return [
            $this->getElement(
                self::TYPE_ALPHANUMERIC,
                self::STATUS_MANDATORY,
                2247,
                2,
                "Fin d'enregistrement",
                PHP_EOL
            ),
        ];
    }

    /**
     * Returns a filler with a given position and length
     *
     * @param int $position
     * @param int $length
     *
     * @return mixed[]
     * @throws NormalizerException
     */
    private function makeFiller(int $position, int $length): array
    {
        $this->fillerCount++;

        return $this->getElement(
            self::TYPE_FILLER,
            self::STATUS_EMPTY,
            $position,
            $length,
            sprintf('Filler #%d', $this->fillerCount)
        );
    }

    /**
     * Builds a formatted element ready to be assembled into the export string
     *
     * @param string $format
     * @param string $status
     * @param int    $position
     * @param int    $length
     * @param string $code
     * @param mixed  $value
     *
     * @return mixed[]
     * @throws NormalizerException
     */
    private function getElement(
        string $format,
        string $status,
        int $position,
        int $length,
        string $code,
        $value = null
    ): array {
        if ($status === self::STATUS_MANDATORY && $value === null) {
            throw new NormalizerException(sprintf('missing value for mandatory attribute %s', $code));
        }
        /** @var mixed[] $element */
        $element = [];
        if ($status === self::STATUS_EMPTY || ($status === self::STATUS_OPTIONAL && $value === '')) {
            $format = self::TYPE_FILLER;
        }
        $element['code']     = $code;
        $element['position'] = $position;
        $element['length']   = $length;
        $element['value']    = $this->format($value, $format, $length);

        return $element;
    }

    /**
     * Formats the different kind of data to DPD Station requirements
     *
     * @param mixed  $value
     * @param string $format
     * @param int    $length
     *
     * @return string
     */
    private function format($value, string $format, int $length): string
    {
        /** @var string $output */
        $output = '';
        switch ($format) {
            case self::TYPE_NUMERIC:
                /** @var float $value */
                $value  = round((float)$value, 2);
                $output = str_pad((string)$value, $length, '0', STR_PAD_LEFT);
                break;
            case self::TYPE_FILLER:
                $output = str_repeat(' ', $length);
                break;
            case self::TYPE_ALPHANUMERIC:
                $value = (string)$value;
                $output = str_pad($value ?? '', $length, ' ', STR_PAD_RIGHT);
                break;
            default:
                $output = $value;
                break;
        }

        return substr($output, 0, $length);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Order && $format === 'dpd_fr_station';
    }
}
