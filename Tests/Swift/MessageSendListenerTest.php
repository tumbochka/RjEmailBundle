<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Rj\EmailBundle\Swift\Events\SendListener\MessageSendListener;
use Rj\EmailBundle\Entity\SentEmailManager;
use Rj\EmailBundle\Swift\Message;
use Rj\EmailBundle\Entity\SentEmail;
use PHPUnit\Framework\TestCase;

class MessageSendListenerTest extends TestCase
{
    protected $em;
    protected $repository;
    protected $manager;

    public function setUp(): void
    {
        $this->em = $this->createMock(EntityManager::class);
        $this->repository = $this->createMock(EntityRepository::class);

        $this->manager = new SentEmailManager($this->em, $this->repository);
    }

    /**
     * @test
     */
    public function testSendPerformed()
    {
        $message = new Message;
        $message
            ->setFrom(array('jeremy@test.com' => 'Jeremy'))
            ->setTo(array('jeremy@test.com' => 'Jeremy'))
            ->setSubject('subject')
            ->setBody('body')
            ->generateId();

        $evt = $this->_createSendEvent();
        $evt->expects($this->any())
            ->method('getMessage')
            ->willReturn($message);

        //test the manager is called only once
        $manager = $this->createMock(SentEmailManager::class);

        $sentMessage = SentEmail::fromMessage($message);
        $manager->expects($this->once())
            ->method('createSentEmail')
            ->willReturn($sentMessage);

        $plugin = new MessageSendListener($manager);
        $plugin->sendPerformed($evt);
        $plugin->sendPerformed($evt);
    }

    private function _createSendEvent()
    {
        return $this->createMock(\Swift_Events_SendEvent::class);
    }
}
