<?php

use Axytos\ECommerce\Clients\CredentialValidation\CredentialValidationClientInterface;
use AxytosKaufAufRechnungShopware5\ErrorReporting\ErrorHandler;

class Shopware_Controllers_Backend_AxytosKaufAufRechnungApiTestController extends Shopware_Controllers_Backend_ExtJs
{
    /**
     * @return void
     */
    public function validateAction()
    {
        try {
            /** @var CredentialValidationClientInterface */
            $credentialValidationClient = Shopware()->Container()->get(CredentialValidationClientInterface::class);
            $isValid = $credentialValidationClient->validateApiKey();

            $this->View()->assign('title', 'API-Verbindung');

            if ($isValid) {
                $this->View()->assign('text', 'API-Verbindung wurde erfolgreich getestet.');
            } else {
                $this->View()->assign('text', 'API-Verbindung konnte nicht hergestellt werden. Bitte prüfe die Konfiguration von API Host und API Key.');
            }
        } catch (Throwable $th) {
            /** @var ErrorHandler */
            $errorHandler = Shopware()->Container()->get(ErrorHandler::class);
            $errorHandler->handle($th);
            $this->View()->assign('text', 'API-Verbindung konnte nicht hergestellt werden. Bitte prüfe die Konfiguration von API Host und API Key.');
        } catch (Exception $th) { // @phpstan-ignore-line | php5.6 compatibility
            /** @var ErrorHandler */
            $errorHandler = Shopware()->Container()->get(ErrorHandler::class);
            $errorHandler->handle($th);
            $this->View()->assign('text', 'API-Verbindung konnte nicht hergestellt werden. Bitte prüfe die Konfiguration von API Host und API Key.');
        }
    }
}
