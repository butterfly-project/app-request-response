<?php

namespace Butterfly\Application\RequestResponse\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Marat Fakhertdinov <marat.fakhertdinov@gmail.com>
 */
class ResponseEvent extends Event
{
    const EVENT_NAME = 'application.response';

    /**
     * @var Response
     */
    protected $response;

    /**
     * @param Response $response
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}
