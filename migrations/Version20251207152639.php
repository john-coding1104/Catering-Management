<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251207152639 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Check if tables/columns already exist before creating/altering
        $schemaManager = $this->connection->createSchemaManager();
        
        if (!$schemaManager->tablesExist(['activity_logs'])) {
            $this->addSql('CREATE TABLE activity_logs (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, username VARCHAR(255) NOT NULL, role VARCHAR(50) NOT NULL, action VARCHAR(50) NOT NULL, target_data LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, INDEX idx_user_id (user_id), INDEX idx_action (action), INDEX idx_created_at (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('ALTER TABLE activity_logs ADD CONSTRAINT FK_F34B1DCEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        }
        
        $columns = $schemaManager->listTableColumns('user');
        if (!isset($columns['email']) && !isset($columns['status']) && !isset($columns['created_at'])) {
            $this->addSql('ALTER TABLE user ADD email VARCHAR(255) NOT NULL, ADD status VARCHAR(20) DEFAULT \'active\' NOT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
            $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity_logs DROP FOREIGN KEY FK_F34B1DCEA76ED395');
        $this->addSql('DROP TABLE activity_logs');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
        $this->addSql('ALTER TABLE user DROP email, DROP status, DROP created_at, DROP updated_at');
    }
}
