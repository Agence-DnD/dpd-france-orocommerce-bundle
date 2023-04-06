<?php /** @noinspection ALL */

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Migrations\Schema\v1_1;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\DBAL\Types\Types;
use Oro\Bundle\EntityBundle\EntityConfig\DatagridScope;
use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

/**
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 * @noinspection SpellCheckingInspection
 * @noinspection SpellCheckingInspection
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
        self::addProductMaxQtyForDpdFranceAttribute($schema);
    }

    /**
     * Adds a column to oro_product table to store the max qty shippable via dpd France for the product.
     *
     * @throws SchemaException
     */
    public static function addProductMaxQtyForDpdFranceAttribute(Schema $schema): void
    {
        $productTable = $schema->getTable('oro_product');
        $productTable->addColumn('max_qty_for_dpd_fr', Types::INTEGER, [
            'notnull' => true,
            'default' => -1,
            'oro_options' => [
                'extend' => [
                    'is_extend' => true,
                    'owner' => ExtendScope::OWNER_CUSTOM,
                    'is_serialized' => false,
                ],
                'form' => ['is_enabled' => true],
                'datagrid' => ['is_visible' => DatagridScope::IS_VISIBLE_FALSE],
                'merge' => ['display' => true],
            ],
        ]);
    }
}
