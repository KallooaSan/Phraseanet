<?php

/*
 * This file is part of Phraseanet
 *
 * (c) 2005-2014 Alchemy
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alchemy\Phrasea\Core\Provider;

use Alchemy\Zippy\Zippy;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Application;


class ZippyServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['zippy'] = function () {
            return Zippy::load();
        };
    }
}
