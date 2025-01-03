<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\Country;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Component\Domain\Domain;

class CountryFacade
{
    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Model\Country\CountryRepository $countryRepository
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     * @param \Shopsys\FrameworkBundle\Model\Country\CountryFactoryInterface $countryFactory
     */
    public function __construct(
        protected readonly EntityManagerInterface $em,
        protected readonly CountryRepository $countryRepository,
        protected readonly Domain $domain,
        protected readonly CountryFactoryInterface $countryFactory,
    ) {
    }

    /**
     * @param int $countryId
     * @return \Shopsys\FrameworkBundle\Model\Country\Country
     */
    public function getById($countryId): Country
    {
        return $this->countryRepository->getById($countryId);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Country\CountryData $countryData
     * @return \Shopsys\FrameworkBundle\Model\Country\Country
     */
    public function create(CountryData $countryData): Country
    {
        $country = $this->countryFactory->create($countryData);
        $this->em->persist($country);
        $this->em->flush();

        return $country;
    }

    /**
     * @param int $countryId
     * @param \Shopsys\FrameworkBundle\Model\Country\CountryData $countryData
     * @return \Shopsys\FrameworkBundle\Model\Country\Country
     */
    public function edit($countryId, CountryData $countryData): Country
    {
        $country = $this->countryRepository->getById($countryId);
        $country->edit($countryData);
        $this->em->flush();

        return $country;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Country\Country[]
     */
    public function getAll(): array
    {
        return $this->countryRepository->getAll();
    }

    /**
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Country\Country[]
     */
    public function getAllEnabledOnDomain(int $domainId): array
    {
        $localeByDomain = $this->domain->getDomainConfigById($domainId)->getLocale();

        return $this->countryRepository->getAllEnabledByDomainIdWithLocale($domainId, $localeByDomain);
    }

    /**
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Country\Country[]
     */
    public function getAllOnDomain(int $domainId): array
    {
        $localeByDomain = $this->domain->getDomainConfigById($domainId)->getLocale();

        return $this->countryRepository->getAllByDomainIdWithLocale($domainId, $localeByDomain);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Country\Country[]
     */
    public function getAllEnabledOnCurrentDomain(): array
    {
        return $this->countryRepository->getAllEnabledByDomainIdWithLocale(
            $this->domain->getId(),
            $this->domain->getLocale(),
        );
    }

    /**
     * @param string $countryCode
     * @return \Shopsys\FrameworkBundle\Model\Country\Country|null
     */
    public function findByCode(string $countryCode): ?Country
    {
        return $this->countryRepository->findByCode($countryCode);
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->countryRepository->getCount();
    }
}
