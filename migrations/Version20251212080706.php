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
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking ADD created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_E00CEDDEB03A8386 ON booking (created_by_id)');
        $this->addSql('ALTER TABLE inventory ADD created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE inventory ADD CONSTRAINT FK_B12D4A36B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_B12D4A36B03A8386 ON inventory (created_by_id)');
        $this->addSql('ALTER TABLE product ADD created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_D34A04ADB03A8386 ON product (created_by_id)');
        $this->addSql('ALTER TABLE services ADD created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE services ADD CONSTRAINT FK_7332E169B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_7332E169B03A8386 ON services (created_by_id)');
        $this->addSql('ALTER TABLE supplier ADD created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE supplier ADD CONSTRAINT FK_9B2A6C7EB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_9B2A6C7EB03A8386 ON supplier (created_by_id)');
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
