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

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Application;


class FtpServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(Container $app)
    {
        $app['phraseanet.ftp.client'] = $app->protect(
            function ($host, $port = 21, $timeout = 90, $ssl = false, $proxy = false, $proxyport = false, $proxyuser = false, $proxypwd = false) {
                return new \ftpclient($host, $port, $timeout, $ssl, $proxy, $proxyport, $proxyuser, $proxypwd);
            }
        );
    }
}
