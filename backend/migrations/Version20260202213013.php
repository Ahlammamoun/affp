<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260202213013 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE live_update (id INT AUTO_INCREMENT NOT NULL, tag VARCHAR(32) DEFAULT NULL, title VARCHAR(255) NOT NULL, url VARCHAR(500) DEFAULT NULL, happened_at DATETIME NOT NULL, is_active TINYINT DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, article_id INT DEFAULT NULL, INDEX IDX_FB7383AC7294869C (article_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE live_update ADD CONSTRAINT FK_FB7383AC7294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE live_update DROP FOREIGN KEY FK_FB7383AC7294869C');
        $this->addSql('DROP TABLE live_update');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
