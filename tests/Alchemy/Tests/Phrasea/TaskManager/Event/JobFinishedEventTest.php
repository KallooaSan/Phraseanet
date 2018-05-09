<?php

namespace Alchemy\Tests\Phrasea\TaskManager\Event;

use Alchemy\Phrasea\TaskManager\Event\JobFinishedEvent;

/**
 * @group functional
 * @group legacy
 */
class JobFinishedEventTest extends \PhraseanetTestCase
{
    public function testEvent()
    {
        $task = $this->createMock('Alchemy\Phrasea\Model\Entities\Task');
        $event = new JobFinishedEvent($task);
        $this->assertSame($task, $event->getTask());
    }
}
