<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class Version20241105132620 extends AbstractMigration implements ContainerAwareInterface
{
    use MultidomainMigrationTrait;

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema): void
    {
        foreach ($this->getAllDomainIds() as $domainId) {
            $this->sql('INSERT INTO setting_values (name, domain_id, value, type) VALUES (:name, :domainId, :value, :type)', [
                'domainId' => $domainId,
                'name' => 'feedDeliveryDaysForOutOfStockProducts',
                'value' => -1,
                'type' => 'integer',
            ]);
        }
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema): void
    {
    }
}
