<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Entity;

use Oro\Bundle\IntegrationBundle\Entity\Transport;
use Symfony\Component\HttpFoundation\ParameterBag;
use Doctrine\ORM\Mapping as ORM;
/**
 * Class DpdFranceTransportSettings
 *
 * @ORM\Entity
 *
 * @package   Dnd\Bundle\DpdFranceShippingBundle\Entity
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class DpdFranceTransportSettings extends Transport
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
     * The default value allowed for DPD France shipping
     *
     * @var int DEFAULT_MAX_QTY
     */
    public const DEFAULT_MAX_QTY = 5;

    /**
     * The default name for DPD Classic method
     *
     * @var string DEFAULT_CLASSIC_METHOD_NAME
     */
    public const DEFAULT_CLASSIC_METHOD_NAME = 'DPD Classic';

    /**
     * The default description for DPD Classic method
     *
     * @var string DEFAULT_CLASSIC_METHOD_DESC
     */
    public const DEFAULT_CLASSIC_METHOD_DESC = 'Description de la méthode DPD Classic';

    /**
     * The default name for DPD Predict method
     *
     * @var string DEFAULT_PREDICT_METHOD_NAME
     */
    public const DEFAULT_PREDICT_METHOD_NAME = 'DPD Predict';

    /**
     * The default description for DPD Predict method
     *
     * @var string DEFAULT_PREDICT_METHOD_DESC
     */
    public const DEFAULT_PREDICT_METHOD_DESC = 'Description de la méthode DPD Predict';

    /**
     * The default name for DPD Relay method
     *
     * @var string DEFAULT_RELAY_METHOD_NAME
     */
    public const DEFAULT_RELAY_METHOD_NAME = 'DPD Relais';

    /**
     * The default description for DPD Relay method
     *
     * @var string DEFAULT_RELAY_METHOD_DESC
     */
    public const DEFAULT_RELAY_METHOD_DESC = 'Description de la méthode DPD Relais';

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
     * @ORM\Column(name="dpd_fr_station_ftp_host", type="string", length=255)
     *
     * @var string $stationFtpPort
     */
    protected string $stationFtpHost;

    /**
     * Description $stationFtpUser field
     *
     * @ORM\Column(name="dpd_fr_station_ftp_user", type="string", length=255)
     *
     * @var string $stationFtpUser
     */
    protected string $stationFtpUser;

    /**
     * Description $stationFtpPassword field
     *
     * @ORM\Column(name="dpd_fr_station_ftp_password", type="string", length=255)
     *
     * @var string|null stationFtpPassword
     */
    protected ?string $stationFtpPassword = null;

    /**
     * Description $orderStatusesSentToStation field
     *
     * @ORM\Column(name="dpd_fr_order_statuses_sent_to_station", type="string", length=255)
     *
     * @var string|null orderStatusesSentToStation
     */
    protected ?string $orderStatusesSentToStation = null;

    /**
     * Description $agencyCode field
     *
     * @ORM\Column(name="dpd_fr_agency_code", type="string", length=255)
     *
     * @var string $agencyCode
     */
    protected string $agencyCode;

    /**
     * Description $contractNumber field
     *
     * @ORM\Column(name="dpd_fr_contract_number", type="string", length=255)
     *
     * @var string $contractNumber
     */
    protected string $contractNumber;

    /**
     * Description $maxQty field
     *
     * @ORM\Column(name="dpd_fr_max_qty", type="integer")
     *
     * @var int $maxQty
     */
    protected int $maxQty;

    /**
     * Name of DpdFrance classic method
     *
     * @ORM\Column(name="dpd_fr_classic_method_name", type="string", length=255)
     *
     * @var string $classicMethodName
     */
    protected string $classicMethodName;

    /**
     * Description of DpdFrance classic method
     *
     * @ORM\Column(name="dpd_fr_classic_method_desc", type="text")
     *
     * @var string $classicMethodDesc
     */
    protected string $classicMethodDesc;

    /**
     * Name of DpdFrance predict method
     *
     * @ORM\Column(name="dpd_fr_predict_method_name", type="string", length=255)
     *
     * @var string $predictMethodName
     */
    protected string $predictMethodName;

    /**
     * Description of DpdFrance predict method
     *
     * @ORM\Column(name="dpd_fr_predict_method_desc", type="text")
     *
     * @var string $predictMethodDesc
     */
    protected string $predictMethodDesc;

    /**
     * Name of DpdFrance relay method
     *
     * @ORM\Column(name="dpd_fr_relay_method_name", type="string", length=255)
     *
     * @var string $relayMethodName
     */
    protected string $relayMethodName;

    /**
     * Description of DpdFrance relay method
     *
     * @ORM\Column(name="dpd_fr_relay_method_desc", type="text")
     *
     * @var string $relayMethodDesc
     */
    protected string $relayMethodDesc;

    /**
     * DpdFranceTransportSettings constructor
     * Setting default values
     */
    public function __construct()
    {
        $this->stationFtpPort     = self::DEFAULT_STATION_FTP_PORT;
        $this->stationFtpHost     = self::DEFAULT_STATION_FTP_HOST;
        $this->stationFtpUser     = self::DEFAULT_STATION_FTP_USER;
        $this->stationFtpPassword = self::DEFAULT_STATION_FTP_PASSWORD;
        $this->maxQty             = self::DEFAULT_MAX_QTY;
        $this->classicMethodName  = self::DEFAULT_CLASSIC_METHOD_NAME;
        $this->classicMethodDesc  = self::DEFAULT_CLASSIC_METHOD_DESC;
        $this->predictMethodName  = self::DEFAULT_PREDICT_METHOD_NAME;
        $this->predictMethodDesc  = self::DEFAULT_PREDICT_METHOD_DESC;
        $this->relayMethodName    = self::DEFAULT_RELAY_METHOD_NAME;
        $this->relayMethodDesc    = self::DEFAULT_RELAY_METHOD_DESC;
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
                    //STATION SETTINGS
                    'dpd_fr_order_statuses_sent_to_station' => $this->getOrderStatusesSentToStation(),
                    'dpd_fr_station_ftp_port' => $this->getStationFtpPort(),
                    'dpd_fr_station_ftp_host' => $this->getStationFtpHost(),
                    'dpd_fr_station_ftp_user' => $this->getStationFtpUser(),
                    'dpd_fr_station_ftp_password' => $this->getStationFtpPassword(),

                    //GENERAL SETTINGS
                    'dpd_fr_agency_code' => $this->getAgencyCode(),
                    'dpd_fr_contract_number' => $this->getContractNumber(),
                    'dpd_fr_max_qty' => $this->getMaxQty(),

                    //METHODS NAME & DESCRIPTION SETTINGS
                    'dpd_fr_classic_method_name' => $this->getClassicMethodName(),
                    'dpd_fr_classic_method_desc' => $this->getClassicMethodDesc(),
                    'dpd_fr_predict_method_name' => $this->getPredictMethodName(),
                    'dpd_fr_predict_method_desc' => $this->getPredictMethodDesc(),
                    'dpd_fr_relay_method_name' => $this->getRelayMethodName(),
                    'dpd_fr_relay_method_desc' => $this->getRelayMethodDesc(),
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
     * @return string|null
     */
    public function getStationFtpPassword(): ?string
    {
        return $this->stationFtpPassword;
    }

    /**
     * Description setStationFtpPassword function
     *
     * @param string|null $stationFtpPassword
     *
     * @return void
     */
    public function setStationFtpPassword(?string $stationFtpPassword): void
    {
        $this->stationFtpPassword = $stationFtpPassword;
    }

    /**
     * Description getOrderStatusesSentToStation function
     *
     * @return string|null
     */
    public function getOrderStatusesSentToStation(): ?string
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

    /**
     * Description getContractNumber function
     *
     * @return string
     */
    public function getContractNumber(): string
    {
        return $this->contractNumber;
    }

    /**
     * Description setContractNumber function
     *
     * @param string $contractNumber
     *
     * @return void
     */
    public function setContractNumber(string $contractNumber): void
    {
        $this->contractNumber = $contractNumber;
    }

    /**
     * Description getMaxQty function
     *
     * @return int
     */
    public function getMaxQty()
    {
        return $this->maxQty;
    }

    /**
     * Description setMaxQty function
     *
     * @param mixed $maxQty
     *
     * @return void
     */
    public function setMaxQty($maxQty): void
    {
        $this->maxQty = $maxQty;
    }

    /**
     * Description getClassicMethodName function
     *
     * @return string
     */
    public function getClassicMethodName(): string
    {
        return $this->classicMethodName;
    }

    /**
     * Description setClassicMethodName function
     *
     * @param string $classicMethodName
     *
     * @return void
     */
    public function setClassicMethodName(string $classicMethodName): void
    {
        $this->classicMethodName = $classicMethodName;
    }

    /**
     * Description getClassicMethodDesc function
     *
     * @return string
     */
    public function getClassicMethodDesc(): string
    {
        return $this->classicMethodDesc;
    }

    /**
     * Description setClassicMethodDesc function
     *
     * @param string $classicMethodDesc
     *
     * @return void
     */
    public function setClassicMethodDesc(string $classicMethodDesc): void
    {
        $this->classicMethodDesc = $classicMethodDesc;
    }

    /**
     * Description getPredictMethodName function
     *
     * @return string
     */
    public function getPredictMethodName(): string
    {
        return $this->predictMethodName;
    }

    /**
     * Description setPredictMethodName function
     *
     * @param string $predictMethodName
     *
     * @return void
     */
    public function setPredictMethodName(string $predictMethodName): void
    {
        $this->predictMethodName = $predictMethodName;
    }

    /**
     * Description getPredictMethodDesc function
     *
     * @return string
     */
    public function getPredictMethodDesc(): string
    {
        return $this->predictMethodDesc;
    }

    /**
     * Description setPredictMethodDesc function
     *
     * @param string $predictMethodDesc
     *
     * @return void
     */
    public function setPredictMethodDesc(string $predictMethodDesc): void
    {
        $this->predictMethodDesc = $predictMethodDesc;
    }

    /**
     * Description getRelayMethodName function
     *
     * @return string
     */
    public function getRelayMethodName(): string
    {
        return $this->relayMethodName;
    }

    /**
     * Description setRelayMethodName function
     *
     * @param string $relayMethodName
     *
     * @return void
     */
    public function setRelayMethodName(string $relayMethodName): void
    {
        $this->relayMethodName = $relayMethodName;
    }

    /**
     * Description getRelayMethodDesc function
     *
     * @return string
     */
    public function getRelayMethodDesc(): string
    {
        return $this->relayMethodDesc;
    }

    /**
     * Description setRelayMethodDesc function
     *
     * @param string $relayMethodDesc
     *
     * @return void
     */
    public function setRelayMethodDesc(string $relayMethodDesc): void
    {
        $this->relayMethodDesc = $relayMethodDesc;
    }
}
