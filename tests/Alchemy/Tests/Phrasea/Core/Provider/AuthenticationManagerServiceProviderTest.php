<?php

namespace Alchemy\Tests\Phrasea\Core\Provider;

use Alchemy\Phrasea\Authentication\AccountCreator;
use Alchemy\Phrasea\Authentication\Authenticator;
use Alchemy\Phrasea\Authentication\Manager as AuthenticationManager;
use Alchemy\Phrasea\Authentication\PersistentCookie\Manager as PersistentCookieManager;
use Alchemy\Phrasea\Authentication\Phrasea\FailureHandledNativeAuthentication;
use Alchemy\Phrasea\Authentication\Phrasea\FailureManager;
use Alchemy\Phrasea\Authentication\Phrasea\NativeAuthentication;
use Alchemy\Phrasea\Authentication\Phrasea\OldPasswordEncoder;
use Alchemy\Phrasea\Authentication\Phrasea\PasswordAuthenticationInterface;
use Alchemy\Phrasea\Authentication\Phrasea\PasswordEncoder;
use Alchemy\Phrasea\Authentication\Provider\Factory;
use Alchemy\Phrasea\Authentication\ProvidersCollection;
use Alchemy\Phrasea\Authentication\SuggestionFinder;
use Alchemy\Phrasea\Core\Provider\RepositoriesServiceProvider;
use Alchemy\Phrasea\Core\Provider\AuthenticationManagerServiceProvider;
use Alchemy\Phrasea\Core\Provider\ConfigurationServiceProvider;
use Alchemy\Phrasea\Model\Entities\User;
use Alchemy\Phrasea\Model\Repositories\AuthFailureRepository;
use Alchemy\Phrasea\Model\Repositories\UserRepository;
use Neutron\ReCaptcha\ReCaptcha;

/**
 * @group functional
 * @group legacy
 * @covers Alchemy\Phrasea\Core\Provider\AuthenticationManagerServiceProvider
 */
class AuthenticationManagerServiceProviderTest extends ServiceProviderTestCase
{
    public function provideServiceDescription()
    {
        return [
            [
                AuthenticationManagerServiceProvider::class,
                'authentication',
                Authenticator::class,
            ],
            [
                AuthenticationManagerServiceProvider::class,
                'authentication.persistent-manager',
                PersistentCookieManager::class
            ],
            [
                AuthenticationManagerServiceProvider::class,
                'authentication.suggestion-finder',
                SuggestionFinder::class
            ],
            [
                AuthenticationManagerServiceProvider::class,
                'authentication.providers.factory',
                Factory::class
            ],
            [
                AuthenticationManagerServiceProvider::class,
                'authentication.providers',
                ProvidersCollection::class
            ],
            [
                AuthenticationManagerServiceProvider::class,
                'authentication.manager',
                AuthenticationManager::class
            ],
            [
                AuthenticationManagerServiceProvider::class,
                'auth.password-encoder',
                PasswordEncoder::class
            ],
            [
                AuthenticationManagerServiceProvider::class,
                'auth.old-password-encoder',
                OldPasswordEncoder::class
            ],
            [
                AuthenticationManagerServiceProvider::class,
                'auth.native.failure-manager',
                FailureManager::class
            ],
            [
                AuthenticationManagerServiceProvider::class,
                'auth.native',
                PasswordAuthenticationInterface::class
            ],
            [
                AuthenticationManagerServiceProvider::class,
                'authentication.providers.account-creator',
                AccountCreator::class
            ],
        ];
    }

    public function testFailureManagerAttemptsConfiguration()
    {
        $app = $this->loadApp();

        $app['conf']->set(['authentication', 'captcha', 'trials-before-display'], 42);

        //$app['orm.em'] = $this->createEntityManagerMock();
        $app['recaptcha'] = $this->createReCaptchaMock();

        $manager = $app['auth.native.failure-manager'];
        $this->assertEquals(42, $manager->getTrials());
    }

    public function testFailureAccountCreator()
    {
        $app = $this->getApplication();
        $app->offsetUnset('phraseanet.configuration.yaml-parser');
        $app->offsetUnset('phraseanet.configuration.compiler');
        $app->offsetUnset('configuration.store');
        $app->offsetUnset('conf');
        $app->offsetUnset('phraseanet.configuration');
        $app->register(new ConfigurationServiceProvider());
        $app['conf']->set(['authentication', 'auto-create'], ['templates' => []]);
        $app['authentication.providers.account-creator'];
    }

    public function testAuthNativeWithCaptchaEnabled()
    {
        $app = $this->loadApp();
        $app['root.path'] = __DIR__ . '/../../../../../../';
        $app->offsetUnset('phraseanet.configuration.yaml-parser');
        $app->offsetUnset('phraseanet.configuration.compiler');
        $app->offsetUnset('configuration.store');
        $app->offsetUnset('conf');
        $app->offsetUnset('phraseanet.configuration');
        $app->register(new AuthenticationManagerServiceProvider());
        $app->register(new ConfigurationServiceProvider());
        $app->register(new RepositoriesServiceProvider());
        $app['phraseanet.appbox'] = self::$DI['app']['phraseanet.appbox'];

        $app['conf']->set(['authentication', 'captcha'], ['enabled' => true]);

        $app['orm.em'] = $this->createEntityManagerMock();
        $app['repo.users'] = $this->createUserRepositoryMock();
        $app['repo.auth-failures'] = $this->getMockBuilder(AuthFailureRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $app['recaptcha'] = $this->createReCaptchaMock();

        $this->assertInstanceOf(FailureHandledNativeAuthentication::class, $app['auth.native']);
    }

    public function testAuthNativeWithCaptchaDisabled()
    {
        $app = $this->loadApp();
        $app['root.path'] = __DIR__ . '/../../../../../../';
        $app->offsetUnset('phraseanet.configuration.yaml-parser');
        $app->offsetUnset('phraseanet.configuration.compiler');
        $app->offsetUnset('configuration.store');
        $app->offsetUnset('conf');
        $app->offsetUnset('phraseanet.configuration');
        $app->register(new AuthenticationManagerServiceProvider());
        $app->register(new ConfigurationServiceProvider());
        $app['phraseanet.appbox'] = self::$DI['app']['phraseanet.appbox'];

        $app['conf']->set(['authentication', 'captcha'], ['enabled' => false]);

        $app['orm.em'] = $this->createEntityManagerMock();
        $app['repo.users'] = $this->createUserRepositoryMock();
        $app['recaptcha'] = $this->createReCaptchaMock();

        $this->assertInstanceOf(NativeAuthentication::class, $app['auth.native']);
    }

    public function testAccountCreator()
    {
        $app = $this->getApplication();
        $template1 = $user = $app['manipulator.user']->createTemplate('template1', self::$DI['user']);
        $template2 = $user = $app['manipulator.user']->createTemplate('template2', self::$DI['user']);

        $app['conf']->set(['authentication', 'auto-create'], ['templates' => [$template1->getId(), $template2->getId()]]);

        $this->assertEquals([$template1->getLogin(), $template2->getLogin()], array_map(function (User $user) {
            return $user->getLogin();
        }, $app['authentication.providers.account-creator']->getTemplates()));

        $this->removeUser($app, $template1);
        $this->removeUser($app, $template2);
    }

    private function createUserRepositoryMock()
    {
        return $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return ReCaptcha|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function createReCaptchaMock()
    {
        return $this->getMockBuilder(ReCaptcha::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
