<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250812120653 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tax_rate ADD company_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tax_rate ADD CONSTRAINT FK_C36330C1979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('CREATE INDEX IDX_C36330C1979B1AD6 ON tax_rate (company_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tax_rate DROP FOREIGN KEY FK_C36330C1979B1AD6');
        $this->addSql('DROP INDEX IDX_C36330C1979B1AD6 ON tax_rate');
        $this->addSql('ALTER TABLE tax_rate DROP company_id');
    }
}
