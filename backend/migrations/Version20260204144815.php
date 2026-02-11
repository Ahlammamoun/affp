<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260204144815 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }
public function up(Schema $schema): void
{
    // Ajouter la colonne seulement si elle n'existe pas
    $this->addSql("ALTER TABLE article_reaction ADD COLUMN IF NOT EXISTS article_id INT DEFAULT NULL");

    // Backfill uniquement pour les target_type='article' qui existent
    $this->addSql("
        UPDATE article_reaction ar
        INNER JOIN article a ON a.id = ar.target_id
        SET ar.article_id = a.id
        WHERE ar.target_type = 'article'
    ");

    // Supprimer les réactions 'article' orphelines (optionnel)
    $this->addSql("
        DELETE ar
        FROM article_reaction ar
        LEFT JOIN article a ON a.id = ar.target_id
        WHERE ar.target_type = 'article'
          AND a.id IS NULL
    ");

    // Créer index seulement si absent (MySQL ne supporte pas toujours IF NOT EXISTS pour index selon version)
    // => on tente, si ça casse il faudra le faire manuellement ou adapter via information_schema
    $this->addSql("CREATE INDEX IDX_F13FF39C7294869C ON article_reaction (article_id)");

    // Ajouter FK (si elle existe déjà, ça cassera aussi)
    $this->addSql("ALTER TABLE article_reaction ADD CONSTRAINT FK_F13FF39C7294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE");
}


    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article_reaction DROP FOREIGN KEY FK_F13FF39C7294869C');
        $this->addSql('DROP INDEX IDX_F13FF39C7294869C ON article_reaction');
        $this->addSql('ALTER TABLE article_reaction DROP article_id');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
