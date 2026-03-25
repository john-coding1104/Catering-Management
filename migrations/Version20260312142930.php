<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260312142930 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Migration skipped - all tables already exist in the database
        // This migration was likely auto-generated and the database was already populated
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity_logs DROP FOREIGN KEY FK_F34B1DCEA76ED395');
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDEED5CA9E6');
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDEB03A8386');
        $this->addSql('ALTER TABLE booking_inventory DROP FOREIGN KEY FK_221F81353301C60');
        $this->addSql('ALTER TABLE booking_inventory DROP FOREIGN KEY FK_221F81359EEA759');
        $this->addSql('ALTER TABLE inventory DROP FOREIGN KEY FK_B12D4A362ADD6D8C');
        $this->addSql('ALTER TABLE inventory DROP FOREIGN KEY FK_B12D4A36B03A8386');
        $this->addSql('ALTER TABLE inventory DROP FOREIGN KEY FK_B12D4A364584665A');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD2ADD6D8C');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADB03A8386');
        $this->addSql('ALTER TABLE service_inventory DROP FOREIGN KEY FK_E9BC707CED5CA9E6');
        $this->addSql('ALTER TABLE service_inventory DROP FOREIGN KEY FK_E9BC707C9EEA759');
        $this->addSql('ALTER TABLE services DROP FOREIGN KEY FK_7332E169B03A8386');
        $this->addSql('ALTER TABLE services_inventory DROP FOREIGN KEY FK_E666CF8FAEF5A6C1');
        $this->addSql('ALTER TABLE services_inventory DROP FOREIGN KEY FK_E666CF8F9EEA759');
        $this->addSql('ALTER TABLE supplier DROP FOREIGN KEY FK_9B2A6C7EB03A8386');
        $this->addSql('DROP TABLE activity_logs');
        $this->addSql('DROP TABLE booking');
        $this->addSql('DROP TABLE booking_inventory');
        $this->addSql('DROP TABLE inventory');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE service_inventory');
        $this->addSql('DROP TABLE services');
        $this->addSql('DROP TABLE services_inventory');
        $this->addSql('DROP TABLE supplier');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
