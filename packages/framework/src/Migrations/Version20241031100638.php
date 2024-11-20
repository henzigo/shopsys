<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20241031100638 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->sql('ALTER TABLE slider_items ADD COLUMN description TEXT DEFAULT NULL');
        $this->sql('ALTER TABLE slider_items ADD COLUMN rgb_background_color VARCHAR(7) NOT NULL DEFAULT \'#808080\'');
        $this->sql('ALTER TABLE slider_items ADD COLUMN opacity NUMERIC(3, 2) NOT NULL DEFAULT 0.8');
        $this->sql('ALTER TABLE slider_items ALTER rgb_background_color DROP DEFAULT');
        $this->sql('ALTER TABLE slider_items ALTER opacity DROP DEFAULT;');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema): void
    {
    }
}
