<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251202072617 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE booking (id INT AUTO_INCREMENT NOT NULL, service_id INT NOT NULL, customer_name VARCHAR(255) NOT NULL, event_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', status VARCHAR(32) NOT NULL, guest_count INT NOT NULL, total_price DOUBLE PRECISION NOT NULL, INDEX IDX_E00CEDDEED5CA9E6 (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE inventory (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, category VARCHAR(100) NOT NULL, current_stock INT NOT NULL, minimum_stock INT NOT NULL, maximum_stock INT NOT NULL, unit_price NUMERIC(10, 2) NOT NULL, unit VARCHAR(50) NOT NULL, supplier VARCHAR(100) DEFAULT NULL, supplier_contact VARCHAR(255) DEFAULT NULL, last_restocked DATE DEFAULT NULL, expiry_date DATE DEFAULT NULL, status VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, image_path VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE services (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, event_type VARCHAR(255) NOT NULL, base_price DOUBLE PRECISION NOT NULL, min_guests INT NOT NULL, max_guests INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL, image VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE services_inventory (services_id INT NOT NULL, inventory_id INT NOT NULL, INDEX IDX_E666CF8FAEF5A6C1 (services_id), INDEX IDX_E666CF8F9EEA759 (inventory_id), PRIMARY KEY(services_id, inventory_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEED5CA9E6 FOREIGN KEY (service_id) REFERENCES services (id)');
        $this->addSql('ALTER TABLE services_inventory ADD CONSTRAINT FK_E666CF8FAEF5A6C1 FOREIGN KEY (services_id) REFERENCES services (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE services_inventory ADD CONSTRAINT FK_E666CF8F9EEA759 FOREIGN KEY (inventory_id) REFERENCES inventory (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDEED5CA9E6');
        $this->addSql('ALTER TABLE services_inventory DROP FOREIGN KEY FK_E666CF8FAEF5A6C1');
        $this->addSql('ALTER TABLE services_inventory DROP FOREIGN KEY FK_E666CF8F9EEA759');
        $this->addSql('DROP TABLE booking');
        $this->addSql('DROP TABLE inventory');
        $this->addSql('DROP TABLE services');
        $this->addSql('DROP TABLE services_inventory');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
