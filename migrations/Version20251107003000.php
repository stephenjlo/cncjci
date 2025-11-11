<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Ajout du champ must_change_password dans la table user
 */
final class Version20251107003000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute le champ must_change_password (TINYINT) pour forcer le changement de mot de passe au premier login';
    }

    public function up(Schema $schema): void
    {
        // MySQL syntax for adding boolean column
        $this->addSql('ALTER TABLE user ADD must_change_password TINYINT(1) DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP must_change_password');
    }
}
