<?php

use Axytos\ECommerce\Clients\Invoice\PluginConfigurationValidator;
use Shopware\Components\CSRFWhitelistAware;
use Shopware\Components\DependencyInjection\Container;
use Shopware\Models\Order\Order;
use Axytos\KaufAufRechnung\Core\Abstractions\Model\AxytosOrderCheckoutAction;
use Axytos\KaufAufRechnung\Core\Abstractions\Model\AxytosOrderEvents;
use Axytos\KaufAufRechnung\Core\Model\AxytosOrderFactory;
use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\OrderFactory;
use AxytosKaufAufRechnungShopware5\Adapter\PluginOrderFactory;
use AxytosKaufAufRechnungShopware5\Core\OrderStateMachine;
use AxytosKaufAufRechnungShopware5\ErrorReporting\ErrorHandler;
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
     * @var \AxytosKaufAufRechnungShopware5\ErrorReporting\ErrorHandler
     */
    private $errorHandler;

    /**
     * @var \AxytosKaufAufRechnungShopware5\Core\OrderStateMachine
     */
    private $orderStateMachine;

    /**
     * @var \AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\OrderFactory
     */
    private $unifiedOrderFactory;

    /**
     * @var \AxytosKaufAufRechnungShopware5\Adapter\PluginOrderFactory
     */
    private $pluginOrderFactory;

    /**
     * @var \Axytos\KaufAufRechnung\Core\Model\AxytosOrderFactory
     */
    private $axytosOrderFactory;



    /**
     * @var \AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order
     */
    private $orderHandle;

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
        $this->errorHandler = $container->get(ErrorHandler::class);
        /** @phpstan-ignore-next-line */
        $this->orderStateMachine = $container->get(OrderStateMachine::class);
        /** @phpstan-ignore-next-line */
        $this->unifiedOrderFactory = $container->get(OrderFactory::class);
        /** @phpstan-ignore-next-line */
        $this->pluginOrderFactory = $container->get(PluginOrderFactory::class);
        /** @phpstan-ignore-next-line */
        $this->axytosOrderFactory = $container->get(AxytosOrderFactory::class);
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

            $this->executeAxytosKaufAufRechnung();
        } catch (\Throwable $th) {
            $this->errorHandler->handle($th);
            $this->redirectToChangePaymentMethod();
        } catch (\Exception $th) { // @phpstan-ignore-line | php5.6 compatibility
            $this->errorHandler->handle($th);
            $this->redirectToChangePaymentMethod();
        }
    }

    /**
     * @return void
     */
    private function executeAxytosKaufAufRechnung()
    {
        $temporaryOrder = $this->loadTemporaryOrder();
        $this->orderHandle = $this->unifiedOrderFactory->create($temporaryOrder);
        $pluginOrder = $this->pluginOrderFactory->create($this->orderHandle);
        $axytosOrder = $this->axytosOrderFactory->create($pluginOrder);

        $axytosOrder->subscribeEventListener(AxytosOrderEvents::CHECKOUT_AFTER_ACCEPTED, [$this, 'afterAxytosAccepted']);
        $axytosOrder->subscribeEventListener(AxytosOrderEvents::CHECKOUT_AFTER_CONFIRMED, [$this, 'afterAxytosConfirmed']);
        $axytosOrder->checkout();

        if ($axytosOrder->getOrderCheckoutAction() === AxytosOrderCheckoutAction::CHANGE_PAYMENT_METHOD) {
            $this->redirectToChangePaymentMethod();
            return;
        }

        $this->redirectToCompleteCheckout();
    }

    /**
     * @return void
     */
    public function afterAxytosAccepted()
    {
        $oldAttributes = $this->orderHandle->getAttributes();

        $actualOrder = $this->saveActualOrder();
        $this->orderHandle->setShopwareOrderObject($actualOrder);

        $newAttributes = $this->orderHandle->getAttributes();
        $newAttributes->setAxytosKaufAufRechnungOrderState($oldAttributes->getAxytosKaufAufRechnungOrderState());
        $newAttributes->setAxytosKaufAufRechnungOrderStateData($oldAttributes->getAxytosKaufAufRechnungOrderStateData());
        $newAttributes->setAxytosKaufAufRechnungPrecheckResponse($oldAttributes->getAxytosKaufAufRechnungPrecheckResponse());
        $newAttributes->setAxytosKaufAufRechnungOrderBasketHash($oldAttributes->getAxytosKaufAufRechnungOrderBasketHash());
        $newAttributes->persist();
    }

    /**
     * @return void
     */
    public function afterAxytosConfirmed()
    {
        $actualOrder = $this->orderHandle->getShopwareOrderObject();
        $this->orderStateMachine->setConfiguredAfterCheckoutOrderStatus($actualOrder);
        $this->orderStateMachine->setConfiguredAfterCheckoutPaymentStatus($actualOrder);
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
    private function redirectToCompleteCheckout()
    {
        $this->redirect([
            'controller' => 'checkout',
            'action' => 'finish',
            'sUniqueId' => $this->orderHandle->getTemporaryId(), // generated payment unique id is stored as temporary id for actual orders
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

        /** @var array<string,array<string,mixed>> */
        $user = $this->getUser();

        /** @var array<string,mixed> */
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

        /** @var float */
        $invoiceShipping = $basket['sShippingcostsWithTax'];
        $order->setInvoiceShipping($invoiceShipping);
        /** @var float */
        $invoiceShippingNet = $basket['sShippingcosts'];
        $order->setInvoiceShippingNet($invoiceShippingNet);

        // shopware 5.3 compatibility
        if (method_exists($order, 'setInvoiceShippingTaxRate')) {
            /** @var float|null */
            $invoiceShippingTaxRate = $basket['sShippingcostsTax'];
            $order->setInvoiceShippingTaxRate($invoiceShippingTaxRate);
        }

        return $order;
    }

    /**
     * @return array<int,string>
     */
    public function getWhitelistedCSRFActions()
    {
        return [
            'index'
        ];
    }
}
