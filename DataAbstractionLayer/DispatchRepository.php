<?php

namespace AxytosKaufAufRechnungShopware5\DataAbstractionLayer;

use Shopware\Components\Model\ModelManager;
use Shopware\Models\Dispatch\Dispatch;

class DispatchRepository
{
    /**
     * @return array<\Shopware\Models\Dispatch\Dispatch>
     */
    public function findAll()
    {
        /** @var ModelManager */
        $modelManager = Shopware()->Container()->get('models');

        /** @var \Shopware\Models\Dispatch\Repository */
        $dispatchRepository = $modelManager->getRepository(Dispatch::class);

        return $dispatchRepository->findAll();
    }
}
