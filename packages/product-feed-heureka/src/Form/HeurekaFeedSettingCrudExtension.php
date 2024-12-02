<?php

declare(strict_types=1);

namespace Shopsys\ProductFeed\HeurekaBundle\Form;

use Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade;
use Shopsys\FrameworkBundle\Component\Setting\Setting;
use Shopsys\Plugin\PluginCrudExtensionInterface;
use Shopsys\ProductFeed\HeurekaBundle\Model\Setting\HeurekaFeedSettingEnum;

class HeurekaFeedSettingCrudExtension implements PluginCrudExtensionInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Component\Setting\Setting $setting
     * @param \Shopsys\ProductFeed\HeurekaBundle\Model\Setting\HeurekaFeedSettingEnum $heurekaFeedSettingEnum
     * @param \Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade $adminDomainTabsFacade
     */
    public function __construct(
        protected readonly Setting $setting,
        protected readonly HeurekaFeedSettingEnum $heurekaFeedSettingEnum,
        protected readonly AdminDomainTabsFacade $adminDomainTabsFacade,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getFormTypeClass(): string
    {
        return HeurekaFeedSettingFormType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormLabel(): string
    {
        return t('Heureka XML feed settings');
    }

    /**
     * {@inheritdoc}
     */
    public function getData($id): array
    {
        $data = [];

        foreach ($this->heurekaFeedSettingEnum->getAllCases() as $settingName) {
            $data[$settingName] = $this->setting->getForDomain($settingName, $this->adminDomainTabsFacade->getSelectedDomainId());
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function saveData($id, $data): void
    {
        foreach ($data as $name => $value) {
            $this->setting->setForDomain($name, $value, $this->adminDomainTabsFacade->getSelectedDomainId());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeData($id): void
    {
        foreach ($this->heurekaFeedSettingEnum->getAllCases() as $settingName) {
            $this->setting->deleteByName($settingName);
        }
    }
}
