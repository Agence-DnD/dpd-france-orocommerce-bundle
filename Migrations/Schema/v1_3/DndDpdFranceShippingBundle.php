<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Migrations\Schema\v1_3;

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
        self::updateOroOrderTable($schema);
        self::updateOroCheckoutTable($schema);
    }

    /**
     * Add a delivery_phone number, a relay_id and a dpd exported stamp at the order level
     *
     * @param Schema $schema
     *
     * @return void
     * @throws SchemaException
     */
    public static function updateOroOrderTable(Schema $schema): void
    {
        /** @var Table $table */
        $table = $schema->getTable('oro_order');
        $table->addColumn('delivery_phone', Types::STRING, [
            'notnull'     => false,
            'oro_options' => [
                'extend' => [
                    'is_extend'     => true,
                    'owner'         => ExtendScope::OWNER_CUSTOM,
                    'is_serialized' => false,
                ],
                'form'        => ['is_enabled' => true],
                'datagrid'    => ['is_visible' => DatagridScope::IS_VISIBLE_TRUE],
                'merge'       => ['display' => true],
            ],
        ]);
        $table->addColumn('dpd_fr_relay_id', Types::STRING, [
            'notnull'     => false,
            'oro_options' => [
                'extend' => [
                    'is_extend'     => true,
                    'owner'         => ExtendScope::OWNER_CUSTOM,
                    'is_serialized' => false,
                ],
                'form'        => ['is_enabled' => true],
                'datagrid'    => ['is_visible' => DatagridScope::IS_VISIBLE_TRUE],
                'merge'       => ['display' => true],
            ],
        ]);
        $table->addColumn('synchronized_dpd', Types::DATETIME_MUTABLE, [
            'notnull'     => false,
            'oro_options' => [
                'extend' => [
                    'is_extend'     => true,
                    'owner'         => ExtendScope::OWNER_CUSTOM,
                    'is_serialized' => false,
                ],
                'form'        => ['is_enabled' => true],
                'datagrid'    => ['is_visible' => DatagridScope::IS_VISIBLE_TRUE],
                'merge'       => ['display' => true],
            ],
        ]);
    }

    /**
     * Add a phone number field and a dpd exported stamp at the checkout level
     *
     * @param Schema $schema
     *
     * @return void
     * @throws SchemaException
     */
    public static function updateOroCheckoutTable(Schema $schema): void
    {
        /** @var Table $table */
        $table = $schema->getTable('oro_checkout');
        $table->addColumn('delivery_phone', Types::STRING, [
            'notnull'     => false,
            'oro_options' => [
                'extend' => [
                    'is_extend'     => true,
                    'owner'         => ExtendScope::OWNER_CUSTOM,
                    'is_serialized' => false,
                ],
                'form'        => ['is_enabled' => true],
                'datagrid'    => ['is_visible' => DatagridScope::IS_VISIBLE_TRUE],
                'merge'       => ['display' => true],
            ],
        ]);
        $table->addColumn('dpd_fr_relay_id', Types::STRING, [
            'notnull'     => false,
            'oro_options' => [
                'extend' => [
                    'is_extend'     => true,
                    'owner'         => ExtendScope::OWNER_CUSTOM,
                    'is_serialized' => false,
                ],
                'form'        => ['is_enabled' => true],
                'datagrid'    => ['is_visible' => DatagridScope::IS_VISIBLE_TRUE],
                'merge'       => ['display' => true],
            ],
        ]);
    }
}