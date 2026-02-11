<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260204174820 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }
    public function up(Schema $schema): void
    {
        $sm = $this->connection->createSchemaManager();

        // 1) colonne
        $cols = $sm->listTableColumns('media');
        if (!isset($cols['destination_id'])) {
            $this->addSql('ALTER TABLE media ADD destination_id INT DEFAULT NULL');
        }

        // 2) foreign key (si pas déjà là)
        $fks = $sm->listTableForeignKeys('media');
        $hasFk = false;
        foreach ($fks as $fk) {
            if ($fk->getName() === 'FK_6A2CA10C816C6140') {
                $hasFk = true;
                break;
            }
        }
        if (!$hasFk) {
            $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C816C6140 FOREIGN KEY (destination_id) REFERENCES destination (id) ON DELETE CASCADE');
        }

        // 3) index (si pas déjà là)
        $idx = $sm->listTableIndexes('media');
        if (!isset($idx['idx_6a2ca10c816c6140'])) {
            $this->addSql('CREATE INDEX IDX_6A2CA10C816C6140 ON media (destination_id)');
        }
    }


    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10C816C6140');
        $this->addSql('DROP INDEX IDX_6A2CA10C816C6140 ON media');
        $this->addSql('ALTER TABLE media DROP destination_id');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
