<?php

/*
 * This file is part of Phraseanet
 *
 * (c) 2005-2014 Alchemy
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alchemy\Phrasea\Core\CLIProvider;

use Alchemy\Phrasea\Application;
use Alchemy\Phrasea\Command\Developer\Utils\ConstraintExtractor;
use Alchemy\Phrasea\Command\Developer\Utils\HelpMessageExtractor;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\DocParser;
use JMS\TranslationBundle\Translation\Dumper\SymfonyDumperAdapter;
use JMS\TranslationBundle\Translation\Dumper\XliffDumper;
use JMS\TranslationBundle\Translation\Extractor\File\DefaultPhpFileExtractor;
use JMS\TranslationBundle\Translation\Extractor\File\FormExtractor;
use JMS\TranslationBundle\Translation\Extractor\File\TwigFileExtractor;
use JMS\TranslationBundle\Translation\Extractor\File\ValidationExtractor;
use JMS\TranslationBundle\Translation\Extractor\FileExtractor;
use JMS\TranslationBundle\Translation\ExtractorManager;
use JMS\TranslationBundle\Translation\FileWriter;
use JMS\TranslationBundle\Translation\Loader\SymfonyLoaderAdapter;
use JMS\TranslationBundle\Translation\Loader\XliffLoader;
use JMS\TranslationBundle\Translation\LoaderManager;
use JMS\TranslationBundle\Translation\Updater;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Translation\Dumper\PoFileDumper;
use Symfony\Component\Translation\Loader\PoFileLoader;


class TranslationExtractorServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['translation-extractor.logger'] = function (Application $app) {
            return $app['monolog'];
        }
        ;
        $app['translation-extractor.doc-parser'] = function () {
            $parser =  new DocParser();
            $parser->addNamespace("JMS\\TranslationBundle\\Annotation");

            return $parser;
        };

        $app['translation-extractor.node-visitors'] = function (Application $app) {
            return [
                new ConstraintExtractor($app),
                new ValidationExtractor($app['validator']->getMetadataFactory()),
                new DefaultPhpFileExtractor($app['translation-extractor.doc-parser']),
                new TwigFileExtractor($app['twig']),
                new FormExtractor($app['translation-extractor.doc-parser']),
                new HelpMessageExtractor($app['translation-extractor.doc-parser']),
            ];
        };

        $app['translation-extractor.file-extractor'] = function (Application $app) {
            return new FileExtractor($app['twig'], $app['translation-extractor.logger'], $app['translation-extractor.node-visitors']);
        };

        $app['translation-extractor.extractor-manager'] = function (Application $app) {
            return new ExtractorManager($app['translation-extractor.file-extractor'], $app['translation-extractor.logger']);
        };

        $app['translation-extractor.writer'] = function (Application $app) {
            return new FileWriter($app['translation-extractor.writers']);
        };

        $app['translation-extractor.writers'] = function () {
            return [
                'po' => new SymfonyDumperAdapter(new PoFileDumper(), 'po'),
                'xlf' => new XliffDumper(),
            ];
        };

        $app['translation-extractor.loader-manager'] = function (Application $app) {
            return new LoaderManager($app['translation-extractor.loaders']);
        };

        $app['translation-extractor.loaders'] = function () {
            return [
                'po' => new SymfonyLoaderAdapter(new PoFileLoader()),
                'xlf' => new XliffLoader()
            ];
        };

        $app['translation-extractor.updater'] = function (Application $app) {
            AnnotationRegistry::registerAutoloadNamespace('JMS\TranslationBundle\Annotation', $app['root.path'].'/vendor/jms/translation-bundle');

            return new Updater($app['translation-extractor.loader-manager'], $app['translation-extractor.extractor-manager'], $app['translation-extractor.logger'], $app['translation-extractor.writer']);
        };
    }
}
