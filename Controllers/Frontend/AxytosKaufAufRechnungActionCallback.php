<?php

use Axytos\ECommerce\Clients\Invoice\PluginConfigurationValidator;
use Axytos\KaufAufRechnung\Core\Abstractions\Model\Actions\ActionExecutorInterface;
use Axytos\KaufAufRechnung\Core\AxytosActionControllerTrait;
use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Logging\LoggerAdapterInterface;
use AxytosKaufAufRechnungShopware5\ErrorReporting\ErrorHandler;
use Shopware\Components\CSRFWhitelistAware;

/**
 * URL of this controll: http://localhost/AxytosKaufAufRechnungActionCallback/execute.
 *
 * For controller development see:
 * - https://developers.shopware.com/developers-guide/controller/
 */
class Shopware_Controllers_Frontend_AxytosKaufAufRechnungActionCallback extends Enlight_Controller_Action implements CSRFWhitelistAware
{
    use AxytosActionControllerTrait;

    /**
     * @var ErrorHandler
     */
    private $errorHandler;

    /**
     * @return void
     */
    public function preDispatch()
    {
        /** @phpstan-ignore-next-line */
        $this->errorHandler = Shopware()->Container()->get(ErrorHandler::class);
        /** @phpstan-ignore-next-line */
        $this->pluginConfigurationValidator = Shopware()->Container()->get(PluginConfigurationValidator::class);
        /** @phpstan-ignore-next-line */
        $this->actionExecutor = Shopware()->Container()->get(ActionExecutorInterface::class);
        /** @phpstan-ignore-next-line */
        $this->logger = Shopware()->Container()->get(LoggerAdapterInterface::class);
    }

    /**
     * @return array<int,string>
     */
    public function getWhitelistedCSRFActions()
    {
        return [
            'execute',
        ];
    }

    /**
     * @return void
     */
    public function executeAction()
    {
        try {
            $this->executeActionInternal();
        } catch (Throwable $th) {
            $this->setErrorResult();
            $this->errorHandler->handle($th);
        } catch (Exception $th) { // @phpstan-ignore-line | php5.6 compatibility
            $this->setErrorResult();
            $this->errorHandler->handle($th);
        }
    }

    /**
     * @return string
     */
    protected function getRequestBody()
    {
        $rawBody = $this->request->getRawBody();
        if (!is_string($rawBody)) {
            return '';
        }

        return $rawBody;
    }

    /**
     * @return string
     */
    protected function getRequestMethod()
    {
        return strtoupper($this->request->getMethod());
    }

    /**
     * @param string $responseBody
     * @param int    $statusCode
     *
     * @return void
     */
    protected function setResponseBody($responseBody, $statusCode)
    {
        $this->setResponseStatusCode($statusCode);
        $this->response->setHeader('Content-Type', 'application/json');
        $this->response->setBody($responseBody);
    }

    /**
     * @param int $statusCode
     *
     * @return void
     */
    private function setResponseStatusCode($statusCode)
    {
        if (!method_exists($this->response, 'setStatusCode')) {
            return;
        }
        $this->response->setStatusCode($statusCode);
    }
}
