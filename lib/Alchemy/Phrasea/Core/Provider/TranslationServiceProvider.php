<?php

namespace Alchemy\Phrasea\Core\Provider;

use Alchemy\Phrasea\Utilities\CachedTranslator;
use JMS\TranslationBundle\Translation\Loader\Symfony\XliffLoader;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Application;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Loader\PoFileLoader;
use Symfony\Component\Translation\MessageSelector;


class TranslationServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['translator.cache-options'] = [];

        $app['translator'] = function ($app) {
            $app['translator.cache-options'] = array_replace(
                [
                    'debug' => $app['debug'],
                ], $app['translator.cache-options']
            );

            $translator = new CachedTranslator($app, $app['translator.message_selector'], $app['translator.cache-options']);

            // Handle deprecated 'locale_fallback'
            if (isset($app['locale_fallback'])) {
                $app['locale_fallbacks'] = (array) $app['locale_fallback'];
            }

            $translator->setFallbackLocales($app['locale_fallbacks']);

            $translator->addLoader('array', new ArrayLoader());
            // to load Symfony resources
            $translator->addLoader('xliff', new XliffLoader());
            // to load Phraseanet resources
            $translator->addLoader('xlf', new XliffLoader());
            $translator->addLoader('po', new PoFileLoader());

            foreach ($app['translator.domains'] as $domain => $data) {
                foreach ($data as $locale => $messages) {
                    $translator->addResource('array', $messages, $locale, $domain);
                }
            }

            foreach ($app['translator.resources'] as $resource) {
                $translator->addResource(
                    $resource[0],
                    $resource[1],
                    $resource[2],
                    isset($resource[3]) ? $resource[3] : null
                );
            }

            return $translator;
        };

        $app['translator.message_selector'] = function () {
            return new MessageSelector();
        };

        $app['translator.domains'] = [];
        $app['locale_fallbacks'] = ['en'];
    }
}
