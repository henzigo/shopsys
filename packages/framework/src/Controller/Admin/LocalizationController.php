<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Shopsys\FrameworkBundle\Component\Translation\Translator;
use Shopsys\FrameworkBundle\Model\Administrator\AdministratorLocalizationFacade;
use Shopsys\FrameworkBundle\Model\Localization\Exception\AdminLocaleNotFoundException;
use Shopsys\FrameworkBundle\Model\Localization\Localization;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LocalizationController extends AdminBaseController
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Administrator\AdministratorLocalizationFacade $administratorLocalizationFacade
     * @param \Shopsys\FrameworkBundle\Model\Localization\Localization $localization
     */
    public function __construct(
        protected readonly AdministratorLocalizationFacade $administratorLocalizationFacade,
        protected readonly Localization $localization,
    ) {
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $locale
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route(path: '/administrator/select-locale/{locale}')]
    public function selectLocaleAction(Request $request, string $locale): Response
    {
        $redirectUrl = $request->headers->get('referer', $this->generateUrl('admin_default_dashboard'));

        try {
            $administrator = $this->getCurrentAdministrator();
            $this->administratorLocalizationFacade->setSelectedLocale($administrator, $locale);
            $this->addSuccessFlash(t('Administration localization was changed to "%locale%"', ['%locale%' => $this->localization->getLanguageName($locale, $locale)], Translator::DEFAULT_TRANSLATION_DOMAIN, $locale));
        } catch (AdminLocaleNotFoundException $exception) {
            $this->addErrorFlash(t('Locale "%locale%" is not supported. You can choose only from the following locales: "%supportedLocales%".', [
                '%locale%' => $locale,
                '%supportedLocales%' => implode('", "', $exception->getPossibleLocales()),
            ]));
        }

        return $this->redirect($redirectUrl);
    }
}
