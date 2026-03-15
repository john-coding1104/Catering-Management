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
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE booking_inventory (id INT AUTO_INCREMENT NOT NULL, booking_id INT NOT NULL, inventory_id INT NOT NULL, quantity_used DOUBLE PRECISION NOT NULL, added_at DATETIME DEFAULT NULL, INDEX IDX_221F81353301C60 (booking_id), INDEX IDX_221F81359EEA759 (inventory_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE booking_inventory ADD CONSTRAINT FK_221F81353301C60 FOREIGN KEY (booking_id) REFERENCES booking (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE booking_inventory ADD CONSTRAINT FK_221F81359EEA759 FOREIGN KEY (inventory_id) REFERENCES inventory (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking_inventory DROP FOREIGN KEY FK_221F81353301C60');
        $this->addSql('ALTER TABLE booking_inventory DROP FOREIGN KEY FK_221F81359EEA759');
        $this->addSql('DROP TABLE booking_inventory');
    }
}
