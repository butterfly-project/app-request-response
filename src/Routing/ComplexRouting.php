<?php

namespace Butterfly\Application\RequestResponse\Routing;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Marat Fakhertdinov <marat.fakhertdinov@gmail.com>
 */
class ComplexRouting implements IRouter
{
    /**
     * @var string
     */
    protected $actionNameOf404;

    /**
     * @var IRouter[]
     */
    protected $routers = array();

    /**
     * @param string $actionNameOf404
     * @param IRouter[] $routers
     */
    public function __construct($actionNameOf404, array $routers = array())
    {
        $this->actionNameOf404 = $actionNameOf404;

        foreach ($routers as $router) {
            $this->addRouter($router);
        }
    }

    /**
     * @param IRouter $router
     */
    public function addRouter(IRouter $router)
    {
        $this->routers[] = $router;
    }

    /**
     * @param Request $request
     * @return array|null ($actionName, array $parameters)
     * @throws UndefinedUriException if route is not found
     */
    public function getAction(Request $request)
    {
        foreach ($this->routers as $router) {
            $action = $router->getAction($request);

            if (null !== $action) {
                return $action;
            }
        }

        if (empty($this->actionNameOf404)) {
            throw new UndefinedUriException(sprintf("Route for uri %s not found.", $request->getPathInfo()));
        }

        return array($this->actionNameOf404, array($request));
    }
}
