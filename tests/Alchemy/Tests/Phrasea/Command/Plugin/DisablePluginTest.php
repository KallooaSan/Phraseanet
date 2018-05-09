<?php

namespace Alchemy\Tests\Phrasea\Command\Plugin;

use Alchemy\Phrasea\Command\Plugin\DisablePlugin;

/**
 * @group functional
 * @group legacy
 */
class DisablePluginTest extends PluginCommandTestCase
{
    /**
     * @dataProvider provideVariousInitialConfs
     */
    public function testExecute($initial)
    {
        $input = $this->createMock('Symfony\Component\Console\Input\InputInterface');
        $output = $this->createMock('Symfony\Component\Console\Output\OutputInterface');
        $input->expects($this->once())
              ->method('getArgument')
              ->with($this->equalTo('name'))
              ->will($this->returnValue('test-plugin'));

        self::$DI['cli']['conf']->set(['plugins', 'test-plugin', 'enabled'], $initial);

        $command = new DisablePlugin();
        $command->setContainer(self::$DI['cli']);

        $this->assertSame(0, $command->execute($input, $output));
        $this->assertFalse(self::$DI['cli']['conf']->get(['plugins', 'test-plugin', 'enabled']));
    }

    public function provideVariousInitialConfs()
    {
        return [[true], [false]];
    }
}
