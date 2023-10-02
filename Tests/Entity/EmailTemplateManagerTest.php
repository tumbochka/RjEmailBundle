<?php

namespace Rj\EmailBundle\Tests\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Rj\EmailBundle\Entity\EmailTemplate;
use Rj\EmailBundle\Entity\EmailTemplateManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class EmailTemplateManagerTest extends TestCase
{
    protected EntityManager $em;
    protected EntityRepository $repository;
    protected RouterInterface $router;
    protected ContainerInterface $container;


    public function setUp(): void
    {
        $this->em = $this->createMock(EntityManager::class);
        $this->repository = $this->createMock(EntityRepository::class);
        $this->router = $this->createMock(RouterInterface::class);
        $this->container = $this->createMock(ContainerInterface::class);

        $this->em->expects($this->any())
            ->method('getRepository')
            ->willReturn($this->repository);
        $this->container
            ->expects($this->any())
            ->method('get')
            ->with('rj_email.twig')
            ->willReturn($this->createMock(Environment::class));
    }

    public function testFindTemplateByName()
    {
        $emailTemplate = $this->createMock(EmailTemplate::class);

        $criteria = array('name' => 'test');
        $this->repository->expects($this->once())
                ->method('findOneBy')
                ->with($criteria)
                ->willReturn($emailTemplate);

        $manager = new EmailTemplateManager(
            $this->em,
            $this->repository,
            $this->router,
            $this->container,
            "",
            ""
        );
        $result = $manager->findTemplateByName('test');

        $this->assertEquals($result, $emailTemplate);
    }

    public function testGetTemplate()
    {
        $emailTemplate = $this->createMock(EmailTemplate::class);

        $criteria = array('name' => 'template_name');
        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with($criteria)
            ->willReturn($emailTemplate);

        $manager = new EmailTemplateManager(
            $this->em,
            $this->repository,
            $this->router,
            $this->container,
            "",
            ""
        );
        $result = $manager->getTemplate('template_name');

        $this->assertEquals($result, $emailTemplate);
    }

    public function testGetTemplateTranslation()
    {
        $locale = "fr_FR";
        $emailTemplate = new EmailTemplate();
        $emailTemplate->setName('test');
        $emailTemplate->translate('fr')->setBody("Bonjour");

        $manager = new EmailTemplateManager(
            $this->em,
            $this->repository,
            $this->router,
            $this->container,
            "",
            ""
        );
        $result = $manager->getTemplateTranslation($emailTemplate, $locale);
        $this->assertEquals($result->getBody(), "Bonjour");
    }
}
