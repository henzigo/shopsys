<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\Product\Availability;

use Shopsys\FrameworkBundle\Component\Cache\InMemoryCache;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Setting\Setting;
use Shopsys\FrameworkBundle\Component\Translation\Translator;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Stock\ProductStockFacade;
use Shopsys\FrameworkBundle\Model\Store\StoreFacade;

class ProductAvailabilityFacade
{
    protected const int DAYS_IN_WEEK = 7;

    protected const string PRODUCT_AVAILABILITY_CACHE_NAMESPACE = 'productAvailabilityDomain';

    /**
     * @param \Shopsys\FrameworkBundle\Component\Setting\Setting $setting
     * @param \Shopsys\FrameworkBundle\Model\Stock\ProductStockFacade $productStockFacade
     * @param \Shopsys\FrameworkBundle\Model\Store\StoreFacade $storeFacade
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     * @param \Shopsys\FrameworkBundle\Component\Cache\InMemoryCache $inMemoryCache
     */
    public function __construct(
        protected readonly Setting $setting,
        protected readonly ProductStockFacade $productStockFacade,
        protected readonly StoreFacade $storeFacade,
        protected readonly Domain $domain,
        protected readonly InMemoryCache $inMemoryCache,
    ) {
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param int $domainId
     * @return string
     */
    public function getProductAvailabilityInformationByDomainId(Product $product, int $domainId): string
    {
        $domainLocale = $this->domain->getDomainConfigById($domainId)->getLocale();

        if ($this->isProductAvailableOnDomainCached($product, $domainId)) {
            return $this->getOnStockText($domainLocale);
        }

        return $this->getOutOfStockText($domainLocale);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param int $domainId
     * @return int
     */
    public function getProductAvailabilityDaysForFeedsByDomainId(Product $product, int $domainId): int
    {
        if ($this->isProductAvailableOnDomainCached($product, $domainId)) {
            return 0;
        }

        return $this->setting->getForDomain(Setting::FEED_DELIVERY_DAYS_FOR_OUT_OF_STOCK_PRODUCTS, $domainId);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param int $domainId
     * @return string
     */
    public function getProductAvailabilityStatusByDomainId(
        Product $product,
        int $domainId,
    ): string {
        if ($this->isProductAvailableOnDomainCached($product, $domainId)) {
            return AvailabilityStatusEnum::IN_STOCK;
        }

        return AvailabilityStatusEnum::OUT_OF_STOCK;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param int $domainId
     * @return int|null
     */
    public function getAvailableStoresCount(Product $product, int $domainId): ?int
    {
        if ($product->isMainVariant()) {
            return null;
        }

        $productStocks = $this->productStockFacade->getProductStocksByProduct($product);

        $count = 0;

        foreach ($productStocks as $productStock) {
            if ($productStock->getProductQuantity() > 0 && $productStock->getStock()->isEnabled($domainId)) {
                $count += count($productStock->getStock()->getStores());
            }
        }

        return $count;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param int $domainId
     * @return bool
     */
    public function isProductAvailableOnDomainCached(Product $product, int $domainId): bool
    {
        return $this->inMemoryCache->getOrSaveValue(
            static::PRODUCT_AVAILABILITY_CACHE_NAMESPACE,
            fn () => $this->productStockFacade->isProductAvailableOnDomain($product, $domainId),
            $product->getId(),
            $domainId,
        );
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Product\Availability\ProductStoreAvailabilityInformation[]
     */
    public function getProductStoresAvailabilitiesInformationByDomainIdIndexedByStoreId(
        Product $product,
        int $domainId,
    ): array {
        if ($product->isMainVariant()) {
            return [];
        }

        $stores = $this->storeFacade->getStoresByDomainId($domainId);

        $isAvailable = $this->isProductAvailableOnDomainCached($product, $domainId);

        $productStocksIndexedByStockId = $this->productStockFacade->getProductStocksByProductIndexedByStockId($product);

        $productStoresAvailabilityInformationList = [];

        $domainLocale = $this->domain->getDomainConfigById($domainId)->getLocale();

        foreach ($stores as $store) {
            $availabilityStatus = AvailabilityStatusEnum::IN_STOCK;
            $availabilityInformation = t('Available immediately', [], Translator::DEFAULT_TRANSLATION_DOMAIN, $domainLocale);

            if (!$isAvailable) {
                $availabilityStatus = AvailabilityStatusEnum::OUT_OF_STOCK;
                $availabilityInformation = t('Unavailable', [], Translator::DEFAULT_TRANSLATION_DOMAIN, $domainLocale);
            } else {
                $stock = $store->getStock();

                $productStock = null;

                if ($stock !== null && $stock->isEnabled($domainId)) {
                    $productStock = $productStocksIndexedByStockId[$stock->getId()];
                }

                if ($productStock === null || $productStock->getProductQuantity() <= 0) {
                    $weeks = $this->getTransferWeeksByDomainId($domainId);
                    $availabilityInformation = $this->getWeeksAvailabilityMessageByWeeks($weeks, $domainId);
                }
            }

            $productStoresAvailabilityInformationList[$store->getId()] = new ProductStoreAvailabilityInformation(
                $store->getName(),
                $store->getId(),
                $availabilityInformation,
                $availabilityStatus,
            );
        }

        return $productStoresAvailabilityInformationList;
    }

    /**
     * @param int $weeks
     * @param int $domainId
     * @return string
     */
    protected function getWeeksAvailabilityMessageByWeeks(int $weeks, int $domainId): string
    {
        $domainLocale = $this->domain->getDomainConfigById($domainId)->getLocale();

        return t(
            '{0,1} Available in one week|[2,Inf] Available in %count% weeks',
            ['%count%' => $weeks],
            Translator::DEFAULT_TRANSLATION_DOMAIN,
            $domainLocale,
        );
    }

    /**
     * @param int $days
     * @return int
     */
    public static function calculateDaysToWeeks(int $days): int
    {
        return (int)ceil($days / static::DAYS_IN_WEEK);
    }

    /**
     * @param int $domainId
     * @return int
     */
    protected function getTransferWeeksByDomainId(int $domainId): int
    {
        return self::calculateDaysToWeeks($this->getTransferDaysByDomainId($domainId));
    }

    /**
     * @param int $domainId
     * @return int
     */
    public function getTransferDaysByDomainId(int $domainId): int
    {
        return $this->setting->getForDomain(Setting::TRANSFER_DAYS_BETWEEN_STOCKS, $domainId);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param int $domainId
     * @return int|null
     */
    public function getGroupedStockQuantityByProductAndDomainId(Product $product, int $domainId): ?int
    {
        if ($product->isMainVariant()) {
            return null;
        }

        $productStocksByDomainIdIndexedByStockId = $this->productStockFacade->getProductStocksByProductAndDomainIdIndexedByStockId($product, $domainId);

        return $this->sumProductStockQuantities($productStocksByDomainIdIndexedByStockId);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Stock\ProductStock[] $productStocksByDomainIdIndexedByStockId
     * @return int
     */
    protected function sumProductStockQuantities(array $productStocksByDomainIdIndexedByStockId): int
    {
        $totalProductStocksQuantity = 0;

        foreach ($productStocksByDomainIdIndexedByStockId as $productStock) {
            $totalProductStocksQuantity += $productStock->getProductQuantity();
        }

        return $totalProductStocksQuantity;
    }

    /**
     * @param string $domainLocale
     * @return string
     */
    public function getOnStockText(string $domainLocale): string
    {
        return t('In stock', [], Translator::DEFAULT_TRANSLATION_DOMAIN, $domainLocale);
    }

    /**
     * @param string $domainLocale
     * @return string
     */
    public function getOutOfStockText(string $domainLocale): string
    {
        return t('Out of stock', [], Translator::DEFAULT_TRANSLATION_DOMAIN, $domainLocale);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param int $domainId
     * @param int $quantityToAdd
     * @return int|null
     */
    public function getNotOnStockQuantity(Product $product, int $domainId, int $quantityToAdd): ?int
    {
        if ($product->isMainVariant()) {
            return null;
        }

        $notOnStockQuantity = 0;
        $productTotalQuantity = $this->getGroupedStockQuantityByProductAndDomainId($product, $domainId);

        if ($quantityToAdd > $productTotalQuantity) {
            $notOnStockQuantity = $quantityToAdd - $productTotalQuantity;
        }

        return $notOnStockQuantity;
    }
}
