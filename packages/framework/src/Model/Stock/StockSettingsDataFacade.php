<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\Stock;

use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Component\Setting\Setting;
use Shopsys\FrameworkBundle\Model\Product\Elasticsearch\Scope\ProductExportScopeConfig;
use Shopsys\FrameworkBundle\Model\Product\Recalculation\ProductRecalculationDispatcher;

class StockSettingsDataFacade
{
    /**
     * @param \Shopsys\FrameworkBundle\Component\Setting\Setting $setting
     * @param \Shopsys\FrameworkBundle\Model\Product\Recalculation\ProductRecalculationDispatcher $productRecalculationDispatcher
     */
    public function __construct(
        protected readonly Setting $setting,
        protected readonly ProductRecalculationDispatcher $productRecalculationDispatcher,
    ) {
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Stock\StockSettingsData $stockSettingsData
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     */
    public function edit(StockSettingsData $stockSettingsData, DomainConfig $domainConfig): void
    {
        $domainId = $domainConfig->getId();

        $this->setting->setForDomain(
            Setting::TRANSFER_DAYS_BETWEEN_STOCKS,
            (int)$stockSettingsData->transfer,
            $domainId,
        );

        $this->setting->setForDomain(
            Setting::LUIGIS_BOX_RANK,
            $stockSettingsData->luigisBoxRank,
            $domainId,
        );

        $this->setting->setForDomain(
            Setting::FEED_DELIVERY_DAYS_FOR_OUT_OF_STOCK_PRODUCTS,
            $stockSettingsData->feedDeliveryDaysForOutOfStockProducts,
            $domainId,
        );

        $this->productRecalculationDispatcher->dispatchAllProducts([ProductExportScopeConfig::SCOPE_STOCKS]);
    }
}
