<?php

use Axytos\ECommerce\Clients\Invoice\PluginConfigurationValidator;
use Axytos\KaufAufRechnung\Core\Abstractions\Model\Actions\ActionExecutorInterface;
use Axytos\KaufAufRechnung\Core\Abstractions\Model\Actions\ActionResultInterface;
use Axytos\KaufAufRechnung\Core\Model\Actions\Results\FatalErrorResult;
use Axytos\KaufAufRechnung\Core\Model\Actions\Results\InvalidDataResult;
use Axytos\KaufAufRechnung\Core\Model\Actions\Results\InvalidMethodResult;
use Axytos\KaufAufRechnung\Core\Model\Actions\Results\PluginNotConfiguredResult;
use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Logging\LoggerAdapterInterface;
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
     * @var \Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Logging\LoggerAdapterInterface
     */
    private $logger;

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
            'execute'
        ];
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
     * @return void
     */
    private function processAction()
    {
        $rawBody = $this->getRequestBody();

        if ($rawBody === '') {
            $this->logger->error('Process Action Request: HTTP request body empty');
            $this->setResult(new InvalidDataResult('HTTP request body empty'));
            return;
        }

        $decodedBody = json_decode($rawBody, true);
        if (!is_array($decodedBody)) {
            $this->logger->error('Process Action Request: HTTP request body is not a json object');
            $this->setResult(new InvalidDataResult('HTTP request body is not a json object'));
            return;
        }

        $loggableRequestBody = $decodedBody;
        if (array_key_exists('clientSecret', $loggableRequestBody)) {
            $loggableRequestBody['clientSecret'] = '****';
        }
        $encodedLoggableRequestBody = json_encode($loggableRequestBody);
        $this->logger->info("Process Action Request: request body '$encodedLoggableRequestBody'");

        $clientSecret = array_key_exists('clientSecret', $decodedBody) ? $decodedBody['clientSecret'] : null;
        if (!is_string($clientSecret)) {
            $this->logger->error("Process Action Request: Required string property 'clientSecret' is missing");
            $this->setResult(new InvalidDataResult('Required string property', 'clientSecret'));
            return;
        }

        $action = array_key_exists('action', $decodedBody) ?  $decodedBody['action'] : null;
        if (!is_string($action)) {
            $this->logger->error("Process Action Request: Required string property 'action' is missing");
            $this->setResult(new InvalidDataResult('Required string property', 'action'));
            return;
        }

        $params = array_key_exists('params', $decodedBody) ? $decodedBody['params'] : null;
        if (!is_null($params) && !is_array($params)) {
            $this->logger->error("Process Action Request: Optional object property 'params' ist not an array");
            $this->setResult(new InvalidDataResult('Optional object property', 'params'));
            return;
        }

        $result = $this->actionExecutor->executeAction($clientSecret, $action, $params);
        $this->setResult($result);
    }

    /**
     * @return string
     */
    private function getRequestBody()
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
    private function getRequestMethod()
    {
        return strtoupper($this->request->getMethod());
    }

    /**
     * @return bool
     */
    private function isNotPostRequest()
    {
        return $this->getRequestMethod() !== 'POST';
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
