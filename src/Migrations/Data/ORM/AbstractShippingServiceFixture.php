<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Migrations\Data\ORM;

use Dnd\Bundle\DpdFranceShippingBundle\Entity\ShippingService;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

/**
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
abstract class AbstractShippingServiceFixture extends AbstractFixture
{
    /**
     * Default value for the parcel max perimeter setting
     */
    public const DEFAULT_PARCEL_MAX_PERIMETER = 3;
    /**
     * Default value for the parcel max perimeter setting
     */
    public const DEFAULT_PARCEL_MAX_LENGTH = 2;
    /**
     * Default value for the parcel max perimeter setting
     */
    public const DEFAULT_PARCEL_MAX_WEIGHT = 30;
    /**
     * Default value for the parcel max perimeter setting
     */
    public const DEFAULT_PARCEL_MAX_AMOUNT = 5;
    /**
     * Default value for the parcel max cumulated value
     */
    public const DEFAULT_PARCEL_MAX_VALUE = 22000;

    protected function loadShippingServices(
        ObjectManager $manager,
        array $shippingServices,
        bool $setReferences = false
    ): void {
        $repository = $manager->getRepository('DndDpdFranceShippingBundle:ShippingService');
        foreach ($shippingServices as $ref => $shippingService) {
            $entity = $repository->findOneBy(['code' => $shippingService['code']]);
            if (!$entity) {
                /** @var ShippingService $entity */
                $entity = new ShippingService();
            }

            $entity->setCode($shippingService['code']);
            $entity->setIcon($shippingService['icon']);
            $entity->setLabel($shippingService['label']);
            $entity->setParcelMaxPerimeter(
                $shippingService['parcel_max_perimeter'] ?? self::DEFAULT_PARCEL_MAX_PERIMETER
            );
            $entity->setParcelMaxLength(
                $shippingService['parcel_max_length'] ?? self::DEFAULT_PARCEL_MAX_LENGTH
            );
            $entity->setParcelMaxWeight(
                $shippingService['parcel_max_weight'] ?? self::DEFAULT_PARCEL_MAX_WEIGHT
            );
            $entity->setParcelMaxAmount(
                $shippingService['parcel_max_amount'] ?? null
            );
            $entity->setParcelMaxValue(
                $shippingService['parcel_max_value'] ?? self::DEFAULT_PARCEL_MAX_VALUE
            );
            $manager->persist($entity);

            if ($setReferences) {
                $this->setReference($ref, $entity);
            }
        }
    }
}
