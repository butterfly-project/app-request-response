<?php

namespace Butterfly\Application\RequestResponse\Handler;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Marat Fakhertdinov <marat.fakhertdinov@gmail.com>
 */
interface IRequestHandler
{
    /**
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request);
}
