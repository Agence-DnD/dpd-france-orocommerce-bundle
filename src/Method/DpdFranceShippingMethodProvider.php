<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Method;

use Dnd\Bundle\DpdFranceShippingBundle\Integration\DpdFranceChannel;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\ShippingBundle\Method\Factory\IntegrationShippingMethodFactoryInterface;
use Oro\Bundle\ShippingBundle\Method\Provider\Integration\ChannelShippingMethodProvider;
use Oro\Bundle\ShippingBundle\Method\Provider\Integration\ShippingMethodLoader;

/**
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class DpdFranceShippingMethodProvider extends ChannelShippingMethodProvider
{
    /**
     * {@inheritDoc}
     */
    public function __construct(
        $channelType,
        protected readonly IntegrationShippingMethodFactoryInterface $methodFactory,
        protected readonly ShippingMethodLoader $shippingMethodLoader
    ) {
        parent::__construct($channelType, $methodFactory, $shippingMethodLoader);
    }

    public static function isDpdFrShippingMethod(?string $shippingMethodIdentifier): bool
    {
        return $shippingMethodIdentifier !== null && str_contains($shippingMethodIdentifier, DpdFranceChannel::TYPE);
    }
}
