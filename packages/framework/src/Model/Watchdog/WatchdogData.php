<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\Watchdog;

class WatchdogData
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Product|null
     */
    public $product;

    /**
     * @var int|null
     */
    public $domainId;

    /**
     * @var string|null
     */
    public $email;

    /**
     * @var \DateTime|null
     */
    public $createdAt;

    /**
     * @var \DateTime|null
     */
    public $updatedAt;

    /**
     * @var \DateTime|null
     */
    public $validUntil;
}
