<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Adapter\Common\UnifiedShopwareModel;

use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\OrderAttributes;
use AxytosKaufAufRechnungShopware5\DataAbstractionLayer\OrderRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Order\Order;

class OrderAttributesTest extends TestCase
{
    /**
     * @return void
     */
    public function test_persist_saves_order()
    {
        /** @var Order&MockObject */
        $order = $this->createMock(Order::class);

        /** @var OrderRepository&MockObject */
        $orderRepository = $this->createMock(OrderRepository::class);
        $orderRepository->expects($this->once())->method('saveOrder')->with($order);

        $sut = new OrderAttributes($order, $orderRepository);
        $sut->persist();
    }

    /**
     * @dataProvider delegation_of_generated_getters_test_cases
     * @param string $proxyMethodName
     * @param string $generatedMethodName
     * @param mixed $getterResult
     * @return void
     */
    #[DataProvider('delegation_of_generated_getters_test_cases')]
    public function test_delegation_of_generated_getters($proxyMethodName, $generatedMethodName, $getterResult)
    {
        /** @var ExpectedGeneratedOrderAttributesInterface&MockObject */
        $attribute = $this->createMock(ExpectedGeneratedOrderAttributesInterface::class);
        $attribute->method($generatedMethodName)->willReturn($getterResult);

        /** @var Order&MockObject */
        $order = $this->createMock(Order::class);
        $order->method('getAttribute')->willReturn($attribute);

        /** @var OrderRepository&MockObject */
        $orderRepository = $this->createMock(OrderRepository::class);

        $sut = new OrderAttributes($order, $orderRepository);

        /** @var callable */
        $callable = [$sut, $proxyMethodName];
        $actual = call_user_func($callable);

        $this->assertEquals($getterResult, $actual);
    }

    /**
     * @return array<array<mixed>>
     */
    public static function delegation_of_generated_getters_test_cases()
    {
        return [
            ['getAxytosKaufAufRechnungOrderState', 'getAxytosKaufAufRechnungOrderState', '1234'],
            ['getAxytosKaufAufRechnungOrderStateData', 'getAxytosKaufAufRechnungOrderStateData', '1234'],
            ['getAxytosKaufAufRechnungHasShippingReported', 'getAxytosKaufAufRechnungHasShippingReported', true],
            ['getAxytosKaufAufRechnungPrecheckResponse', 'getAxytosKaufAufRechnungPrecheckResponse', '1234'],
            ['getAxytosKaufAufRechnungOrderBasketHash', 'getAxytosKaufAufRechnungOrderBasketHash', '1234'],
            ['getAxytosKaufAufRechnungReportedTrackingCode', 'getAxytosKaufAufRechnungReportedTrackingCode', '1234'],
            ['getAxytosKaufAufRechnungPrecheckResponse', 'getAxytosKaufAufRechnungPrecheckResponse', '1234']
        ];
    }

    /**
     * @dataProvider delegation_of_generated_setters_test_cases
     * @param string $proxyMethodName
     * @param string $generatedMethodName
     * @param mixed $setterArg
     * @return void
     */
    #[DataProvider('delegation_of_generated_setters_test_cases')]
    public function test_delegation_of_generated_setters($proxyMethodName, $generatedMethodName, $setterArg)
    {
        /** @var ExpectedGeneratedOrderAttributesInterface&MockObject */
        $attribute = $this->createMock(ExpectedGeneratedOrderAttributesInterface::class);
        $attribute->expects($this->once())->method($generatedMethodName)->with($setterArg);

        /** @var Order&MockObject */
        $order = $this->createMock(Order::class);
        $order->method('getAttribute')->willReturn($attribute);

        /** @var OrderRepository&MockObject */
        $orderRepository = $this->createMock(OrderRepository::class);

        $sut = new OrderAttributes($order, $orderRepository);

        /** @var callable */
        $callable = [$sut, $proxyMethodName];
        call_user_func($callable, $setterArg);
    }

    /**
     * @return array<array<mixed>>
     */
    public static function delegation_of_generated_setters_test_cases()
    {
        return [
            ['setAxytosKaufAufRechnungOrderState', 'setAxytosKaufAufRechnungOrderState', '1234'],
            ['setAxytosKaufAufRechnungOrderStateData', 'setAxytosKaufAufRechnungOrderStateData', '1234'],
            ['setAxytosKaufAufRechnungHasShippingReported', 'setAxytosKaufAufRechnungHasShippingReported', true],
            ['setAxytosKaufAufRechnungPrecheckResponse', 'setAxytosKaufAufRechnungPrecheckResponse', '1234'],
            ['setAxytosKaufAufRechnungOrderBasketHash', 'setAxytosKaufAufRechnungOrderBasketHash', '1234'],
            ['setAxytosKaufAufRechnungReportedTrackingCode', 'setAxytosKaufAufRechnungReportedTrackingCode', '1234'],
            ['setAxytosKaufAufRechnungPrecheckResponse', 'setAxytosKaufAufRechnungPrecheckResponse', '1234']
        ];
    }
}
