<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Component\Domain;

use DateTimeZone;
use Shopsys\FormTypesBundle\Domain\DomainIdsProviderInterface;
use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Component\Domain\Exception\InvalidDomainIdException;
use Shopsys\FrameworkBundle\Component\Domain\Exception\NoDomainSelectedException;
use Shopsys\FrameworkBundle\Component\Domain\Exception\UnableToResolveDomainException;
use Shopsys\FrameworkBundle\Component\Setting\Exception\SettingValueNotFoundException;
use Shopsys\FrameworkBundle\Component\Setting\Setting;
use Shopsys\FrameworkBundle\Model\Administrator\AdministratorFacade;
use Symfony\Component\HttpFoundation\Request;

class Domain implements DomainIdsProviderInterface
{
    public const FIRST_DOMAIN_ID = 1;
    public const SECOND_DOMAIN_ID = 2;
    public const THIRD_DOMAIN_ID = 3;
    public const MAIN_ADMIN_DOMAIN_ID = 1;

    protected ?DomainConfig $currentDomainConfig = null;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig[] $domainConfigs
     * @param \Shopsys\FrameworkBundle\Component\Setting\Setting $setting
     * @param \Shopsys\FrameworkBundle\Model\Administrator\AdministratorFacade $administratorFacade
     */
    public function __construct(
        protected readonly array $domainConfigs,
        protected readonly Setting $setting,
        protected readonly AdministratorFacade $administratorFacade,
    ) {
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->getCurrentDomainConfig()->getId();
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->getCurrentDomainConfig()->getLocale();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getCurrentDomainConfig()->getName();
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->getCurrentDomainConfig()->getUrl();
    }

    /**
     * @return string|null
     */
    public function getDesignId()
    {
        return $this->getCurrentDomainConfig()->getDesignId();
    }

    /**
     * @return bool
     */
    public function isHttps()
    {
        return $this->getCurrentDomainConfig()->isHttps();
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig[]
     */
    public function getAll()
    {
        $domainConfigsWithDataCreated = [];

        $this->setting->initAllDomainsSettings();

        foreach ($this->domainConfigs as $domainConfig) {
            $domainId = $domainConfig->getId();

            try {
                $this->setting->getForDomain(Setting::DOMAIN_DATA_CREATED, $domainId);
                $domainConfigsWithDataCreated[$domainId] = $domainConfig;
            } catch (SettingValueNotFoundException $ex) {
                continue;
            }
        }

        return $domainConfigsWithDataCreated;
    }

    /**
     * @return int[]
     */
    public function getAllIds()
    {
        $ids = [];

        foreach ($this->getAll() as $domainConfig) {
            $ids[] = $domainConfig->getId();
        }

        return $ids;
    }

    /**
     * @return string[]
     */
    public function getAllLocales(): array
    {
        $locales = [];

        foreach ($this->getAll() as $domainConfig) {
            $locale = $domainConfig->getLocale();
            $locales[$locale] = $locale;
        }

        return $locales;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig[]
     */
    public function getAllIncludingDomainConfigsWithoutDataCreated()
    {
        return $this->domainConfigs;
    }

    /**
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig
     */
    public function getDomainConfigById($domainId)
    {
        foreach ($this->domainConfigs as $domainConfig) {
            if ($domainId === $domainConfig->getId()) {
                return $domainConfig;
            }
        }

        throw new InvalidDomainIdException();
    }

    /**
     * @param int $domainId
     */
    public function switchDomainById($domainId)
    {
        $this->currentDomainConfig = $this->getDomainConfigById($domainId);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function switchDomainByRequest(Request $request)
    {
        $url = $request->getSchemeAndHttpHost();

        foreach ($this->domainConfigs as $domainConfig) {
            if ($domainConfig->getUrl() === $url) {
                $this->currentDomainConfig = $domainConfig;

                return;
            }
        }

        throw new UnableToResolveDomainException($url);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig
     */
    public function getCurrentDomainConfig()
    {
        if ($this->currentDomainConfig === null) {
            throw new NoDomainSelectedException();
        }

        return $this->currentDomainConfig;
    }

    /**
     * @return bool
     */
    public function isMultidomain()
    {
        return count($this->getAll()) > 1;
    }

    /**
     * @return \DateTimeZone
     */
    public function getDateTimeZone(): DateTimeZone
    {
        return $this->getCurrentDomainConfig()->getDateTimeZone();
    }

    /**
     * @return bool
     */
    public function isB2b(): bool
    {
        return $this->getCurrentDomainConfig()->isB2b();
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig|null
     */
    public function findFirstB2bDomain(): ?DomainConfig
    {
        foreach ($this->getAll() as $domainConfig) {
            if ($domainConfig->isB2b()) {
                return $domainConfig;
            }
        }

        return null;
    }

    /**
     * @return int[]
     */
    public function getAdminEnabledDomainIds(): array
    {
        $selectedDomainIds = $this->administratorFacade->getCurrentlyLoggedAdministrator()->getDisplayOnlyDomainIds();

        return count($selectedDomainIds) > 0 ? $selectedDomainIds : $this->getAllIds();
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig[]
     */
    public function getAdminEnabledDomains(): array
    {
        $domains = [];

        foreach ($this->getAdminEnabledDomainIds() as $selectedDomainId) {
            $domains[$selectedDomainId] = $this->getDomainConfigById($selectedDomainId);
        }

        return $domains;
    }

    /**
     * @return bool
     */
    public function hasAdminAllDomainsEnabled(): bool
    {
        return count($this->getAdminEnabledDomainIds()) === count($this->getAllIds());
    }
}
