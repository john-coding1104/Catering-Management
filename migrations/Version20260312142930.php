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
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activity_logs (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, username VARCHAR(255) NOT NULL, role VARCHAR(50) NOT NULL, action VARCHAR(50) NOT NULL, record_type VARCHAR(100) DEFAULT NULL, record_id INT DEFAULT NULL, target_data LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, INDEX idx_user_id (user_id), INDEX idx_action (action), INDEX idx_created_at (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE booking (id INT AUTO_INCREMENT NOT NULL, service_id INT NOT NULL, created_by_id INT DEFAULT NULL, customer_name VARCHAR(255) NOT NULL, event_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', status VARCHAR(32) NOT NULL, guest_count INT NOT NULL, total_price DOUBLE PRECISION NOT NULL, INDEX IDX_E00CEDDEED5CA9E6 (service_id), INDEX IDX_E00CEDDEB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE booking_inventory (id INT AUTO_INCREMENT NOT NULL, booking_id INT NOT NULL, inventory_id INT NOT NULL, quantity_used DOUBLE PRECISION NOT NULL, added_at DATETIME DEFAULT NULL, INDEX IDX_221F81353301C60 (booking_id), INDEX IDX_221F81359EEA759 (inventory_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE inventory (id INT AUTO_INCREMENT NOT NULL, supplier_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, product_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, category VARCHAR(100) NOT NULL, current_stock INT NOT NULL, minimum_stock INT NOT NULL, maximum_stock INT NOT NULL, unit_price NUMERIC(10, 2) NOT NULL, unit VARCHAR(50) NOT NULL, supplier_contact VARCHAR(255) DEFAULT NULL, last_restocked DATE DEFAULT NULL, expiry_date DATE DEFAULT NULL, status VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, image_path VARCHAR(255) DEFAULT NULL, INDEX IDX_B12D4A362ADD6D8C (supplier_id), INDEX IDX_B12D4A36B03A8386 (created_by_id), INDEX IDX_B12D4A364584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, supplier_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, category VARCHAR(100) NOT NULL, unit_price NUMERIC(10, 2) NOT NULL, create_date DATETIME NOT NULL, image_path VARCHAR(255) DEFAULT NULL, INDEX IDX_D34A04AD2ADD6D8C (supplier_id), INDEX IDX_D34A04ADB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_inventory (id INT AUTO_INCREMENT NOT NULL, service_id INT NOT NULL, inventory_id INT NOT NULL, quantity_used DOUBLE PRECISION NOT NULL, added_at DATETIME DEFAULT NULL, INDEX IDX_E9BC707CED5CA9E6 (service_id), INDEX IDX_E9BC707C9EEA759 (inventory_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE services (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, event_type VARCHAR(255) NOT NULL, base_price DOUBLE PRECISION NOT NULL, min_guests INT NOT NULL, max_guests INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL, image VARCHAR(255) DEFAULT NULL, INDEX IDX_7332E169B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE services_inventory (services_id INT NOT NULL, inventory_id INT NOT NULL, INDEX IDX_E666CF8FAEF5A6C1 (services_id), INDEX IDX_E666CF8F9EEA759 (inventory_id), PRIMARY KEY(services_id, inventory_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE supplier (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, contact VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, image_path VARCHAR(255) DEFAULT NULL, product VARCHAR(255) DEFAULT NULL, INDEX IDX_9B2A6C7EB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, email VARCHAR(255) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, status VARCHAR(20) DEFAULT \'active\' NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE activity_logs ADD CONSTRAINT FK_F34B1DCEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEED5CA9E6 FOREIGN KEY (service_id) REFERENCES services (id)');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE booking_inventory ADD CONSTRAINT FK_221F81353301C60 FOREIGN KEY (booking_id) REFERENCES booking (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE booking_inventory ADD CONSTRAINT FK_221F81359EEA759 FOREIGN KEY (inventory_id) REFERENCES inventory (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE inventory ADD CONSTRAINT FK_B12D4A362ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE inventory ADD CONSTRAINT FK_B12D4A36B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE inventory ADD CONSTRAINT FK_B12D4A364584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD2ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE service_inventory ADD CONSTRAINT FK_E9BC707CED5CA9E6 FOREIGN KEY (service_id) REFERENCES services (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_inventory ADD CONSTRAINT FK_E9BC707C9EEA759 FOREIGN KEY (inventory_id) REFERENCES inventory (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE services ADD CONSTRAINT FK_7332E169B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE services_inventory ADD CONSTRAINT FK_E666CF8FAEF5A6C1 FOREIGN KEY (services_id) REFERENCES services (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE services_inventory ADD CONSTRAINT FK_E666CF8F9EEA759 FOREIGN KEY (inventory_id) REFERENCES inventory (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE supplier ADD CONSTRAINT FK_9B2A6C7EB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
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
