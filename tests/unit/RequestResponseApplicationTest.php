<?php

namespace Butterfly\Application\RequestResponse\Tests;

use Butterfly\Adapter\Sf2EventDispatcher\EventDispatcher;
use Butterfly\Application\RequestResponse\Handler\IRequestHandler;
use Butterfly\Application\RequestResponse\RequestResponseApplication;

class RequestResponseApplicationTest extends \PHPUnit_Framework_TestCase
{
    public function testRun()
    {
        $request  = $this->getRequest();
        $response = $this->getResponse();

        $eventDispatcher = $this->getEventDispatcher();
        $eventDispatcher
            ->expects($this->any())
            ->method('dispatch')
            ->withAnyParameters();

        $requestHandler = $this->getRequestHandler();
        $requestHandler
            ->expects($this->once())
            ->method('handle')
            ->with($request)
            ->will($this->returnValue($response));

        $app = new RequestResponseApplication($eventDispatcher, $requestHandler, $request);
        $app->run();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testRunIfIncorrectResponse()
    {
        $request  = $this->getRequest();
        $response = array();

        $eventDispatcher = $this->getEventDispatcher();
        $requestHandler  = $this->getRequestHandler();
        $requestHandler
            ->expects($this->once())
            ->method('handle')
            ->with($request)
            ->will($this->returnValue($response));

        $app = new RequestResponseApplication($eventDispatcher, $requestHandler, $request);
        $app->run();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|EventDispatcher
     */
    protected function getEventDispatcher()
    {
        return $this->getMock('\Butterfly\Adapter\Sf2EventDispatcher\EventDispatcher');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|IRequestHandler
     */
    protected function getRequestHandler()
    {
        return $this->getMock('\Butterfly\Application\RequestResponse\Handler\IRequestHandler');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\HttpFoundation\Request
     */
    protected function getRequest()
    {
        return $this->getMock('Symfony\Component\HttpFoundation\Request');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\HttpFoundation\Response
     */
    protected function getResponse()
    {
        return $this->getMock('Symfony\Component\HttpFoundation\Response');
    }
}
