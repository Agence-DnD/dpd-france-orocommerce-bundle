<?php

declare(strict_types=1);

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
     * Description $container field
     *
     * @var ContainerInterface $container
     */
    protected ContainerInterface $container;

    /**
     * {@inheritdoc}
     *
     * @param ContainerInterface|null $container
     *
     * @return void
     */
    public function setContainer(ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     *
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $this->loadShippingServices($manager, $this->getShippingServicesData());
        $manager->flush();
    }

    /**
     * Description getShippingServicesData function
     *
     * @return mixed[]
     */
    protected function getShippingServicesData(): array
    {
        return Yaml::parse(file_get_contents(__DIR__ . '/data/dpd_services.yml'));
    }
}
