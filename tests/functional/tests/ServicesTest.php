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

    public function getDataForTestService()
    {
        return array(
            array('bfy_adapter.http_foundation.session'),
            array('bfy_adapter.http_foundation.request'),

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
        self::$container->get($serviceName);
    }
}
