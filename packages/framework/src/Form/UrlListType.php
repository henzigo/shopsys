<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Form;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Router\DomainRouterFactory;
use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade;
use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\UrlListData;
use Shopsys\FrameworkBundle\Form\Constraints\UniqueSlugsOnDomains;
use Shopsys\FrameworkBundle\Form\Exception\MissingRouteNameException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UrlListType extends AbstractType
{
    /**
     * @param \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade $friendlyUrlFacade
     * @param \Shopsys\FrameworkBundle\Component\Router\DomainRouterFactory $domainRouterFactory
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     */
    public function __construct(
        private readonly FriendlyUrlFacade $friendlyUrlFacade,
        private readonly DomainRouterFactory $domainRouterFactory,
        private readonly Domain $domain,
    ) {
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['route_name'] === null) {
            throw new MissingRouteNameException();
        }

        $builder->add('toDelete', FormType::class);
        $builder->add('mainFriendlyUrlsByDomainId', FormType::class);
        $builder->add('newUrls', CollectionType::class, [
            'entry_type' => FriendlyUrlType::class,
            'required' => false,
            'allow_add' => true,
            'error_bubbling' => false,
            'constraints' => [
                new UniqueSlugsOnDomains(),
            ],
        ]);

        $friendlyUrlsByDomain = $this->getFriendlyUrlsIndexedByDomain(
            $options['route_name'],
            (int)$options['entity_id'],
        );

        foreach ($friendlyUrlsByDomain as $domainId => $friendlyUrls) {
            $builder->get('toDelete')->add(
                $builder->create((string)$domainId, ChoiceType::class, [
                    'required' => false,
                    'multiple' => true,
                    'expanded' => true,
                    'choices' => $friendlyUrls,
                    'choice_label' => 'slug',
                    'choice_value' => 'slug',
                ]),
            );

            $builder->get('mainFriendlyUrlsByDomainId')->add(
                $builder->create((string)$domainId, ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => true,
                    'choices' => $friendlyUrls,
                    'choice_label' => 'slug',
                    'choice_value' => 'slug',
                    'invalid_message' => 'Previously selected main URL dos not exist any more',
                ]),
            );
        }
    }

    /**
     * @param \Symfony\Component\Form\FormView $view
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $absoluteUrlsByDomainIdAndSlug = $this->getAbsoluteUrlsIndexedByDomainIdAndSlug(
            $options['route_name'],
            (int)$options['entity_id'],
        );
        $mainUrlsSlugsOnDomains = $this->getMainFriendlyUrlSlugsIndexedByDomainId(
            $options['route_name'],
            $options['entity_id'],
        );

        $view->vars['absoluteUrlsByDomainIdAndSlug'] = $absoluteUrlsByDomainIdAndSlug;
        $view->vars['routeName'] = $options['route_name'];
        $view->vars['entityId'] = $options['entity_id'];
        $view->vars['mainUrlsSlugsOnDomains'] = $mainUrlsSlugsOnDomains;
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UrlListData::class,
            'required' => false,
            'route_name' => null,
            'entity_id' => null,
        ]);
    }

    /**
     * @param string $routeName
     * @param int $entityId
     * @return \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrl[][]
     */
    private function getFriendlyUrlsIndexedByDomain(string $routeName, int $entityId): array
    {
        $friendlyUrlsByDomain = [];

        $friendlyUrls = $this->friendlyUrlFacade->getAllByRouteNameDomainIdsAndEntityIds(
            $routeName,
            $entityId,
            $this->domain->getAdminEnabledDomainIds(),
        );

        foreach ($friendlyUrls as $friendlyUrl) {
            $friendlyUrlsByDomain[$friendlyUrl->getDomainId()][] = $friendlyUrl;
        }

        return $friendlyUrlsByDomain;
    }

    /**
     * @param string $routeName
     * @param int $entityId
     * @return string[][]
     */
    private function getAbsoluteUrlsIndexedByDomainIdAndSlug(string $routeName, int $entityId): array
    {
        $friendlyUrlsByDomain = $this->getFriendlyUrlsIndexedByDomain($routeName, $entityId);
        $absoluteUrlsByDomainIdAndSlug = [];

        foreach ($friendlyUrlsByDomain as $domainId => $friendlyUrls) {
            $domainRouter = $this->domainRouterFactory->getRouter($domainId);
            $absoluteUrlsByDomainIdAndSlug[$domainId] = [];

            foreach ($friendlyUrls as $friendlyUrl) {
                $absoluteUrlsByDomainIdAndSlug[$domainId][$friendlyUrl->getSlug()] =
                    $domainRouter->generateByFriendlyUrl(
                        $friendlyUrl,
                        [],
                        UrlGeneratorInterface::ABSOLUTE_URL,
                    );
            }
        }

        return $absoluteUrlsByDomainIdAndSlug;
    }

    /**
     * @param string $routeName
     * @param int|null $entityId
     * @return string[]
     */
    private function getMainFriendlyUrlSlugsIndexedByDomainId(string $routeName, ?int $entityId): array
    {
        $mainFriendlyUrlsSlugsByDomainId = [];

        foreach ($this->domain->getAdminEnabledDomainIds() as $domainId) {
            $mainFriendlyUrl = $this->friendlyUrlFacade->findMainFriendlyUrl(
                $domainId,
                $routeName,
                $entityId,
            );

            if ($mainFriendlyUrl !== null) {
                $mainFriendlyUrlsSlugsByDomainId[$domainId] = $mainFriendlyUrl->getSlug();
            } else {
                $mainFriendlyUrlsSlugsByDomainId[$domainId] = null;
            }
        }

        return $mainFriendlyUrlsSlugsByDomainId;
    }
}
