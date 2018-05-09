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

use Alchemy\Phrasea\Application;
use Alchemy\Phrasea\Model\Manager\UserManager;
use Alchemy\Phrasea\Model\Manipulator\ACLManipulator;
use Alchemy\Phrasea\Model\Manipulator\ApiAccountManipulator;
use Alchemy\Phrasea\Model\Manipulator\ApiApplicationManipulator;
use Alchemy\Phrasea\Model\Manipulator\ApiLogManipulator;
use Alchemy\Phrasea\Model\Manipulator\ApiOauthCodeManipulator;
use Alchemy\Phrasea\Model\Manipulator\ApiOauthRefreshTokenManipulator;
use Alchemy\Phrasea\Model\Manipulator\ApiOauthTokenManipulator;
use Alchemy\Phrasea\Model\Manipulator\BasketManipulator;
use Alchemy\Phrasea\Model\Manipulator\LazaretManipulator;
use Alchemy\Phrasea\Model\Manipulator\PresetManipulator;
use Alchemy\Phrasea\Model\Manipulator\RegistrationManipulator;
use Alchemy\Phrasea\Model\Manipulator\TaskManipulator;
use Alchemy\Phrasea\Model\Manipulator\TokenManipulator;
use Alchemy\Phrasea\Model\Manipulator\UserManipulator;
use Alchemy\Phrasea\Model\Manipulator\WebhookEventDeliveryManipulator;
use Alchemy\Phrasea\Model\Manipulator\WebhookEventManipulator;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Application as SilexApplication;


class ManipulatorServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['manipulator.task'] = function (Application $app) {
            return new TaskManipulator($app['orm.em'], $app['translator'], $app['task-manager.notifier']);
        };

        $app['manipulator.user'] = function (Application $app) {
            return new UserManipulator(
                $app['model.user-manager'],
                $app['auth.password-encoder'],
                $app['geonames.connector'],
                $app['repo.users'],
                $app['random.low'],
                $app['dispatcher']
            );
        };

        $app['manipulator.token'] = function (Application $app) {
            return new TokenManipulator(
                $app['orm.em'],
                $app['random.medium'],
                $app['repo.tokens'],
                $app['tmp.download.path']
            );
        };

        $app['manipulator.preset'] = function (Application $app) {
            return new PresetManipulator($app['orm.em'], $app['repo.presets']);
        };

        $app['manipulator.acl'] = function (Application $app) {
            return new ACLManipulator($app['acl'], $app->getApplicationBox());
        };

        $app['model.user-manager'] = function (Application $app) {
            return new UserManager($app['orm.em'], $app->getApplicationBox()->get_connection());
        };

        $app['manipulator.registration'] = function (Application $app) {
            return new RegistrationManipulator(
                $app,
                $app['orm.em'],
                $app['acl'],
                $app->getApplicationBox(),
                $app['repo.registrations']
            );
        };

        $app['manipulator.api-application'] = function (Application $app) {
            return new ApiApplicationManipulator($app['orm.em'], $app['repo.api-applications'], $app['random.medium']);
        };

        $app['manipulator.api-account'] = function (Application $app) {
            return new ApiAccountManipulator($app['orm.em']);
        };

        $app['manipulator.api-oauth-code'] = function (Application $app) {
            return new ApiOauthCodeManipulator($app['orm.em'], $app['repo.api-oauth-codes'], $app['random.medium']);
        };

        $app['manipulator.api-oauth-token'] = function (Application $app) {
            return new ApiOauthTokenManipulator($app['orm.em'], $app['repo.api-oauth-tokens'], $app['random.medium']);
        };

        $app['manipulator.api-oauth-refresh-token'] = function (Application $app) {
            return new ApiOauthRefreshTokenManipulator($app['orm.em'], $app['repo.api-oauth-refresh-tokens'], $app['random.medium']);
        };

        $app['manipulator.api-log'] = function (Application $app) {
            return new ApiLogManipulator($app['orm.em'], $app['repo.api-logs']);
        };

        $app['manipulator.webhook-event'] = function (Application $app) {
            return new WebhookEventManipulator(
                $app['orm.em'],
                $app['repo.webhook-event'],
                $app['webhook.publisher']
            );
        };

        $app['manipulator.webhook-delivery'] = function (Application $app) {
            return new WebhookEventDeliveryManipulator($app['orm.em'], $app['repo.webhook-delivery']);
        };

        $app['manipulator.basket'] = function (Application $app) {
            return new BasketManipulator($app, $app['repo.baskets'], $app['orm.em']);
        };

        $app['manipulator.lazaret'] = function (Application $app) {
            return new LazaretManipulator($app, $app['repo.lazaret-files'], $app['filesystem'], $app['orm.em']);
        };

    }
}
