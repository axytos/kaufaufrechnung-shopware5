<?php

namespace AxytosKaufAufRechnungShopware5;

use AxytosKaufAufRechnungShopware5\Configuration\PluginConfiguration;
use AxytosKaufAufRechnungShopware5\DataAbstractionLayer\OrderAttributesRepository;
use AxytosKaufAufRechnungShopware5\Paymentmethod\PaymentMethodOptions;
use Doctrine\Common\Collections\ArrayCollection;
use Shopware\Components\Model\ModelManager;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;
use Shopware\Components\Plugin\PaymentInstaller;
use Shopware\Models\Payment\Payment;
use Symfony\Component\DependencyInjection\ContainerInterface;

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

/**
 * @phpstan-property ContainerInterface $container
 */
class AxytosKaufAufRechnungShopware5 extends Plugin
{
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PreDispatch_Frontend' => ['onFrontend', -100],
            'Enlight_Controller_Action_PreDispatch_Widgets' => ['onFrontend', -100],
            'Enlight_Controller_Action_PostDispatchSecure_Frontend' => 'onPostDispatch',
            'Enlight_Controller_Action_PostDispatchSecure_Widgets' => 'onPostDispatch',
        ];
    }

    /**
     * @return void
     */
    public function install(InstallContext $context)
    {
        /** @var ContainerInterface */
        $container = $this->container;
        /** @var PaymentInstaller */
        $installer = $container->get('shopware.plugin_payment_installer');
        $installer->createOrUpdate($context->getPlugin()->getName(), PaymentMethodOptions::OPTIONS);

        $orderAttributesRepository = OrderAttributesRepository::create();
        $orderAttributesRepository->install();

        $context->scheduleClearCache(InstallContext::CACHE_LIST_DEFAULT);
    }

    /**
     * @return void
     */
    public function update(UpdateContext $context)
    {
        $orderAttributesRepository = OrderAttributesRepository::create();
        $orderAttributesRepository->update();

        $context->scheduleClearCache(InstallContext::CACHE_LIST_DEFAULT);
    }

    /**
     * @return void
     */
    public function uninstall(UninstallContext $context)
    {
        $this->setActiveFlag($context->getPlugin()->getPayments(), false);
        $context->scheduleClearCache(UninstallContext::CACHE_LIST_DEFAULT);
    }

    /**
     * @return void
     */
    public function deactivate(DeactivateContext $context)
    {
        $this->setActiveFlag($context->getPlugin()->getPayments(), false);
        $context->scheduleClearCache(DeactivateContext::CACHE_LIST_DEFAULT);
    }

    /**
     * @return void
     */
    public function activate(ActivateContext $context)
    {
        $this->setActiveFlag($context->getPlugin()->getPayments(), true);
        $context->scheduleClearCache(ActivateContext::CACHE_LIST_DEFAULT);
    }

    /**
     * @param ArrayCollection<int,Payment> $payments
     * @param bool                         $active
     *
     * @return void
     */
    private function setActiveFlag($payments, $active)
    {
        /** @var ContainerInterface */
        $container = $this->container;
        /** @var ModelManager */
        $em = $container->get('models');

        foreach ($payments as $payment) {
            $payment->setActive($active);
        }
        $em->flush();
    }

    /**
     * @return void
     */
    public function onFrontend(\Enlight_Event_EventArgs $args)
    {
        /** @var ContainerInterface */
        $container = $this->container;
        // @phpstan-ignore-next-line
        $container->get('Template')->addTemplateDir(
            $this->getPath() . '/Resources/views/'
        );
    }

    /**
     * @return void
     */
    public function onPostDispatch(\Enlight_Controller_ActionEventArgs $args)
    {
        /** @var ContainerInterface */
        $container = $this->container;
        $subject = $args->getSubject();
        if ($subject instanceof \Shopware_Controllers_Frontend_Checkout) {
            /** @var PluginConfiguration */
            $configuration = $container->get(PluginConfiguration::class);
            $errorMessage = $configuration->getCustomErrorMessage();
            $subject->View()->assign('sAxytosErrorMessage', $errorMessage);
        }
    }
}
