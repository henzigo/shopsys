<?php

namespace Shopsys\FrameworkBundle\Command;

use Shopsys\FrameworkBundle\Component\Redis\RedisFacade;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CleanRedisCacheCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'shopsys:redis:clean';

    /**
     * @var \Shopsys\FrameworkBundle\Component\Redis\RedisFacade
     */
    private $redisFacade;

    /**
     * @param string $prefix
     */
    public function __construct(RedisFacade $redisFacade)
    {
        parent::__construct();
        $this->redisFacade = $redisFacade;
    }

    protected function configure()
    {
        $this
            ->setDescription('Clean redis cache');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Cleaning redis cache');

        $this->redisFacade->clearCacheByPattern();

        $output->writeln('Redis cache was cleaned successfully!');
    }
}
