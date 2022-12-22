<?php

use Axytos\ECommerce\Clients\Invoice\InvoiceClientInterface;
use Axytos\ECommerce\Clients\Invoice\InvoiceOrderContextInterface;
use Axytos\ECommerce\Clients\Invoice\PluginConfigurationValidator;
use AxytosKaufAufRechnungShopware5\Core\InvoiceOrderContextFactory;
use Shopware\Components\CSRFWhitelistAware;
use Shopware\Components\DependencyInjection\Container;
use Shopware\Models\Order\Order;
use Axytos\ECommerce\Clients\Invoice\ShopActions;
use AxytosKaufAufRechnungShopware5\Core\OrderCheckProcessStateMachine;
use AxytosKaufAufRechnungShopware5\Core\OrderStateMachine;
use AxytosKaufAufRechnungShopware5\ErrorReporting\ErrorHandler;
use Shopware\Bundle\CartBundle\CheckoutKey;
use Shopware\Models\Customer\Address;
use Shopware\Models\Order\Billing;
use Shopware\Models\Order\Shipping;

class Shopware_Controllers_Frontend_AxytosKaufAufRechnungController extends \Shopware_Controllers_Frontend_Payment implements CSRFWhitelistAware
{
    /**
     * @var \Axytos\ECommerce\Clients\Invoice\PluginConfigurationValidator
     */
    private $pluginConfigurationValidator;
    /**
     * @var \Axytos\ECommerce\Clients\Invoice\InvoiceClientInterface
     */
    private $invoiceClient;
    /**
     * @var \AxytosKaufAufRechnungShopware5\ErrorReporting\ErrorHandler
     */
    private $errorHandler;
    /**
     * @var \AxytosKaufAufRechnungShopware5\Core\InvoiceOrderContextFactory
     */
    private $invoiceOrderContextFactory;
    /**
     * @var \AxytosKaufAufRechnungShopware5\Core\OrderStateMachine
     */
    private $orderStateMachine;
    /**
     * @var \AxytosKaufAufRechnungShopware5\Core\OrderCheckProcessStateMachine
     */
    private $orderCheckProcessStateMachine;

    /**
     * @return void
     */
    public function setContainer(Container $container = null)
    {
        if (is_null($container)) {
            return;
        }

        parent::setContainer($container);

        /** @phpstan-ignore-next-line */
        $this->pluginConfigurationValidator = $container->get(PluginConfigurationValidator::class);
        /** @phpstan-ignore-next-line */
        $this->invoiceClient = $container->get(InvoiceClientInterface::class);
        /** @phpstan-ignore-next-line */
        $this->errorHandler = $container->get(ErrorHandler::class);
        /** @phpstan-ignore-next-line */
        $this->invoiceOrderContextFactory = $container->get(InvoiceOrderContextFactory::class);
        /** @phpstan-ignore-next-line */
        $this->orderStateMachine = $container->get(OrderStateMachine::class);
        /** @phpstan-ignore-next-line */
        $this->orderCheckProcessStateMachine = $container->get(OrderCheckProcessStateMachine::class);
    }

    /**
     * @return void
     */
    public function indexAction()
    {
        try {
            if ($this->pluginConfigurationValidator->isInvalid()) {
                return;
            }

            $this->executeAxytosInvoice();
        } catch (\Throwable $th) {
            $this->errorHandler->handle($th);
        } catch (\Exception $th) { // @phpstan-ignore-line | php5.6 compatibility
            $this->errorHandler->handle($th);
        }
    }

    /**
     * @return mixed[]
     */
    public function getWhitelistedCSRFActions()
    {
        return [
            'index'
        ];
    }

    /**
     * @return void
     */
    private function executeAxytosInvoice()
    {
        try {
            $temporaryOrder = $this->loadTemporaryOrder();

            $precheckOrderContext = $this->createPrecheckOrderContext($temporaryOrder);
            $shopAction = $this->invoiceClient->precheck($precheckOrderContext);

            if ($shopAction === ShopActions::CHANGE_PAYMENT_METHOD) {
                $this->redirectToChangePaymentMethod();
                return;
            }

            $preCheckResponseData = $precheckOrderContext->getPreCheckResponseData();

            $actualOrder = $this->saveActualOrder();

            $this->orderStateMachine->setPaymentReview($actualOrder);
            $this->orderCheckProcessStateMachine->setChecked($actualOrder);

            $confirmOrderContext = $this->createConfirmOrderContext($actualOrder, $preCheckResponseData);

            $this->invoiceClient->confirmOrder($confirmOrderContext);
            $this->orderCheckProcessStateMachine->setConfirmed($actualOrder);
            $this->orderStateMachine->setConfiguredAfterCheckoutOrderStatus($actualOrder);
            $this->orderStateMachine->setConfiguredAfterCheckoutPaymentStatus($actualOrder);

            $this->redirectToFinishCheckout($actualOrder);
        } catch (\Throwable $th) {
            if (isset($actualOrder)) {
                $this->orderCheckProcessStateMachine->setFailed($actualOrder);
            }
            $this->errorHandler->handle($th);
            $this->redirectToChangePaymentMethod();
        } catch (\Exception $th) { // @phpstan-ignore-line | php5.6 compatibility
            if (isset($actualOrder)) {
                $this->orderCheckProcessStateMachine->setFailed($actualOrder);
            }
            $this->errorHandler->handle($th);
            $this->redirectToChangePaymentMethod();
        }
    }

    /**
     * @return \Axytos\ECommerce\Clients\Invoice\InvoiceOrderContextInterface
     */
    private function createPrecheckOrderContext(Order $temporaryOrder)
    {
        return $this->invoiceOrderContextFactory->create($temporaryOrder);
    }

    /**
     * @return \Axytos\ECommerce\Clients\Invoice\InvoiceOrderContextInterface
     */
    private function createConfirmOrderContext(Order $actualOrder, array $preCheckResponseData)
    {
        $invoiceOrderContext = $this->invoiceOrderContextFactory->create($actualOrder);
        $invoiceOrderContext->setPreCheckResponseData($preCheckResponseData);
        return $invoiceOrderContext;
    }

    /**
     * @return void
     */
    private function redirectToChangePaymentMethod()
    {
        $this->redirect([
            'controller' => 'checkout',
            'action' => 'shippingPayment',
            'axytos' => "error",
            'forceSecure' => true
        ]);
    }

    /**
     * @return void
     */
    private function redirectToFinishCheckout(Order $order)
    {
        $this->redirect([
            'controller' => 'checkout',
            'action' => 'finish',
            'sUniqueId' => $order->getTemporaryId(), // generated payment unique id is stored as temporary id for actual orders
            'forceSecure' => true
        ]);
    }

    /**
     * @return \Shopware\Models\Order\Order
     */
    private function saveActualOrder()
    {
        $orderRepository = $this->getModelManager()->getRepository(Order::class);

        $transactionId = $this->createPaymentUniqueId();
        $paymentUniqueId = $this->createPaymentUniqueId();
        $orderNumber = $this->saveOrder($transactionId, $paymentUniqueId);

        /** @var Order */
        return $orderRepository->findOneBy(['number' => $orderNumber]);
    }

    /**
     * @return \Shopware\Models\Order\Order
     */
    private function loadTemporaryOrder()
    {
        $modelManager = $this->getModelManager();

        $orderRepository = $modelManager->getRepository(Order::class);
        $addressRepository = $modelManager->getRepository(Address::class);

        $sessionId = Shopware()->Session()->get('sessionId');

        /** @var array */
        $user = $this->getUser();

        /** @var array */
        $basket = $this->getBasket();

        $billingAddressId = $user['billingaddress']['id'];
        $shippingAddressId = $user['shippingaddress']['id'];

        /** @var Order */
        $order = $orderRepository->findOneBy(['temporaryId' => $sessionId]);

        /** @var Address */
        $billingAddress = $addressRepository->findOneBy(['id' => $billingAddressId]);
        $billing = new Billing();
        $billing->fromAddress($billingAddress);
        $order->setBilling($billing);

        /** @var Address */
        $shippingAddress = $addressRepository->findOneBy(['id' => $shippingAddressId]);
        $shipping = new Shipping();
        $shipping->fromAddress($shippingAddress);
        $order->setShipping($shipping);

        $order->setInvoiceShipping($basket[CheckoutKey::SHIPPING_COSTS_WITH_TAX]);
        $order->setInvoiceShippingNet($basket[CheckoutKey::SHIPPING_COSTS]);
        $order->setInvoiceShippingTaxRate($basket[CheckoutKey::SHIPPING_COSTS_TAX]);
        return $order;
    }
}
