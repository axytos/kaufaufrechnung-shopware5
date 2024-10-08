<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Adapter;

use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\PluginOrderInterface;
use AxytosKaufAufRechnungShopware5\Adapter\Common\BasketFactory;
use AxytosKaufAufRechnungShopware5\Adapter\Common\HashCalculation\HashCalculator;
use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\OrderFactory;
use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\OrderSyncRepository as UnifiedShopwareModelOrderSyncRepository;
use AxytosKaufAufRechnungShopware5\Adapter\Information\Refund\BasketFactory as ReportRefundBasketFactory;
use AxytosKaufAufRechnungShopware5\Adapter\OrderSyncRepository;
use AxytosKaufAufRechnungShopware5\Adapter\PluginOrderFactory;
use AxytosKaufAufRechnungShopware5\Configuration\PluginConfiguration;
use AxytosKaufAufRechnungShopware5\DataAbstractionLayer\OrderRepository;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Order\Order as ShopwareOrder;

/**
 * @internal
 */
class OrderSyncRepositoryTest extends TestCase
{
    /**
     * @var OrderRepository&MockObject
     */
    private $orderRepository;

    /**
     * @var OrderSyncRepository
     */
    private $sut;

    /**
     * @return void
     *
     * @before
     */
    #[Before]
    public function beforeEach()
    {
        $this->orderRepository = $this->createMock(OrderRepository::class);

        $this->sut = new OrderSyncRepository(
            new UnifiedShopwareModelOrderSyncRepository(
                new OrderFactory(
                    $this->orderRepository,
                    $this->createMock(PluginConfiguration::class)
                ),
                $this->orderRepository
            ),
            new PluginOrderFactory(
                $this->createMock(BasketFactory::class),
                $this->createMock(ReportRefundBasketFactory::class),
                $this->createMock(HashCalculator::class)
            )
        );
    }

    /**
     * @return void
     */
    public function test_get_orders_by_states()
    {
        $states = ['STATE_A', 'STATE_B'];
        $shopwareOrders = [
            $this->createMock(ShopwareOrder::class),
            $this->createMock(ShopwareOrder::class),
            $this->createMock(ShopwareOrder::class),
        ];

        $this->orderRepository
            ->method('getOrdersByStates')
            ->with($states)
            ->willReturn($shopwareOrders)
        ;

        $orders = $this->sut->getOrdersByStates($states);

        $this->assertCount(3, $orders);
        foreach ($orders as $order) {
            $this->assertInstanceOf(PluginOrderInterface::class, $order);
        }
    }

    /**
     * @return void
     */
    public function test_get_order_by_order_number()
    {
        $this->orderRepository
            ->method('findOrderByOrderNumber')
            ->with('OrderNumber')
            ->willReturn($this->createMock(ShopwareOrder::class))
        ;

        $order = $this->sut->getOrderByOrderNumber('OrderNumber');

        $this->assertInstanceOf(PluginOrderInterface::class, $order);
    }

    /**
     * @return void
     */
    public function test_get_order_by_order_number_returns_null_if_order_not_found()
    {
        $this->orderRepository
            ->method('findOrderByOrderNumber')
            ->with('OrderNumber')
            ->willReturn(null)
        ;

        $order = $this->sut->getOrderByOrderNumber('OrderNumber');

        $this->assertNull($order);
    }
}
