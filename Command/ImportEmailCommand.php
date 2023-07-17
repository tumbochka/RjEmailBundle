<?php

namespace Rj\EmailBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Rj\EmailBundle\Entity\EmailTemplate;

/**
 * @author Jeremy Marc <jeremy.marc@me.com>
 */
#[AsCommand(name: 'rj:email:import-fosuserbundle', description: 'Import FOSUserbundle emails into EmailTemplate')]
class ImportEmailCommand extends Command
{
    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $emails = array('resetting', 'registration');
        $langs = array('en', 'fr', 'de');

        $container = $this->getApplication()->getKernel()->getContainer();
        $em = $container->get('doctrine')->getEntityManager();
        $translator = $container->get('translator');
        $session = $container->get('session');

        foreach($emails as $email) {
            $template = new EmailTemplate();
            $template->setName($email);

            foreach($langs as $lang) {
                $subject = $this->replaceTranslatorVariables($translator->trans($email . '.email.subject', array(), 'FOSUserBundle', $lang));
                $body = $this->replaceTranslatorVariables($translator->trans($email . '.email.message', array(), 'FOSUserBundle', $lang));

                $template->translate($lang)
                    ->setSubject($subject)
                    ->setBody($body);
            }

            $em->persist($template);
            $em->flush();
        }

        $output->writeln("All email templates have been imported.");
    }

    private function replaceTranslatorVariables($str)
    {
        //todo: REGEX /%([^%]+)%/
        $str = str_replace('%username%', '{{ username }}', $str);
        $str = str_replace('%confirmationUrl%', '{{ confirmationUrl }}', $str);

        return $str;
    }
}
