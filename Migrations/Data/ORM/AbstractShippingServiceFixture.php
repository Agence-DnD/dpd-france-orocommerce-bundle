<?php

namespace Dnd\Bundle\DpdFranceShippingBundle\Migrations\Data\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Dnd\Bundle\DpdFranceShippingBundle\Entity\ShippingService;
use Doctrine\Persistence\ObjectManager;

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
     * Description addUpdateShippingServices function
     *
     * @param ObjectManager $manager
     * @param array         $shippingServices
     * @param false         $setReferences
     *
     * @return void
     */
    protected function addUpdateShippingServices(
        ObjectManager $manager,
        array $shippingServices,
        bool $setReferences = false
    ) {
        $repository = $manager->getRepository('DndDpdFranceShippingBundle:ShippingService');
        foreach ($shippingServices as $ref => $shippingService) {
            $entity = $repository->findOneBy(['code' => $shippingService['code']]);
            if (!$entity) {
                $entity = new ShippingService();
            }

            $entity->setCode($shippingService['code']);
            $entity->setIcon($shippingService['icon']);
            $entity->setLabel($shippingService['label']);
            $manager->persist($entity);

            if ($setReferences) {
                $this->setReference($ref, $entity);
            }
        }
    }
}
