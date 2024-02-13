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
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Order\Document\Document;
use Shopware\Models\Order\Status;

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
     * @before
     */
    public function beforeEach()
    {
        $this->order = $this->createMock(Order::class);
        $this->orderAttributes = $this->createMock(OrderAttributes::class);
        $this->basketFactory = $this->createMock(BasketFactory::class);
        $this->refundBasketFactory = $this->createMock(RefundBasketFactory::class);
        $this->hashCalculator = $this->createMock(HashCalculator::class);

        $this->order
            ->method('getAttributes')
            ->willReturn($this->orderAttributes);

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
    public function test_getOrderNumber_returnsOrderNumber()
    {
        $this->order
            ->method('getNumber')
            ->willReturn('order-123');

        $this->assertEquals('order-123', $this->sut->getOrderNumber());
    }

    /**
     * @return void
     */
    public function test_loadState_createsStateFromAttributes()
    {
        $testState = 'test-state';
        $testStateData = 'test-state-data';
        $this->orderAttributes
            ->method('getAxytosKaufAufRechnungOrderState')
            ->willReturn($testState);
        $this->orderAttributes
            ->method('getAxytosKaufAufRechnungOrderStateData')
            ->willReturn($testStateData);

        $expected = new AxytosOrderStateInfo($testState, $testStateData);
        $this->assertEquals($expected, $this->sut->loadState());
    }

    /**
     * @return void
     */
    public function test_saveState_persistsStateToAttributes()
    {
        $testState = 'test-state';
        $testStateData = 'test-state-data';

        $this->orderAttributes
            ->expects($this->once())
            ->method('setAxytosKaufAufRechnungOrderState')
            ->with($testState);
        $this->orderAttributes
            ->expects($this->once())
            ->method('setAxytosKaufAufRechnungOrderStateData')
            ->with($testStateData);
        $this->orderAttributes
            ->expects($this->once())
            ->method('persist');

        $this->sut->saveState($testState, $testStateData);
    }

    /**
     * @return void
     */
    public function test_freezeBasket()
    {
        $testBasket = $this->createMock(Basket::class);
        $testHash = 'test-hash-sum';

        $this->basketFactory
            ->method('create')
            ->with($this->order)
            ->willReturn($testBasket);
        $this->hashCalculator
            ->method('calculateBasketHash')
            ->with($testBasket)
            ->willReturn($testHash);

        $this->orderAttributes
            ->expects($this->once())
            ->method('setAxytosKaufAufRechnungOrderBasketHash')
            ->with($testHash);
        $this->orderAttributes
            ->expects($this->once())
            ->method('persist');

        $this->sut->freezeBasket();
    }

    /**
     * @return void
     */
    public function test_hasBeenCanceled_returns_true_when_cancellation_document_is_found()
    {
        $this->order
            ->method('findCancellationDocument')
            ->willReturn($this->createMock(Document::class));

        $this->assertTrue($this->sut->hasBeenCanceled());
    }

    /**
     * @return void
     */
    public function test_hasBeenCanceled_returns_false_when_cancellation_document_is_not_found()
    {
        $this->order
            ->method('findCancellationDocument')
            ->willReturn(null);

        $this->assertFalse($this->sut->hasBeenCanceled());
    }

    /**
     * @return void
     */
    public function test_hasBeenInvoiced_returns_true_when_invoice_document_is_found()
    {
        $this->order
            ->method('findInvoiceDocument')
            ->willReturn($this->createMock(Document::class));

        $this->assertTrue($this->sut->hasBeenInvoiced());
    }

    /**
     * @return void
     */
    public function test_hasBeenInvoiced_returns_false_when_invoice_document_is_not_found()
    {
        $this->order
            ->method('findInvoiceDocument')
            ->willReturn(null);

        $this->assertFalse($this->sut->hasBeenInvoiced());
    }

    /**
     * @return void
     */
    public function test_hasBeenRefunded_returns_true_when_cancellation_document_is_found()
    {
        $this->order
            ->method('findCreditDocument')
            ->willReturn($this->createMock(Document::class));

        $this->assertTrue($this->sut->hasBeenRefunded());
    }

    /**
     * @return void
     */
    public function test_hasBeenRefunded_returns_false_when_cancellation_document_is_not_found()
    {
        $this->order
            ->method('findCreditDocument')
            ->willReturn(null);

        $this->assertFalse($this->sut->hasBeenRefunded());
    }

    /**
     * @return void
     */
    public function test_hasShippingReported_returns_true_when_attribute_is_true()
    {
        $this->orderAttributes->method('getAxytosKaufAufRechnungHasShippingReported')->willReturn(true);

        $this->order->method('getAttributes')->willReturn($this->orderAttributes);

        $this->assertTrue($this->sut->hasShippingReported());
    }

    /**
     * @return void
     */
    public function test_hasShippingReported_returns_false_when_attribute_is_false()
    {
        $this->orderAttributes->method('getAxytosKaufAufRechnungHasShippingReported')->willReturn(false);

        $this->order->method('getAttributes')->willReturn($this->orderAttributes);

        $this->assertFalse($this->sut->hasShippingReported());
    }

    /**
     * @return void
     */
    public function test_hasBeenShipped_returns_true_when_cancellation_document_is_found()
    {
        $this->order->method('findDeliveryNoteDocument')->willReturn($this->createMock(Document::class));

        $this->assertTrue($this->sut->hasBeenShipped());
    }

    /**
     * @return void
     */
    public function test_hasBeenShipped_returns_false_when_cancellation_document_is_not_found()
    {
        $this->order->method('findDeliveryNoteDocument')->willReturn(null);

        $this->assertFalse($this->sut->hasBeenShipped());
    }

    /**
     * @return void
     */
    public function test_saveHasShippingReported_sets_attribute_to_true()
    {
        $this->orderAttributes->expects($this->once())->method('setAxytosKaufAufRechnungHasShippingReported')->willReturn(false);

        $this->order->method('getAttributes')->willReturn($this->orderAttributes);

        $this->sut->saveHasShippingReported();
    }

    /**
     * @return void
     */
    public function test_saveHasShippingReported_persists_attribute()
    {
        $this->orderAttributes->expects($this->once())->method('persist');

        $this->order->method('getAttributes')->willReturn($this->orderAttributes);

        $this->sut->saveHasShippingReported();
    }

    /**
     * @return void
     */
    public function test_hasNewTrackingInformation_returnsTrueIfTrackingCodesAreDifferent()
    {
        $this->orderAttributes->method('getAxytosKaufAufRechnungReportedTrackingCode')->willReturn('old-code');

        $this->order->method('getAttributes')->willReturn($this->orderAttributes);

        $this->order
            ->method('getTrackingCode')
            ->willReturn('new-code');

        $result = $this->sut->hasNewTrackingInformation();

        $this->assertTrue($result);
    }

    /**
     * @return void
     */
    public function test_hasNewTrackingInformation_returnsFalseIfTrackingCodesAreEqual()
    {
        $this->orderAttributes->method('getAxytosKaufAufRechnungReportedTrackingCode')->willReturn('old-code');

        $this->order->method('getAttributes')->willReturn($this->orderAttributes);

        $this->order
            ->method('getTrackingCode')
            ->willReturn('old-code');

        $result = $this->sut->hasNewTrackingInformation();

        $this->assertFalse($result);
    }

    /**
     * @return void
     */
    public function test_saveNewTrackingInformation_updatesOrderWithNewTrackingCode()
    {
        $this->order->method('getAttributes')->willReturn($this->orderAttributes);

        $newTrackingCode = 'new-code';
        $this->order
            ->method('getTrackingCode')
            ->willReturn($newTrackingCode);
        $this->orderAttributes
            ->expects($this->once())
            ->method('setAxytosKaufAufRechnungReportedTrackingCode')
            ->with($newTrackingCode);
        $this->orderAttributes
            ->expects($this->once())
            ->method('persist');

        $this->sut->saveNewTrackingInformation();
    }

    /**
     * @return void
     */
    public function test_hasBasketUpdates_returnsTrueIfHashesAreDifferent()
    {
        /** @var BasketInterface&MockObject */
        $basket = $this->createMock(BasketInterface::class);
        $this->basketFactory
            ->method('create')
            ->with($this->order)
            ->willReturn($basket);
        $this->hashCalculator
            ->method('calculateBasketHash')
            ->with($basket)
            ->willReturn('new-hash');
        $this->orderAttributes
            ->method('getAxytosKaufAufRechnungOrderBasketHash')
            ->willReturn('old-hash');

        $this->assertTrue($this->sut->hasBasketUpdates());
    }

    /**
     * @return void
     */
    public function test_hasBasketUpdates_returnsFalseIfHashesAreIdentical()
    {
        /** @var BasketInterface&MockObject */
        $basket = $this->createMock(BasketInterface::class);
        $this->basketFactory
            ->method('create')
            ->with($this->order)
            ->willReturn($basket);
        $this->hashCalculator
            ->method('calculateBasketHash')
            ->with($basket)
            ->willReturn('old-hash');
        $this->orderAttributes
            ->method('getAxytosKaufAufRechnungOrderBasketHash')
            ->willReturn('old-hash');

        $this->assertFalse($this->sut->hasBasketUpdates());
    }

    /**
     * @return void
     */
    public function test_saveBasketUpdatesReported_storesNewHashWithOrder()
    {
        /** @var BasketInterface&MockObject */
        $basket = $this->createMock(BasketInterface::class);
        $this->basketFactory
            ->method('create')
            ->with($this->order)
            ->willReturn($basket);
        $this->hashCalculator
            ->method('calculateBasketHash')
            ->with($basket)
            ->willReturn('new-hash');
        $this->orderAttributes
            ->expects($this->once())
            ->method('setAxytosKaufAufRechnungOrderBasketHash');
        $this->orderAttributes
            ->expects($this->once())
            ->method('persist');

        $this->sut->saveBasketUpdatesReported();
    }

    /**
     * @return void
     */
    public function test_setOrderStatusPaid_updatesThePaymentStatusToCompletelyPaid()
    {
        $this->order
            ->expects($this->once())
            ->method('savePaymentStatus')
            ->with(Status::PAYMENT_STATE_COMPLETELY_PAID);

        $this->sut->saveHasBeenPaid();
    }
}
