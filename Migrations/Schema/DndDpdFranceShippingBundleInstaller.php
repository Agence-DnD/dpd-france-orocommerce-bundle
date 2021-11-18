<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Migrations\Schema;

use Dnd\Bundle\DpdFranceShippingBundle\Migrations\Schema\v1_0\DndDpdFranceShippingBundle as DndDpdFranceShippingBundle_v1_O;
use Dnd\Bundle\DpdFranceShippingBundle\Migrations\Schema\v1_1\DndDpdFranceShippingBundle as DndDpdFranceShippingBundle_v1_1;
use Dnd\Bundle\DpdFranceShippingBundle\Migrations\Schema\v1_2\DndDpdFranceShippingBundle as DndDpdFranceShippingBundle_v1_2;
use Dnd\Bundle\DpdFranceShippingBundle\Migrations\Schema\v1_3\DndDpdFranceShippingBundle as DndDpdFranceShippingBundle_v1_3;
use Dnd\Bundle\DpdFranceShippingBundle\Migrations\Schema\v1_4\DndDpdFranceShippingBundle as DndDpdFranceShippingBundle_v1_4;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaException;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

/**
 * Class DndDpdFranceShippingBundleInstaller
 *
 * @package   Dnd\Bundle\DpdFranceShippingBundle\Migrations\Schema
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class DndDpdFranceShippingBundleInstaller implements Installation
{
    /**
     * {@inheritdoc}
     */
    public function getMigrationVersion(): string
    {
        return 'v1_4';
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     * @throws SchemaException
     */
    public function up(Schema $schema, QueryBag $queries): void
    {
        DndDpdFranceShippingBundle_v1_O::addStationFTPTransportColumns($schema);
        DndDpdFranceShippingBundle_v1_O::addGeneralDpdTransportColumns($schema);
        DndDpdFranceShippingBundle_v1_O::addDpdMethodsTransportColumns($schema);
        DndDpdFranceShippingBundle_v1_1::addProductMaxQtyForDpdFranceAttribute($schema);
        DndDpdFranceShippingBundle_v1_2::createDndDpdFrShippingServiceTable($schema);
        DndDpdFranceShippingBundle_v1_2::createDndDpdFrTransportShipServiceTable($schema);
        DndDpdFranceShippingBundle_v1_2::addDndDpdFrTransportShipServiceForeignKeys($schema);
        DndDpdFranceShippingBundle_v1_3::updateOroOrderTable($schema);
        DndDpdFranceShippingBundle_v1_3::updateOroCheckoutTable($schema);
        DndDpdFranceShippingBundle_v1_4::addStationFTPTransportColumns($schema);
    }
}
