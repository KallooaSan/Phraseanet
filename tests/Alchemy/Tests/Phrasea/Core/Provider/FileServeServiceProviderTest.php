<?php

namespace Alchemy\Tests\Phrasea\Core\Provider;

use Alchemy\Phrasea\Core\Provider\ConfigurationServiceProvider;
use Alchemy\Phrasea\Core\Provider\FileServeServiceProvider;

/**
 * @group functional
 * @group legacy
 * @covers Alchemy\Phrasea\Core\Provider\FileServeServiceProvider
 */
class FileServeServiceProviderTest extends ServiceProviderTestCase
{
    public function provideServiceDescription()
    {
        return [
            [
                'Alchemy\Phrasea\Core\Provider\FileServeServiceProvider',
                'phraseanet.file-serve',
                'Alchemy\Phrasea\Http\ServeFileResponseFactory'
            ],
            [
                'Alchemy\Phrasea\Core\Provider\FileServeServiceProvider',
                'phraseanet.xsendfile-factory',
                'Alchemy\Phrasea\Http\XSendFile\XSendFileFactory'
            ],
            [
                'Alchemy\Phrasea\Core\Provider\FileServeServiceProvider',
                'phraseanet.h264-factory',
                'Alchemy\Phrasea\Http\H264PseudoStreaming\H264Factory'
            ],
            [
                'Alchemy\Phrasea\Core\Provider\FileServeServiceProvider',
                'phraseanet.h264',
                'Alchemy\Phrasea\Http\H264PseudoStreaming\H264Interface'
            ],
        ];
    }

    public function testMapping()
    {
        $app = clone $this->getApplication();
        $app['root.path'] = __DIR__ . '/../../../../../..';
        $app->offsetUnset('phraseanet.configuration');
        $app->offsetUnset('phraseanet.configuration.yaml-parser');
        $app->offsetUnset('phraseanet.configuration.compiler');
        $app->offsetUnset('configuration.store');
        $app->offsetUnset('conf');
        $app->register(new ConfigurationServiceProvider());
        $app->register(new FileServeServiceProvider());
        $app['phraseanet.configuration.config-path'] = __DIR__ . '/fixtures/config-mapping.yml';
        $app['phraseanet.configuration.config-compiled-path'] = __DIR__ . '/fixtures/config-mapping.php';
        $this->assertInstanceOf('Alchemy\Phrasea\Http\XSendFile\NginxMode', $app['phraseanet.xsendfile-factory']->getMode());
        $this->assertEquals(1, count($app['phraseanet.xsendfile-factory']->getMode()->getMapping()));

        unlink($app['phraseanet.configuration.config-compiled-path']);
        unset($app);
    }
}
