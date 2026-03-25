<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify your needs!
 */
final class Version20251211100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create service_inventory table to track inventory usage in services';
    }

    public function up(Schema $schema): void
    {
        // Create the service_inventory table
        $this->addSql('CREATE TABLE IF NOT EXISTS service_inventory (id INT AUTO_INCREMENT NOT NULL, service_id INT NOT NULL, inventory_id INT NOT NULL, quantity_used DOUBLE PRECISION NOT NULL, added_at DATETIME DEFAULT NULL, INDEX IDX_5F5D34F4ED5CA94B (service_id), INDEX IDX_5F5D34F49EEA759 (inventory_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE service_inventory ADD CONSTRAINT FK_5F5D34F4ED5CA94B FOREIGN KEY (service_id) REFERENCES services (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_inventory ADD CONSTRAINT FK_5F5D34F49EEA759 FOREIGN KEY (inventory_id) REFERENCES inventory (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // Drop the service_inventory table
        $this->addSql('ALTER TABLE service_inventory DROP FOREIGN KEY FK_5F5D34F4ED5CA94B');
        $this->addSql('ALTER TABLE service_inventory DROP FOREIGN KEY FK_5F5D34F49EEA759');
        $this->addSql('DROP TABLE service_inventory');
    }
}
