<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260203145215 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article_reaction DROP FOREIGN KEY `FK_F13FF39C7294869C`');
        $this->addSql('DROP INDEX IDX_F13FF39C7294869C ON article_reaction');
        $this->addSql('DROP INDEX uniq_article_fingerprint ON article_reaction');
        $this->addSql('ALTER TABLE article_reaction ADD target_type VARCHAR(20) NOT NULL, CHANGE value value SMALLINT DEFAULT 1 NOT NULL, CHANGE article_id target_id INT NOT NULL');
        $this->addSql('CREATE INDEX reaction_target_idx ON article_reaction (target_type, target_id)');
        $this->addSql('CREATE INDEX reaction_fp_idx ON article_reaction (fingerprint)');
        $this->addSql('CREATE UNIQUE INDEX reaction_unique_like ON article_reaction (fingerprint, target_type, target_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX reaction_target_idx ON article_reaction');
        $this->addSql('DROP INDEX reaction_fp_idx ON article_reaction');
        $this->addSql('DROP INDEX reaction_unique_like ON article_reaction');
        $this->addSql('ALTER TABLE article_reaction DROP target_type, CHANGE value value SMALLINT NOT NULL, CHANGE target_id article_id INT NOT NULL');
        $this->addSql('ALTER TABLE article_reaction ADD CONSTRAINT `FK_F13FF39C7294869C` FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('CREATE INDEX IDX_F13FF39C7294869C ON article_reaction (article_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_article_fingerprint ON article_reaction (article_id, fingerprint)');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
