<?php

use Rj\EmailBundle\Entity\EmailTemplateManager;
use Rj\EmailBundle\Twig\EmailTemplateLoader;
use Rj\EmailBundle\Entity\EmailTemplate;
use PHPUnit\Framework\TestCase;
use Twig\Error\LoaderError;
use Twig\Loader\LoaderInterface;

class EmailTemplateLoaderTest extends TestCase
{
    protected $manager;
    protected $parent;

    public function setUp(): void
    {
        $this->manager = $this->createMock(EmailTemplateManager::class);
        $this->parent = $this->createMock(LoaderInterface::class);
    }

    /**
     */
    public function shouldGenerateExceptionWithInvalidTemplateName()
    {
        $loader = new EmailTemplateLoader($this->parent, $this->manager);
        $source = $loader->getSource('invalid template');
        //* @test
        //* @expectedException Exception
        //* @expectedExceptionMessage invalid template name
    }

    /**
     * @test
     */
    public function shouldGenerateTwigException()
    {
        $this->expectException(LoaderError::class);
        $this->expectExceptionMessage('Unable to find email template name');

        $loader = new EmailTemplateLoader($this->parent, $this->manager);
        $source = $loader->getSource('email_template:name:fr_FR:body');
    }

    /**
     * @test
     */
    public function shouldGenerateInvalidTemplateException()
    {
        $this->expectException(LoaderError::class);
        $this->expectExceptionMessage('Invalid template part invalid');

        $template = new EmailTemplate;
        $template->setName('name');
        $template->translate('fr')->setBody('body');

        $this->manager->expects($this->once())
            ->method('getTemplate')
            ->willReturn($template);

        $this->manager->expects($this->once())
            ->method('getTemplateTranslation')
            ->willReturn($template->translate('fr'));


        $loader = new EmailTemplateLoader($this->parent, $this->manager);
        $source = $loader->getSource('email_template:name:fr_FR:invalid');
    }

    /**
     * @test
     */
    public function shouldGetSource()
    {
        $template = new EmailTemplate;
        $template->setName('name');
        $template->translate('fr')->setBody('body');

        $this->manager->expects($this->once())
            ->method('getTemplate')
            ->willReturn($template);

        $this->manager->expects($this->once())
            ->method('getTemplateTranslation')
            ->willReturn($template->translate('fr'));


        $loader = new EmailTemplateLoader($this->parent, $this->manager);
        $source = $loader->getSource('email_template:name:fr_FR:body');
        $this->assertEquals($source, 'body');
    }
}
