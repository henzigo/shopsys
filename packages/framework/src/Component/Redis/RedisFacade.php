<?php

namespace Shopsys\FrameworkBundle\Component\Redis;

use Doctrine\Common\Cache\RedisCache;

class RedisFacade
{
    /**
     * @var \Redis|null
     */
    private $redisClient;

    /**
     * @var string
     */
    private $cachePrefix;

    public function __construct(RedisCache $redisCache, $cachePrefix)
    {
        $this->redisClient = $redisCache->getRedis();
        $this->cachePrefix = $cachePrefix;
    }

    /**
     * @param string $pattern
     * @param string|null id
     * @return bool
     */
    public function clearCacheByPattern($pattern = '')
    {
        $pattern = $this->cachePrefix . ':' . $pattern . '*';

        if ($this->isRedisClient() === true) {
            $this->redisClient->eval('return redis.call(\'del\', unpack(redis.call(\'keys\', ARGV[1])))', [$pattern]);
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    private function isRedisClient()
    {
        return $this->redisClient !== null;
    }
}
