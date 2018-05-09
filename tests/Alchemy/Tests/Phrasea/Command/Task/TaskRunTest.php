<?php

namespace Alchemy\Tests\Phrasea\Command\Task;

use Alchemy\Phrasea\Command\Task\TaskRun;
use Alchemy\Phrasea\Model\Entities\Task;

/**
 * @group functional
 * @group legacy
 */
class TaskRunTest extends \PhraseanetTestCase
{
    public function testRunWithoutProblems()
    {
        $input = $this->createMock('Symfony\Component\Console\Input\InputInterface');
        $output = $this->createMock('Symfony\Component\Console\Output\OutputInterface');

        $input->expects($this->any())
                ->method('getArgument')
                ->with('task_id')
                ->will($this->returnValue(1));

        $command = new TaskRun();
        $command->setContainer(self::$DI['cli']);

        $job = $this->createMock('Alchemy\Phrasea\TaskManager\Job\JobInterface');

        self::$DI['cli']['task-manager.job-factory'] = $this->getMockBuilder('Alchemy\Phrasea\TaskManager\Job\Factory')
            ->disableOriginalConstructor()
            ->getMock();

        self::$DI['cli']['task-manager.job-factory']->expects($this->once())
                ->method('create')
                ->will($this->returnValue($job));

        $job->expects($this->once())
            ->method('run');
        $job->expects($this->exactly(2))
            ->method('addSubscriber');

        $command->execute($input, $output);
    }
}
