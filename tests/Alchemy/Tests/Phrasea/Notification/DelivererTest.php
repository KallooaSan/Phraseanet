<?php

namespace Alchemy\Tests\Phrasea\Notification;

use Alchemy\Phrasea\Notification\Deliverer;
use Alchemy\Phrasea\Exception\LogicException;

/**
 * @group functional
 * @group legacy
 */
class DelivererTest extends \PhraseanetTestCase
{
    /**
     * @covers Alchemy\Phrasea\Notification\Deliverer::deliver
     */
    public function testDeliver()
    {
        $mail = $this->createMock('Alchemy\Phrasea\Notification\Mail\MailInterface');

        $mail->expects($this->any())
            ->method('getReceiver')
            ->will($this->returnValue($this->getReceiverMock()));

        $mailer = $this->getMailerMock();
        $mailer->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf('\Swift_Message'))
            ->will($this->returnValue(42));

        $deliverer = new Deliverer($mailer, $this->getEventDispatcherMock(), $this->getEmitterMock());
        $this->assertEquals(42, $deliverer->deliver($mail));
    }

    /**
     * @covers Alchemy\Phrasea\Notification\Deliverer::deliver
     */
    public function testDeliverWithoutReceiverShouldThrowAnException()
    {
        $mail = $this->createMock('Alchemy\Phrasea\Notification\Mail\MailInterface');

        $deliverer = new Deliverer($this->getMailerMock(), $this->getEventDispatcherMock(), $this->getEmitterMock());

        try {
            $deliverer->deliver($mail);
            $this->fail('Should have raised an exception');
        } catch (LogicException $e) {
            $this->assertEquals('You must provide a receiver for a mail notification', $e->getMessage());
        }
    }

    /**
     * @covers Alchemy\Phrasea\Notification\Deliverer::deliver
     */
    public function testDeliverWithoutReceipt()
    {
        $mail = $this->createMock('Alchemy\Phrasea\Notification\Mail\MailInterface');

        $mail->expects($this->any())
            ->method('getReceiver')
            ->will($this->returnValue($this->getReceiverMock()));

        $catchEmail = null;

        $mailer = $this->getMailerMock();
        $mailer->expects($this->once())
            ->method('send')
            ->will($this->returnCallback(function ($email) use (&$catchEmail) {
                $catchEmail = $email;
            }));

        $deliverer = new Deliverer($mailer, $this->getEventDispatcherMock(), $this->getEmitterMock());
        $deliverer->deliver($mail);

        /* @var $catchEmail \Swift_Message */
        $this->assertNull($catchEmail->getReadReceiptTo());
    }

    /**
     * @covers Alchemy\Phrasea\Notification\Deliverer::deliver
     */
    public function testDeliverWithAReadReceiptWithoutEmitterShouldThrowException()
    {
        $mail = $this->createMock('Alchemy\Phrasea\Notification\Mail\MailInterface');

        $mail->expects($this->any())
            ->method('getReceiver')
            ->will($this->returnValue($this->getReceiverMock()));

        $deliverer = new Deliverer($this->getMailerMock(), $this->getEventDispatcherMock(), $this->getEmitterMock());

        try {
            $deliverer->deliver($mail, true);
            $this->fail('Should have raised an exception');
        } catch (LogicException $e) {
            $this->assertEquals('You must provide an emitter for a ReadReceipt', $e->getMessage());
        }
    }

    /**
     * @covers Alchemy\Phrasea\Notification\Deliverer::deliver
     */
    public function testDeliverWithReadReceipt()
    {
        $mail = $this->createMock('Alchemy\Phrasea\Notification\Mail\MailInterface');

        $mail->expects($this->any())
            ->method('getReceiver')
            ->will($this->returnValue($this->getReceiverMock()));

        $name = 'replyto-name';
        $email = 'replyto-email@domain.com';

        $emitter = $this->createMock('Alchemy\Phrasea\Notification\EmitterInterface');
        $emitter->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name));

        $emitter->expects($this->any())
            ->method('getEmail')
            ->will($this->returnValue($email));

        $mail->expects($this->any())
            ->method('getEmitter')
            ->will($this->returnValue($emitter));

        $catchEmail = null;

        $mailer = $this->getMailerMock();
        $mailer->expects($this->once())
            ->method('send')
            ->will($this->returnCallback(function ($email) use (&$catchEmail) {
                $catchEmail = $email;
            }));

        $deliverer = new Deliverer($mailer, $this->getEventDispatcherMock(), $this->getEmitterMock());
        $deliverer->deliver($mail, true);

        /* @var $catchEmail \Swift_Message */
        $this->assertEquals([$email => $name], $catchEmail->getReadReceiptTo());
    }

    /**
     * @covers Alchemy\Phrasea\Notification\Deliverer::deliver
     */
    public function testDeliverWithRightSubject()
    {
        $mail = $this->createMock('Alchemy\Phrasea\Notification\Mail\MailInterface');

        $mail->expects($this->any())
            ->method('getReceiver')
            ->will($this->returnValue($this->getReceiverMock()));

        $subject = 'Un joli message';

        $mail->expects($this->any())
            ->method('getSubject')
            ->will($this->returnValue($subject));

        $catchEmail = null;

        $mailer = $this->getMailerMock();
        $mailer->expects($this->once())
            ->method('send')
            ->will($this->returnCallback(function ($email) use (&$catchEmail) {
                $catchEmail = $email;
            }));

        $deliverer = new Deliverer($mailer, $this->getEventDispatcherMock(), $this->getEmitterMock());
        $deliverer->deliver($mail);

        /* @var $catchEmail \Swift_Message */
        $this->assertEquals(0, strpos($catchEmail->getSubject(), $subject));
    }

    /**
     * @covers Alchemy\Phrasea\Notification\Deliverer::deliver
     */
    public function testDeliverWithRightPrefix()
    {
        $mail = $this->createMock('Alchemy\Phrasea\Notification\Mail\MailInterface');

        $mail->expects($this->any())
            ->method('getReceiver')
            ->will($this->returnValue($this->getReceiverMock()));

        $subject = 'Un joli message';

        $mail->expects($this->any())
            ->method('getSubject')
            ->will($this->returnValue($subject));

        $catchEmail = null;

        $mailer = $this->getMailerMock();
        $mailer->expects($this->once())
            ->method('send')
            ->will($this->returnCallback(function ($email) use (&$catchEmail) {
                $catchEmail = $email;
            }));

        $prefix = 'prefix' . mt_rand();

        $deliverer = new Deliverer($mailer, $this->getEventDispatcherMock(), $this->getEmitterMock(), $prefix);
        $deliverer->deliver($mail);

        /* @var $catchEmail \Swift_Message */
        $this->assertEquals(0, strpos($catchEmail->getSubject(), $prefix));
        $this->assertNotEquals(false, strpos($catchEmail->getSubject(), $subject));
    }

    /**
     * @covers Alchemy\Phrasea\Notification\Deliverer::deliver
     */
    public function testDeliverWithFromHeader()
    {
        $mail = $this->createMock('Alchemy\Phrasea\Notification\Mail\MailInterface');

        $mail->expects($this->any())
            ->method('getReceiver')
            ->will($this->returnValue($this->getReceiverMock()));

        $name = 'emitter-name';
        $email = 'emitter-email@domain.com';

        $emitter = $this->getEmitterMock();
        $emitter->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name));

        $emitter->expects($this->any())
            ->method('getEmail')
            ->will($this->returnValue($email));

        $catchEmail = null;

        $mailer = $this->getMailerMock();
        $mailer->expects($this->once())
            ->method('send')
            ->will($this->returnCallback(function ($email) use (&$catchEmail) {
                $catchEmail = $email;
            }));

        $deliverer = new Deliverer($mailer, $this->getEventDispatcherMock(), $emitter);
        $deliverer->deliver($mail);

        /* @var $catchEmail \Swift_Message */
        $this->assertEquals([$email => $name], $catchEmail->getFrom());
    }

    /**
     * @covers Alchemy\Phrasea\Notification\Deliverer::deliver
     */
    public function testDeliverWithToHeader()
    {
        $mail = $this->createMock('Alchemy\Phrasea\Notification\Mail\MailInterface');

        $name = 'receiver-name';
        $email = 'receiver-email@domain.com';

        $receiver = $this->createMock('Alchemy\Phrasea\Notification\ReceiverInterface');
        $receiver->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name));

        $receiver->expects($this->any())
            ->method('getEmail')
            ->will($this->returnValue($email));

        $mail->expects($this->any())
            ->method('getReceiver')
            ->will($this->returnValue($receiver));

        $catchEmail = null;

        $mailer = $this->getMailerMock();
        $mailer->expects($this->once())
            ->method('send')
            ->will($this->returnCallback(function ($email) use (&$catchEmail) {
                $catchEmail = $email;
            }));

        $deliverer = new Deliverer($mailer, $this->getEventDispatcherMock(), $this->getEmitterMock());
        $deliverer->deliver($mail);

        /* @var $catchEmail \Swift_Message */
        $this->assertEquals([$email => $name], $catchEmail->getTo());
    }

    /**
     * @covers Alchemy\Phrasea\Notification\Deliverer::deliver
     */
    public function testDeliverWithReplyToHeader()
    {
        $mail = $this->createMock('Alchemy\Phrasea\Notification\Mail\MailInterface');

        $name = 'replyto-name';
        $email = 'replyto-email@domain.com';

        $emitter = $this->createMock('Alchemy\Phrasea\Notification\EmitterInterface');
        $emitter->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name));

        $emitter->expects($this->any())
            ->method('getEmail')
            ->will($this->returnValue($email));

        $mail->expects($this->any())
            ->method('getEmitter')
            ->will($this->returnValue($emitter));

        $mail->expects($this->any())
            ->method('getReceiver')
            ->will($this->returnValue($this->getReceiverMock()));

        $catchEmail = null;

        $mailer = $this->getMailerMock();
        $mailer->expects($this->once())
            ->method('send')
            ->will($this->returnCallback(function ($email) use (&$catchEmail) {
                $catchEmail = $email;
            }));

        $deliverer = new Deliverer($mailer, $this->getEventDispatcherMock(), $this->getEmitterMock());
        $deliverer->deliver($mail);

        /* @var $catchEmail \Swift_Message */
        $this->assertEquals([$email => $name], $catchEmail->getReplyTo());
    }

    private function getEventDispatcherMock()
    {
        return $this->createMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
    }

    private function getMailerMock()
    {
        $mailer = $this->getMockBuilder('\Swift_Mailer')
            ->disableOriginalConstructor()
            ->getMock();

        $transport = $this->createMock('Swift_Transport');
        $transport->expects($this->any())
            ->method('isStarted')
            ->willReturn(true);

        $mailer->expects($this->any())
            ->method('getTransport')
            ->willReturn($transport);

        return $mailer;
    }

    private function getEmitterMock()
    {
        return $this->createMock('Alchemy\Phrasea\Notification\EmitterInterface');
    }

    private function getReceiverMock()
    {
        $receiver = $this->createMock('Alchemy\Phrasea\Notification\ReceiverInterface');

        $receiver->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('name'));

        $receiver->expects($this->any())
            ->method('getEmail')
            ->will($this->returnValue('name@domain.com'));

        return $receiver;
    }
}
