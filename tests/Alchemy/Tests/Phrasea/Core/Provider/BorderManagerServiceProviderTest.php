<?php

namespace Alchemy\Tests\Phrasea\Core\Provider;

use Alchemy\Phrasea\Core\Provider\PhraseanetServiceProvider;
use Alchemy\Phrasea\Core\Provider\BorderManagerServiceProvider;
use Alchemy\Phrasea\Core\Provider\ConfigurationServiceProvider;
use Symfony\Component\Process\ExecutableFinder;
use XPDF\XPDFServiceProvider;

/**
 * @group functional
 * @group legacy
 * @covers Alchemy\Phrasea\Core\Provider\BorderManagerServiceProvider
 */
class BorderManagerServiceProviderTest extends ServiceProviderTestCase
{
    public function provideServiceDescription()
    {
        return [
            [
                'Alchemy\Phrasea\Core\Provider\BorderManagerServiceProvider',
                'border-manager',
                'Alchemy\\Phrasea\\Border\\Manager'
            ],
        ];
    }

    public function testItLoadsWithoutXPDF()
    {
        $app = $this->loadApp();
        $app->offsetUnset('border-manager.mime-guesser-configuration');
        $app->offsetUnset('phraseanet.configuration.yaml-parser');
        $app->offsetUnset('phraseanet.configuration.compiler');
        $app->offsetUnset('configuration.store');
        $app->offsetUnset('conf');
        $app->offsetUnset('phraseanet.configuration');
        $app->register(new XPDFServiceProvider(), [
            'xpdf.configuration' => [
                'pdftotext.binaries' => '/path/to/nowhere',
            ]
        ]);
        $app->register(new BorderManagerServiceProvider());
        $app->register(new PhraseanetServiceProvider());
        $app['root.path'] = __DIR__ . '/../../../../../..';
        $app->register(new ConfigurationServiceProvider());
        $app['conf']->set(['border-manager', 'enabled'], false);

        $this->assertInstanceOf('Alchemy\Phrasea\Border\Manager', $app['border-manager']);
        $this->assertNull($app['phraseanet.metadata-reader']->getPdfToText());
    }

    public function testItLoadsWithXPDF()
    {
        $finder = new ExecutableFinder();
        $php = $finder->find('php');

        if (null === $php) {
            $this->markTestSkipped('Unable to find php binary, mandatory for this test');
        }

        $app = $this->loadApp();
        $app->offsetUnset('border-manager.mime-guesser-configuration');
        $app->offsetUnset('phraseanet.configuration.yaml-parser');
        $app->offsetUnset('phraseanet.configuration.compiler');
        $app->offsetUnset('configuration.store');
        $app->offsetUnset('conf');
        $app->offsetUnset('phraseanet.configuration');
        $app->register(new PhraseanetServiceProvider());
        $app->register(new XPDFServiceProvider(), [
            'xpdf.configuration' => [
                'pdftotext.binaries' => $php,
            ]
        ]);
        $app->register(new BorderManagerServiceProvider());
        $app['root.path'] = __DIR__ . '/../../../../../..';
        $app->register(new ConfigurationServiceProvider());
        $app['conf']->set(['border-manager', 'enabled'], false);

        $this->assertInstanceOf('Alchemy\Phrasea\Border\Manager', $app['border-manager']);
        $this->assertInstanceOf('XPDF\PdfToText', $app['phraseanet.metadata-reader']->getPdfToText());
    }
}
