<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Migrations\Schema\v1_3;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Types;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

/**
 * Class DndDpdFranceShippingBundle
 *
 * @package   Dnd\Bundle\DpdFranceShippingBundle\Migrations\Schema\v1_3
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
    public function up(Schema $schema, QueryBag $queries): void
    {
        self::updateDndDpdFrShippingServiceTable($schema);
    }

    /**
     * Add some configuration parameters for the shipping services
     *
     * @param Schema $schema
     *
     * @return void
     * @throws SchemaException
     */
    public static function updateDndDpdFrShippingServiceTable(Schema $schema): void
    {
        /** @var Table $table */
        $table = $schema->getTable('dnd_dpd_fr_shipping_service');
        $table->addColumn('parcel_max_perimeter', Types::FLOAT, ['notnull' => false]);
        $table->addColumn('parcel_max_length', Types::FLOAT, ['notnull' => false]);
        $table->addColumn('parcel_max_weight', Types::FLOAT, ['notnull' => false]);
        $table->addColumn('parcel_max_amount', Types::INTEGER, ['notnull' => false]);
        $table->addColumn('parcel_max_value', Types::FLOAT, ['notnull' => false]);
    }
}