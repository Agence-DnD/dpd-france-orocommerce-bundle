<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Method;

use Dnd\Bundle\DpdFranceShippingBundle\Integration\DpdFranceChannel;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\ShippingBundle\Method\Factory\IntegrationShippingMethodFactoryInterface;
use Oro\Bundle\ShippingBundle\Method\Provider\Integration\ChannelShippingMethodProvider;

/**
 * Class DpdFranceShippingMethodProvider
 * Provides methods for getting shipping method object by name
 *
 * @package   Dnd\Bundle\DpdFranceShippingBundle\Method
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class DpdFranceShippingMethodProvider extends ChannelShippingMethodProvider
{
    /**
     * {@inheritDoc}
     */
    public function __construct(
        $channelType,
        DoctrineHelper $doctrineHelper,
        IntegrationShippingMethodFactoryInterface $methodFactory
    ) {
        parent::__construct($channelType, $doctrineHelper, $methodFactory);
    }

    /**
     * Description isDpdFrShippingMethod function
     *
     * @param string|null $shippingMethodIdentifier
     *
     * @return bool
     */
    public static function isDpdFrShippingMethod(?string $shippingMethodIdentifier): bool
    {
        return $shippingMethodIdentifier !== null && str_contains($shippingMethodIdentifier, DpdFranceChannel::TYPE);
    }
}
