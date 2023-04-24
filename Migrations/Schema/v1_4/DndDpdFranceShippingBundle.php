<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Migrations\Schema\v1_4;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\DBAL\Types\Types;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

/**
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class DndDpdFranceShippingBundle implements Migration
{
    /**
     * {@inheritdoc}
     *
     * @throws SchemaException
     */
    public function up(Schema $schema, QueryBag $queries): void
    {
        self::addStationFTPTransportColumns($schema);
    }

    /**
     * Adds station status column to oro_integration_transport table.
     *
     * @throws SchemaException
     */
    public static function addStationFTPTransportColumns(Schema $schema): void
    {
        $transportTable = $schema->getTable('oro_integration_transport');
        $transportTable->addColumn('dpd_fr_station_enabled', Types::BOOLEAN, ['notnull' => false]);
    }
}
