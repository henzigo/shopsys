<?php

declare(strict_types=1);

namespace Tests\FrameworkBundle\Test;

use Metadata\MetadataFactory;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Domain\Exception\NoDomainSelectedException;
use Shopsys\FrameworkBundle\Model\Administration\AdministrationFacade;
use Shopsys\FrameworkBundle\Model\Localization\Localization;
use Shopsys\FrameworkBundle\Model\Localization\TranslatableListener;

class TestTranslatableListener extends TranslatableListener
{
    /**
     * @param \Metadata\MetadataFactory $factory
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     * @param \Shopsys\FrameworkBundle\Model\Administration\AdministrationFacade $administrationFacade
     * @param \Shopsys\FrameworkBundle\Model\Localization\Localization $localization
     */
    public function __construct(
        MetadataFactory $factory,
        protected readonly Domain $domain,
        protected readonly AdministrationFacade $administrationFacade,
        protected readonly Localization $localization,
    ) {
        parent::__construct($factory);
    }

    /**
     * @return string
     */
    public function getCurrentLocale()
    {
        if ($this->administrationFacade->isInAdmin()) {
            return $this->localization->getAdminLocale();
        }

        try {
            return $this->domain->getLocale();
        } catch (NoDomainSelectedException) {
            return $this->getFirstDomainLocale();
        }
    }

    /**
     * @return string
     */
    protected function getFirstDomainLocale(): string
    {
        return $this->domain->getDomainConfigById(Domain::FIRST_DOMAIN_ID)->getLocale();
    }
}
