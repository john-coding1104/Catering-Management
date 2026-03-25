<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251212080706 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Migration skipped - created_by_id columns already exist in all tables
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDEB03A8386');
        $this->addSql('DROP INDEX IDX_E00CEDDEB03A8386 ON booking');
        $this->addSql('ALTER TABLE booking DROP created_by_id');
        $this->addSql('ALTER TABLE supplier DROP FOREIGN KEY FK_9B2A6C7EB03A8386');
        $this->addSql('DROP INDEX IDX_9B2A6C7EB03A8386 ON supplier');
        $this->addSql('ALTER TABLE supplier DROP created_by_id');
        $this->addSql('ALTER TABLE inventory DROP FOREIGN KEY FK_B12D4A36B03A8386');
        $this->addSql('DROP INDEX IDX_B12D4A36B03A8386 ON inventory');
        $this->addSql('ALTER TABLE inventory DROP created_by_id');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADB03A8386');
        $this->addSql('DROP INDEX IDX_D34A04ADB03A8386 ON product');
        $this->addSql('ALTER TABLE product DROP created_by_id');
        $this->addSql('ALTER TABLE services DROP FOREIGN KEY FK_7332E169B03A8386');
        $this->addSql('DROP INDEX IDX_7332E169B03A8386 ON services');
        $this->addSql('ALTER TABLE services DROP created_by_id');
    }
}
