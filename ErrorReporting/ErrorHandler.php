<?php

namespace AxytosKaufAufRechnungShopware5\ErrorReporting;

use Axytos\ECommerce\Clients\ErrorReporting\ErrorReportingClientInterface;

class ErrorHandler
{
    /**
     * @var ErrorReportingClientInterface
     */
    private $errorReportingClient;

    public function __construct(
        ErrorReportingClientInterface $errorReportingClient
    ) {
        $this->errorReportingClient = $errorReportingClient;
    }

    /**
     * @param \Throwable $throwable
     *
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
            return 'production' !== getenv('SHOPWARE_ENV')
                || 'production' !== getenv('REDIRECT_SHOPWARE_ENV');
        } catch (\Throwable $th) {
            return false;
        } catch (\Exception $th) { /** @phpstan-ignore-line because of php5 compatibility */
            return false;
        }
    }
}
