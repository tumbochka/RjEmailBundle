<?php

namespace Rj\EmailBundle\Email;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;

class Message extends Email
{
    protected $uniqueId;

    public function __construct($subject = null, $body = null, $contentType = null, $charset = null)
    {
        parent::__construct($subject, $body, $contentType, $charset);
        $this->uniqueId = $this->generateUniqueId();
    }

    public static function newInstance($subject = null, $body = null, $contentType = null, $charset = null)
    {
        return new static($subject, $body, $contentType, $charset);
    }

    public function getUniqueId()
    {
        return $this->uniqueId;
    }

    public static function fromArray($parameters)
    {
        $charset = isset($parameters['charset']) ?? null;
        $message = new static();
        if (isset($parameters['subject'])) {
            $message->subject($parameters['subject']);
        }
        if (isset($parameters['body'])) {
            $message->addPart(new DataPart($parameters['body'], null, 'text/plain', $charset));
        }
        if (isset($parameters['bodyHtml'])) {
            $message->addPart(new DataPart($parameters['bodyHtml'], null, 'text/html', $charset));
        }
        if (isset($parameters['fromEmail'])) {
            $message->from($parameters['fromEmail'], isset($parameters['fromName'])? $parameters['fromName']: null);
        }

        return $message;
    }

    protected function generateUniqueId()
    {
        return bin2hex(pack('d', microtime(true))); //. Random::generateToken();
    }
}
