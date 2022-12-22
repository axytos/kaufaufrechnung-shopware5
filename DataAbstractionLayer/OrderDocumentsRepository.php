<?php

namespace AxytosKaufAufRechnungShopware5\DataAbstractionLayer;

use Shopware\Components\Model\ModelManager;
use Shopware\Models\Order\Document\Document;
use Shopware\Models\Order\Order;
use Exception;

class OrderDocumentsRepository
{
    /**
     * @param \Shopware\Models\Order\Order $order
     * @param int $documentType
     * @return \Shopware\Models\Order\Document\Document
     */
    public function findOrderDocumentWithType($order, $documentType)
    {
        /** @var ModelManager */
        $modelManager = Shopware()->Container()->get(ModelManager::class);

        /** @var \Shopware\Models\Order\Document\Repository */
        $repository = $modelManager->getRepository(Document::class);

        foreach ($order->getDocuments() as $document) {
            if ($document->getTypeId() === $documentType) {
                $id = $document->getId();

                /** @var Document */
                $document = $repository->find($id);

                $docID = $this->fetchDocID($document);

                $document->setDocumentId($docID);

                return $document;
            }
        }

        throw new Exception('Document not found');
    }

    /**
     * @return string
     */
    private function fetchDocID(Document $document)
    {
        /**
         * Fetch most recent docID because Shopware5 overwrites it
         * a) concurrently after document entry is created in the database
         * b) bypassing Doctrine ORM
         * see: engine/Shopware/Components/Document.php, line 805
         */
        $stmt = Shopware()->Db()->query('SELECT docID FROM s_order_documents WHERE id = ? LIMIT 1;', [
            $document->getId()
        ]);

        return strval($stmt->fetchColumn(0));
    }
}
