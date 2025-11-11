<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251110130319 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, entity_type VARCHAR(50) NOT NULL, entity_id INT NOT NULL, filename VARCHAR(255) NOT NULL, filepath VARCHAR(255) NOT NULL, label VARCHAR(255) DEFAULT NULL, category VARCHAR(50) DEFAULT NULL, description LONGTEXT DEFAULT NULL, mime_type VARCHAR(50) NOT NULL, file_size INT NOT NULL, position INT DEFAULT 0 NOT NULL, is_primary TINYINT(1) DEFAULT 0 NOT NULL, uploaded_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX idx_entity (entity_type, entity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE address CHANGE country country VARCHAR(120) DEFAULT NULL');
        $this->addSql('ALTER TABLE cabinet CHANGE is_active is_active TINYINT(1) NOT NULL, CHANGE type type VARCHAR(20) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE is_active is_active TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE image');
        $this->addSql('ALTER TABLE address CHANGE country country VARCHAR(120) DEFAULT \'Côte d\'\'Ivoire\'');
        $this->addSql('ALTER TABLE `user` CHANGE is_active is_active TINYINT(1) DEFAULT 1 NOT NULL');
        $this->addSql('ALTER TABLE cabinet CHANGE is_active is_active TINYINT(1) DEFAULT 1 NOT NULL, CHANGE type type VARCHAR(20) DEFAULT \'Cabinet\' NOT NULL');
    }
}
