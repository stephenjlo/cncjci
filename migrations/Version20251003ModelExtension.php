<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251003ModelExtension extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add CabinetType, Address, Phone, EmailAddress + relations (managingPartner, description).';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE IF NOT EXISTS cabinet_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(120) NOT NULL, slug VARCHAR(120) NOT NULL, UNIQUE INDEX UNIQ_CABTYPE_NAME (name), UNIQUE INDEX UNIQ_CABTYPE_SLUG (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS address (id INT AUTO_INCREMENT NOT NULL, line1 VARCHAR(180) DEFAULT NULL, line2 VARCHAR(180) DEFAULT NULL, city VARCHAR(120) DEFAULT NULL, postal_code VARCHAR(20) DEFAULT NULL, country VARCHAR(120) DEFAULT NULL, lat DOUBLE PRECISION DEFAULT NULL, lng DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS phone (id INT AUTO_INCREMENT NOT NULL, lawyer_id INT DEFAULT NULL, cabinet_id INT DEFAULT NULL, label VARCHAR(30) DEFAULT NULL, number VARCHAR(50) NOT NULL, is_primary TINYINT(1) DEFAULT 0 NOT NULL, position INT DEFAULT 0 NOT NULL, INDEX IDX_PHONE_LAWYER (lawyer_id), INDEX IDX_PHONE_CABINET (cabinet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS email_address (id INT AUTO_INCREMENT NOT NULL, lawyer_id INT DEFAULT NULL, cabinet_id INT DEFAULT NULL, label VARCHAR(30) DEFAULT NULL, email VARCHAR(180) NOT NULL, is_primary TINYINT(1) DEFAULT 0 NOT NULL, position INT DEFAULT 0 NOT NULL, INDEX IDX_EMAIL_LAWYER (lawyer_id), INDEX IDX_EMAIL_CABINET (cabinet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE cabinet ADD type_id INT DEFAULT NULL, ADD managing_partner_id INT DEFAULT NULL, ADD description LONGTEXT DEFAULT NULL, ADD address_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cabinet ADD CONSTRAINT FK_CABINET_TYPE FOREIGN KEY (type_id) REFERENCES cabinet_type (id)');
        $this->addSql('ALTER TABLE cabinet ADD CONSTRAINT FK_CABINET_MP FOREIGN KEY (managing_partner_id) REFERENCES lawyer (id)');
        $this->addSql('ALTER TABLE cabinet ADD CONSTRAINT FK_CABINET_ADDRESS FOREIGN KEY (address_id) REFERENCES address (id)');
        $this->addSql('CREATE INDEX IDX_CABINET_TYPE ON cabinet (type_id)');
        $this->addSql('CREATE INDEX IDX_CABINET_MP ON cabinet (managing_partner_id)');
        $this->addSql('CREATE INDEX IDX_CABINET_ADDRESS ON cabinet (address_id)');

        $this->addSql('ALTER TABLE lawyer ADD address_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lawyer ADD CONSTRAINT FK_LAWYER_ADDRESS FOREIGN KEY (address_id) REFERENCES address (id)');
        $this->addSql('CREATE INDEX IDX_LAWYER_ADDRESS ON lawyer (address_id)');

        $this->addSql('ALTER TABLE phone ADD CONSTRAINT FK_PHONE_LAWYER FOREIGN KEY (lawyer_id) REFERENCES lawyer (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE phone ADD CONSTRAINT FK_PHONE_CABINET FOREIGN KEY (cabinet_id) REFERENCES cabinet (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE email_address ADD CONSTRAINT FK_EMAIL_LAWYER FOREIGN KEY (lawyer_id) REFERENCES lawyer (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE email_address ADD CONSTRAINT FK_EMAIL_CABINET FOREIGN KEY (cabinet_id) REFERENCES cabinet (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cabinet DROP FOREIGN KEY FK_CABINET_TYPE');
        $this->addSql('ALTER TABLE cabinet DROP FOREIGN KEY FK_CABINET_MP');
        $this->addSql('ALTER TABLE cabinet DROP FOREIGN KEY FK_CABINET_ADDRESS');
        $this->addSql('ALTER TABLE lawyer DROP FOREIGN KEY FK_LAWYER_ADDRESS');
        $this->addSql('ALTER TABLE phone DROP FOREIGN KEY FK_PHONE_LAWYER');
        $this->addSql('ALTER TABLE phone DROP FOREIGN KEY FK_PHONE_CABINET');
        $this->addSql('ALTER TABLE email_address DROP FOREIGN KEY FK_EMAIL_LAWYER');
        $this->addSql('ALTER TABLE email_address DROP FOREIGN KEY FK_EMAIL_CABINET');

        $this->addSql('DROP TABLE IF EXISTS email_address');
        $this->addSql('DROP TABLE IF EXISTS phone');
        $this->addSql('DROP TABLE IF EXISTS address');
        $this->addSql('DROP TABLE IF EXISTS cabinet_type');

        $this->addSql('ALTER TABLE cabinet DROP INDEX IDX_CABINET_TYPE, DROP INDEX IDX_CABINET_MP, DROP INDEX IDX_CABINET_ADDRESS, DROP COLUMN type_id, DROP COLUMN managing_partner_id, DROP COLUMN description, DROP COLUMN address_id');
        $this->addSql('DROP INDEX IDX_LAWYER_ADDRESS ON lawyer');
        $this->addSql('ALTER TABLE lawyer DROP COLUMN address_id');
    }
}
