<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Tests\Unit\Stubs;

use Oro\Bundle\ProductBundle\Entity\Product;

class ProductStub extends Product
{
    private ?string $name = null;

    public function __construct(int $id)
    {
        $this->id = $id;

        parent::__construct();
    }

    public function getName(): ?string
    {
        return $this->name;
    }
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
