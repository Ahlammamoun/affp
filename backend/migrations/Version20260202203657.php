<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260202203657 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE featured_slot (id INT AUTO_INCREMENT NOT NULL, `key` VARCHAR(64) NOT NULL, starts_at DATETIME DEFAULT NULL, ends_at DATETIME DEFAULT NULL, article_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_56C8AD1B8A90ABA9 (`key`), INDEX IDX_56C8AD1B7294869C (article_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE featured_slot ADD CONSTRAINT FK_56C8AD1B7294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE featured_slot DROP FOREIGN KEY FK_56C8AD1B7294869C');
        $this->addSql('DROP TABLE featured_slot');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
