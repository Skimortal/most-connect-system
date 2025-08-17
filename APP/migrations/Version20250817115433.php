<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250817115433 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice ADD tax_sum NUMERIC(10, 2) DEFAULT NULL, ADD total_netto NUMERIC(10, 2) DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice_item ADD tax_sum NUMERIC(10, 2) NOT NULL, ADD total_netto NUMERIC(10, 2) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice DROP tax_sum, DROP total_netto');
        $this->addSql('ALTER TABLE invoice_item DROP tax_sum, DROP total_netto');
    }
}
