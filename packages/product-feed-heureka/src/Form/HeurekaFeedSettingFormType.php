<?php

declare(strict_types=1);

namespace Shopsys\ProductFeed\HeurekaBundle\Form;

use Shopsys\FrameworkBundle\Form\MessageType;
use Shopsys\ProductFeed\HeurekaBundle\Model\Setting\HeurekaFeedSettingEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class HeurekaFeedSettingFormType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(HeurekaFeedSettingEnum::HEUREKA_FEED_DELIVERY_DAYS, IntegerType::class, [
                'label' => t('Number of delivery days for out of stock products for Heureka XML feed'),
                'required' => false,
            ])
            ->add('heurekaFeedDeliveryDaysInfo', MessageType::class, [
                'message_level' => MessageType::MESSAGE_LEVEL_INFO,
                'data' => t('The value is used for DELIVERY_DATE setting in Heureka XML feed. See <a href="https://sluzby.heureka.cz/napoveda/xml-feed/#DELIVERY_DATE">the docs</a> for more information.'),
            ]);
    }
}
