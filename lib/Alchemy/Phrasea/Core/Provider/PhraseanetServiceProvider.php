<?php

/*
 * This file is part of Phraseanet
 *
 * (c) 2005-2016 Alchemy
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alchemy\Phrasea\Core\Provider;

use Alchemy\Phrasea\Application;
use Alchemy\Phrasea\Authentication\ACLProvider;
use Alchemy\Phrasea\Http\StaticFile\Symlink\SymLinker;
use Alchemy\Phrasea\Http\StaticFile\Symlink\SymLinkerEncoder;
use Alchemy\Phrasea\Metadata\PhraseanetMetadataReader;
use Alchemy\Phrasea\Metadata\PhraseanetMetadataSetter;
use Alchemy\Phrasea\Security\Firewall;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Application as SilexApplication;
use XPDF\Exception\BinaryNotFoundException;


class PhraseanetServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['phraseanet.appbox'] = function (Application $app) {
            return new \appbox($app);
        };

        $app['firewall'] = function (Application $app) {
            return new Firewall($app);
        };

        $app['events-manager'] = function (Application $app) {
            $events = new \eventsmanager_broker($app);
            $events->start();

            return $events;
        };

        $app['phraseanet.thumb-symlinker'] = function (SilexApplication $app) {
            return new SymLinker(
                $app['phraseanet.thumb-symlinker-encoder'],
                $app['filesystem'],
                $app['thumbnail.path']
            );
        };

        $app['phraseanet.thumb-symlinker-encoder'] = function (SilexApplication $app) {
            return new SymLinkerEncoder($app['phraseanet.configuration']['main']['key']);
        };

        $app['acl'] = function (SilexApplication $app) {
            return new ACLProvider($app);
        };

        $app['phraseanet.metadata-reader'] = function (SilexApplication $app) {
            $reader = new PhraseanetMetadataReader();

            try {
                $reader->setPdfToText($app['xpdf.pdftotext']);
            } catch (BinaryNotFoundException $e) {

            }

            return $reader;
        };

        $app['phraseanet.metadata-setter'] = function (Application $app) {
            return new PhraseanetMetadataSetter($app['repo.databoxes']);
        };

        $app['phraseanet.user-query'] = $app->factory(function (Application $app) {
            return new \User_Query($app);
        });

        $app['phraseanet.logger'] = $app->protect(function ($databox) use ($app) {
            try {
                return \Session_Logger::load($app, $databox);
            } catch (\Exception_Session_LoggerNotFound $e) {
                return \Session_Logger::create($app, $databox, $app['browser']);
            }
        });

        $app['date-formatter'] = function (Application $app) {
            return new \phraseadate($app);
        };
    }
}
