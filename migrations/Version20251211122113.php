<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251211122113 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Migration skipped - table and constraints already exist
        // $this->addSql('CREATE TABLE IF NOT EXISTS booking_inventory (...)');
        // Foreign keys are already present in the database
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking_inventory DROP FOREIGN KEY FK_221F81353301C60');
        $this->addSql('ALTER TABLE booking_inventory DROP FOREIGN KEY FK_221F81359EEA759');
        $this->addSql('DROP TABLE booking_inventory');
    }
}
