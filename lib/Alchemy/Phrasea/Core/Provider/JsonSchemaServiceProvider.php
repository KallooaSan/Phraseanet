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

use Alchemy\Phrasea\Helper\JsonBodyHelper;
use JsonSchema\RefResolver;
use JsonSchema\Uri\UriResolver;
use JsonSchema\Uri\UriRetriever;
use JsonSchema\Validator;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Application;
use Webmozart\Json\JsonDecoder;
use Webmozart\Json\JsonEncoder;
use Webmozart\Json\JsonValidator;


class JsonSchemaServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['json-schema.base_uri'] = 'file://' . realpath(__DIR__ . '/../../../../../lib/conf.d/json_schema') . '/';

        $app['json-schema.retriever'] = function () {
            return new UriRetriever();
        };

        $app['json-schema.ref_resolver'] = function (Application $app) {
            return new RefResolver($app['json-schema.retriever'], new UriResolver());
        };

        $app['json-schema.validator'] = function (Application $app) {
            return new Validator(Validator::CHECK_MODE_NORMAL, $app['json-schema.retriever']);
        };

        $app['json.validator'] = function (Application $app) {
            return new JsonValidator($app['json-schema.validator']);
        };

        $app['json.decoder'] = function (Application $app) {
            return new JsonDecoder($app['json.validator']);
        };

        $app['json.encoder'] = function (Application $app) {
            return new JsonEncoder($app['json.validator']);
        };

        $app['json.body_helper'] = function (Application $app) {
            return new JsonBodyHelper(
                $app['json.validator'],
                $app['json.decoder'],
                $app['json-schema.retriever'],
                $app['json-schema.ref_resolver'],
                $app['json-schema.base_uri']
            );
        };
    }
}
