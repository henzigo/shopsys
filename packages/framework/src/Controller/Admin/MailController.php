<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade;
use Shopsys\FrameworkBundle\Component\FlashMessage\ErrorExtractor;
use Shopsys\FrameworkBundle\Form\Admin\Mail\MailSettingFormType;
use Shopsys\FrameworkBundle\Form\Admin\Mail\MailTemplateFormType;
use Shopsys\FrameworkBundle\Form\Admin\Mail\MailTemplateSendFormType;
use Shopsys\FrameworkBundle\Model\AdminNavigation\BreadcrumbOverrider;
use Shopsys\FrameworkBundle\Model\Mail\Grid\MailTemplateGridFactory;
use Shopsys\FrameworkBundle\Model\Mail\MailTemplateConfiguration;
use Shopsys\FrameworkBundle\Model\Mail\MailTemplateDataFactory;
use Shopsys\FrameworkBundle\Model\Mail\MailTemplateFacade;
use Shopsys\FrameworkBundle\Model\Mail\MailTemplateSender\MailTemplateSenderFacade;
use Shopsys\FrameworkBundle\Model\Mail\Setting\MailSettingFacade;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class MailController extends AdminBaseController
{
    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade $adminDomainTabsFacade
     * @param \Shopsys\FrameworkBundle\Model\Mail\MailTemplateFacade $mailTemplateFacade
     * @param \Shopsys\FrameworkBundle\Model\Mail\Setting\MailSettingFacade $mailSettingFacade
     * @param \Shopsys\FrameworkBundle\Model\AdminNavigation\BreadcrumbOverrider $breadcrumbOverrider
     * @param \Shopsys\FrameworkBundle\Model\Mail\Grid\MailTemplateGridFactory $mailTemplateGridFactory
     * @param \Shopsys\FrameworkBundle\Model\Mail\MailTemplateConfiguration $mailTemplateConfiguration
     * @param \Shopsys\FrameworkBundle\Model\Mail\MailTemplateDataFactory $mailTemplateDataFactory
     * @param \Shopsys\FrameworkBundle\Model\Mail\MailTemplateSender\MailTemplateSenderFacade $mailTemplateSenderFacade
     * @param \Shopsys\FrameworkBundle\Component\FlashMessage\ErrorExtractor $errorExtractor
     */
    public function __construct(
        protected readonly AdminDomainTabsFacade $adminDomainTabsFacade,
        protected readonly MailTemplateFacade $mailTemplateFacade,
        protected readonly MailSettingFacade $mailSettingFacade,
        protected readonly BreadcrumbOverrider $breadcrumbOverrider,
        protected readonly MailTemplateGridFactory $mailTemplateGridFactory,
        protected readonly MailTemplateConfiguration $mailTemplateConfiguration,
        protected readonly MailTemplateDataFactory $mailTemplateDataFactory,
        protected readonly MailTemplateSenderFacade $mailTemplateSenderFacade,
        protected readonly ErrorExtractor $errorExtractor,
    ) {
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route(path: '/mail/template/')]
    public function templateAction(): Response
    {
        $grid = $this->mailTemplateGridFactory->create();

        return $this->render('@ShopsysFramework/Admin/Content/Mail/list.html.twig', [
            'gridView' => $grid->createView(),
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route(path: '/mail/edit/{id}', requirements: ['id' => '\d+'])]
    public function editAction(Request $request, int $id): Response
    {
        $mailTemplate = $this->mailTemplateFacade->getById($id);
        $mailTemplateData = $this->mailTemplateDataFactory->createFromMailTemplate($mailTemplate);

        $mailTemplateVariables = $this->mailTemplateConfiguration->getMailTemplateVariablesBySlug(
            $mailTemplate->getName(),
        );

        $form = $this->createForm(
            MailTemplateFormType::class,
            $mailTemplateData,
            [
                'allow_disable_sending' => in_array(
                    $mailTemplateVariables->getType(),
                    MailTemplateConfiguration::TYPES_WITH_SEND_MAIL_SETTING,
                    true,
                ),
                'entity' => $mailTemplate,
                'required_subject_variables' => $mailTemplateVariables->getRequiredSubjectVariables(),
                'required_body_variables' => $mailTemplateVariables->getRequiredBodyVariables(),
                'body_variables' => $this->transformBodyVariables(
                    $mailTemplateVariables->getLabeledVariables(),
                    $mailTemplateVariables->getRequiredBodyVariables(),
                ),
            ],
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->mailTemplateFacade->edit($id, $mailTemplateData);

            $this->addSuccessFlashTwig(
                t('Email template <strong><a href="{{ url }}">{{ name }}</a></strong> modified'),
                [
                    'name' => $mailTemplateVariables->getReadableName(),
                    'url' => $this->generateUrl('admin_mail_edit', ['id' => $mailTemplate->getId()]),
                ],
            );

            return $this->redirectToRoute('admin_mail_template');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addErrorFlash(t('Please check the correctness of all data filled.'));
        }

        $this->breadcrumbOverrider->overrideLastItem(
            t('Editing email template - %name%', ['%name%' => $mailTemplateVariables->getReadableName()]),
        );

        return $this->render('@ShopsysFramework/Admin/Content/Mail/edit.html.twig', [
            'form' => $form->createView(),
            'mailTemplateName' => $mailTemplateVariables->getReadableName(),
            'bodyVariables' => $mailTemplateVariables->getBodyVariables(),
            'subjectVariables' => $mailTemplateVariables->getSubjectVariables(),
            'requiredBodyVariables' => $mailTemplateVariables->getRequiredBodyVariables(),
            'requiredSubjectVariables' => $mailTemplateVariables->getRequiredSubjectVariables(),
            'labeledVariables' => $mailTemplateVariables->getLabeledVariables(),
            'entity' => $mailTemplate,
            'mailSenderExists' => $this->mailTemplateSenderFacade->mailSenderExists($mailTemplate),
        ]);
    }

    /**
     * @param string[] $variables
     * @param string[] $requiredVariables
     * @return array<int, array<string, bool|int|string>>
     */
    protected function transformBodyVariables(array $variables, array $requiredVariables): array
    {
        $transformedVariables = [];

        foreach ($variables as $placeholder => $label) {
            $transformedVariables[] = [
                'label' => $label,
                'placeholder' => $placeholder,
                'isRequired' => in_array($placeholder, $requiredVariables, true),
            ];
        }

        return $transformedVariables;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route(path: '/mail/setting/')]
    public function settingAction(Request $request): Response
    {
        $selectedDomainId = $this->adminDomainTabsFacade->getSelectedDomainId();

        $mailSettingData = [
            'email' => $this->mailSettingFacade->getMainAdminMail($selectedDomainId),
            'name' => $this->mailSettingFacade->getMainAdminMailName($selectedDomainId),
            'facebookUrl' => $this->mailSettingFacade->getFacebookUrl($selectedDomainId),
            'instagramUrl' => $this->mailSettingFacade->getInstagramUrl($selectedDomainId),
            'youtubeUrl' => $this->mailSettingFacade->getYoutubeUrl($selectedDomainId),
            'linkedinUrl' => $this->mailSettingFacade->getLinkedInUrl($selectedDomainId),
            'tiktokUrl' => $this->mailSettingFacade->getTiktokUrl($selectedDomainId),
            'footerText' => $this->mailSettingFacade->getFooterTextUrl($selectedDomainId),
        ];

        $form = $this->createForm(MailSettingFormType::class, $mailSettingData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mailSettingData = $form->getData();

            $this->mailSettingFacade->setMainAdminMail($mailSettingData['email'], $selectedDomainId);
            $this->mailSettingFacade->setMainAdminMailName($mailSettingData['name'], $selectedDomainId);
            $this->mailSettingFacade->setFacebookUrl($mailSettingData['facebookUrl'], $selectedDomainId);
            $this->mailSettingFacade->setInstagramUrl($mailSettingData['instagramUrl'], $selectedDomainId);
            $this->mailSettingFacade->setYoutubeUrl($mailSettingData['youtubeUrl'], $selectedDomainId);
            $this->mailSettingFacade->setLinkedInUrl($mailSettingData['linkedinUrl'], $selectedDomainId);
            $this->mailSettingFacade->setTiktokUrl($mailSettingData['tiktokUrl'], $selectedDomainId);
            $this->mailSettingFacade->setFooterText($mailSettingData['footerText'], $selectedDomainId);

            $this->addSuccessFlash(t('Email settings modified.'));
        }

        return $this->render('@ShopsysFramework/Admin/Content/Mail/setting.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $mailTemplateId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route(path: '/mail/send/{mailTemplateId}', requirements: ['mailTemplateId' => '\d+'], condition: 'request.isXmlHttpRequest()')]
    public function sendAction(Request $request, int $mailTemplateId): Response
    {
        $mailTemplate = $this->mailTemplateFacade->getById($mailTemplateId);
        $form = $this->createForm(MailTemplateSendFormType::class, null, [
            'action' => $this->generateUrl('admin_mail_send', ['mailTemplateId' => $mailTemplateId]),
            'mailTemplate' => $mailTemplate,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $this->mailTemplateSenderFacade->sendMail($mailTemplate, $data['mailTo'], $data['entityIdentifier'] ?? null);
                $this->addSuccessFlash(t('Test email sent to %email%.', ['%email%' => $data['mailTo']]));

                return new JsonResponse(['result' => 'valid']);
            } catch (Throwable $exception) {
                $this->addErrorFlash(t('Error occurred while sending email.'));
                $this->addErrorFlash($exception->getMessage());

                return $this->createInvalidResponse($form);
            }
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            return $this->createInvalidResponse($form);
        }

        return $this->render('@ShopsysFramework/Admin/Content/Mail/mailTemplateSend.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param \Symfony\Component\Form\FormInterface $form
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function createInvalidResponse(FormInterface $form): JsonResponse
    {
        return new JsonResponse([
            'result' => 'invalid',
            'errors' => $this->errorExtractor->getAllErrorsAsArray($form, $this->getErrorMessages()),
        ]);
    }
}
