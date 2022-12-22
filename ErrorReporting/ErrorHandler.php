<?php

namespace AxytosKaufAufRechnungShopware5\ErrorReporting;

use Axytos\ECommerce\Clients\ErrorReporting\ErrorReportingClientInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Throwable;

class ErrorHandler
{
    /**
     * @var \Axytos\ECommerce\Clients\ErrorReporting\ErrorReportingClientInterface
     */
    private $errorReportingClient;
    /**
     * @var \Symfony\Component\HttpKernel\KernelInterface
     */
    private $kernel;

    public function __construct(
        ErrorReportingClientInterface $errorReportingClient,
        KernelInterface $kernel
    ) {
        $this->errorReportingClient = $errorReportingClient;
        $this->kernel = $kernel;
    }

    /**
     * @param \Throwable $throwable
     * @return void
     */
    public function handle($throwable)
    {
        $this->errorReportingClient->reportError($throwable);

        if ($this->kernel->isDebug()) {
            throw $throwable;
        }
    }
}
