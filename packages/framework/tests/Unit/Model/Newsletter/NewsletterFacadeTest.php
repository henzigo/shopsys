<?php

declare(strict_types=1);

namespace Tests\FrameworkBundle\Unit\Model\Newsletter;

use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;
use Shopsys\FrameworkBundle\Model\Newsletter\NewsletterFacade;
use Shopsys\FrameworkBundle\Model\Newsletter\NewsletterRepository;
use Shopsys\FrameworkBundle\Model\Newsletter\NewsletterSubscriber;
use Shopsys\FrameworkBundle\Model\Newsletter\NewsletterSubscriberFactory;

class NewsletterFacadeTest extends TestCase
{
    private EntityManager|MockObject $em;

    private NewsletterRepository|MockObject $newsletterRepository;

    private NewsletterFacade $newsletterFacade;

    protected function setUp(): void
    {
        parent::setUp();

        $this->em = $this->createMock(EntityManager::class);
        $this->newsletterRepository = $this->createMock(NewsletterRepository::class);
        $this->newsletterFacade = new NewsletterFacade(
            $this->em,
            $this->newsletterRepository,
            new NewsletterSubscriberFactory(new EntityNameResolver([])),
        );
    }

    public function testAddSubscribedEmail(): void
    {
        $this->em
            ->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(NewsletterSubscriber::class));

        $this->em
            ->expects($this->once())
            ->method('flush');

        $this->newsletterFacade->addSubscribedEmailIfNotExists('no-reply@shopsys.com', 1);
    }

    public function testDeleteSubscribedEmail(): void
    {
        $newsletterSubscriberInstance = $this->createMock(NewsletterSubscriber::class);

        $this->newsletterRepository
            ->expects($this->any())
            ->method('findNewsletterSubscriberById')
            ->willReturn($newsletterSubscriberInstance);

        $this->em
            ->expects($this->once())
            ->method('remove')
            ->with($this->isInstanceOf(NewsletterSubscriber::class));

        $this->em
            ->expects($this->once())
            ->method('flush');

        $this->newsletterFacade->delete($newsletterSubscriberInstance);
    }
}
