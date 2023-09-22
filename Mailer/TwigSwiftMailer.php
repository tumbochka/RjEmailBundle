<?php

namespace Rj\EmailBundle\Mailer;

use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Routing\RouterInterface;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Mailer\MailerInterface;
use Rj\EmailBundle\Entity\EmailTemplateManager;
use Rj\EmailBundle\Email\Message;

/**
 * @author Jeremy Marc <jeremy.marc@me.com>
 */
class TwigSwiftMailer implements MailerInterface
{
    protected $mailer;
    protected $router;
    protected $parameters;
    protected $manager;

    public function __construct($mailer, RouterInterface $router, EmailTemplateManager $manager, array $parameters)
    {
        $this->mailer     = $mailer;
        $this->router     = $router;
        $this->manager    = $manager;
        $this->parameters = $parameters;
    }

    public function sendConfirmationEmailMessage(UserInterface $user)
    {
        $template = $this->parameters['template']['confirmation'];
        $url = $this->router->generate('fos_user_registration_confirm', array('token' => $user->getConfirmationToken()), true);

        $message = Message::newInstance();
        $rendered = $this->manager->renderEmail($template, null, array(
            'username' => $user->getUsername(),
            'confirmationUrl' =>  $url,
        ), $message);

        $this->sendEmailMessage($rendered, $this->parameters['from_email']['confirmation'], $user->getEmail(), $message);
    }

    public function sendResettingEmailMessage(UserInterface $user)
    {
        $template = $this->parameters['template']['resetting'];
        $url = $this->router->generate('fos_user_resetting_reset', array('token' => $user->getConfirmationToken()), true);

        $message = Message::newInstance();
        $rendered = $this->manager->renderEmail($template, null, array(
            'username' => $user->getUsername(),
            'confirmationUrl' => $url,
        ), $message);

        $this->sendEmailMessage($rendered, $this->parameters['from_email']['resetting'], $user->getEmail(), $message);
    }

    protected function sendEmailMessage($renderedTemplate, $fromEmail, $toEmail, $message = null)
    {
        if (!$message) {
            $message = new Message();
        }

        $message
            ->subject($renderedTemplate['subject'])
            ->from($fromEmail)
            ->to($toEmail)
        ;

        if (array_key_exists('body', $renderedTemplate)) {
            $message->addPart(new DataPart($renderedTemplate['body'], null, 'text/plain'));
            if (array_key_exists('bodyHtml', $renderedTemplate)) {
                $message->addPart(new DataPart($renderedTemplate['bodyHtml'], null, 'text/html'));
            }
        } else if (array_key_exists('bodyHtml', $renderedTemplate)) {
            $message->addPart(new DataPart($renderedTemplate['bodyHtml'], null, 'text/html'));
        }

        $this->mailer->send($message);
    }
}
