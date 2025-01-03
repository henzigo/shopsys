<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Form\Admin\Order;

use Shopsys\FrameworkBundle\Form\Transformers\CopyTotalPricesOfOrderItemTransformer;
use Shopsys\FrameworkBundle\Form\Transformers\InverseTransformer;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderItemData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class OrderTransportFormType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('transport', ChoiceType::class, [
                'required' => true,
                'choices' => $options['transports'],
                'choice_label' => 'name',
                'choice_value' => 'id',
                'error_bubbling' => true,
            ])
            ->add('unitPriceWithVat', MoneyType::class, [
                'scale' => 6,
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter unit price without VAT']),
                ],
                'error_bubbling' => true,
            ])
            ->add('vatPercent', NumberType::class, [
                'input' => 'string',
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter VAT rate']),
                ],
                'error_bubbling' => true,
            ])
            ->add('unitPriceWithoutVat', MoneyType::class, [
                'scale' => 6,
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'Please enter price',
                        'groups' => [OrderItemFormType::VALIDATION_GROUP_NOT_USING_PRICE_CALCULATION],
                    ]),
                ],
                'error_bubbling' => true,
            ])
            ->add(
                $builder->create('setPricesManually', CheckboxType::class, [
                    'property_path' => 'usePriceCalculation',
                ])->addModelTransformer(new InverseTransformer()),
            )
            ->addModelTransformer(new CopyTotalPricesOfOrderItemTransformer());
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('transports')
            ->setAllowedTypes('transports', 'array')
            ->setDefaults([
                'data_class' => OrderItemData::class,
                'attr' => ['novalidate' => 'novalidate'],
                'validation_groups' => function (FormInterface $form) {
                    return OrderItemFormType::resolveValidationGroups($form);
                },
            ]);
    }
}
