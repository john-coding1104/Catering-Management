<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251206031341 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inventory ADD supplier_id INT DEFAULT NULL, DROP supplier');
        $this->addSql('ALTER TABLE inventory ADD CONSTRAINT FK_B12D4A362ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (id)');
        $this->addSql('CREATE INDEX IDX_B12D4A362ADD6D8C ON inventory (supplier_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inventory DROP FOREIGN KEY FK_B12D4A362ADD6D8C');
        $this->addSql('DROP INDEX IDX_B12D4A362ADD6D8C ON inventory');
        $this->addSql('ALTER TABLE inventory ADD supplier VARCHAR(100) DEFAULT NULL, DROP supplier_id');
    }
}
