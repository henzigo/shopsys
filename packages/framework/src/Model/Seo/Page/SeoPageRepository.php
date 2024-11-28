<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\Seo\Page;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Shopsys\FrameworkBundle\Model\Seo\Page\Exception\SeoPageNotFoundException;

class SeoPageRepository
{
    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     */
    public function __construct(
        protected readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @param int $seoPageId
     * @return \Shopsys\FrameworkBundle\Model\Seo\Page\SeoPage
     */
    public function getById(int $seoPageId): SeoPage
    {
        /** @var \Shopsys\FrameworkBundle\Model\Seo\Page\SeoPage|null $seoPage */
        $seoPage = $this->getSeoPageRepository()->find($seoPageId);

        if ($seoPage === null) {
            $message = sprintf('SeoPage with ID %d not found.', $seoPageId);

            throw new SeoPageNotFoundException($message);
        }

        return $seoPage;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Seo\Page\SeoPage[]
     */
    public function getAll(): array
    {
        return $this->getSeoPageRepository()->findAll();
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getAllQueryBuilder(): QueryBuilder
    {
        return $this->getSeoPageRepository()->createQueryBuilder('sp')
            ->select('sp, spd')
            ->join('sp.domains', 'spd');
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getSeoPageRepository(): EntityRepository
    {
        return $this->em->getRepository(SeoPage::class);
    }

    /**
     * @param int $domainId
     * @param string $pageSlug
     * @return \Shopsys\FrameworkBundle\Model\Seo\Page\SeoPage
     */
    public function getByDomainIdAndPageSlug(int $domainId, string $pageSlug): SeoPage
    {
        $seoPage = $this->findByDomainIdAndPageSlug($domainId, $pageSlug);

        if ($seoPage === null) {
            $message = sprintf('SeoPage with slug \'%s\' not found.', $pageSlug);

            throw new SeoPageNotFoundException($message);
        }

        return $seoPage;
    }

    /**
     * @param int $domainId
     * @param string $pageSlug
     * @return \Shopsys\FrameworkBundle\Model\Seo\Page\SeoPage|null
     */
    public function findByDomainIdAndPageSlug(int $domainId, string $pageSlug): ?SeoPage
    {
        $seoPage = $this->getSeoPageRepository()
            ->createQueryBuilder('sp')
            ->join('sp.domains', 'spd')
            ->where('spd.domainId = :domainId')
            ->andWhere('spd.pageSlug = :pageSlug')
            ->setParameter('domainId', $domainId)
            ->setParameter('pageSlug', $pageSlug)
            ->getQuery()
            ->getResult();

        return count($seoPage) === 0 ? null : reset($seoPage);
    }
}
