<?php

namespace Butterfly\Application\RequestResponse\Routing;

use Symfony\Component\HttpFoundation\Request;

interface IRouter
{
    /**
     * @param Request $request
     * @return string
     */
    public function getActionCode(Request $request);

    /**
     * @param string $route
     * @param array  $parameters
     * @param bool   $isAbsolute
     * @return string The generated URL
     */
    public function generateUrl($route, array $parameters = array(), $isAbsolute = true);
}
