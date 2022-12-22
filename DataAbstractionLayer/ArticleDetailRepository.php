<?php

namespace AxytosKaufAufRechnungShopware5\DataAbstractionLayer;

use Doctrine\Persistence\ObjectRepository;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Article\Detail as ArticleDetail;
use Shopware\Models\Order\Detail as OrderDetail;

class ArticleDetailRepository
{
    /**
     * @param OrderDetail $orderDetail
     * @return float
     */
    public function findArticleWeightForOrderDetail($orderDetail)
    {
        $articleDetail = $this->findOneForOrderDetail($orderDetail);

        if (is_null($articleDetail)) {
            return 0;
        }

        return floatval($articleDetail->getWeight());
    }

    /**
     * @return ArticleDetail|null
     */
    private function findOneForOrderDetail(OrderDetail $orderDetail)
    {
        /** @var ModelManager */
        $modelManager = Shopware()->Container()->get(ModelManager::class);

        /** @var ObjectRepository<ArticleDetail> */
        $articleDetailRepository = $modelManager->getRepository(ArticleDetail::class);

        $articleId = $orderDetail->getArticleId();
        $articleNumber = $orderDetail->getArticleNumber();

        /** @var ?ArticleDetail */
        $articleDetail = $articleDetailRepository->findOneBy([
            'articleId' => $articleId,
            'number' => $articleNumber
        ]);

        return $articleDetail;
    }
}
