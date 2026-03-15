<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251206060539 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inventory DROP FOREIGN KEY FK_B12D4A362ADD6D8C');
        $this->addSql('ALTER TABLE inventory ADD CONSTRAINT FK_B12D4A362ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inventory DROP FOREIGN KEY FK_B12D4A362ADD6D8C');
        $this->addSql('ALTER TABLE inventory ADD CONSTRAINT FK_B12D4A362ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
