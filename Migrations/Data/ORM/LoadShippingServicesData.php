<?php

namespace Dnd\Bundle\DpdFranceShippingBundle\Migrations\Data\ORM;

use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Class LoadShippingServicesData
 *
 * @package   Oro\Bundle\DPDBundle\Migrations\Data\ORM
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class LoadShippingServicesData extends AbstractShippingServiceFixture implements ContainerAwareInterface
{
    /**
     * @var array
     */
    protected $loadedCountries;
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function load(ObjectManager $manager)
    {
        $this->addUpdateShippingServices($manager, $this->getShippingServicesData());
        $manager->flush();
    }

    /**
     * @return array
     */
    protected function getShippingServicesData()
    {
        return Yaml::parse(file_get_contents(__DIR__ . '/data/dpd_services.yml'));
    }
}
