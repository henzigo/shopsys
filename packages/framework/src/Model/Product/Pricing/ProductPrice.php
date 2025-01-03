<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\Product\Pricing;

use Shopsys\FrameworkBundle\Model\Pricing\Price;

class ProductPrice extends Price
{
    protected bool $priceFrom;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $price
     * @param bool $priceFrom
     */
    public function __construct(Price $price, $priceFrom)
    {
        $this->priceFrom = $priceFrom;

        parent::__construct($price->getPriceWithoutVat(), $price->getPriceWithVat());
    }

    /**
     * @return bool
     */
    public function isPriceFrom()
    {
        return $this->priceFrom;
    }

    /**
     * @return self
     */
    public static function createHiddenProductPrice(): self
    {
        return new self(
            self::createHiddenPrice(),
            false,
        );
    }
}
