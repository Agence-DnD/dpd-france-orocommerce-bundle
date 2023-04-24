<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\IntegrationBundle\Entity\Transport;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
#[ORM\Entity()]
class DpdFranceTransportSettings extends Transport
{
    /**
     * The DPD Classic method's identifier
     */
    public const IDENTIFIER_CLASSIC = 'dpd_fr_classic';
    /**
     * The DPD Predict method's identifier
     */
    public const IDENTIFIER_PREDICT = 'dpd_fr_predict';
    /**
     * The DPD Pickup method's identifier
     */
    public const IDENTIFIER_PICKUP = 'dpd_fr_pickup';
    /**
     * The default value for station FTP port
     */
    public const DEFAULT_STATION_FTP_PORT = 21;
    /**
     * The default value for station FTP host
     */
    public const DEFAULT_STATION_FTP_HOST = '127.0.0.1';
    /**
     * The default value for station FTP user
     */
    public const DEFAULT_STATION_FTP_USER = 'user';
    /**
     * The default value for station FTP password
     */
    public const DEFAULT_STATION_FTP_PASSWORD = 'password';
    /**
     * The default value allowed for DPD France shipping
     */
    public const DEFAULT_MAX_QTY = 1;
    /**
     * The default name for DPD Classic method
     */
    public const DEFAULT_CLASSIC_METHOD_NAME = 'DPD Classic';
    /**
     * The default description for DPD Classic method
     */
    public const DEFAULT_CLASSIC_METHOD_DESC = 'Livraison à domicile DPD';
    /**
     * The default name for DPD Predict method
     */
    public const DEFAULT_PREDICT_METHOD_NAME = 'DPD Predict';
    /**
     * The default description for DPD Predict method
     */
    public const DEFAULT_PREDICT_METHOD_DESC = 'Livraison à domicile Predict sur rendez-vous';
    /**
     * The default name for DPD Pickup method
     */
    public const DEFAULT_PICKUP_METHOD_NAME = 'DPD Relais';
    /**
     * The default description for DPD Pickup method
     */
    public const DEFAULT_PICKUP_METHOD_DESC = 'Livraison en relais Pickup proche de chez vous';
    /**
     * The settings for the DPD France transport
     */
    protected ?ParameterBag $settings = null;
    #[ORM\Column(name: "dpd_fr_station_enabled", type: "boolean")]
    protected ?bool $stationEnabled = false;
    #[ORM\Column(name: "dpd_fr_station_ftp_port", type: "integer")]
    protected ?int $stationFtpPort = null;
    #[ORM\Column(name: "dpd_fr_station_ftp_host", type: "string", length: 255)]
    protected ?string $stationFtpHost = null;
    #[ORM\Column(name: "dpd_fr_station_ftp_user", type: "string", length: 255)]
    protected ?string $stationFtpUser = null;
    #[ORM\Column(name: "dpd_fr_station_ftp_password", type: "string", length: 255)]
    protected ?string $stationFtpPassword = null;
    #[ORM\Column(name: "dpd_fr_order_statuses_sent_to_station", type: "string", length: 255)]
    protected ?string $orderStatusesSentToStation = null;
    #[ORM\Column(name: "dpd_fr_agency_code", type: "string", length: 255)]
    protected ?string $agencyCode = null;
    #[ORM\Column(name: "dpd_fr_contract_number", type: "string", length: 255)]
    protected ?string $contractNumber = null;
    #[ORM\Column(name: "dpd_fr_max_qty", type: "integer")]
    protected ?int $maxQty = null;
    #[ORM\Column(name: "dpd_fr_classic_method_name", type: "string", length: 255)]
    protected ?string $classicMethodName = null;
    #[ORM\Column(name: "dpd_fr_classic_method_desc", type: "text")]
    protected ?string $classicMethodDesc = null;
    #[ORM\Column(name: "dpd_fr_predict_method_name", type: "string", length: 255)]
    protected ?string $predictMethodName = null;
    #[ORM\Column(name: "dpd_fr_predict_method_desc", type: "text")]
    protected ?string $predictMethodDesc = null;
    #[ORM\Column(name: "dpd_fr_pickup_method_name", type: "string", length: 255)]
    protected ?string $pickupMethodName = null;
    #[ORM\Column(name: "dpd_fr_pickup_method_desc", type: "text")]
    protected ?string $pickupMethodDesc = null;
    #[ORM\ManyToMany(targetEntity: ShippingService::class, fetch: 'EAGER')]
    #[ORM\JoinTable(name: "dnd_dpd_fr_transport_ship_service")]
    #[ORM\JoinColumn(name: "transport_id", referencedColumnName: "id", onDelete: "CASCADE")]
    #[ORM\InverseJoinColumn(name: "ship_service_code", referencedColumnName: "code", unique: true, onDelete: "CASCADE")]
    protected ?Collection $shippingServices = null;
    #[ORM\Column(name: "dpd_fr_google_maps_api_key", type: "string", length: 255)]
    protected ?string $googleMapsApiKey = null;

    /**
     * Setting default values
     */
    public function __construct()
    {
        $this->stationEnabled = false;
        $this->stationFtpPort = self::DEFAULT_STATION_FTP_PORT;
        $this->stationFtpHost = self::DEFAULT_STATION_FTP_HOST;
        $this->stationFtpUser = self::DEFAULT_STATION_FTP_USER;
        $this->stationFtpPassword = self::DEFAULT_STATION_FTP_PASSWORD;
        $this->maxQty = self::DEFAULT_MAX_QTY;
        $this->classicMethodName = self::DEFAULT_CLASSIC_METHOD_NAME;
        $this->classicMethodDesc = self::DEFAULT_CLASSIC_METHOD_DESC;
        $this->predictMethodName = self::DEFAULT_PREDICT_METHOD_NAME;
        $this->predictMethodDesc = self::DEFAULT_PREDICT_METHOD_DESC;
        $this->pickupMethodName = self::DEFAULT_PICKUP_METHOD_NAME;
        $this->pickupMethodDesc = self::DEFAULT_PICKUP_METHOD_DESC;
        $this->googleMapsApiKey = '';
        $this->shippingServices = new ArrayCollection();
    }

    /**
     * Returns all the DPD FR integration settings in a ParameterBag
     */
    public function getSettingsBag(): ParameterBag
    {
        if (null === $this->settings) {
            $this->settings = new ParameterBag([
                //STATION SETTINGS
                'dpd_fr_order_statuses_sent_to_station' => $this->getOrderStatusesSentToStation(),
                'dpd_fr_station_enabled' => $this->isStationEnabled(),
                'dpd_fr_station_ftp_port' => $this->getStationFtpPort(),
                'dpd_fr_station_ftp_host' => $this->getStationFtpHost(),
                'dpd_fr_station_ftp_user' => $this->getStationFtpUser(),
                'dpd_fr_station_ftp_password' => $this->getStationFtpPassword(),

                //GENERAL SETTINGS
                'dpd_fr_agency_code' => $this->getAgencyCode(),
                'dpd_fr_contract_number' => $this->getContractNumber(),
                'dpd_fr_max_qty' => $this->getMaxQty(),
                'google_maps_api_key' => $this->getGoogleMapsApiKey(),

                //METHODS NAME & DESCRIPTION SETTINGS
                'dpd_fr_classic_method_name' => $this->getClassicMethodName(),
                'dpd_fr_classic_method_desc' => $this->getClassicMethodDesc(),
                'dpd_fr_predict_method_name' => $this->getPredictMethodName(),
                'dpd_fr_predict_method_desc' => $this->getPredictMethodDesc(),
                'dpd_fr_pickup_method_name' => $this->getPickupMethodName(),
                'dpd_fr_pickup_method_desc' => $this->getPickupMethodDesc(),

                'applicable_shipping_services' => $this->getShippingServices()->toArray(),
            ]);
        }

        return $this->settings;
    }

    public function getSettings(): ?ParameterBag
    {
        return $this->settings;
    }

    public function setSettings(?ParameterBag $settings): void
    {
        $this->settings = $settings;
    }

    public function getStationFtpPort(): ?int
    {
        return $this->stationFtpPort;
    }

    public function setStationFtpPort(?int $stationFtpPort): void
    {
        $this->stationFtpPort = $stationFtpPort;
    }

    public function getStationFtpHost(): ?string
    {
        return $this->stationFtpHost;
    }

    public function setStationFtpHost(?string $stationFtpHost): void
    {
        $this->stationFtpHost = $stationFtpHost;
    }

    public function getStationFtpUser(): ?string
    {
        return $this->stationFtpUser;
    }

    public function setStationFtpUser(?string $stationFtpUser): void
    {
        $this->stationFtpUser = $stationFtpUser;
    }

    public function getStationFtpPassword(): ?string
    {
        return $this->stationFtpPassword;
    }

    public function setStationFtpPassword(?string $stationFtpPassword): void
    {
        $this->stationFtpPassword = $stationFtpPassword;
    }

    public function getOrderStatusesSentToStation(): ?string
    {
        return $this->orderStatusesSentToStation;
    }

    public function setOrderStatusesSentToStation(?string $orderStatusesSentToStation): void
    {
        $this->orderStatusesSentToStation = $orderStatusesSentToStation;
    }

    public function getAgencyCode(): ?string
    {
        return $this->agencyCode;
    }

    public function setAgencyCode(?string $agencyCode): void
    {
        $this->agencyCode = $agencyCode;
    }

    public function getContractNumber(): ?string
    {
        return $this->contractNumber;
    }

    public function setContractNumber(?string $contractNumber): void
    {
        $this->contractNumber = $contractNumber;
    }

    public function getMaxQty(): int
    {
        return $this->maxQty;
    }

    public function setMaxQty(?int $maxQty): void
    {
        $this->maxQty = $maxQty;
    }

    public function getClassicMethodName(): ?string
    {
        return $this->classicMethodName;
    }

    public function setClassicMethodName(?string $classicMethodName): void
    {
        $this->classicMethodName = $classicMethodName;
    }

    public function getClassicMethodDesc(): ?string
    {
        return $this->classicMethodDesc;
    }

    public function setClassicMethodDesc(?string $classicMethodDesc): void
    {
        $this->classicMethodDesc = $classicMethodDesc;
    }

    public function getPredictMethodName(): ?string
    {
        return $this->predictMethodName;
    }

    public function setPredictMethodName(?string $predictMethodName): void
    {
        $this->predictMethodName = $predictMethodName;
    }

    public function getPredictMethodDesc(): ?string
    {
        return $this->predictMethodDesc;
    }

    public function setPredictMethodDesc(?string $predictMethodDesc): void
    {
        $this->predictMethodDesc = $predictMethodDesc;
    }

    public function getPickupMethodName(): ?string
    {
        return $this->pickupMethodName;
    }

    public function setPickupMethodName(?string $pickupMethodName): void
    {
        $this->pickupMethodName = $pickupMethodName;
    }

    public function getPickupMethodDesc(): ?string
    {
        return $this->pickupMethodDesc;
    }

    public function setPickupMethodDesc(?string $pickupMethodDesc): void
    {
        $this->pickupMethodDesc = $pickupMethodDesc;
    }

    /**
     * @return string[]
     */
    public function getLabels(): array
    {
        $labels = [];
        $labels[self::IDENTIFIER_CLASSIC] = $this->getClassicMethodName();
        $labels[self::IDENTIFIER_PREDICT] = $this->getPredictMethodName();
        $labels[self::IDENTIFIER_PICKUP] = $this->getPickupMethodName();

        return $labels;
    }

    public function getLabel(string $identifier): ?string
    {
        /** @var string[] $labels */
        $labels = $this->getLabels();

        return $labels[$identifier] ?? null;
    }

    /**
     * @return string[]
     */
    public function getDescriptions(): array
    {
        $descriptions = [];
        $descriptions[self::IDENTIFIER_CLASSIC] = $this->getClassicMethodDesc();
        $descriptions[self::IDENTIFIER_PREDICT] = $this->getPredictMethodDesc();
        $descriptions[self::IDENTIFIER_PICKUP] = $this->getPickupMethodDesc();

        return $descriptions;
    }

    public function getDescription(string $identifier): ?string
    {
        $descriptions = $this->getDescriptions();

        return $descriptions[$identifier] ?? null;
    }

    public function getShippingServices(): ?Collection
    {
        return $this->shippingServices;
    }

    public function getShippingService(string $code): ?ShippingService
    {
        /** @var ShippingService $service */
        foreach ($this->shippingServices as $service) {
            if ($service->getCode() === $code) {
                return $service;
            }
        }

        return null;
    }

    public function addShippingService(ShippingService $service): self
    {
        if (!$this->shippingServices->contains($service)) {
            $this->shippingServices->add($service);
        }

        return $this;
    }

    public function removeShippingService(ShippingService $service): self
    {
        if ($this->shippingServices->contains($service)) {
            $this->shippingServices->removeElement($service);
        }

        return $this;
    }

    public function getGoogleMapsApiKey(): ?string
    {
        return $this->googleMapsApiKey;
    }

    public function setGoogleMapsApiKey(?string $googleMapsApiKey): void
    {
        $this->googleMapsApiKey = $googleMapsApiKey;
    }

    public function isStationEnabled(): bool
    {
        return (bool)$this->stationEnabled;
    }

    public function setStationEnabled(bool $stationEnabled): void
    {
        $this->stationEnabled = $stationEnabled;
    }
}
