<?php

namespace AxytosKaufAufRechnungShopware5\Controllers;

trait AxytosControllerTrait
{
    /**
     * @param int $statusCode
     * @return void
     */
    protected function setResponseStatusCode($statusCode)
    {
        if (!method_exists($this->response, 'setStatusCode')) {
            return;
        }
        $this->response->setStatusCode($statusCode);
    }

    /**
     * @return bool
     */
    protected function isNotPostRequest()
    {
        $requestMethod = strtolower($this->request->getMethod());
        return $requestMethod !== 'post';
    }
}
