<?php

namespace AxytosKaufAufRechnungShopware5\ErrorReporting;

use Axytos\ECommerce\Clients\ErrorReporting\ErrorReportingClientInterface;
use Throwable;

class ErrorHandler
{
    /**
     * @var \Axytos\ECommerce\Clients\ErrorReporting\ErrorReportingClientInterface
     */
    private $errorReportingClient;

    public function __construct(
        ErrorReportingClientInterface $errorReportingClient
    ) {
        $this->errorReportingClient = $errorReportingClient;
    }

    /**
     * @param \Throwable $throwable
     * @return void
     */
    public function handle($throwable)
    {
        $this->errorReportingClient->reportError($throwable);

        if ($this->isDebug()) {
            throw $throwable;
        }
    }

    /**
     * @return bool
     */
    private function isDebug()
    {
        try {
            $environment = getenv('SHOPWARE_ENV') ?: getenv('REDIRECT_SHOPWARE_ENV') ?: 'production';
            return $environment !== 'production';
        } catch (\Throwable $th) {
            return false;
        } catch (\Exception $th) { /** @phpstan-ignore-line because of php5 compatibility */
            return false;
        }
    }
}
