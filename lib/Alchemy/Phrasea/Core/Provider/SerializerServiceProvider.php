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

use Alchemy\Phrasea\Core\LazyLocator;
use Alchemy\Phrasea\Model\Serializer\CaptionSerializer;
use Alchemy\Phrasea\Model\Serializer\ESRecordSerializer;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Application;


class SerializerServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['serializer.caption'] = function (Application $app) {
            return new CaptionSerializer(new LazyLocator($app, 'service.technical_data'));
        };

        $app['serializer.es-record'] = function () {
            return new ESRecordSerializer();
        };
    }
}
