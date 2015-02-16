<?php

namespace Butterfly\Tests;

class ServicesTest extends BaseDiTest
{
    protected static function getAdditionalConfigPaths()
    {
        return array(
            self::$baseDir . '/config/test.yml',
        );
    }

    public function getDataForTestParameter()
    {
        return array(
            array('bfy_app.routing.action_name_of_404', ''),
        );
    }

    /**
     * @dataProvider getDataForTestParameter
     * @param string $parameterName
     * @param mixed $expectedValue
     */
    public function testParameter($parameterName, $expectedValue)
    {
        $this->assertEquals($expectedValue, self::$container->getParameter($parameterName));
    }

    public function getDataForTestService()
    {
        return array(
            array('bfy_adapter.http_foundation.request'),
            array('bfy_adapter.http_foundation.session'),

            array('bfy_app.routing.complex_routing'),
            array('bfy_app.request_response.request_handler.default'),
            array('bfy_app.request_response'),
        );
    }

    /**
     * @dataProvider getDataForTestService
     * @param string $serviceName
     */
    public function testService($serviceName)
    {
        self::$container->getService($serviceName);
    }
}
