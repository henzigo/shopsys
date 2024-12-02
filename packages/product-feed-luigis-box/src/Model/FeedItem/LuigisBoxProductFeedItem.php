<?php

declare(strict_types=1);

namespace Shopsys\ProductFeed\LuigisBoxBundle\Model\FeedItem;

use Shopsys\FrameworkBundle\Component\Image\ImageUrlWithSizeHelper;
use Shopsys\FrameworkBundle\Model\Feed\FeedItemInterface;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency;
use Shopsys\FrameworkBundle\Model\Pricing\Price;

class LuigisBoxProductFeedItem implements FeedItemInterface
{
    public const string UNIQUE_IDENTIFIER_PREFIX = 'product';
    protected const int SMALL_IMAGE_SIZE = 100;
    protected const int MEDIUM_IMAGE_SIZE = 200;
    protected const int LARGE_IMAGE_SIZE = 600;
    protected const int SELLABLE_PRODUCT_AVAILABILITY = 1;

    /**
     * @param int $id
     * @param string $name
     * @param string $catalogNumber
     * @param string $availabilityText
     * @param int $availabilityRank
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $price
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency $currency
     * @param int $mainCategoryId
     * @param string $url
     * @param array<int, string> $categoryHierarchyNamesByCategoryId
     * @param bool $isMainVariant
     * @param string[] $flagNames
     * @param array<string, string> $productParameterValuesIndexedByName
     * @param string|null $mainCategoryName
     * @param string|null $ean
     * @param string|null $catnum
     * @param string|null $brandName
     * @param string|null $description
     * @param string|null $imgUrl
     * @param int|null $mainVariantId
     */
    public function __construct(
        protected readonly int $id,
        protected readonly string $name,
        protected readonly string $catalogNumber,
        protected readonly string $availabilityText,
        protected readonly int $availabilityRank,
        protected readonly Price $price,
        protected readonly Currency $currency,
        protected readonly int $mainCategoryId,
        protected readonly string $url,
        protected readonly array $categoryHierarchyNamesByCategoryId,
        protected readonly bool $isMainVariant,
        protected readonly array $flagNames,
        protected readonly array $productParameterValuesIndexedByName,
        protected readonly ?string $mainCategoryName,
        protected readonly ?string $ean,
        protected readonly ?string $catnum,
        protected readonly ?string $brandName,
        protected readonly ?string $description,
        protected readonly ?string $imgUrl = null,
        protected readonly ?int $mainVariantId = null,
    ) {
    }

    /**
     * @return int
     */
    public function getSeekId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getIdentity(): string
    {
        return static::UNIQUE_IDENTIFIER_PREFIX . '-' . $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getBrand(): ?string
    {
        return $this->brandName;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string|null
     */
    public function getImageLinkS(): ?string
    {
        if ($this->imgUrl === null) {
            return null;
        }

        return ImageUrlWithSizeHelper::limitSizeInImageUrl($this->imgUrl, static::SMALL_IMAGE_SIZE, static::SMALL_IMAGE_SIZE);
    }

    /**
     * @return string|null
     */
    public function getImageLinkM(): ?string
    {
        if ($this->imgUrl === null) {
            return null;
        }

        return ImageUrlWithSizeHelper::limitSizeInImageUrl($this->imgUrl, static::MEDIUM_IMAGE_SIZE, static::MEDIUM_IMAGE_SIZE);
    }

    /**
     * @return string|null
     */
    public function getImageLinkL(): ?string
    {
        if ($this->imgUrl === null) {
            return null;
        }

        return ImageUrlWithSizeHelper::limitSizeInImageUrl($this->imgUrl, static::LARGE_IMAGE_SIZE, static::LARGE_IMAGE_SIZE);
    }

    /**
     * @return string
     */
    public function getAvailabilityRankText(): string
    {
        return $this->availabilityText;
    }

    /**
     * @return int
     */
    public function getAvailabilityRank(): int
    {
        return $this->availabilityRank;
    }

    /**
     * @return int
     */
    public function getAvailability(): int
    {
        return self::SELLABLE_PRODUCT_AVAILABILITY;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price
     */
    public function getPrice(): Price
    {
        return $this->price;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * @return string|null
     */
    public function getItemGroupId(): ?string
    {
        if ($this->isMainVariant) {
            return $this->getIdentity();
        }

        if ($this->mainVariantId === null) {
            return null;
        }

        return static::UNIQUE_IDENTIFIER_PREFIX . '-' . $this->mainVariantId;
    }

    /**
     * @return array<int, string>
     */
    public function getCategoryNamesIndexedByCategoryId(): array
    {
        return $this->categoryHierarchyNamesByCategoryId;
    }

    /**
     * @return string[]
     */
    public function getFlagNames(): array
    {
        return $this->flagNames;
    }

    /**
     * @return array<string, string>
     */
    public function getProductParameterValuesIndexedByName(): array
    {
        return $this->productParameterValuesIndexedByName;
    }

    /**
     * @return string|null
     */
    public function getEan(): ?string
    {
        return $this->ean;
    }

    /**
     * @return string
     */
    public function getSku(): string
    {
        return $this->catalogNumber;
    }

    /**
     * @return string|null
     */
    public function getProductCode(): ?string
    {
        return $this->catnum;
    }

    /**
     * @return int
     */
    public function getMainCategoryId(): int
    {
        return $this->mainCategoryId;
    }
}
