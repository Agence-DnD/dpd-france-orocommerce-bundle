<?php

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
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="code", type="string", length=30, unique=true)
     */
    protected $code;
    /**
     * @var string
     *
     * @ORM\Column(name="icon", type="string", length=255)
     */
    protected $icon;
    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255)
     */
    protected $label;

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     *
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     *
     * @return $this
     */
    public function setIcon($icon)
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
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getLabel();
    }
}
