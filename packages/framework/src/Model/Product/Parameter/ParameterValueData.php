<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\Product\Parameter;

class ParameterValueData
{
    /**
     * @var string|null
     */
    public $text;

    /**
     * @var string|null
     */
    public $numericValue;

    /**
     * @var string|null
     */
    public $locale;

    /**
     * @var string|null
     */
    public $uuid;

    /**
     * @var string|null
     */
    public $rgbHex;

    /**
     * @var \Shopsys\FrameworkBundle\Component\UploadedFile\UploadedFileData
     */
    public $colourIcon;
}
