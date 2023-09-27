<?php

namespace Rj\EmailBundle\Email\Events\SendListener;

use Rj\EmailBundle\Entity\SentEmailManager;
use Rj\EmailBundle\Email\Message;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Event\MessageEvent;

class MessageSendListener implements EventSubscriberInterface
{
    protected $manager;
    protected $sentUniqueIds;

    public function __construct(SentEmailManager $manager)
    {
        $this->manager = $manager;
        $this->sentUniqueIds = array();
    }

    /**
     * Invoked immediately after the Message is sent.
     */
    public function onMessage(SentMessageEvent $evt)
    {
        $message = $evt->getMessage();

        if (!$message instanceof Message) {
            return;
        }

        $id = $message->getUniqueId();

        // The sendPerformed event may be triggered multiple times by
        // multiple transports (e.g. the Spool and then the real transport)

        if (isset($this->sentUniqueIds[$id])) {
            return;
        }

        $this->sentUniqueIds[$id] = true;

        $sentEmail = $this->manager->createSentEmail($message);
        $this->manager->updateSentEmail($sentEmail);
    }

    public static function getSubscribedEvents()
    {
        return [
            MessageEvent::class => 'onMessage'
        ];
    }
}
