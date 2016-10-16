<?php

namespace Butterfly\Application\RequestResponse\Handler;

use Butterfly\Application\RequestResponse\Routing\IRouter;
use Butterfly\Component\DI\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Marat Fakhertdinov <marat.fakhertdinov@gmail.com>
 */
class DefaultRequestHandler implements IRequestHandler
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
     * @param IRouter $router
     */
    public function __construct(Container $container, IRouter $router)
    {
        $this->container = $container;
        $this->router    = $router;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request)
    {
        list($actionName, $parameters) = $this->router->getAction($request);
        $action = $this->getAction($actionName);

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
}
