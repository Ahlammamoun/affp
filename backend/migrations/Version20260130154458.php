<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260130154458 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(180) NOT NULL, excerpt LONGTEXT NOT NULL, content LONGTEXT NOT NULL, status VARCHAR(20) NOT NULL, published_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_23A0E66989D9B62 (slug), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE article_reaction (id INT AUTO_INCREMENT NOT NULL, value SMALLINT NOT NULL, fingerprint VARCHAR(64) NOT NULL, created_at DATETIME NOT NULL, article_id INT NOT NULL, INDEX IDX_F13FF39C7294869C (article_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE footer_link (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(100) NOT NULL, url VARCHAR(500) NOT NULL, position INT NOT NULL, group_name VARCHAR(50) NOT NULL, is_active TINYINT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE media (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(20) NOT NULL, url VARCHAR(500) NOT NULL, caption VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, article_id INT DEFAULT NULL, INDEX IDX_6A2CA10C7294869C (article_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE site_setting (id INT AUTO_INCREMENT NOT NULL, setting_key VARCHAR(100) NOT NULL, setting_value LONGTEXT NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_64D05A535FA1E697 (setting_key), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE article_reaction ADD CONSTRAINT FK_F13FF39C7294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C7294869C FOREIGN KEY (article_id) REFERENCES article (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article_reaction DROP FOREIGN KEY FK_F13FF39C7294869C');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10C7294869C');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE article_reaction');
        $this->addSql('DROP TABLE footer_link');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE site_setting');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
