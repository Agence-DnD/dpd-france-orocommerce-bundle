<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Migrations\Data\ORM;

use Dnd\Bundle\DpdFranceShippingBundle\Entity\ShippingService;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;

/**
 * Class AbstractShippingServiceFixture
 *
 * @package   Dnd\Bundle\DpdFranceShippingBundle\Migrations\Data\ORM
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
abstract class AbstractShippingServiceFixture extends AbstractFixture
{
    /**
     * Default value for the parcel max perimeter setting
     *
     * @var float DEFAULT_PARCEL_MAX_PERIMETER
     */
    public const DEFAULT_PARCEL_MAX_PERIMETER = 3;
    /**
     * Default value for the parcel max perimeter setting
     *
     * @var float DEFAULT_PARCEL_MAX_LENGTH
     */
    public const DEFAULT_PARCEL_MAX_LENGTH = 2;
    /**
     * Default value for the parcel max perimeter setting
     *
     * @var float DEFAULT_PARCEL_MAX_WEIGHT
     */
    public const DEFAULT_PARCEL_MAX_WEIGHT = 30;
    /**
     * Default value for the parcel max perimeter setting
     *
     * @var integer DEFAULT_PARCEL_MAX_AMOUNT
     */
    public const DEFAULT_PARCEL_MAX_AMOUNT = 5;

    /**
     * Description loadShippingServices function
     *
     * @param ObjectManager $manager
     * @param array         $shippingServices
     * @param false         $setReferences
     *
     * @return void
     */
    protected function loadShippingServices(
        ObjectManager $manager,
        array $shippingServices,
        bool $setReferences = false
    ): void {
        /** @var ObjectRepository $repository */
        $repository = $manager->getRepository('DndDpdFranceShippingBundle:ShippingService');
        /**
         * @var string $ref
         * @var ShippingService $shippingService
         */
        foreach ($shippingServices as $ref => $shippingService) {
            /** @var ShippingService|false $entity */
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
                $shippingService['parcel_max_amount'] ?? self::DEFAULT_PARCEL_MAX_AMOUNT
            );
            $manager->persist($entity);

            if ($setReferences) {
                $this->setReference($ref, $entity);
            }
        }
    }
}
