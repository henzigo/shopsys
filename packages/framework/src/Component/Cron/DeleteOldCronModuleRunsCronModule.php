<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Component\Cron;

use Monolog\Logger;
use Shopsys\Plugin\Cron\SimpleCronModuleInterface;

class DeleteOldCronModuleRunsCronModule implements SimpleCronModuleInterface
{
    protected const DELETE_OLD_CRON_MODULE_RUNS_AFTER_DAYS = 30;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Cron\CronModuleFacade $cronModuleFacade
     */
    public function __construct(
        protected readonly CronModuleFacade $cronModuleFacade,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function setLogger(Logger $logger): void
    {
    }

    public function run(): void
    {
        $this->cronModuleFacade->deleteOldCronModuleRuns(static::DELETE_OLD_CRON_MODULE_RUNS_AFTER_DAYS);
    }
}
