<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251207000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add ActivityLog entity and update User entity with email, status, and timestamps';
    }

    public function up(Schema $schema): void
    {
        // Create activity_logs table only if it doesn't exist
        $schemaManager = $this->connection->createSchemaManager();
        if (!$schemaManager->tablesExist(['activity_logs'])) {
            $this->addSql('CREATE TABLE activity_logs (
                id INT AUTO_INCREMENT NOT NULL,
                user_id INT NOT NULL,
                username VARCHAR(255) NOT NULL,
                role VARCHAR(50) NOT NULL,
                action VARCHAR(50) NOT NULL,
                target_data LONGTEXT,
                created_at DATETIME NOT NULL,
                INDEX idx_user_id (user_id),
                INDEX idx_action (action),
                INDEX idx_created_at (created_at),
                PRIMARY KEY (id),
                CONSTRAINT FK_ACTIVITY_USER FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        }

        // Update user table - add columns only if they don't exist
        $columns = $schemaManager->listTableColumns('user');
        
        if (!isset($columns['email'])) {
            $this->addSql('ALTER TABLE user ADD COLUMN email VARCHAR(255) NOT NULL DEFAULT \'\'');
        }
        
        if (!isset($columns['status'])) {
            $this->addSql('ALTER TABLE user ADD COLUMN status VARCHAR(20) NOT NULL DEFAULT \'active\'');
        }
        
        if (!isset($columns['created_at'])) {
            $this->addSql('ALTER TABLE user ADD COLUMN created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');
        }
        
        if (!isset($columns['updated_at'])) {
            $this->addSql('ALTER TABLE user ADD COLUMN updated_at DATETIME');
        }
        
        // Only add index if email column was just created
        if (!isset($columns['email'])) {
            $this->addSql('ALTER TABLE user ADD UNIQUE INDEX UNIQ_EMAIL (email)');
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS activity_logs');
        $this->addSql('ALTER TABLE user DROP COLUMN IF EXISTS email');
        $this->addSql('ALTER TABLE user DROP COLUMN IF EXISTS status');
        $this->addSql('ALTER TABLE user DROP COLUMN IF EXISTS created_at');
        $this->addSql('ALTER TABLE user DROP COLUMN IF EXISTS updated_at');
    }
}
