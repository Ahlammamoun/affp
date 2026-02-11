<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260202171629 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE dossier (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, lead LONGTEXT DEFAULT NULL, content LONGTEXT DEFAULT NULL, conclusion LONGTEXT DEFAULT NULL, author_name VARCHAR(255) NOT NULL, author_bio LONGTEXT DEFAULT NULL, thumb VARCHAR(500) DEFAULT NULL, status VARCHAR(20) NOT NULL, published_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_3D48E037989D9B62 (slug), INDEX idx_dossier_slug (slug), INDEX idx_dossier_status (status), INDEX idx_dossier_published_at (published_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE dossier_article (dossier_id INT NOT NULL, article_id INT NOT NULL, INDEX IDX_4060E75E611C0C56 (dossier_id), INDEX IDX_4060E75E7294869C (article_id), PRIMARY KEY (dossier_id, article_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE dossier_article ADD CONSTRAINT FK_4060E75E611C0C56 FOREIGN KEY (dossier_id) REFERENCES dossier (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dossier_article ADD CONSTRAINT FK_4060E75E7294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE article CHANGE created_at created_at DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dossier_article DROP FOREIGN KEY FK_4060E75E611C0C56');
        $this->addSql('ALTER TABLE dossier_article DROP FOREIGN KEY FK_4060E75E7294869C');
        $this->addSql('DROP TABLE dossier');
        $this->addSql('DROP TABLE dossier_article');
        $this->addSql('ALTER TABLE article CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
