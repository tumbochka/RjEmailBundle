<?php

namespace Rj\EmailBundle\Tests\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Rj\EmailBundle\Entity\EmailTemplate;
use Rj\EmailBundle\Entity\EmailTemplateManager;
use Twig\Environment;

class EmailTemplateManagerTest extends TestCase
{
    protected $em;
    protected $repository;
    protected $twig;

    public function setUp(): void
    {
        $this->em = $this->createMock(EntityManager::class);
        $this->repository = $this->getMockBuilder(EntityRepository::class);
        $this->twig = $this->createMock(Environment::class);

        $this->em->expects($this->any())
            ->method('getRepository')
            ->willReturn($this->repository);
    }

    public function testFindTemplateByName()
    {
        $emailTemplate = $this->createMock(EmailTemplate::class);

        $criteria = array('name' => 'test');
        $this->repository->expects($this->once())
                ->method('findOneBy')
                ->with($criteria)
                ->willReturn($emailTemplate);

        $manager = new EmailTemplateManager($this->em, $this->repository, $this->twig);
        $result = $manager->findTemplateByName('test');

        $this->assertEquals($result, $emailTemplate);
    }

    public function testRenderTemplate()
    {
        $locale = "en_US";
        $emailTemplate = new EmailTemplate();
        $emailTemplate->setName('test');
        $emailTemplate->translate('en')->setBody("Hello {#name}");
        $this->em->persist($emailTemplate);
        $this->em->flush();

        $manager = new EmailTemplateManager($this->em, $this->repository, $this->twig);
        $html = $manager->renderTemplate('test', $locale, 'body', array('name' => 'Jeremy'));
        //$this->assertTrue(is_array($html));
        //$this->assertEquals($html->getBody(), 'Hello Jeremy');
    }

    public function testGetTemplate()
    {
        $emailTemplate = $this->createMock('FOS\EmailBundle\Entity\EmailTemplate');

        $criteria = array('name' => 'template_name');
        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with($criteria)
            ->willReturn($emailTemplate);

        $manager = new EmailTemplateManager($this->em, $this->repository, $this->twig);
        $result = $manager->getTemplate('template_name');

        $this->assertEquals($result, $emailTemplate);
    }

    public function testGetTemplateTranslation()
    {
        $locale = "fr_FR";
        $emailTemplate = new EmailTemplate();
        $emailTemplate->setName('test');
        $emailTemplate->translate('fr')->setBody("Bonjour");

        $manager = new EmailTemplateManager($this->em, $this->repository, $this->twig);
        $result = $manager->getTemplateTranslation($emailTemplate, $locale);
        $this->assertEquals($result->getBody(), "Bonjour");
    }
}
