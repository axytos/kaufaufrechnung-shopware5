<?php

namespace AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel;

class ShopwareModelReflector
{
    /**
     * @param object $modelInstance
     * @param string $methodName
     *
     * @return bool
     */
    public function hasMethod($modelInstance, $methodName)
    {
        return method_exists($modelInstance, $methodName);
    }

    /**
     * @param object $modelInstance
     * @param string $methodName
     * @param mixed  ...$args
     *
     * @return mixed
     */
    public function callMethod($modelInstance, $methodName, ...$args)
    {
        $callable = [$modelInstance, $methodName];

        if (!is_callable($callable)) {
            throw new \Exception("Method '{$methodName}' not found!");
        }

        return call_user_func($callable, ...$args);
    }
}
