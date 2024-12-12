<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Form\Admin\Mail;

use Shopsys\FrameworkBundle\Form\Constraints\Email;
use Shopsys\FrameworkBundle\Model\Administrator\AdministratorFacade;
use Shopsys\FrameworkBundle\Model\Mail\MailTemplate;
use Shopsys\FrameworkBundle\Model\Mail\MailTemplateSender\MailTemplateSenderFacade;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class MailTemplateSendFormType extends AbstractType
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Administrator\AdministratorFacade $administratorFacade
     * @param \Shopsys\FrameworkBundle\Model\Mail\MailTemplateSender\MailTemplateSenderFacade $mailTemplateSenderFacade
     */
    public function __construct(
        protected readonly AdministratorFacade $administratorFacade,
        protected readonly MailTemplateSenderFacade $mailTemplateSenderFacade,
    ) {
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentAdministrator = $this->administratorFacade->getCurrentlyLoggedAdministrator();
        $labelForEntityIdentifier = $this->mailTemplateSenderFacade->getFormLabelForEntityIdentifier($options['mailTemplate']);
        $builder
            ->add('mailTo', TextType::class, [
                'label' => t('Send mail to'),
                'required' => true,
                'data' => $currentAdministrator->getEmail(),
                'constraints' => [
                    new NotBlank(['message' => 'Please enter email address']),
                    new Email(['message' => 'Please enter valid email']),
                ],
            ]);

        if ($labelForEntityIdentifier !== null) {
            $builder
                ->add('entityIdentifier', IntegerType::class, [
                    'label' => $labelForEntityIdentifier,
                    'required' => true,
                    'constraints' => new NotBlank(['message' => 'Please enter an ID']),
                ]);
        }

        $builder
            ->add('save', SubmitType::class, [
                'label' => t('Send'),
                'attr' => [
                    'class' => 'margin-top-20',
                ],
            ]);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('mailTemplate')
            ->setAllowedTypes('mailTemplate', MailTemplate::class)
            ->setDefaults([
                'attr' => ['novalidate' => 'novalidate'],
            ]);
    }
}
