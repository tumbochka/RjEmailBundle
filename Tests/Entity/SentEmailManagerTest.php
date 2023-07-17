<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Rj\EmailBundle\Entity\SentEmail;
use Rj\EmailBundle\Entity\SentEmailManager;
use Rj\EmailBundle\Swift\Message;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

class SentEmailManagerTest extends TestCase
{
    protected $em;
    protected $repository;
    protected $twig;

    public function setUp(): void
    {
        $this->em = $this->createMock(EntityManager::class);
        $this->repository = $this->createMock(EntityRepository::class);
        $this->twig = $this->createMock(Environment::class);

        $this->em->expects($this->any())
            ->method('getRepository')
            ->willReturn($this->repository);
    }

    public function testFindSentEmailByUniqueId()
    {
        $sentEmail = $this->createMock(SentEmail::class);
        $criteria = array('uniqueId' => 'uniqueid');

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with($criteria)
            ->willReturn($sentEmail);

        $manager = new SentEmailManager($this->em, $this->repository);
        $return = $manager->findSentEmailByUniqueId('uniqueid');

        $this->assertEquals($sentEmail, $return);
    }

    public function testCreateSentEmail()
    {
        $message = new Message('subject', 'body', 'text/plain', 'utf-8');
        $manager = new SentEmailManager($this->em, $this->repository);
        $return = $manager->createSentEmail($message);

        $this->assertEquals($return->getSubject(), 'subject');
    }
}
