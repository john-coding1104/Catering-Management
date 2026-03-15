<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251209000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ensure activity_logs table has all required columns';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->getTable('activity_logs');

        // Check and add columns if they don't exist
        if (!$table->hasColumn('id')) {
            $table->addColumn('id', 'integer', ['autoincrement' => true]);
            $table->setPrimaryKey(['id']);
        }

        if (!$table->hasColumn('user_id')) {
            $table->addColumn('user_id', 'integer', ['notnull' => true]);
            $table->addForeignKeyConstraint('user', ['user_id'], ['id'], ['onDelete' => 'CASCADE']);
        }

        if (!$table->hasColumn('username')) {
            $table->addColumn('username', 'string', ['length' => 255]);
        }

        if (!$table->hasColumn('role')) {
            $table->addColumn('role', 'string', ['length' => 50]);
        }

        if (!$table->hasColumn('action')) {
            $table->addColumn('action', 'string', ['length' => 50]);
        }

        if (!$table->hasColumn('target_data')) {
            $table->addColumn('target_data', 'text', ['notnull' => false]);
        }

        if (!$table->hasColumn('created_at')) {
            $table->addColumn('created_at', 'datetime');
        }

        // Create indexes if they don't exist
        if (!$table->hasIndex('idx_user_id')) {
            $table->addIndex(['user_id'], 'idx_user_id');
        }

        if (!$table->hasIndex('idx_action')) {
            $table->addIndex(['action'], 'idx_action');
        }

        if (!$table->hasIndex('idx_created_at')) {
            $table->addIndex(['created_at'], 'idx_created_at');
        }
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('activity_logs');
    }
}
