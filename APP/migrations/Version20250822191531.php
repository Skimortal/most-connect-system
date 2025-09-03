<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250822191531 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE email_template (id INT AUTO_INCREMENT NOT NULL, company_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, template_key VARCHAR(190) NOT NULL, locale VARCHAR(10) NOT NULL, subject_tpl LONGTEXT NOT NULL, html_tpl LONGTEXT NOT NULL, text_tpl LONGTEXT DEFAULT NULL, variables_hint JSON DEFAULT NULL, INDEX IDX_9C0600CA979B1AD6 (company_id), UNIQUE INDEX uniq_template_key_locale_company (template_key, locale, company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE email_template ADD CONSTRAINT FK_9C0600CA979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE email_template DROP FOREIGN KEY FK_9C0600CA979B1AD6');
        $this->addSql('DROP TABLE email_template');
    }
}
