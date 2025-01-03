<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Form\Admin\Country\CountryFormType;
use Shopsys\FrameworkBundle\Model\AdminNavigation\BreadcrumbOverrider;
use Shopsys\FrameworkBundle\Model\Country\CountryDataFactory;
use Shopsys\FrameworkBundle\Model\Country\CountryFacade;
use Shopsys\FrameworkBundle\Model\Country\Grid\CountryGridFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CountryController extends AdminBaseController
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Country\Grid\CountryGridFactory $countryGridFactory
     * @param \Shopsys\FrameworkBundle\Model\Country\CountryDataFactory $countryDataFactory
     * @param \Shopsys\FrameworkBundle\Model\Country\CountryFacade $countryFacade
     * @param \Shopsys\FrameworkBundle\Model\AdminNavigation\BreadcrumbOverrider $breadcrumbOverrider
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     */
    public function __construct(
        protected readonly CountryGridFactory $countryGridFactory,
        protected readonly CountryDataFactory $countryDataFactory,
        protected readonly CountryFacade $countryFacade,
        protected readonly BreadcrumbOverrider $breadcrumbOverrider,
        protected readonly Domain $domain,
    ) {
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route(path: '/country/list/')]
    public function listAction(): Response
    {
        $grid = $this->countryGridFactory->create();

        return $this->render('@ShopsysFramework/Admin/Content/Country/list.html.twig', [
            'gridView' => $grid->createView(),
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route(path: '/country/edit/{id}', requirements: ['id' => '\d+'])]
    public function editAction(Request $request, int $id): Response
    {
        $country = $this->countryFacade->getById($id);
        $countryData = $this->countryDataFactory->createFromCountry($country);

        $form = $this->createForm(CountryFormType::class, $countryData, ['country' => $country]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->countryFacade->edit($id, $countryData);

            $this
                ->addSuccessFlashTwig(
                    t('Country <strong><a href="{{ url }}">{{ name }}</a></strong> modified'),
                    [
                        'name' => $country->getName(),
                        'url' => $this->generateUrl('admin_country_edit', ['id' => $country->getId()]),
                    ],
                );

            return $this->redirectToRoute('admin_country_list');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addErrorFlashTwig(t('Please check the correctness of all data filled.'));
        }

        $this->breadcrumbOverrider->overrideLastItem(t('Editing country - %name%', ['%name%' => $country->getName()]));

        return $this->render('@ShopsysFramework/Admin/Content/Country/edit.html.twig', [
            'form' => $form->createView(),
            'country' => $country,
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route(path: '/country/new/')]
    public function newAction(Request $request): Response
    {
        $countryData = $this->countryDataFactory->create();

        $form = $this->createForm(CountryFormType::class, $countryData, ['country' => null]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->domain->hasAdminAllDomainsEnabled()) {
                $this->addErrorFlash(t('Creating a record requires all domains to be enabled as domain-specific fields cannot be empty. If you want to proceed, select all domains in the Domain filter in the header first.'));

                return $this->redirectToRoute('admin_country_new');
            }

            $country = $this->countryFacade->create($countryData);

            $this
                ->addSuccessFlashTwig(
                    t('Country <strong><a href="{{ url }}">{{ name }}</a></strong> created'),
                    [
                        'name' => $country->getName(),
                        'url' => $this->generateUrl('admin_country_edit', ['id' => $country->getId()]),
                    ],
                );

            return $this->redirectToRoute('admin_country_list');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addErrorFlashTwig(t('Please check the correctness of all data filled.'));
        }

        return $this->render('@ShopsysFramework/Admin/Content/Country/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
