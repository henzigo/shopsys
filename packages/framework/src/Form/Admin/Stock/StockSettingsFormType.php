<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Form\Admin\Stock;

use Shopsys\FrameworkBundle\Component\Plugin\PluginCrudExtensionFacade;
use Shopsys\FrameworkBundle\Form\GroupType;
use Shopsys\FrameworkBundle\Form\MessageType;
use Shopsys\FrameworkBundle\Model\Stock\StockSettingsData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;
use Twig\Environment;

class StockSettingsFormType extends AbstractType
{
    /**
     * @param \Twig\Environment $environment
     * @param \Shopsys\FrameworkBundle\Component\Plugin\PluginCrudExtensionFacade $pluginCrudExtensionFacade
     */
    public function __construct(
        protected readonly Environment $environment,
        protected readonly PluginCrudExtensionFacade $pluginCrudExtensionFacade,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builderStockSettingGroup = $builder->create('stockSettings', GroupType::class, [
            'label' => t('Warehouse settings'),
        ]);

        $builderStockSettingGroup
            ->add('transfer', TextType::class, [
                'label' => t('Days for transfer between warehouses'),
                'constraints' => [
                    new Constraints\NotBlank(),
                    new Constraints\Regex(['pattern' => '/^\d+$/']),
                    new Constraints\GreaterThanOrEqual(['value' => 0]),
                ],
            ]);

        $builderFeedSettingGroup = $builder->create('feedSettings', GroupType::class, [
            'label' => t('XML feeds settings'),
        ]);

        $builderFeedSettingGroup
            ->add('feedDeliveryDaysForOutOfStockProducts', IntegerType::class, [
                'label' => t('Number of delivery days for out of stock products in XML feeds'),
                'required' => true,
                'constraints' => [
                    new Constraints\NotNull([
                        'message' => 'Please enter the number of delivery days.',
                    ]),
                ],
            ])
            ->add('feedDeliveryDaysForOutOfStockProductsInfo', MessageType::class, [
                'message_level' => MessageType::MESSAGE_LEVEL_INFO,
                'data' => $this->environment->render('@ShopsysFramework/Admin/Content/Feed/feedDeliveryDaysForOutOfStockProductsInfo.html.twig'),
            ]);

        $builder
            ->add($builderStockSettingGroup)
            ->add($builderFeedSettingGroup)
            ->add('save', SubmitType::class);

        $this->pluginCrudExtensionFacade->extendForm($builder, 'stockSettings', 'pluginData');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => StockSettingsData::class,
                'attr' => ['novalidate' => 'novalidate'],
            ]);
    }
}
