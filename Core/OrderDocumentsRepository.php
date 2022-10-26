<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\Core;

use Shopware\Components\Model\ModelManager;
use Shopware\Models\Order\Document\Document;
use Shopware\Models\Order\Order;
use Exception;

class OrderDocumentsRepository
{
    public function findOrderDocumentWithType(Order $order, int $documentType): Document
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

    private function fetchDocID(Document $document): string
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
