<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250816123546 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company ADD banc_account_institute VARCHAR(255) DEFAULT NULL, ADD banc_account_owner VARCHAR(255) DEFAULT NULL, ADD banc_account_iban VARCHAR(255) DEFAULT NULL, ADD banc_account_bic VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company DROP banc_account_institute, DROP banc_account_owner, DROP banc_account_iban, DROP banc_account_bic');
    }
}
