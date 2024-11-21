<?php

declare(strict_types=1);

namespace Shopsys\FrontendApiBundle\Model\Resolver\Products\DataMapper;

use GraphQL\Executor\Promise\Promise;
use Overblog\DataLoader\DataLoaderInterface;
use Shopsys\FrameworkBundle\Model\Category\CategoryFacade;
use Shopsys\FrameworkBundle\Model\Customer\User\CurrentCustomerUser;
use Shopsys\FrameworkBundle\Model\Product\Brand\Brand;
use Shopsys\FrameworkBundle\Model\Product\Brand\BrandFacade;
use Shopsys\FrameworkBundle\Model\Product\Flag\FlagFacade;
use Shopsys\FrameworkBundle\Model\Product\ProductElasticsearchProvider;
use Shopsys\FrameworkBundle\Model\Product\ProductFrontendLimitProvider;
use Shopsys\FrameworkBundle\Model\Product\ProductTypeEnum;
use Shopsys\FrontendApiBundle\Model\Parameter\ParameterWithValuesFactory;

class ProductArrayFieldMapper
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\CategoryFacade $categoryFacade
     * @param \Shopsys\FrameworkBundle\Model\Product\Flag\FlagFacade $flagFacade
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\BrandFacade $brandFacade
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductElasticsearchProvider $productElasticsearchProvider
     * @param \Shopsys\FrontendApiBundle\Model\Parameter\ParameterWithValuesFactory $parameterWithValuesFactory
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductFrontendLimitProvider $productFrontendLimitProvider
     * @param \Overblog\DataLoader\DataLoaderInterface $productsSellableByIdsBatchLoader
     * @param \Shopsys\FrameworkBundle\Model\Customer\User\CurrentCustomerUser $currentCustomerUser
     * @param \Overblog\DataLoader\DataLoaderInterface $productsSellableCountByIdsBatchLoader
     */
    public function __construct(
        protected readonly CategoryFacade $categoryFacade,
        protected readonly FlagFacade $flagFacade,
        protected readonly BrandFacade $brandFacade,
        protected readonly ProductElasticsearchProvider $productElasticsearchProvider,
        protected readonly ParameterWithValuesFactory $parameterWithValuesFactory,
        protected readonly ProductFrontendLimitProvider $productFrontendLimitProvider,
        protected readonly DataLoaderInterface $productsSellableByIdsBatchLoader,
        protected readonly CurrentCustomerUser $currentCustomerUser,
        protected readonly DataLoaderInterface $productsSellableCountByIdsBatchLoader,
    ) {
    }

    /**
     * @param array $data
     * @return string|null
     */
    public function getShortDescription(array $data): ?string
    {
        return $data['short_description'];
    }

    /**
     * @param array $data
     * @return string
     */
    public function getLink(array $data): string
    {
        return $data['detail_url'];
    }

    /**
     * @param array $data
     * @return \Shopsys\FrameworkBundle\Model\Category\Category[]
     */
    public function getCategories(array $data): array
    {
        return $this->categoryFacade->getByIds($data['categories']);
    }

    /**
     * @param array $data
     * @return \Shopsys\FrameworkBundle\Model\Product\Flag\Flag[]
     */
    public function getFlags(array $data): array
    {
        return $this->flagFacade->getByIds($data['flags']);
    }

    /**
     * @param array $data
     * @return array{name: string, status: string}
     */
    public function getAvailability(array $data): array
    {
        return [
            'name' => $data['availability'],
            'status' => $data['availability_status'],
        ];
    }

    /**
     * @param array $data
     * @return string[]
     */
    public function getUnit(array $data): array
    {
        return ['name' => $data['unit']];
    }

    /**
     * @param array $data
     * @return int|null
     */
    public function getStockQuantity(array $data): ?int
    {
        return $data['stock_quantity'];
    }

    /**
     * @param array $data
     * @return \Shopsys\FrameworkBundle\Model\Product\Brand\Brand|null
     */
    public function getBrand(array $data): ?Brand
    {
        if ((int)$data['brand'] > 0) {
            return $this->brandFacade->getById((int)$data['brand']);
        }

        return null;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function isSellingDenied(array $data): bool
    {
        return $data['calculated_selling_denied'];
    }

    /**
     * @param array $data
     * @return \GraphQL\Executor\Promise\Promise
     */
    public function getAccessoriesPromise(array $data): Promise
    {
        return $this->productsSellableByIdsBatchLoader->load($data['accessories']);
    }

    /**
     * @param array $data
     * @return string|null
     */
    public function getDescription(array $data): ?string
    {
        return $data['description'];
    }

    /**
     * @param array $data
     * @return array
     */
    public function getParameters(array $data): array
    {
        return $this->parameterWithValuesFactory->createParametersArrayFromProductArray($data);
    }

    /**
     * @param array $data
     * @return string|null
     */
    public function getSeoH1(array $data): ?string
    {
        return $data['seo_h1'];
    }

    /**
     * @param array $data
     * @return string|null
     */
    public function getSeoTitle(array $data): ?string
    {
        return $data['seo_title'];
    }

    /**
     * @param array $data
     * @return string|null
     */
    public function getSeoMetaDescription(array $data): ?string
    {
        return $data['seo_meta_description'];
    }

    /**
     * @param array $data
     * @return int
     */
    public function getOrderingPriority(array $data): int
    {
        return $data['ordering_priority'];
    }

    /**
     * @param array $data
     * @return array
     */
    public function getVariants(array $data): array
    {
        return $this->productElasticsearchProvider->getSellableProductArrayByIds($data['variants']);
    }

    /**
     * @param array $data
     * @return \GraphQL\Executor\Promise\Promise
     */
    public function getVariantsCount(array $data): Promise
    {
        return $this->productsSellableCountByIdsBatchLoader->load($data['variants']);
    }

    /**
     * @param array $data
     * @return array
     */
    public function getMainVariant(array $data): array
    {
        return $this->productElasticsearchProvider->getVisibleProductArrayById($data['main_variant_id']);
    }

    /**
     * @param array $data
     * @return array
     */
    public function getHreflangLinks(array $data): array
    {
        return $data['hreflang_links'];
    }

    /**
     * @param array $data
     * @return bool
     */
    public function isVisible(array $data): bool
    {
        $currentCustomerPricingGroup = $this->currentCustomerUser->getPricingGroup();

        foreach ($data['visibility'] as $visibility) {
            if ($currentCustomerPricingGroup->getId() === $visibility['pricing_group_id']) {
                return $visibility['visible'];
            }
        }

        return false;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function isInquiryType(array $data): bool
    {
        return $data['product_type'] === ProductTypeEnum::TYPE_INQUIRY;
    }

    /**
     * @param array $data
     * @return string
     */
    public function getProductType(array $data): string
    {
        return $data['product_type'];
    }

    /**
     * @param array $data
     * @return string|null
     */
    public function getNamePrefix(array $data): ?string
    {
        return $data['name_prefix'];
    }

    /**
     * @param array $data
     * @return string|null
     */
    public function getNameSuffix(array $data): ?string
    {
        return $data['name_suffix'];
    }

    /**
     * @param array $data
     * @return string
     */
    public function getFullname(array $data): string
    {
        return trim(
            $data['name_prefix']
            . ' '
            . $data['name']
            . ' '
            . $data['name_suffix'],
        );
    }

    /**
     * @param array $data
     * @return array
     */
    public function getStoreAvailabilities(array $data): array
    {
        return $data['store_availabilities_information'];
    }

    /**
     * @param array $data
     * @return int|null
     */
    public function getAvailableStoresCount(array $data): ?int
    {
        return $data['available_stores_count'];
    }
}
