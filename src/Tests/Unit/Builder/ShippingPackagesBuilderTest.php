<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Tests\Unit\Builder;

use Dnd\Bundle\DpdFranceShippingBundle\Builder\ShippingPackagesBuilder;
use Dnd\Bundle\DpdFranceShippingBundle\Entity\ShippingService;
use Dnd\Bundle\DpdFranceShippingBundle\Exception\PackageException;
use Dnd\Bundle\DpdFranceShippingBundle\Factory\DpdShippingPackageOptionsFactory;
use Dnd\Bundle\DpdFranceShippingBundle\Factory\DpdShippingPackageOptionsFactoryInterface;
use Dnd\Bundle\DpdFranceShippingBundle\Provider\SettingsProvider;
use Dnd\Bundle\DpdFranceShippingBundle\Tests\Unit\Stubs\ProductStub;
use Oro\Bundle\CurrencyBundle\Entity\Price;
use Oro\Bundle\PricingBundle\Provider\WebsiteCurrencyProvider;
use Oro\Bundle\ShippingBundle\Context\ShippingLineItem;
use Oro\Bundle\ShippingBundle\Entity\LengthUnit;
use Oro\Bundle\ShippingBundle\Entity\WeightUnit;
use Oro\Bundle\ShippingBundle\Model\Dimensions;
use Oro\Bundle\ShippingBundle\Model\Weight;
use Oro\Bundle\ShippingBundle\Provider\MeasureUnitConversion;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test class for ShippingPackagesBuilder
 *
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class ShippingPackagesBuilderTest extends TestCase
{
    private DpdShippingPackageOptionsFactoryInterface $packageOptionsFactory;
    private MockObject|MeasureUnitConversion $measureUnitConversionMock;
    private MockObject|SettingsProvider $settingsProviderMock;
    private MockObject|WebsiteCurrencyProvider $websiteCurrencyProviderMock;
    private ?ShippingPackagesBuilder $shippingPackagesBuilder = null;
    public const WEBSITE_ID = 11;
    public const CURRENCY_CODE = 'EUR';
    public const SHIPPING_SERVICE_CODE = 'dpd_fr_pickup';
    public const SHIPPING_SERVICE_LABEL = 'DPD Pickup';
    public const MAX_VALUE = 22000;
    public const MAX_LENGTH = 2;
    public const MAX_WEIGHT = 20;
    public const MAX_PERIMETER = 2.5;
    public const MAX_PACKAGE_AMOUNT = 5;
    public const SMALL_ITEM = [
        'id'     => 1,
        'sku'    => 'item001',
        'name'   => 'smallItem',
        'length' => 0.1,
        'width'  => 0.1,
        'height' => 0.1,
        'weight' => 2.5,
        'price'  => 100,
    ];
    public const MEDIUM_ITEM = [
        'id'     => 2,
        'sku'    => 'item002',
        'name'   => 'mediumItem',
        'length' => 0.15,
        'width'  => 0.15,
        'height' => 0.15,
        'weight' => 5,
        'price'  => 500,
    ];
    public const LARGE_ITEM = [
        'id'     => 3,
        'sku'    => 'item003',
        'name'   => 'largeItem',
        'length' => 0.200,
        'width'  => 0.100,
        'height' => 0.150,
        'weight' => 12,
        'price'  => 500,
    ];
    public const HEAVY_ITEM = [
        'id'     => 4,
        'sku'    => 'item004',
        'name'   => 'heavyItem',
        'length' => 0.15,
        'width'  => 0.15,
        'height' => 0.15,
        'weight' => 250,
        'price'  => 500,
    ];
    public const VERY_EXPENSIVE_ITEM = [
        'id'     => 5,
        'sku'    => 'item005',
        'name'   => 'veryExpensiveItem',
        'length' => 0.10,
        'width'  => 0.10,
        'height' => 0.10,
        'weight' => 2.5,
        'price'  => 12000,
    ];
    public const TOO_EXPENSIVE_ITEM = [
        'id'     => 6,
        'sku'    => 'item006',
        'name'   => 'tooExpensiveItem',
        'length' => 0.10,
        'width'  => 0.10,
        'height' => 0.10,
        'weight' => 2.5,
        'price'  => 25000,
    ];
    public const TOO_BIG_ITEM = [
        'id'     => 7,
        'sku'    => 'item007',
        'name'   => 'tooBigItem',
        'length' => 1,
        'width'  => 1,
        'height' => 1,
        'weight' => 2.5,
        'price'  => 25,
    ];
    public const TOO_LONG_ITEM = [
        'id'     => 8,
        'sku'    => 'item008',
        'name'   => 'tooLongItem',
        'length' => 3,
        'width'  => 0.10,
        'height' => 0.10,
        'weight' => 2.5,
        'price'  => 25,
    ];

    public function setUp(): void
    {
        $this->packageOptionsFactory       = new DpdShippingPackageOptionsFactory();
        $this->measureUnitConversionMock   = $this->getMockBuilder(MeasureUnitConversion::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->settingsProviderMock        = $this->getMockBuilder(SettingsProvider::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->websiteCurrencyProviderMock = $this->getMockBuilder(WebsiteCurrencyProvider::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->websiteCurrencyProviderMock->method('getWebsiteDefaultCurrency')->willReturn(self::CURRENCY_CODE);

        $this->measureUnitConversionMock->method('convert')->will(
            $this->returnCallback(function ($value, $unit) {
                return $value;
            })
        );
    }

    /**
     * @dataProvider shippingLineItemsProvider
     */
    public function testPackageBuilder(
        array $items,
        ?int $expectedPackageAmount,
        ?string $expectedErrorMessage,
    ): void {
        $this->shippingPackagesBuilder = new ShippingPackagesBuilder(
            $this->packageOptionsFactory,
            $this->measureUnitConversionMock,
            $this->settingsProviderMock,
            $this->websiteCurrencyProviderMock
        );

        $shippingService = new ShippingService();
        $shippingService->setCode(self::SHIPPING_SERVICE_CODE);
        $shippingService->setIcon('bundles/dnddpdfranceshipping/img/livraison-dpd-pickup.png');
        $shippingService->setLabel(self::SHIPPING_SERVICE_LABEL);
        $shippingService->setParcelMaxPerimeter(self::MAX_PERIMETER);
        $shippingService->setParcelMaxLength(self::MAX_LENGTH);
        $shippingService->setParcelMaxWeight(self::MAX_WEIGHT);
        $shippingService->setParcelMaxAmount(self::MAX_PACKAGE_AMOUNT);
        $shippingService->setParcelMaxValue(self::MAX_VALUE);
        $this->shippingPackagesBuilder->init($shippingService, self::WEBSITE_ID);

        if ($expectedPackageAmount < 0) {
            $this->expectException(PackageException::class);
            $this->expectExceptionMessage($expectedErrorMessage);
        }
        foreach ($items as $item) {
            $product = new ProductStub($item['item']['id']);
            $product->setSku($item['item']['sku']);
            $product->setName($item['item']['name']);
            $shippingLineItem = new ShippingLineItem([
                ShippingLineItem::FIELD_DIMENSIONS  => Dimensions::create(
                    $item['item']['length'],
                    $item['item']['width'],
                    $item['item']['height'],
                    (new LengthUnit())->setCode(ShippingPackagesBuilder::LENGTH_UNIT)
                ),
                ShippingLineItem::FIELD_WEIGHT      => Weight::create(
                    $item['item']['weight'],
                    (new WeightUnit())->setCode(ShippingPackagesBuilder::WEIGHT_UNIT)
                ),
                ShippingLineItem::FIELD_PRICE       => Price::create(
                    $item['item']['price'],
                    self::CURRENCY_CODE
                ),
                ShippingLineItem::FIELD_QUANTITY    => $item['quantity'],
                ShippingLineItem::FIELD_PRODUCT_SKU => $item['item']['sku'],
                ShippingLineItem::FIELD_PRODUCT     => $product,
            ]);
            $this->shippingPackagesBuilder->addLineItem($shippingLineItem);
        }

        $packages = $this->shippingPackagesBuilder->getPackages();

        $this->assertEquals($expectedPackageAmount, count($packages));
    }

    public function shippingLineItemsProvider(): array
    {
        return [
            'single_small_item'            => [
                'items'                 => [
                    [
                        'item'     => self::SMALL_ITEM,
                        'quantity' => 1,
                    ],
                ],
                'expectedPackageAmount' => 1,
                'expectedError'         => null,
            ],
            'small_item_qty_10'            => [
                'items'                 => [
                    [
                        'item'     => self::SMALL_ITEM,
                        'quantity' => 10,
                    ],
                ],
                'expectedPackageAmount' => 2,
                'expectedError'         => null,
            ],
            'single_medium_item'           => [
                'items'                 => [
                    [
                        'item'     => self::MEDIUM_ITEM,
                        'quantity' => 1,
                    ],
                ],
                'expectedPackageAmount' => 1,
                'expectedError'         => null,
            ],
            'medium_item_qty_50'           => [
                'items'                 => [
                    [
                        'item'     => self::MEDIUM_ITEM,
                        'quantity' => 50,
                    ],
                ],
                'expectedPackageAmount' => -1,
                'expectedError'         => sprintf(
                    'Too many packages, need more than %d packages while %d are allowed for %s. Advise splitting order.',
                    self::MAX_PACKAGE_AMOUNT + 1,
                    self::MAX_PACKAGE_AMOUNT,
                    self::SHIPPING_SERVICE_LABEL,
                ),
            ],
            'item001_and_item002'          => [
                'items'                 => [
                    [
                        'item'     => self::SMALL_ITEM,
                        'quantity' => 1,
                    ],
                    [
                        'item'     => self::MEDIUM_ITEM,
                        'quantity' => 1,
                    ],
                ],
                'expectedPackageAmount' => 1,
                'expectedError'         => null,
            ],
            'various_sizes_items'          => [
                'items'                 => [
                    [
                        'item'     => self::SMALL_ITEM,
                        'quantity' => 4,
                    ],
                    [
                        'item'     => self::MEDIUM_ITEM,
                        'quantity' => 3,
                    ],
                    [
                        'item'     => self::LARGE_ITEM,
                        'quantity' => 2,
                    ],
                ],
                'expectedPackageAmount' => 3,
                'expectedError'         => null,
            ],
            'too_many_various_sizes_items'          => [
                'items'                 => [
                    [
                        'item'     => self::SMALL_ITEM,
                        'quantity' => 12,
                    ],
                    [
                        'item'     => self::MEDIUM_ITEM,
                        'quantity' => 10,
                    ],
                    [
                        'item'     => self::LARGE_ITEM,
                        'quantity' => 2,
                    ],
                ],
                'expectedPackageAmount' => -1,
                'expectedError'         => sprintf(
                    'Too many packages, need more than %d packages while %d are allowed for %s. Advise splitting order.',
                    self::MAX_PACKAGE_AMOUNT + 1,
                    self::MAX_PACKAGE_AMOUNT,
                    self::SHIPPING_SERVICE_LABEL,
                ),
            ],
            'multiple_item001_and_item002' => [
                'items'                 => [
                    [
                        'item'     => self::SMALL_ITEM,
                        'quantity' => 4,
                    ],
                    [
                        'item'     => self::MEDIUM_ITEM,
                        'quantity' => 6,
                    ],
                ],
                'expectedPackageAmount' => 2,
                'expectedError'         => null,
            ],
            'expensive_item_qty_3'         => [
                'items'                 => [
                    [
                        'item'     => self::VERY_EXPENSIVE_ITEM,
                        'quantity' => 3,
                    ],
                ],
                'expectedPackageAmount' => 3,
                'expectedError'         => null,
            ],
            'too_expensive_item_qty_1'     => [
                'items'                 => [
                    [
                        'item'     => self::TOO_EXPENSIVE_ITEM,
                        'quantity' => 1,
                    ],
                ],
                'expectedPackageAmount' => -1,
                'expectedError'         => sprintf(
                    'The item %s (%s) cannot be shipped with %s. Either too heavy, too big or too pricey.',
                    self::TOO_EXPENSIVE_ITEM['name'],
                    self::TOO_EXPENSIVE_ITEM['sku'],
                    self::SHIPPING_SERVICE_LABEL,
                ),
            ],
            'too_big_item_qty_1'           => [
                'items'                 => [
                    [
                        'item'     => self::TOO_BIG_ITEM,
                        'quantity' => 1,
                    ],
                ],
                'expectedPackageAmount' => -1,
                'expectedError'         => sprintf(
                    'The item %s (%s) cannot be shipped with %s. Either too heavy, too big or too pricey.',
                    self::TOO_BIG_ITEM['name'],
                    self::TOO_BIG_ITEM['sku'],
                    self::SHIPPING_SERVICE_LABEL,
                ),
            ],
            'too_long_item_qty_1'          => [
                'items'                 => [
                    [
                        'item'     => self::TOO_LONG_ITEM,
                        'quantity' => 1,
                    ],
                ],
                'expectedPackageAmount' => -1,
                'expectedError'         => sprintf(
                    'The item %s (%s) cannot be shipped with %s. Either too heavy, too big or too pricey.',
                    self::TOO_LONG_ITEM['name'],
                    self::TOO_LONG_ITEM['sku'],
                    self::SHIPPING_SERVICE_LABEL,
                ),
            ],
            'too_heavy_item_qty_1'          => [
                'items'                 => [
                    [
                        'item'     => self::HEAVY_ITEM,
                        'quantity' => 1,
                    ],
                ],
                'expectedPackageAmount' => -1,
                'expectedError'         => sprintf(
                    'The item %s (%s) cannot be shipped with %s. Either too heavy, too big or too pricey.',
                    self::HEAVY_ITEM['name'],
                    self::HEAVY_ITEM['sku'],
                    self::SHIPPING_SERVICE_LABEL,
                ),
            ],
        ];
    }
}
