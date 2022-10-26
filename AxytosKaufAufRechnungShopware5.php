<?php

namespace AxytosKaufAufRechnungShopware5;

use AxytosKaufAufRechnungShopware5\Core\OrderAttributesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Shopware\Components\Model\ModelManager;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Plugin\Context\DeactivateContext;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Models\Payment\Payment;
use AxytosKaufAufRechnungShopware5\Paymentmethod\PaymentMethodOptions;
use Shopware\Bundle\AttributeBundle\Service\CrudServiceInterface;
use Shopware\Bundle\AttributeBundle\Service\DataLoaderInterface;
use Shopware\Bundle\AttributeBundle\Service\DataPersisterInterface;
use Shopware\Components\Plugin\PaymentInstaller;

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

class AxytosKaufAufRechnungShopware5 extends Plugin
{
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PreDispatch_Frontend' => ['onFrontend',-100],
            'Enlight_Controller_Action_PreDispatch_Widgets' => ['onFrontend',-100]
        ];
    }

    public function install(InstallContext $context): void
    {
        /** @var PaymentInstaller */
        $installer = $this->container->get('shopware.plugin_payment_installer');
        $installer->createOrUpdate($context->getPlugin()->getName(), PaymentMethodOptions::OPTIONS);


        /** @var CrudServiceInterface */
        $crudService = $this->container->get(CrudServiceInterface::class);

        /** @var DataLoaderInterface */
        $dataLoader = $this->container->get(DataLoaderInterface::class);

        /** @var DataPersisterInterface */
        $dataPersister = $this->container->get(DataPersisterInterface::class);

        $orderAttributesRepository = new OrderAttributesRepository($crudService, $dataLoader, $dataPersister);
        $orderAttributesRepository->install();

        $context->scheduleClearCache(InstallContext::CACHE_LIST_DEFAULT);
    }

    /**
     * @param UninstallContext $context
     */
    public function uninstall(UninstallContext $context): void
    {
        $this->setActiveFlag($context->getPlugin()->getPayments(), false);
        $context->scheduleClearCache(UninstallContext::CACHE_LIST_DEFAULT);
    }

    /**
     * @param DeactivateContext $context
     */
    public function deactivate(DeactivateContext $context): void
    {
        $this->setActiveFlag($context->getPlugin()->getPayments(), false);
        $context->scheduleClearCache(DeactivateContext::CACHE_LIST_DEFAULT);
    }

    /**
     * @param ActivateContext $context
     */
    public function activate(ActivateContext $context): void
    {
        $this->setActiveFlag($context->getPlugin()->getPayments(), true);
        $context->scheduleClearCache(ActivateContext::CACHE_LIST_DEFAULT);
    }

    /**
     * @param ArrayCollection<Payment> $payments
     * @param bool $active
     */
    private function setActiveFlag($payments, $active): void
    {
        /** @var ModelManager */
        $em = $this->container->get('models');

        foreach ($payments as $payment) {
            $payment->setActive($active);
        }
        $em->flush();
    }

    public function onFrontend(\Enlight_Event_EventArgs $args): void
    {
        // @phpstan-ignore-next-line
        $this->container->get('Template')->addTemplateDir(
            $this->getPath() . '/Resources/views/'
        );
    }
}
