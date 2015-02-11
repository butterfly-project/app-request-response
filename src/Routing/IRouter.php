<?php

namespace Butterfly\Application\RequestResponse\Routing;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Marat Fakhertdinov <marat.fakhertdinov@gmail.com>
 */
interface IRouter
{
    /**
     * @param Request $request
     * @return array|null ($actionName, array $parameters)
     */
    public function getAction(Request $request);
}
