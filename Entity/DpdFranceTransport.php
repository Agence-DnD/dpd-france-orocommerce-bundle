<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Entity;

use Oro\Bundle\IntegrationBundle\Entity\Transport;
use Symfony\Component\HttpFoundation\ParameterBag;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class DpdFranceTransport
 *
 * @ORM\Entity
 *
 * @package   Dnd\Bundle\DpdFranceShippingBundle\Entity
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class DpdFranceTransport extends Transport
{
    /**
     * The default value for station FTP port
     *
     * @var int DEFAULT_FTP_PORT
     */
    public const DEFAULT_STATION_FTP_PORT = 21;

    /**
     * The default value for station FTP host
     *
     * @var string DEFAULT_STATION_FTP_HOST
     */
    public const DEFAULT_STATION_FTP_HOST = '127.0.0.1';

    /**
     * The default value for station FTP user
     *
     * @var string DEFAULT_STATION_FTP_USER
     */
    public const DEFAULT_STATION_FTP_USER = 'user';

    /**
     * The default value for station FTP password
     *
     * @var string DEFAULT_STATION_FTP_PASSWORD
     */
    public const DEFAULT_STATION_FTP_PASSWORD = 'password';

    /**
     * The settings for the DPD France transport
     *
     * @var ParameterBag $settings
     */
    protected ParameterBag $settings;

    /**
     * Description $stationFtpPort field
     *
     * @ORM\Column(name="dpd_fr_station_ftp_port", type="integer")
     *
     * @var int $stationFtpPort
     */
    protected int $stationFtpPort;

    /**
     * Description $stationFtpHost field
     *
     * @ORM\Column(name="dpd_fr_station_ftp_host", type="string")
     *
     * @var string $stationFtpPort
     */
    protected string $stationFtpHost;

    /**
     * Description $stationFtpUser field
     *
     * @ORM\Column(name="dpd_fr_station_ftp_user", type="string")
     *
     * @var string $stationFtpUser
     */
    protected string $stationFtpUser;

    /**
     * Description $stationFtpPassword field
     *
     * @ORM\Column(name="dpd_fr_station_ftp_password", type="string")
     *
     * @var string stationFtpPassword
     */
    protected string $stationFtpPassword;

    /**
     * Description $orderStatusesSentToStation field
     *
     * @ORM\Column(name="order_statuses_sent_to_station", type="string")
     *
     * @var string orderStatusesSentToStation
     */
    protected string $orderStatusesSentToStation;

    /**
     * Description $agencyCode field
     *
     * @ORM\Column(name="dpd_fr_agency_code", type="string")
     *
     * @var string $agencyCode
     */
    protected string $agencyCode;

    /**
     * Description $contractNumber field
     *
     * @ORM\Column(name="dpd_fr_contract_number", type="string")
     *
     * @var string $contractNumber
     */
    protected string $contractNumber;

    /**
     * DpdFranceTransport constructor
     */
    public function __construct()
    {
        $this->stationFtpPort = self::DEFAULT_STATION_FTP_PORT;
        $this->stationFtpHost = self::DEFAULT_STATION_FTP_HOST;
        $this->stationFtpUser = self::DEFAULT_STATION_FTP_USER;
        $this->stationFtpPassword = self::DEFAULT_STATION_FTP_PASSWORD;
    }

    /**
     * Description getSettingsBag function
     *
     * @return ParameterBag
     */
    public function getSettingsBag(): ParameterBag
    {
        if (null === $this->settings) {
            $this->settings = new ParameterBag(
                [
                    'order_statuses_sent_to_station' => $this->getOrderStatusesSentToStation(),
                    'dpd_fr_station_ftp_port' => $this->getStationFtpPort(),
                    'dpd_fr_station_ftp_host' => $this->getStationFtpHost(),
                    'dpd_fr_station_ftp_user' => $this->getStationFtpUser(),
                    'dpd_fr_station_ftp_password' => $this->getStationFtpPassword(),
                ]
            );
        }
        return $this->settings;
    }

    /**
     * Description getSettings function
     *
     * @return ParameterBag
     */
    public function getSettings(): ParameterBag
    {
        return $this->settings;
    }

    /**
     * Description setSettings function
     *
     * @param ParameterBag $settings
     *
     * @return void
     */
    public function setSettings(ParameterBag $settings): void
    {
        $this->settings = $settings;
    }

    /**
     * Description getStationFtpPort function
     *
     * @return int
     */
    public function getStationFtpPort(): int
    {
        return $this->stationFtpPort;
    }

    /**
     * Description setStationFtpPort function
     *
     * @param int $stationFtpPort
     *
     * @return void
     */
    public function setStationFtpPort(int $stationFtpPort): void
    {
        $this->stationFtpPort = $stationFtpPort;
    }

    /**
     * Description getStationFtpHost function
     *
     * @return string
     */
    public function getStationFtpHost(): string
    {
        return $this->stationFtpHost;
    }

    /**
     * Description setStationFtpHost function
     *
     * @param string $stationFtpHost
     *
     * @return void
     */
    public function setStationFtpHost(string $stationFtpHost): void
    {
        $this->stationFtpHost = $stationFtpHost;
    }

    /**
     * Description getStationFtpUser function
     *
     * @return string
     */
    public function getStationFtpUser(): string
    {
        return $this->stationFtpUser;
    }

    /**
     * Description setStationFtpUser function
     *
     * @param string $stationFtpUser
     *
     * @return void
     */
    public function setStationFtpUser(string $stationFtpUser): void
    {
        $this->stationFtpUser = $stationFtpUser;
    }

    /**
     * Description getStationFtpPassword function
     *
     * @return string
     */
    public function getStationFtpPassword(): string
    {
        return $this->stationFtpPassword;
    }

    /**
     * Description setStationFtpPassword function
     *
     * @param string $stationFtpPassword
     *
     * @return void
     */
    public function setStationFtpPassword(string $stationFtpPassword): void
    {
        $this->stationFtpPassword = $stationFtpPassword;
    }

    /**
     * Description getOrderStatusesSentToStation function
     *
     * @return string
     */
    public function getOrderStatusesSentToStation(): string
    {
        return $this->orderStatusesSentToStation;
    }

    /**
     * Description setOrderStatusesSentToStation function
     *
     * @param string $orderStatusesSentToStation
     *
     * @return void
     */
    public function setOrderStatusesSentToStation(string $orderStatusesSentToStation): void
    {
        $this->orderStatusesSentToStation = $orderStatusesSentToStation;
    }

    /**
     * Description getAgencyCode function
     *
     * @return string
     */
    public function getAgencyCode(): string
    {
        return $this->agencyCode;
    }

    /**
     * Description setAgencyCode function
     *
     * @param string $agencyCode
     *
     * @return void
     */
    public function setAgencyCode(string $agencyCode): void
    {
        $this->agencyCode = $agencyCode;
    }
}
