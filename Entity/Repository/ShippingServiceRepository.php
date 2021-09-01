<?php

namespace Dnd\Bundle\DpdFranceShippingBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class ShippingServiceRepository
 *
 * @package   Dnd\Bundle\DpdFranceShippingBundle\Entity\Repository
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class ShippingServiceRepository extends EntityRepository
{
    /**
     * Description getAllShippingServiceCodes function
     *
     * @return string[]
     */
    public function getAllShippingServiceCodes(): array
    {
        $qb = $this->createQueryBuilder('shippingService')->select('shippingService.code');

        return array_column($qb->getQuery()->getResult(), 'code');
    }
}
