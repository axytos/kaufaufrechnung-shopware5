<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Adapter;

use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\BasketUpdate\BasketInterface;
use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Model\AxytosOrderStateInfo;
use AxytosKaufAufRechnungShopware5\Adapter\Common\Basket;
use AxytosKaufAufRechnungShopware5\Adapter\Common\BasketFactory;
use AxytosKaufAufRechnungShopware5\Adapter\Common\HashCalculation\HashCalculator;
use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order;
use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\OrderAttributes;
use AxytosKaufAufRechnungShopware5\Adapter\Information\Refund\BasketFactory as RefundBasketFactory;
use AxytosKaufAufRechnungShopware5\Adapter\PluginOrder;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Order\Document\Document;
use Shopware\Models\Order\Status;

/**
 * @internal
 */
class PluginOrderTest extends TestCase
{
    /**
     * @var Order&MockObject
     */
    private $order;

    /**
     * @var OrderAttributes&MockObject
     */
    private $orderAttributes;

    /**
     * @var BasketFactory&MockObject
     */
    private $basketFactory;

    /**
     * @var RefundBasketFactory&MockObject
     */
    private $refundBasketFactory;

    /**
     * @var HashCalculator&MockObject
     */
    private $hashCalculator;

    /**
     * @var PluginOrder
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
        $this->order = $this->createMock(Order::class);
        $this->orderAttributes = $this->createMock(OrderAttributes::class);
        $this->basketFactory = $this->createMock(BasketFactory::class);
        $this->refundBasketFactory = $this->createMock(RefundBasketFactory::class);
        $this->hashCalculator = $this->createMock(HashCalculator::class);

        $this->order
            ->method('getAttributes')
            ->willReturn($this->orderAttributes)
        ;

        $this->sut = new PluginOrder(
            $this->order,
            $this->basketFactory,
            $this->refundBasketFactory,
            $this->hashCalculator
        );
    }

    /**
     * @return void
     */
    public function test_get_order_number_returns_order_number()
    {
        $this->order
            ->method('getNumber')
            ->willReturn('order-123')
        ;

        $this->assertEquals('order-123', $this->sut->getOrderNumber());
    }

    /**
     * @return void
     */
    public function test_load_state_creates_state_from_attributes()
    {
        $testState = 'test-state';
        $testStateData = 'test-state-data';
        $this->orderAttributes
            ->method('getAxytosKaufAufRechnungOrderState')
            ->willReturn($testState)
        ;
        $this->orderAttributes
            ->method('getAxytosKaufAufRechnungOrderStateData')
            ->willReturn($testStateData)
        ;

        $expected = new AxytosOrderStateInfo($testState, $testStateData);
        $this->assertEquals($expected, $this->sut->loadState());
    }

    /**
     * @return void
     */
    public function test_save_state_persists_state_to_attributes()
    {
        $testState = 'test-state';
        $testStateData = 'test-state-data';

        $this->orderAttributes
            ->expects($this->once())
            ->method('setAxytosKaufAufRechnungOrderState')
            ->with($testState)
        ;
        $this->orderAttributes
            ->expects($this->once())
            ->method('setAxytosKaufAufRechnungOrderStateData')
            ->with($testStateData)
        ;
        $this->orderAttributes
            ->expects($this->once())
            ->method('persist')
        ;

        $this->sut->saveState($testState, $testStateData);
    }

    /**
     * @return void
     */
    public function test_freeze_basket()
    {
        $testBasket = $this->createMock(Basket::class);
        $testHash = 'test-hash-sum';

        $this->basketFactory
            ->method('create')
            ->with($this->order)
            ->willReturn($testBasket)
        ;
        $this->hashCalculator
            ->method('calculateBasketHash')
            ->with($testBasket)
            ->willReturn($testHash)
        ;

        $this->orderAttributes
            ->expects($this->once())
            ->method('setAxytosKaufAufRechnungOrderBasketHash')
            ->with($testHash)
        ;
        $this->orderAttributes
            ->expects($this->once())
            ->method('persist')
        ;

        $this->sut->freezeBasket();
    }

    /**
     * @return void
     */
    public function test_has_been_canceled_returns_true_when_order_is_canceled()
    {
        $this->order
            ->method('isCanceled')
            ->willReturn(true)
        ;

        $this->assertTrue($this->sut->hasBeenCanceled());
    }

    /**
     * @return void
     */
    public function test_has_been_canceled_returns_false_when_order_is_not_canceld()
    {
        $this->order
            ->method('isCanceled')
            ->willReturn(false)
        ;

        $this->assertFalse($this->sut->hasBeenCanceled());
    }

    /**
     * @return void
     */
    public function test_has_been_invoiced_returns_true_when_invoice_document_is_found_and_order_is_completed()
    {
        $this->order
            ->method('findInvoiceDocument')
            ->willReturn($this->createMock(Document::class))
        ;

        $this->order
            ->method('isCompleted')
            ->willReturn(true)
        ;

        $this->assertTrue($this->sut->hasBeenInvoiced());
    }

    /**
     * @return void
     */
    public function test_has_been_invoiced_returns_false_when_invoice_document_is_found_and_order_is_not_completed()
    {
        $this->order
            ->method('findInvoiceDocument')
            ->willReturn($this->createMock(Document::class))
        ;

        $this->order
            ->method('isCompleted')
            ->willReturn(false)
        ;

        $this->assertFalse($this->sut->hasBeenInvoiced());
    }

    /**
     * @return void
     */
    public function test_has_been_invoiced_returns_false_when_invoice_document_is_not_found_and_order_is_completed()
    {
        $this->order
            ->method('findInvoiceDocument')
            ->willReturn(null)
        ;

        $this->order
            ->method('isCompleted')
            ->willReturn(true)
        ;

        $this->assertFalse($this->sut->hasBeenInvoiced());
    }

    /**
     * @return void
     */
    public function test_has_been_invoiced_returns_false_when_invoice_document_is_not_found_and_order_is_not_completed()
    {
        $this->order
            ->method('findInvoiceDocument')
            ->willReturn(null)
        ;

        $this->order
            ->method('isCompleted')
            ->willReturn(false)
        ;

        $this->assertFalse($this->sut->hasBeenInvoiced());
    }

    /**
     * @return void
     */
    public function test_has_been_refunded_returns_true_when_cancellation_document_is_found()
    {
        $this->order
            ->method('findCreditDocument')
            ->willReturn($this->createMock(Document::class))
        ;

        // hasBeenRefunded should always return false because this feature is disabled for now
        $this->assertFalse($this->sut->hasBeenRefunded());
    }

    /**
     * @return void
     */
    public function test_has_been_refunded_returns_false_when_cancellation_document_is_not_found()
    {
        $this->order
            ->method('findCreditDocument')
            ->willReturn(null)
        ;

        // hasBeenRefunded should always return false because this feature is disabled for now
        $this->assertFalse($this->sut->hasBeenRefunded());
    }

    /**
     * @return void
     */
    public function test_has_shipping_reported_returns_true_when_attribute_is_true()
    {
        $this->orderAttributes->method('getAxytosKaufAufRechnungHasShippingReported')->willReturn(true);

        $this->order->method('getAttributes')->willReturn($this->orderAttributes);

        $this->assertTrue($this->sut->hasShippingReported());
    }

    /**
     * @return void
     */
    public function test_has_shipping_reported_returns_false_when_attribute_is_false()
    {
        $this->orderAttributes->method('getAxytosKaufAufRechnungHasShippingReported')->willReturn(false);

        $this->order->method('getAttributes')->willReturn($this->orderAttributes);

        $this->assertFalse($this->sut->hasShippingReported());
    }

    /**
     * @return void
     */
    public function test_has_been_shipped_returns_true_when_order_is_completed()
    {
        $this->order->method('isCompleted')->willReturn(true);

        $this->assertTrue($this->sut->hasBeenShipped());
    }

    /**
     * @return void
     */
    public function test_has_been_shipped_returns_true_when_order_is_completely_shipped()
    {
        $this->order->method('isCompletelyShipped')->willReturn(true);

        $this->assertTrue($this->sut->hasBeenShipped());
    }

    /**
     * @return void
     */
    public function test_has_been_shipped_returns_false_when_order_is_neither_shipped_nor_completed()
    {
        $this->order->method('isCompletelyShipped')->willReturn(false);
        $this->order->method('isCompleted')->willReturn(false);

        $this->assertFalse($this->sut->hasBeenShipped());
    }

    /**
     * @return void
     */
    public function test_save_has_shipping_reported_sets_attribute_to_true()
    {
        $this->orderAttributes->expects($this->once())->method('setAxytosKaufAufRechnungHasShippingReported')->willReturn(false);

        $this->order->method('getAttributes')->willReturn($this->orderAttributes);

        $this->sut->saveHasShippingReported();
    }

    /**
     * @return void
     */
    public function test_save_has_shipping_reported_persists_attribute()
    {
        $this->orderAttributes->expects($this->once())->method('persist');

        $this->order->method('getAttributes')->willReturn($this->orderAttributes);

        $this->sut->saveHasShippingReported();
    }

    /**
     * @return void
     */
    public function test_has_new_tracking_information_returns_true_if_tracking_codes_are_different()
    {
        $this->orderAttributes->method('getAxytosKaufAufRechnungReportedTrackingCode')->willReturn('old-code');

        $this->order->method('getAttributes')->willReturn($this->orderAttributes);

        $this->order
            ->method('getTrackingCode')
            ->willReturn('new-code')
        ;

        $result = $this->sut->hasNewTrackingInformation();

        $this->assertTrue($result);
    }

    /**
     * @return void
     */
    public function test_has_new_tracking_information_returns_false_if_tracking_codes_are_equal()
    {
        $this->orderAttributes->method('getAxytosKaufAufRechnungReportedTrackingCode')->willReturn('old-code');

        $this->order->method('getAttributes')->willReturn($this->orderAttributes);

        $this->order
            ->method('getTrackingCode')
            ->willReturn('old-code')
        ;

        $result = $this->sut->hasNewTrackingInformation();

        $this->assertFalse($result);
    }

    /**
     * @return void
     */
    public function test_save_new_tracking_information_updates_order_with_new_tracking_code()
    {
        $this->order->method('getAttributes')->willReturn($this->orderAttributes);

        $newTrackingCode = 'new-code';
        $this->order
            ->method('getTrackingCode')
            ->willReturn($newTrackingCode)
        ;
        $this->orderAttributes
            ->expects($this->once())
            ->method('setAxytosKaufAufRechnungReportedTrackingCode')
            ->with($newTrackingCode)
        ;
        $this->orderAttributes
            ->expects($this->once())
            ->method('persist')
        ;

        $this->sut->saveNewTrackingInformation();
    }

    /**
     * @return void
     */
    public function test_has_basket_updates_returns_true_if_hashes_are_different()
    {
        /** @var BasketInterface&MockObject */
        $basket = $this->createMock(BasketInterface::class);
        $this->basketFactory
            ->method('create')
            ->with($this->order)
            ->willReturn($basket)
        ;
        $this->hashCalculator
            ->method('calculateBasketHash')
            ->with($basket)
            ->willReturn('new-hash')
        ;
        $this->orderAttributes
            ->method('getAxytosKaufAufRechnungOrderBasketHash')
            ->willReturn('old-hash')
        ;

        $this->assertTrue($this->sut->hasBasketUpdates());
    }

    /**
     * @return void
     */
    public function test_has_basket_updates_returns_false_if_hashes_are_identical()
    {
        /** @var BasketInterface&MockObject */
        $basket = $this->createMock(BasketInterface::class);
        $this->basketFactory
            ->method('create')
            ->with($this->order)
            ->willReturn($basket)
        ;
        $this->hashCalculator
            ->method('calculateBasketHash')
            ->with($basket)
            ->willReturn('old-hash')
        ;
        $this->orderAttributes
            ->method('getAxytosKaufAufRechnungOrderBasketHash')
            ->willReturn('old-hash')
        ;

        $this->assertFalse($this->sut->hasBasketUpdates());
    }

    /**
     * @return void
     */
    public function test_save_basket_updates_reported_stores_new_hash_with_order()
    {
        /** @var BasketInterface&MockObject */
        $basket = $this->createMock(BasketInterface::class);
        $this->basketFactory
            ->method('create')
            ->with($this->order)
            ->willReturn($basket)
        ;
        $this->hashCalculator
            ->method('calculateBasketHash')
            ->with($basket)
            ->willReturn('new-hash')
        ;
        $this->orderAttributes
            ->expects($this->once())
            ->method('setAxytosKaufAufRechnungOrderBasketHash')
        ;
        $this->orderAttributes
            ->expects($this->once())
            ->method('persist')
        ;

        $this->sut->saveBasketUpdatesReported();
    }

    /**
     * @return void
     */
    public function test_set_order_status_paid_updates_the_payment_status_to_completely_paid()
    {
        $this->order
            ->expects($this->once())
            ->method('savePaymentStatus')
            ->with(Status::PAYMENT_STATE_COMPLETELY_PAID)
        ;

        $this->sut->saveHasBeenPaid();
    }
}
