<?php

namespace Butterfly\Application\RequestResponse\Handler;

use Butterfly\Application\RequestResponse\Routing\IRouter;
use Butterfly\Application\RequestResponse\Routing\IRouterAware;
use Butterfly\Component\DI\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultRequestHandler implements IRequestHandler, IRouterAware
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var IRouter
     */
    protected $router;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param IRouter $router
     */
    public function setRouter(IRouter $router)
    {
        $this->router = $router;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request)
    {
        $actionCode = $this->router->getActionCode($request);
        $action     = $this->getAction($actionCode);
        $parameters = $this->getParameters($action, $request);

        return call_user_func_array($action, $parameters);
    }

    /**
     * @param string $actionCode
     * @return array
     */
    protected function getAction($actionCode)
    {
        list($actionServiceName, $method) = explode(':', $actionCode);

        $actionService = $this->container->get($actionServiceName);
        $actionMethod  = $method . 'Action';

        return array($actionService, $actionMethod);
    }

    /**
     * @param mixed $action
     * @param Request $request
     * @return array
     */
    protected function getParameters($action, Request $request)
    {
        return array($request);
    }
}
