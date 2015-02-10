<?php

namespace Butterfly\Application\RequestResponse\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Marat Fakhertdinov <marat.fakhertdinov@gmail.com>
 */
class RequestEvent extends Event
{
    const EVENT_NAME = 'application.request';

    /**
     * @var Request
     */
    protected $request;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}
