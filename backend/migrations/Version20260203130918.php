<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260203130918 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ad_slide (id INT AUTO_INCREMENT NOT NULL, badge VARCHAR(50) NOT NULL, title VARCHAR(255) NOT NULL, text VARCHAR(500) NOT NULL, href VARCHAR(255) NOT NULL, is_active TINYINT DEFAULT 1 NOT NULL, position INT DEFAULT 0 NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE ad_slide');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
