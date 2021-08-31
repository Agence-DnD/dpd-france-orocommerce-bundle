<?php

namespace Dnd\Bundle\DpdFranceShippingBundle\Migrations\Schema\v1_2;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Types;
use Oro\Bundle\EntityBundle\EntityConfig\DatagridScope;
use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

/**
 * Class DndDpdFranceShippingBundle
 *
 * @package   Dnd\Bundle\DpdFranceShippingBundle\Migrations\Schema\v1_2
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class DndDpdFranceShippingBundle implements Migration
{
    /**
     * {@inheritdoc}
     *
     * @param Schema   $schema
     * @param QueryBag $queries
     *
     * @return void
     * @throws SchemaException
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        self::createDndDpdFrShippingServiceTable($schema);
        self::createDndDpdFrTransportShipServiceTable($schema);
        self::addDndDpdFrTransportShipServiceForeignKeys($schema);
    }

    /**
     * Description createDndDpdFrShippingServiceTable function
     *
     * @param Schema $schema
     *
     * @return void
     */
    public static function createDndDpdFrShippingServiceTable(Schema $schema)
    {
        $table = $schema->createTable('dnd_dpd_fr_shipping_service');


        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('code', 'string', ['notnull' => true, 'length' => 30]);
        $table->addColumn('label', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('icon', 'string', ['notnull' => false, 'length' => 255]);
        $table->setPrimaryKey(['code']);
        $table->addUniqueIndex(['code']);
    }

    /**
     * Description createDndDpdFrTransportShipServiceTable function
     *
     * @param Schema $schema
     *
     * @return void
     */
    public static function createDndDpdFrTransportShipServiceTable(Schema $schema)
    {
        $table = $schema->createTable('dnd_dpd_fr_transport_ship_service');
        $table->addColumn('transport_id', 'integer', []);
        $table->addColumn('ship_service_code', 'string', ['length' => 30]);
        $table->setPrimaryKey(['transport_id', 'ship_service_code']);
    }

    /**
     * Description addDndDpdFrTransportShipServiceForeignKeys function
     *
     * @param Schema $schema
     *
     * @return void
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