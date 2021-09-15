<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Migrations\Schema\v1_6;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Types;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

/**
 * Class DndDpdFranceShippingBundle
 *
 * @package   Dnd\Bundle\DpdFranceShippingBundle\Migrations\Schema\v1_6
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
        self::addGoogleMapsApiKeyTransportColumn($schema);
    }

    /**
     * Adds  columns to oro_integration_transport table.
     *
     * @param Schema $schema
     *
     * @return void
     * @throws SchemaException
     */
    public static function addGoogleMapsApiKeyTransportColumn(Schema $schema): void
    {
        /** @var Table $transportTable the oro integration transport table */
        $transportTable = $schema->getTable('oro_integration_transport');
        $transportTable->addColumn('dpd_fr_google_maps_api_key', Types::STRING, ['notnull' => false, 'length' => 255]);
    }
}