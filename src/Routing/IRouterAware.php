<?php

namespace Butterfly\Application\RequestResponse\Routing;

interface IRouterAware
{
    /**
     * @param IRouter $router
     */
    public function setRouter(IRouter $router);
}
