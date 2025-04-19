<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250419231738 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE issue ADD creator_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE issue RENAME COLUMN type TO category
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE issue ADD CONSTRAINT FK_12AD233E61220EA6 FOREIGN KEY (creator_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_12AD233E61220EA6 ON issue (creator_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER INDEX idx_14b784184bd2a4c0 RENAME TO IDX_14B784185E7AA58C
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE issue DROP CONSTRAINT FK_12AD233E61220EA6
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_12AD233E61220EA6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE issue DROP creator_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE issue RENAME COLUMN category TO type
        SQL);
        $this->addSql(<<<'SQL'
            ALTER INDEX idx_14b784185e7aa58c RENAME TO idx_14b784184bd2a4c0
        SQL);
    }
}
