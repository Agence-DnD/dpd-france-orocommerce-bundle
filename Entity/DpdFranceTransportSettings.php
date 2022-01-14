<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\IntegrationBundle\Entity\Transport;
use Symfony\Component\HttpFoundation\ParameterBag;

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
     * The DPD Classic method's identifier
     *
     * @var string IDENTIFIER_CLASSIC
     */
    public const IDENTIFIER_CLASSIC = 'dpd_fr_classic';
    /**
     * The DPD Predict method's identifier
     *
     * @var string IDENTIFIER_PREDICT
     */
    public const IDENTIFIER_PREDICT = 'dpd_fr_predict';
    /**
     * The DPD Pickup method's identifier
     *
     * @var string IDENTIFIER_PICKUP
     */
    public const IDENTIFIER_PICKUP = 'dpd_fr_pickup';
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
    public const DEFAULT_MAX_QTY = 1;
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
    public const DEFAULT_CLASSIC_METHOD_DESC = 'Livraison à domicile DPD';
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
    public const DEFAULT_PREDICT_METHOD_DESC = 'Livraison à domicile Predict sur rendez-vous';
    /**
     * The default name for DPD Pickup method
     *
     * @var string DEFAULT_PICKUP_METHOD_NAME
     */
    public const DEFAULT_PICKUP_METHOD_NAME = 'DPD Relais';
    /**
     * The default description for DPD Pickup method
     *
     * @var string DEFAULT_PICKUP_METHOD_DESC
     */
    public const DEFAULT_PICKUP_METHOD_DESC = 'Livraison en relais Pickup proche de chez vous';
    /**
     * The settings for the DPD France transport
     *
     * @var ParameterBag|null $settings
     */
    protected ?ParameterBag $settings = null;
    /**
     * Description $stationEnabled field
     *
     * @ORM\Column(name="dpd_fr_station_enabled", type="boolean")
     *
     * @var bool|null $stationEnabled
     */
    protected ?bool $stationEnabled = false;
    /**
     * Description $stationFtpPort field
     *
     * @ORM\Column(name="dpd_fr_station_ftp_port", type="integer")
     *
     * @var int|null $stationFtpPort
     */
    protected ?int $stationFtpPort = null;
    /**
     * Description $stationFtpHost field
     *
     * @ORM\Column(name="dpd_fr_station_ftp_host", type="string", length=255)
     *
     * @var string|null $stationFtpPort
     */
    protected ?string $stationFtpHost = null;
    /**
     * Description $stationFtpUser field
     *
     * @ORM\Column(name="dpd_fr_station_ftp_user", type="string", length=255)
     *
     * @var string|null $stationFtpUser
     */
    protected ?string $stationFtpUser = null;
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
     * @var string|null $agencyCode
     */
    protected ?string $agencyCode = null;
    /**
     * Description $contractNumber field
     *
     * @ORM\Column(name="dpd_fr_contract_number", type="string", length=255)
     *
     * @var string|null $contractNumber
     */
    protected ?string $contractNumber = null;
    /**
     * Description $maxQty field
     *
     * @ORM\Column(name="dpd_fr_max_qty", type="integer")
     *
     * @var int|null $maxQty
     */
    protected ?int $maxQty = null;
    /**
     * Name of DpdFrance classic method
     *
     * @ORM\Column(name="dpd_fr_classic_method_name", type="string", length=255)
     *
     * @var string|null $classicMethodName
     */
    protected ?string $classicMethodName = null;
    /**
     * Description of DpdFrance classic method
     *
     * @ORM\Column(name="dpd_fr_classic_method_desc", type="text")
     *
     * @var string|null $classicMethodDesc
     */
    protected ?string $classicMethodDesc = null;
    /**
     * Name of DpdFrance predict method
     *
     * @ORM\Column(name="dpd_fr_predict_method_name", type="string", length=255)
     *
     * @var string|null $predictMethodName
     */
    protected ?string $predictMethodName = null;
    /**
     * Description of DpdFrance predict method
     *
     * @ORM\Column(name="dpd_fr_predict_method_desc", type="text")
     *
     * @var string|null $predictMethodDesc
     */
    protected ?string $predictMethodDesc = null;
    /**
     * Name of DpdFrance pickup method
     *
     * @ORM\Column(name="dpd_fr_pickup_method_name", type="string", length=255)
     *
     * @var string|null $pickupMethodName
     */
    protected ?string $pickupMethodName = null;
    /**
     * Description of DpdFrance pickup method
     *
     * @ORM\Column(name="dpd_fr_pickup_method_desc", type="text")
     *
     * @var string|null $pickupMethodDesc
     */
    protected ?string $pickupMethodDesc = null;
    /**
     * @var Collection|ShippingService[]
     *
     * @ORM\ManyToMany(
     *      targetEntity="ShippingService",
     *     fetch="EAGER"
     * )
     * @ORM\JoinTable(
     *      name="dnd_dpd_fr_transport_ship_service",
     *      joinColumns={
     *          @ORM\JoinColumn(name="transport_id", referencedColumnName="id", onDelete="CASCADE")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="ship_service_code", referencedColumnName="code", onDelete="CASCADE")
     *      }
     * )
     */
    protected $shippingServices;
    /**
     * Description $googleMapsApiKey field
     *
     * @ORM\Column(name="dpd_fr_google_maps_api_key", type="string", length=255)
     *
     * @var string|null $googleMapsApiKey
     */
    protected ?string $googleMapsApiKey = null;

    /**
     * DpdFranceTransportSettings constructor
     * Setting default values
     */
    public function __construct()
    {
        $this->stationEnabled     = false;
        $this->stationFtpPort     = self::DEFAULT_STATION_FTP_PORT;
        $this->stationFtpHost     = self::DEFAULT_STATION_FTP_HOST;
        $this->stationFtpUser     = self::DEFAULT_STATION_FTP_USER;
        $this->stationFtpPassword = self::DEFAULT_STATION_FTP_PASSWORD;
        $this->maxQty             = self::DEFAULT_MAX_QTY;
        $this->classicMethodName  = self::DEFAULT_CLASSIC_METHOD_NAME;
        $this->classicMethodDesc  = self::DEFAULT_CLASSIC_METHOD_DESC;
        $this->predictMethodName  = self::DEFAULT_PREDICT_METHOD_NAME;
        $this->predictMethodDesc  = self::DEFAULT_PREDICT_METHOD_DESC;
        $this->pickupMethodName   = self::DEFAULT_PICKUP_METHOD_NAME;
        $this->pickupMethodDesc   = self::DEFAULT_PICKUP_METHOD_DESC;
        $this->googleMapsApiKey   = '';
        $this->shippingServices   = new ArrayCollection();
    }

    /**
     * Description getSettingsBag function
     *
     * @return ParameterBag
     */
    public function getSettingsBag(): ParameterBag
    {
        if (null === $this->settings) {
            $this->settings = new ParameterBag([
                //STATION SETTINGS
                'dpd_fr_order_statuses_sent_to_station' => $this->getOrderStatusesSentToStation(),
                'dpd_fr_station_ftp_port'               => $this->getStationFtpPort(),
                'dpd_fr_station_ftp_host'               => $this->getStationFtpHost(),
                'dpd_fr_station_ftp_user'               => $this->getStationFtpUser(),
                'dpd_fr_station_ftp_password'           => $this->getStationFtpPassword(),

                //GENERAL SETTINGS
                'dpd_fr_agency_code'                    => $this->getAgencyCode(),
                'dpd_fr_contract_number'                => $this->getContractNumber(),
                'dpd_fr_max_qty'                        => $this->getMaxQty(),
                'google_maps_api_key'                   => $this->getGoogleMapsApiKey(),

                //METHODS NAME & DESCRIPTION SETTINGS
                'dpd_fr_classic_method_name'            => $this->getClassicMethodName(),
                'dpd_fr_classic_method_desc'            => $this->getClassicMethodDesc(),
                'dpd_fr_predict_method_name'            => $this->getPredictMethodName(),
                'dpd_fr_predict_method_desc'            => $this->getPredictMethodDesc(),
                'dpd_fr_pickup_method_name'             => $this->getPickupMethodName(),
                'dpd_fr_pickup_method_desc'             => $this->getPickupMethodDesc(),

                'applicable_shipping_services' => $this->getShippingServices()->toArray(),
            ]);
        }

        return $this->settings;
    }

    /**
     * Description getSettings function
     *
     * @return ParameterBag|null
     */
    public function getSettings(): ?ParameterBag
    {
        return $this->settings;
    }

    /**
     * Description setSettings function
     *
     * @param ParameterBag|null $settings
     *
     * @return void
     */
    public function setSettings(?ParameterBag $settings): void
    {
        $this->settings = $settings;
    }

    /**
     * Description getStationFtpPort function
     *
     * @return int|null
     */
    public function getStationFtpPort(): ?int
    {
        return $this->stationFtpPort;
    }

    /**
     * Description setStationFtpPort function
     *
     * @param int|null $stationFtpPort
     *
     * @return void
     */
    public function setStationFtpPort(?int $stationFtpPort): void
    {
        $this->stationFtpPort = $stationFtpPort;
    }

    /**
     * Description getStationFtpHost function
     *
     * @return string|null
     */
    public function getStationFtpHost(): ?string
    {
        return $this->stationFtpHost;
    }

    /**
     * Description setStationFtpHost function
     *
     * @param string|null $stationFtpHost
     *
     * @return void
     */
    public function setStationFtpHost(?string $stationFtpHost): void
    {
        $this->stationFtpHost = $stationFtpHost;
    }

    /**
     * Description getStationFtpUser function
     *
     * @return string|null
     */
    public function getStationFtpUser(): ?string
    {
        return $this->stationFtpUser;
    }

    /**
     * Description setStationFtpUser function
     *
     * @param string|null $stationFtpUser
     *
     * @return void
     */
    public function setStationFtpUser(?string $stationFtpUser): void
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
     * @param string|null $orderStatusesSentToStation
     *
     * @return void
     */
    public function setOrderStatusesSentToStation(?string $orderStatusesSentToStation): void
    {
        $this->orderStatusesSentToStation = $orderStatusesSentToStation;
    }

    /**
     * Description getAgencyCode function
     *
     * @return string|null
     */
    public function getAgencyCode(): ?string
    {
        return $this->agencyCode;
    }

    /**
     * Description setAgencyCode function
     *
     * @param string|null $agencyCode
     *
     * @return void
     */
    public function setAgencyCode(?string $agencyCode): void
    {
        $this->agencyCode = $agencyCode;
    }

    /**
     * Description getContractNumber function
     *
     * @return string|null
     */
    public function getContractNumber(): ?string
    {
        return $this->contractNumber;
    }

    /**
     * Description setContractNumber function
     *
     * @param string|null $contractNumber
     *
     * @return void
     */
    public function setContractNumber(?string $contractNumber): void
    {
        $this->contractNumber = $contractNumber;
    }

    /**
     * Description getMaxQty function
     *
     * @return int
     */
    public function getMaxQty(): int
    {
        return $this->maxQty;
    }

    /**
     * Description setMaxQty function
     *
     * @param int|null $maxQty
     *
     * @return void
     */
    public function setMaxQty(?int $maxQty): void
    {
        $this->maxQty = $maxQty;
    }

    /**
     * Description getClassicMethodName function
     *
     * @return string|null
     */
    public function getClassicMethodName(): ?string
    {
        return $this->classicMethodName;
    }

    /**
     * Description setClassicMethodName function
     *
     * @param string|null $classicMethodName
     *
     * @return void
     */
    public function setClassicMethodName(?string $classicMethodName): void
    {
        $this->classicMethodName = $classicMethodName;
    }

    /**
     * Description getClassicMethodDesc function
     *
     * @return string|null
     */
    public function getClassicMethodDesc(): ?string
    {
        return $this->classicMethodDesc;
    }

    /**
     * Description setClassicMethodDesc function
     *
     * @param string|null $classicMethodDesc
     *
     * @return void
     */
    public function setClassicMethodDesc(?string $classicMethodDesc): void
    {
        $this->classicMethodDesc = $classicMethodDesc;
    }

    /**
     * Description getPredictMethodName function
     *
     * @return string|null
     */
    public function getPredictMethodName(): ?string
    {
        return $this->predictMethodName;
    }

    /**
     * Description setPredictMethodName function
     *
     * @param string|null $predictMethodName
     *
     * @return void
     */
    public function setPredictMethodName(?string $predictMethodName): void
    {
        $this->predictMethodName = $predictMethodName;
    }

    /**
     * Description getPredictMethodDesc function
     *
     * @return string|null
     */
    public function getPredictMethodDesc(): ?string
    {
        return $this->predictMethodDesc;
    }

    /**
     * Description setPredictMethodDesc function
     *
     * @param string|null $predictMethodDesc
     *
     * @return void
     */
    public function setPredictMethodDesc(?string $predictMethodDesc): void
    {
        $this->predictMethodDesc = $predictMethodDesc;
    }

    /**
     * Description getPickupMethodName function
     *
     * @return string|null
     */
    public function getPickupMethodName(): ?string
    {
        return $this->pickupMethodName;
    }

    /**
     * Description setPickupMethodName function
     *
     * @param string|null $pickupMethodName
     *
     * @return void
     */
    public function setPickupMethodName(?string $pickupMethodName): void
    {
        $this->pickupMethodName = $pickupMethodName;
    }

    /**
     * Description getPickupMethodDesc function
     *
     * @return string|null
     */
    public function getPickupMethodDesc(): ?string
    {
        return $this->pickupMethodDesc;
    }

    /**
     * Description setPickupMethodDesc function
     *
     * @param string|null $pickupMethodDesc
     *
     * @return void
     */
    public function setPickupMethodDesc(?string $pickupMethodDesc): void
    {
        $this->pickupMethodDesc = $pickupMethodDesc;
    }

    /**
     * Description getLabels function
     *
     * @return string[]
     */
    public function getLabels(): array
    {
        /** @var string[] $labels */
        $labels                           = [];
        $labels[self::IDENTIFIER_CLASSIC] = $this->getClassicMethodName();
        $labels[self::IDENTIFIER_PREDICT] = $this->getPredictMethodName();
        $labels[self::IDENTIFIER_PICKUP]  = $this->getPickupMethodName();

        return $labels;
    }

    /**
     * Description getLabel function
     *
     * @param string|null $identifier
     *
     * @return string|null
     */
    public function getLabel(string $identifier): ?string
    {
        /** @var string[] $labels */
        $labels = $this->getLabels();

        return $labels[$identifier] ?? null;
    }

    /**
     * Description getDescriptions function
     *
     * @return string[]
     */
    public function getDescriptions(): array
    {
        /** @var string[] $descriptions */
        $descriptions                           = [];
        $descriptions[self::IDENTIFIER_CLASSIC] = $this->getClassicMethodDesc();
        $descriptions[self::IDENTIFIER_PREDICT] = $this->getPredictMethodDesc();
        $descriptions[self::IDENTIFIER_PICKUP]  = $this->getPickupMethodDesc();

        return $descriptions;
    }

    /**
     * Description getDescription function
     *
     * @param string|null $identifier
     *
     * @return string|null
     */
    public function getDescription(string $identifier): ?string
    {
        /** @var string[] $descriptions */
        $descriptions = $this->getDescriptions();

        return $descriptions[$identifier] ?? null;
    }

    /**
     * Description getShippingServices function
     *
     * @return ShippingService[]|ArrayCollection|Collection
     */
    public function getShippingServices()
    {
        return $this->shippingServices;
    }

    /**
     * Description getShippingService function
     *
     * @param $code
     *
     * @return ShippingService|null
     */
    public function getShippingService($code): ?ShippingService
    {
        $result = null;

        /** @var ShippingService $service */
        foreach ($this->shippingServices as $service) {
            if ($service->getCode() === $code) {
                /** @var ShippingService $result */
                $result = $service;
                break;
            }
        }

        return $result;
    }

    /**
     * Description addShippingService function
     *
     * @param ShippingService $service
     *
     * @return $this
     */
    public function addShippingService(ShippingService $service): self
    {
        if (!$this->shippingServices->contains($service)) {
            $this->shippingServices->add($service);
        }

        return $this;
    }

    /**
     * Description removeShippingService function
     *
     * @param ShippingService $service
     *
     * @return $this
     */
    public function removeShippingService(ShippingService $service): self
    {
        if ($this->shippingServices->contains($service)) {
            $this->shippingServices->removeElement($service);
        }

        return $this;
    }

    /**
     * Description getGoogleMapsApiKey function
     *
     * @return string|null
     */
    public function getGoogleMapsApiKey(): ?string
    {
        return $this->googleMapsApiKey;
    }

    /**
     * Description setGoogleMapsApiKey function
     *
     * @param string|null $googleMapsApiKey
     *
     * @return void
     */
    public function setGoogleMapsApiKey(?string $googleMapsApiKey): void
    {
        $this->googleMapsApiKey = $googleMapsApiKey;
    }

    /**
     * Description isStationEnabled function
     *
     * @return bool
     */
    public function isStationEnabled(): bool
    {
        return (bool) $this->stationEnabled;
    }

    /**
     * Description setStationEnabled function
     *
     * @param bool $stationEnabled
     *
     * @return void
     */
    public function setStationEnabled(bool $stationEnabled): void
    {
        $this->stationEnabled = $stationEnabled;
    }

}
