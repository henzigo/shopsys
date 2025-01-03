<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\Product\Parameter;

class ProductParameterValuesLocalizedData
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Parameter\Parameter|null
     */
    public $parameter;

    /**
     * @var string[]
     */
    public $valueTextsByLocale = [];

    /**
     * @var string|null
     */
    public $numericValue;
}
