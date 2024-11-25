<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\ImageSitemap;

use Monolog\Logger;
use Shopsys\Plugin\Cron\SimpleCronModuleInterface;

class ImageSitemapCronModule implements SimpleCronModuleInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\ImageSitemap\ImageSitemapFacade $imageSitemapFacade
     */
    public function __construct(
        protected readonly ImageSitemapFacade $imageSitemapFacade,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function setLogger(Logger $logger)
    {
    }

    public function run()
    {
        $this->imageSitemapFacade->generateForAllDomains();
    }
}
