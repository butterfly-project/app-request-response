<?php

namespace Butterfly\Application\RequestResponse\Tests;

use Butterfly\Application\RequestResponse\Handler\DefaultRequestHandler;
use Butterfly\Application\RequestResponse\Routing\IRouter;

class DefaultRequestHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function forTestAction()
    {
        return 1;
    }

    public function testHandle()
    {
        $request  = $this->getRequest();

        $router    = $this->getRouter();
        $router
            ->expects($this->once())
            ->method('getActionCode')
            ->with($request)
            ->will($this->returnValue('action_service:forTest'));

        $container = $this->getContainer();
        $container
            ->expects($this->once())
            ->method('get')
            ->with('action_service')
            ->will($this->returnValue($this));

        $handler = new DefaultRequestHandler($container);
        $handler->setRouter($router);

        $result = $handler->handle($request);

        $this->assertEquals(1, $result);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\HttpFoundation\Request
     */
    protected function getRequest()
    {
        return $this->getMock('Symfony\Component\HttpFoundation\Request');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Butterfly\Component\DI\Container
     */
    protected function getContainer()
    {
        return $this
            ->getMockBuilder('\Butterfly\Component\DI\Container')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|IRouter
     */
    protected function getRouter()
    {
        return $this->getMock('\Butterfly\Application\RequestResponse\Routing\IRouter');
    }
}
