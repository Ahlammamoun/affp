<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260202204903 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_56C8AD1B8A90ABA9 ON featured_slot');
        $this->addSql('ALTER TABLE featured_slot CHANGE `key` slot_key VARCHAR(64) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_56C8AD1B18A4F944 ON featured_slot (slot_key)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_56C8AD1B18A4F944 ON featured_slot');
        $this->addSql('ALTER TABLE featured_slot CHANGE slot_key `key` VARCHAR(64) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_56C8AD1B8A90ABA9 ON featured_slot (`key`)');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
