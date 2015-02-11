<?php

namespace Butterfly\Application\RequestResponse;

use Butterfly\Adapter\Sf2EventDispatcher\EventDispatcher;
use Butterfly\Application\RequestResponse\Event\RequestEvent;
use Butterfly\Application\RequestResponse\Event\ResponseEvent;
use Butterfly\Application\RequestResponse\Handler\IRequestHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Marat Fakhertdinov <marat.fakhertdinov@gmail.com>
 */
class RequestResponseApplication
{
    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @var IRequestHandler
     */
    protected $handler;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @param EventDispatcher $eventDispatcher
     * @param IRequestHandler $handler
     * @param Request         $request
     */
    public function __construct(EventDispatcher $eventDispatcher, IRequestHandler $handler, Request $request)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->handler         = $handler;
        $this->request         = $request;
    }

    /**
     * @throws \RuntimeException if response is incorrect
     */
    public function run()
    {
        $this->eventDispatcher->dispatch(RequestEvent::EVENT_NAME, new RequestEvent($this->request));

        $response = $this->handler->handle($this->request);
        if (!$response instanceof Response) {
            throw new \RuntimeException('Response is incorrect');
        }

        $this->eventDispatcher->dispatch(ResponseEvent::EVENT_NAME, new ResponseEvent($response));

        $response->send();
    }
}
