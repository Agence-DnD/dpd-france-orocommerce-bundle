<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Entity;

use Dnd\Bundle\DpdFranceShippingBundle\Entity\Repository\ShippingServiceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
#[ORM\Entity(repositoryClass: ShippingServiceRepository::class)]
#[ORM\Table(name: 'dnd_dpd_fr_shipping_service')]
class ShippingService
{
    /**
     * The code for the shipping service
     */
    #[ORM\Id]
    #[ORM\Column(name: "code", type: "string", length: 30, unique: true)]
    protected string $code;
    #[ORM\Column(name: "icon", type: "string", length: 255)]
    protected string $icon;
    #[ORM\Column(name: "label", type: "string", length: 255)]
    protected string $label;
    #[ORM\Column(name: "parcel_max_perimeter", type: "float")]
    protected float $parcelMaxPerimeter;
    #[ORM\Column(name: "parcel_max_length", type: "float")]
    protected float $parcelMaxLength;
    #[ORM\Column(name: "parcel_max_weight", type: "float")]
    protected float $parcelMaxWeight;
    #[ORM\Column(name: "parcel_max_amount", type: "integer", nullable: true)]
    protected ?int $parcelMaxAmount = null;
    #[ORM\Column(name: "parcel_max_value", type: "float")]
    protected float $parcelMaxValue;

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function getParcelMaxPerimeter(): float
    {
        return $this->parcelMaxPerimeter;
    }

    public function setParcelMaxPerimeter(float $parcelMaxPerimeter): void
    {
        $this->parcelMaxPerimeter = $parcelMaxPerimeter;
    }

    public function getParcelMaxLength(): float
    {
        return $this->parcelMaxLength;
    }

    public function setParcelMaxLength(float $parcelMaxLength): void
    {
        $this->parcelMaxLength = $parcelMaxLength;
    }

    public function getParcelMaxWeight(): float
    {
        return $this->parcelMaxWeight;
    }

    public function setParcelMaxWeight(float $parcelMaxWeight): void
    {
        $this->parcelMaxWeight = $parcelMaxWeight;
    }

    public function getParcelMaxAmount(): ?int
    {
        return $this->parcelMaxAmount;
    }

    public function setParcelMaxAmount(?int $parcelMaxAmount): void
    {
        $this->parcelMaxAmount = $parcelMaxAmount;
    }

    public function getParcelMaxValue(): ?float
    {
        return $this->parcelMaxValue;
    }

    public function setParcelMaxValue(float $parcelMaxValue): void
    {
        $this->parcelMaxValue = $parcelMaxValue;
    }

    public function __toString(): string
    {
        return $this->getLabel();
    }
}
