<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Migrations\Schema\v1_2;

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
        self::createDndDpdFrShippingServiceTable($schema);
        self::createDndDpdFrTransportShipServiceTable($schema);
        self::addDndDpdFrTransportShipServiceForeignKeys($schema);
    }

    /**
     * Create a table to store DPD FR service settings
     */
    public static function createDndDpdFrShippingServiceTable(Schema $schema): void
    {
        $table = $schema->createTable('dnd_dpd_fr_shipping_service');
        $table->addColumn('code', Types::STRING, ['length' => 30]);
        $table->addColumn('label', Types::STRING, ['length' => 255]);
        $table->addColumn('icon', Types::STRING, ['length' => 255]);
        $table->addColumn('parcel_max_perimeter', Types::FLOAT);
        $table->addColumn('parcel_max_length', Types::FLOAT);
        $table->addColumn('parcel_max_weight', Types::FLOAT);
        $table->addColumn('parcel_max_amount', Types::INTEGER, ['notnull' => false]);
        $table->addColumn('parcel_max_value', Types::FLOAT);
        $table->setPrimaryKey(['code']);
        $table->addUniqueIndex(['code']);
    }

    /**
     * Create a table to link dpd fr shipping service and dpd fr transport
     */
    public static function createDndDpdFrTransportShipServiceTable(Schema $schema): void
    {
        $table = $schema->createTable('dnd_dpd_fr_transport_ship_service');
        $table->addColumn('transport_id', 'integer', []);
        $table->addColumn('ship_service_code', 'string', ['length' => 30]);
        $table->setPrimaryKey(['transport_id', 'ship_service_code']);
        $table->addUniqueIndex(['ship_service_code']);
    }

    /**
     * Adds the foreign keys to the dnd_dpd_fr_transport_ship_service table
     *
     * @throws SchemaException
     */
    public static function addDndDpdFrTransportShipServiceForeignKeys(Schema $schema): void
    {
        $table = $schema->getTable('dnd_dpd_fr_transport_ship_service');
        $table->addForeignKeyConstraint(
            $schema->getTable('dnd_dpd_fr_shipping_service'),
            ['ship_service_code'],
            ['code'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_integration_transport'),
            ['transport_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
    }
}
