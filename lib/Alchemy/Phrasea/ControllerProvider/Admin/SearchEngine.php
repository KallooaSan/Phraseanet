<?php

/*
 * This file is part of Phraseanet
 *
 * (c) 2005-2016 Alchemy
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alchemy\Phrasea\ControllerProvider\Admin;

use Alchemy\Phrasea\Application as PhraseaApplication;
use Alchemy\Phrasea\Controller\Admin\SearchEngineController;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Silex\ControllerCollection;


class SearchEngine implements ControllerProviderInterface, ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['controller.admin.search-engine'] = function (PhraseaApplication $app) {
            return new SearchEngineController($app);
        };
    }

    public function connect(Application $app)
    {
        /** @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $controllers->post('/drop_index', 'controller.admin.search-engine:dropIndexAction')
            ->bind("admin_searchengine_drop_index");

        $controllers->post('/create_index', 'controller.admin.search-engine:createIndexAction')
            ->bind("admin_searchengine_create_index");

        $controllers->get('/setting_from_index', 'controller.admin.search-engine:getSettingFromIndexAction')
            ->bind('admin_searchengine_setting_from_index');

        $controllers->match('/', 'controller.admin.search-engine:formConfigurationPanelAction')
            ->method('GET|POST')
            ->bind('admin_searchengine_form');

        return $controllers;
    }
}
