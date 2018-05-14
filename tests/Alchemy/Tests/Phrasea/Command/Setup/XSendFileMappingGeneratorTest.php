<?php

namespace Alchemy\Tests\Phrasea\Command\Setup;

use Alchemy\Phrasea\Command\Setup\XSendFileMappingGenerator;

/**
 * @group functional
 * @group legacy
 */
class XSendFileMappingGeneratorTest extends \PhraseanetTestCase
{
    /**
     * @dataProvider provideVariousOptions
     */
    public function testRunWithoutProblems($option)
    {
        $input = $this->createMock('Symfony\Component\Console\Input\InputInterface');
        $output = $this->createMock('Symfony\Component\Console\Output\OutputInterface');

        $input->expects($this->any())
            ->method('getArgument')
            ->with('type')
            ->will($this->returnValue('nginx'));

        $input->expects($this->any())
            ->method('getOption')
            ->with($this->isType('string'))
            ->will($this->returnValue($option));

        $command = new XSendFileMappingGenerator();

        $cli = self::getCLI();

        $cli['monolog'] = function () {
            return $this->getMockBuilder('Monolog\Logger')->disableOriginalConstructor()->getMock();
        };

        $originalConf = $cli['conf'];

        $conf = $this->getMockBuilder('Alchemy\Phrasea\Core\Configuration\PropertyAccess')
            ->disableOriginalConstructor()
            ->getMock();

        $conf->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(function ($property, $defaultValue = null) use ($originalConf) {
                return $originalConf->get($property, $defaultValue);
            }));

        $cli->offsetUnset('conf');
        $cli['conf'] = $conf;

        if ($option) {
            $conf->expects($this->once())
                ->method('set')
                ->with('xsendfile');
        } else {
            $conf->expects($this->never())
                ->method('set');
        }
        $command->setContainer($cli);

        $this->assertEquals(0, $command->execute($input, $output));
    }

    public function testRunWithProblem()
    {
        $input = $this->createMock('Symfony\Component\Console\Input\InputInterface');
        $output = $this->createMock('Symfony\Component\Console\Output\OutputInterface');

        $logger = $this->getMockBuilder('Monolog\Logger')
                  ->disableOriginalConstructor()
                  ->getMock();

        $logger->expects($this->once())
            ->method('error');

        $cli = self::getCLI();
        $cli['monolog'] = function () use ($logger) {
            return $logger;
        };

        $input->expects($this->any())
            ->method('getArgument')
            ->with('type')
            ->will($this->returnValue(null));

        $command = new XSendFileMappingGenerator();
        $command->setContainer($cli);
        $this->expectException('Alchemy\Phrasea\Exception\InvalidArgumentException');
        $command->execute($input, $output);
    }

    public function provideVariousOptions()
    {
        return [
            [true],
            [false],
        ];
    }
}
