<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Migrations\Schema\v1_0;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Types;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

/**
 * Adds dpd france related columns to oro integration transport table
 *
 * @package   Dnd\Bundle\DpdFranceShippingBundle\Migrations\Schema\v1_0
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
     * @param Schema $schema
     * @param QueryBag $queries
     *
     * @return void
     * @throws SchemaException
     */
    public function up(Schema $schema, QueryBag $queries): void
    {
        self::addStationFTPTransportColumns($schema);
        self::addGeneralDpdTransportColumns($schema);
        self::addDpdMethodsTransportColumns($schema);
    }

    /**
     * Adds station FTP columns to oro_integration_transport table.
     *
     * @param Schema $schema
     *
     * @return void
     * @throws SchemaException
     */
    public static function addStationFTPTransportColumns(Schema $schema): void
    {
        /** @var Table $transportTable the oro integration transport table */
        $transportTable = $schema->getTable('oro_integration_transport');
        $transportTable->addColumn(
            'dpd_fr_order_statuses_sent_to_station',
            Types::STRING,
            ['notnull' => false, 'length' => 255]
        );
        $transportTable->addColumn('dpd_fr_station_ftp_port', Types::INTEGER, ['notnull' => false]);
        $transportTable->addColumn('dpd_fr_station_ftp_host', Types::STRING, ['notnull' => false, 'length' => 255]);
        $transportTable->addColumn('dpd_fr_station_ftp_user', Types::STRING, ['notnull' => false, 'length' => 255]);
        $transportTable->addColumn('dpd_fr_station_ftp_password', Types::STRING, ['notnull' => false, 'length' => 255]);
    }

    /**
     * Adds general DPD settings columns to oro_integration_transport table.
     *
     * @param Schema $schema
     *
     * @return void
     * @throws SchemaException
     */
    public static function addGeneralDpdTransportColumns(Schema $schema): void
    {
        /** @var Table $transportTable the oro integration transport table */
        $transportTable = $schema->getTable('oro_integration_transport');
        $transportTable->addColumn('dpd_fr_agency_code', Types::STRING, ['notnull' => false, 'length' => 255]);
        $transportTable->addColumn('dpd_fr_contract_number', Types::STRING, ['notnull' => false, 'length' => 255]);
        $transportTable->addColumn('dpd_fr_max_qty', Types::INTEGER, ['notnull' => false]);
        $transportTable->addColumn('dpd_fr_google_maps_api_key', Types::STRING, ['notnull' => false, 'length' => 255]);
    }

    /**
     * Adds DPD methods names & desc columns to oro_integration_transport table.
     *
     * @param Schema $schema
     *
     * @return void
     * @throws SchemaException
     */
    public static function addDpdMethodsTransportColumns(Schema $schema): void
    {
        /** @var Table $transportTable the oro integration transport table */
        $transportTable = $schema->getTable('oro_integration_transport');
        $transportTable->addColumn('dpd_fr_classic_method_name', Types::STRING, ['notnull' => false, 'length' => 255]);
        $transportTable->addColumn('dpd_fr_classic_method_desc', Types::TEXT, ['notnull' => false]);
        $transportTable->addColumn('dpd_fr_predict_method_name', Types::STRING, ['notnull' => false, 'length' => 255]);
        $transportTable->addColumn('dpd_fr_predict_method_desc', Types::TEXT, ['notnull' => false]);
        $transportTable->addColumn('dpd_fr_pickup_method_name', Types::STRING, ['notnull' => false, 'length' => 255]);
        $transportTable->addColumn('dpd_fr_pickup_method_desc', Types::TEXT, ['notnull' => false]);
    }
}