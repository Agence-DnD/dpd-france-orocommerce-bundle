<?php

namespace Dnd\Bundle\DpdFranceShippingBundle\Migrations\Schema\v1_1;

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
 * @package   Dnd\Bundle\DpdFranceShippingBundle\Migrations\Schema\v1_1
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
        self::addProductMaxQtyForDpdFranceAttribute($schema);
    }

    /**
     * Adds a column to oro_product table to store the max qty shippable via dpd fr for the product.
     *
     * @param Schema $schema
     *
     * @return void
     * @throws SchemaException
     */
    public static function addProductMaxQtyForDpdFranceAttribute(Schema $schema): void
    {
        /** @var Table $productTable the oro product table */
        $productTable = $schema->getTable('oro_product');

        $productTable->addColumn('maxQtyForDpdFr', Types::INTEGER, [
                'notnull'     => false,
                'oro_options' => [
                    'extend' => [
                        'is_extend'     => true,
                        'owner'         => ExtendScope::OWNER_CUSTOM,
                        'is_serialized' => true,
                    ],
                ],
                'form'        => ['is_enabled' => false],
                'datagrid'    => ['is_visible' => DatagridScope::IS_VISIBLE_TRUE],
                'merge'       => ['display' => true],
            ]);
    }
}