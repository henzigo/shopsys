<?php

namespace Tests\ShopBundle\Unit\Component\UploadedFile\Config;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Component\UploadedFile\Config\UploadedFileConfigLoader;
use Symfony\Component\Filesystem\Filesystem;
use Tests\ShopBundle\Unit\Component\UploadedFile\Dummy;

class UploadedFileConfigLoaderTest extends PHPUnit_Framework_TestCase
{
    public function testLoadFromYaml()
    {
        $configurationFilapath = __DIR__ . '/test_config_uploaded_files.yml';
        $filesystem = new Filesystem();

        $uploadedFileConfigLoader = new UploadedFileConfigLoader($filesystem);
        $uploadedFileEntityConfig = $uploadedFileConfigLoader->loadFromYaml($configurationFilapath);
        $uploadedFileEntityConfigs = $uploadedFileEntityConfig->getAllUploadedFileEntityConfigs();

        $this->assertCount(1, $uploadedFileEntityConfigs);
        $this->assertArrayHasKey(Dummy::class, $uploadedFileEntityConfigs);
        $uploadedFileConfig = $uploadedFileEntityConfigs[Dummy::class];
        $this->assertSame('testEntity', $uploadedFileConfig->getEntityName());
        $this->assertSame(Dummy::class, $uploadedFileConfig->getEntityClass());
    }
}