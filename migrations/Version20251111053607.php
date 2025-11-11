<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251111053607 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE address (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, line1 VARCHAR(180) DEFAULT NULL, line2 VARCHAR(180) DEFAULT NULL, city VARCHAR(120) DEFAULT NULL, postal_code VARCHAR(20) DEFAULT NULL, country VARCHAR(120) DEFAULT NULL, lat DOUBLE PRECISION DEFAULT NULL, lng DOUBLE PRECISION DEFAULT NULL)');
        $this->addSql('CREATE TABLE cabinet (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, type_id INTEGER DEFAULT NULL, managing_partner_id INTEGER DEFAULT NULL, address_id INTEGER DEFAULT NULL, name VARCHAR(180) NOT NULL, slug VARCHAR(180) NOT NULL, website VARCHAR(255) DEFAULT NULL, description CLOB DEFAULT NULL, logo_url VARCHAR(255) DEFAULT NULL, is_active BOOLEAN NOT NULL, type VARCHAR(20) NOT NULL, email VARCHAR(180) DEFAULT NULL, phone VARCHAR(50) DEFAULT NULL, old_address VARCHAR(255) DEFAULT NULL, city VARCHAR(120) DEFAULT NULL, lat DOUBLE PRECISION DEFAULT NULL, lng DOUBLE PRECISION DEFAULT NULL, CONSTRAINT FK_4CED05B0C54C8C93 FOREIGN KEY (type_id) REFERENCES cabinet_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_4CED05B0E3EADEC0 FOREIGN KEY (managing_partner_id) REFERENCES lawyer (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_4CED05B0F5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4CED05B0989D9B62 ON cabinet (slug)');
        $this->addSql('CREATE INDEX IDX_4CED05B0C54C8C93 ON cabinet (type_id)');
        $this->addSql('CREATE INDEX IDX_4CED05B0E3EADEC0 ON cabinet (managing_partner_id)');
        $this->addSql('CREATE INDEX IDX_4CED05B0F5B7AF75 ON cabinet (address_id)');
        $this->addSql('CREATE TABLE cabinet_type (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(120) NOT NULL, slug VARCHAR(120) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B149CEE5E237E06 ON cabinet_type (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B149CEE989D9B62 ON cabinet_type (slug)');
        $this->addSql('CREATE TABLE email_address (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, lawyer_id INTEGER DEFAULT NULL, cabinet_id INTEGER DEFAULT NULL, label VARCHAR(30) DEFAULT NULL, email VARCHAR(180) NOT NULL, is_primary BOOLEAN DEFAULT 0 NOT NULL, position INTEGER DEFAULT 0 NOT NULL, CONSTRAINT FK_B08E074E4C19F89F FOREIGN KEY (lawyer_id) REFERENCES lawyer (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_B08E074ED351EC FOREIGN KEY (cabinet_id) REFERENCES cabinet (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_B08E074E4C19F89F ON email_address (lawyer_id)');
        $this->addSql('CREATE INDEX IDX_B08E074ED351EC ON email_address (cabinet_id)');
        $this->addSql('CREATE TABLE image (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, entity_type VARCHAR(50) NOT NULL, entity_id INTEGER NOT NULL, filename VARCHAR(255) NOT NULL, filepath VARCHAR(255) NOT NULL, label VARCHAR(255) DEFAULT NULL, category VARCHAR(50) DEFAULT NULL, description CLOB DEFAULT NULL, mime_type VARCHAR(50) NOT NULL, file_size INTEGER NOT NULL, position INTEGER DEFAULT 0 NOT NULL, is_primary BOOLEAN DEFAULT 0 NOT NULL, uploaded_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('CREATE INDEX idx_entity ON image (entity_type, entity_id)');
        $this->addSql('CREATE TABLE lawyer (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, address_id INTEGER DEFAULT NULL, cabinet_id INTEGER DEFAULT NULL, first_name VARCHAR(120) NOT NULL, last_name VARCHAR(120) NOT NULL, slug VARCHAR(150) NOT NULL, bar_number VARCHAR(50) DEFAULT NULL, biography CLOB DEFAULT NULL, photo_url VARCHAR(255) DEFAULT NULL, email VARCHAR(180) DEFAULT NULL, phone VARCHAR(50) DEFAULT NULL, city VARCHAR(120) DEFAULT NULL, CONSTRAINT FK_61EF7477F5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_61EF7477D351EC FOREIGN KEY (cabinet_id) REFERENCES cabinet (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_61EF7477989D9B62 ON lawyer (slug)');
        $this->addSql('CREATE INDEX IDX_61EF7477F5B7AF75 ON lawyer (address_id)');
        $this->addSql('CREATE INDEX IDX_61EF7477D351EC ON lawyer (cabinet_id)');
        $this->addSql('CREATE TABLE lawyer_specialty (lawyer_id INTEGER NOT NULL, specialty_id INTEGER NOT NULL, PRIMARY KEY(lawyer_id, specialty_id), CONSTRAINT FK_63117B554C19F89F FOREIGN KEY (lawyer_id) REFERENCES lawyer (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_63117B559A353316 FOREIGN KEY (specialty_id) REFERENCES specialty (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_63117B554C19F89F ON lawyer_specialty (lawyer_id)');
        $this->addSql('CREATE INDEX IDX_63117B559A353316 ON lawyer_specialty (specialty_id)');
        $this->addSql('CREATE TABLE news (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description CLOB NOT NULL, url VARCHAR(500) NOT NULL, is_active BOOLEAN DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL)');
        $this->addSql('CREATE TABLE phone (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, lawyer_id INTEGER DEFAULT NULL, cabinet_id INTEGER DEFAULT NULL, label VARCHAR(30) DEFAULT NULL, number VARCHAR(50) NOT NULL, is_primary BOOLEAN DEFAULT 0 NOT NULL, position INTEGER DEFAULT 0 NOT NULL, CONSTRAINT FK_444F97DD4C19F89F FOREIGN KEY (lawyer_id) REFERENCES lawyer (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_444F97DDD351EC FOREIGN KEY (cabinet_id) REFERENCES cabinet (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_444F97DD4C19F89F ON phone (lawyer_id)');
        $this->addSql('CREATE INDEX IDX_444F97DDD351EC ON phone (cabinet_id)');
        $this->addSql('CREATE TABLE specialty (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(120) NOT NULL, slug VARCHAR(120) NOT NULL, description CLOB DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E066A6EC989D9B62 ON specialty (slug)');
        $this->addSql('CREATE TABLE "user" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, lawyer_id INTEGER DEFAULT NULL, cabinet_id INTEGER DEFAULT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, first_name VARCHAR(120) NOT NULL, last_name VARCHAR(120) NOT NULL, is_active BOOLEAN NOT NULL, must_change_password BOOLEAN DEFAULT 0 NOT NULL, CONSTRAINT FK_8D93D6494C19F89F FOREIGN KEY (lawyer_id) REFERENCES lawyer (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_8D93D649D351EC FOREIGN KEY (cabinet_id) REFERENCES cabinet (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6494C19F89F ON "user" (lawyer_id)');
        $this->addSql('CREATE INDEX IDX_8D93D649D351EC ON "user" (cabinet_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE cabinet');
        $this->addSql('DROP TABLE cabinet_type');
        $this->addSql('DROP TABLE email_address');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE lawyer');
        $this->addSql('DROP TABLE lawyer_specialty');
        $this->addSql('DROP TABLE news');
        $this->addSql('DROP TABLE phone');
        $this->addSql('DROP TABLE specialty');
        $this->addSql('DROP TABLE "user"');
    }
}
