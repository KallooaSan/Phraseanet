<?php

namespace Alchemy\Tests\Phrasea\Command;

use Alchemy\Phrasea\Command\CheckConfig;

/**
 * @group functional
 * @group legacy
 */
class CheckConfigTest extends \PhraseanetTestCase
{
    public function testRunWithoutProblems()
    {
        $input = $this->createMock('Symfony\Component\Console\Input\InputInterface');
        $output = $this->createMock('Symfony\Component\Console\Output\OutputInterface');

        //self::$DI['cli']['phraseanet.SE'] = $this->createSearchEngineMock();
        $command = new CheckConfig('check:config');
        $command->setContainer(self::$DI['cli']);
        $this->assertLessThan(2, $command->execute($input, $output));
    }
}
