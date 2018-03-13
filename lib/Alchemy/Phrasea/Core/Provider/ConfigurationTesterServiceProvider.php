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
use Alchemy\Phrasea\Setup\ConfigurationTester;
use Alchemy\Phrasea\Setup\Version\PreSchemaUpgrade\PreSchemaUpgradeCollection;
use Alchemy\Phrasea\Setup\Version\PreSchemaUpgrade\Upgrade39Feeds;
use Alchemy\Phrasea\Setup\Version\PreSchemaUpgrade\Upgrade39Sessions;
use Alchemy\Phrasea\Setup\Version\PreSchemaUpgrade\Upgrade39Tokens;
use Alchemy\Phrasea\Setup\Version\PreSchemaUpgrade\Upgrade39Users;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Application as SilexApplication;


class ConfigurationTesterServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['phraseanet.configuration-tester'] = function (Application $app) {
            return new ConfigurationTester($app);
        };

        $app['phraseanet.pre-schema-upgrader.upgrades'] = function () {
            return [new Upgrade39Feeds(), new Upgrade39Users(), new Upgrade39Tokens(), new Upgrade39Sessions()];
        };

        $app['phraseanet.pre-schema-upgrader'] = function (Application $app) {
            return new PreSchemaUpgradeCollection($app['phraseanet.pre-schema-upgrader.upgrades']);
        };
    }
}
