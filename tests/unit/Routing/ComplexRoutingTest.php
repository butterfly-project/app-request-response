<?php

namespace Butterfly\Application\RequestResponse\Tests;

use Butterfly\Application\RequestResponse\Routing\ComplexRouting;
use Butterfly\Application\RequestResponse\Routing\IRouter;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Marat Fakhertdinov <marat.fakhertdinov@gmail.com>
 */
class ComplexRoutingTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAction()
    {
        $request  = Request::create('/');
        $expected = array('home:index', array($request));

        $routing = $this->getMockRouting();
        $routing
            ->expects($this->once())
            ->method('getAction')
            ->with($request)
            ->willReturn($expected);

        $complexRouting = new ComplexRouting('action_of_404:index', array($routing));

        $result = $complexRouting->getAction($request);

        $this->assertEquals($expected, $result);
    }

    public function testGetActionIfUndefinedUri()
    {
        $request  = Request::create('/undefined');
        $expected = array('action_of_404:index', array($request));

        $routing = $this->getMockRouting();
        $routing
            ->expects($this->once())
            ->method('getAction')
            ->with($request)
            ->willReturn(null);

        $complexRouting = new ComplexRouting('action_of_404:index', array($routing));

        $result = $complexRouting->getAction($request);

        $this->assertEquals($expected, $result);
    }

    /**
     * @expectedException \Butterfly\Application\RequestResponse\Routing\UndefinedUriException
     */
    public function testGetActionIfEmpty404Action()
    {
        $request  = Request::create('/undefined');

        $routing = $this->getMockRouting();
        $routing
            ->expects($this->once())
            ->method('getAction')
            ->with($request)
            ->willReturn(null);

        $complexRouting = new ComplexRouting(null, array($routing));

        $complexRouting->getAction($request);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|IRouter
     */
    protected function getMockRouting()
    {
        return $this->getMock('\Butterfly\Application\RequestResponse\Routing\IRouter');
    }
}
