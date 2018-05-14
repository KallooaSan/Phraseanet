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

use Alchemy\Phrasea\Model\Converter\ApiApplicationConverter;
use Alchemy\Phrasea\Model\Converter\BasketConverter;
use Alchemy\Phrasea\Model\Converter\TaskConverter;
use Alchemy\Phrasea\Model\Converter\TokenConverter;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Application;


class ConvertersServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['converter.task'] = function ($app) {
            return new TaskConverter($app['repo.tasks']);
        };

        $app['converter.task-callback'] = $app->protect(function ($id) use ($app) {
            return $app['converter.task']->convert($id);
        });

        $app['converter.basket'] = function ($app) {
            return new BasketConverter($app['repo.baskets']);
        };

        $app['converter.token'] = function ($app) {
            return new TokenConverter($app['repo.tokens']);
        };

        $app['converter.api-application'] = function ($app) {
            return new ApiApplicationConverter($app['repo.api-applications']);
        };
    }
}
