<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ShippingService
 *
 * @ORM\Table(name="dnd_dpd_fr_shipping_service")
 * @ORM\Entity(repositoryClass="Dnd\Bundle\DpdFranceShippingBundle\Entity\Repository\ShippingServiceRepository")
 *
 * @package   Dnd\Bundle\DpdFranceShippingBundle\Entity
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class ShippingService
{
    /**
     * The code for the shipping service
     *
     * @ORM\Id
     * @ORM\Column(name="code", type="string", length=30, unique=true)
     *
     * @var string $code
     */
    protected string $code;
    /**
     * The icon for the shipping service
     *
     * @ORM\Column(name="icon", type="string", length=255)
     *
     * @var string $icon
     */
    protected string $icon;
    /**
     * The label for the shipping service
     *
     * @ORM\Column(name="label", type="string", length=255)
     *
     * @var string $label
     */
    protected string $label;
    /**
     * The maximum perimeter for each package
     *
     * @ORM\Column(name="parcel_max_perimeter", type="float")
     *
     * @var float $parcelMaxPerimeter
     */
    protected float $parcelMaxPerimeter;
    /**
     * The maximum length for each package
     *
     * @ORM\Column(name="parcel_max_length", type="float")
     *
     * @var float $parcelMaxLength
     */
    protected float $parcelMaxLength;
    /**
     * The maximum weight for each package
     *
     * @ORM\Column(name="parcel_max_weight", type="float")
     *
     * @var float $parcelMaxWeight
     */
    protected float $parcelMaxWeight;
    /**
     * The maximum amount of packages
     *
     * @ORM\Column(name="parcel_max_amount", type="float")
     *
     * @var float $parcelMaxAmount
     */
    protected float $parcelMaxAmount;

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     *
     * @return $this
     */
    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     *
     * @return $this
     */
    public function setIcon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Description getLabel function
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Description setLabel function
     *
     * @param string $label
     *
     * @return void
     */
    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    /**
     * Description getParcelMaxPerimeter function
     *
     * @return float
     */
    public function getParcelMaxPerimeter(): float
    {
        return $this->parcelMaxPerimeter;
    }

    /**
     * Description setParcelMaxPerimeter function
     *
     * @param float $parcelMaxPerimeter
     *
     * @return void
     */
    public function setParcelMaxPerimeter(float $parcelMaxPerimeter): void
    {
        $this->parcelMaxPerimeter = $parcelMaxPerimeter;
    }

    /**
     * Description getParcelMaxLength function
     *
     * @return float
     */
    public function getParcelMaxLength(): float
    {
        return $this->parcelMaxLength;
    }

    /**
     * Description setParcelMaxLength function
     *
     * @param float $parcelMaxLength
     *
     * @return void
     */
    public function setParcelMaxLength(float $parcelMaxLength): void
    {
        $this->parcelMaxLength = $parcelMaxLength;
    }

    /**
     * Description getParcelMaxWeight function
     *
     * @return float
     */
    public function getParcelMaxWeight(): float
    {
        return $this->parcelMaxWeight;
    }

    /**
     * Description setParcelMaxWeight function
     *
     * @param float $parcelMaxWeight
     *
     * @return void
     */
    public function setParcelMaxWeight(float $parcelMaxWeight): void
    {
        $this->parcelMaxWeight = $parcelMaxWeight;
    }

    /**
     * Description getParcelMaxAmount function
     *
     * @return float
     */
    public function getParcelMaxAmount(): float
    {
        return $this->parcelMaxAmount;
    }

    /**
     * Description setParcelMaxAmount function
     *
     * @param float $parcelMaxAmount
     *
     * @return void
     */
    public function setParcelMaxAmount(float $parcelMaxAmount): void
    {
        $this->parcelMaxAmount = $parcelMaxAmount;
    }

    /**
     * Description __toString function
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getLabel();
    }
}
