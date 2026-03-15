<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251211061243 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity_logs ADD record_type VARCHAR(100) DEFAULT NULL, ADD record_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product DROP status');
        $this->addSql('ALTER TABLE service_inventory RENAME INDEX idx_5f5d34f4ed5ca94b TO IDX_E9BC707CED5CA9E6');
        $this->addSql('ALTER TABLE service_inventory RENAME INDEX idx_5f5d34f49eea759 TO IDX_E9BC707C9EEA759');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE service_inventory RENAME INDEX idx_e9bc707c9eea759 TO IDX_5F5D34F49EEA759');
        $this->addSql('ALTER TABLE service_inventory RENAME INDEX idx_e9bc707ced5ca9e6 TO IDX_5F5D34F4ED5CA94B');
        $this->addSql('ALTER TABLE product ADD status VARCHAR(50) DEFAULT \'active\' NOT NULL');
        $this->addSql('ALTER TABLE activity_logs DROP record_type, DROP record_id');
    }
}
