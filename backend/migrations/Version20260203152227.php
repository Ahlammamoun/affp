<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260203152227 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE dossier_reaction (id INT AUTO_INCREMENT NOT NULL, value SMALLINT DEFAULT 1 NOT NULL, fingerprint VARCHAR(64) NOT NULL, created_at DATETIME NOT NULL, dossier_id INT NOT NULL, INDEX IDX_8C97E580611C0C56 (dossier_id), INDEX dossier_reaction_fp_idx (fingerprint), UNIQUE INDEX uniq_dossier_fp (dossier_id, fingerprint), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(180) NOT NULL, category VARCHAR(30) NOT NULL, city VARCHAR(120) DEFAULT NULL, country VARCHAR(120) DEFAULT NULL, event_at DATETIME NOT NULL, link VARCHAR(500) DEFAULT NULL, thumb VARCHAR(500) DEFAULT NULL, description LONGTEXT DEFAULT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_3BAE0AA7989D9B62 (slug), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE dossier_reaction ADD CONSTRAINT FK_8C97E580611C0C56 FOREIGN KEY (dossier_id) REFERENCES dossier (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dossier_reaction DROP FOREIGN KEY FK_8C97E580611C0C56');
        $this->addSql('DROP TABLE dossier_reaction');
        $this->addSql('DROP TABLE event');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
