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
use Pimple\Container;
use Pimple\ServiceProviderInterface;


class TokensServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['tokens'] = function (Application $app) {
            return new \random($app);
        };
    }
}
