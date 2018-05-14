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
use Alchemy\Phrasea\Setup\Installer;
use Pimple\Container;
use Pimple\ServiceProviderInterface;


class InstallerServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['phraseanet.installer'] = function (Application $app) {
            return new Installer($app);
        };
    }
}
