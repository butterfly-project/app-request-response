<?php

namespace Butterfly\Application\RequestResponse\Handler;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface IRequestHandler
{
    /**
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request);
}
