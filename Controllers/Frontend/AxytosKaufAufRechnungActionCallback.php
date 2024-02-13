<?php

use Axytos\ECommerce\Clients\Invoice\PluginConfigurationValidator;
use Axytos\KaufAufRechnung\Core\Abstractions\Model\Actions\ActionExecutorInterface;
use Axytos\KaufAufRechnung\Core\Abstractions\Model\Actions\ActionResultInterface;
use Axytos\KaufAufRechnung\Core\Model\Actions\Results\FatalErrorResult;
use Axytos\KaufAufRechnung\Core\Model\Actions\Results\InvalidDataResult;
use Axytos\KaufAufRechnung\Core\Model\Actions\Results\InvalidMethodResult;
use Axytos\KaufAufRechnung\Core\Model\Actions\Results\PluginNotConfiguredResult;
use AxytosKaufAufRechnungShopware5\Controllers\AxytosControllerTrait;
use AxytosKaufAufRechnungShopware5\ErrorReporting\ErrorHandler;
use Shopware\Components\CSRFWhitelistAware;

class Shopware_Controllers_Frontend_AxytosKaufAufRechnungActionCallback extends Enlight_Controller_Action implements CSRFWhitelistAware
{
    use AxytosControllerTrait;

    /**
     * @var \AxytosKaufAufRechnungShopware5\ErrorReporting\ErrorHandler
     */
    private $errorHandler;

    /**
     * @var \Axytos\ECommerce\Clients\Invoice\PluginConfigurationValidator
     */
    private $pluginConfigurationValidator;

    /**
     * @var \Axytos\KaufAufRechnung\Core\Abstractions\Model\Actions\ActionExecutorInterface
     */
    private $actionExecutor;

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
    }

    /**
     * @return void
     */
    public function executeAction()
    {
        try {
            if ($this->isNotPostRequest()) {
                $this->setResult(new InvalidMethodResult($this->request->getMethod()));
                return;
            }

            if ($this->pluginConfigurationValidator->isInvalid()) {
                $this->setResult(new PluginNotConfiguredResult());
                return;
            }

            $this->processAction();
        } catch (\Throwable $th) {
            $this->setResult(new FatalErrorResult());
            $this->errorHandler->handle($th);
        } catch (\Exception $th) { // @phpstan-ignore-line | php5.6 compatibility
            $this->setResult(new FatalErrorResult());
            $this->errorHandler->handle($th);
        }
    }

    /**
     * @return array<int,string>
     */
    public function getWhitelistedCSRFActions()
    {
        return [
            'execute'
        ];
    }

    /**
     * @return void
     */
    private function processAction()
    {
        $rawBody = $this->request->getRawBody();
        if ($rawBody === false) {
            $this->setResult(new InvalidDataResult('HTTP request body empty'));
            return;
        }

        $decodedBody = json_decode($rawBody, true);
        if (!is_array($decodedBody)) {
            $this->setResult(new InvalidDataResult('HTTP request body is not a json object'));
            return;
        }

        $clientSecret = $decodedBody['clientSecret'];
        if (!is_string($clientSecret)) {
            $this->setResult(new InvalidDataResult('Required string property', 'clientSecret'));
            return;
        }

        $action = $decodedBody['action'];
        if (!is_string($action)) {
            $this->setResult(new InvalidDataResult('Required string property', 'action'));
            return;
        }

        $params = $decodedBody['params'];
        if (!is_null($params) && !is_array($params)) {
            $this->setResult(new InvalidDataResult('Optional object property', 'params'));
            return;
        }

        $result = $this->actionExecutor->executeAction($clientSecret, $action, $params);
        $this->setResult($result);
    }


    /**
     * @param ActionResultInterface $actionResult
     * @return void
     */
    private function setResult($actionResult)
    {
        $this->setResponseStatusCode($actionResult->getHttpStatusCode());
        $this->response->setBody(json_encode($actionResult));
    }
}
