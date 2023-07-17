<?php

namespace Rj\EmailBundle\Entity;

use Doctrine\ORM\EntityManager;
use Rj\EmailBundle\Swift\Message;

class SentEmailManager
{
    protected $em;
    protected $repository;
    protected $class;

    public function __construct(EntityManager $em, $class)
    {
        $this->em = $em;
        $this->repository = $em->getRepository($class);
        $metadata = $em->getClassMetadata($class);
        $this->class = $metadata->name;
    }

    public function findSentEmailByUniqueId($id)
    {
        return $this->repository->findOneBy(array(
            'uniqueId' => $id,
        ));
    }

    public function createSentEmail(Message $message)
    {
        return SentEmail::fromMessage($message);
    }

    public function updateSentEmail(SentEmail $sentEmail, $andFlush = true)
    {
        $this->em->persist($sentEmail);

        if ($andFlush) {
            $this->em->flush();
        }
    }
}
