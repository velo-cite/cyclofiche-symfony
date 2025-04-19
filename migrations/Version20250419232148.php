<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250419232148 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE issue ADD category_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE issue DROP category
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE issue ADD CONSTRAINT FK_12AD233E12469DE2 FOREIGN KEY (category_id) REFERENCES issue_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_12AD233E12469DE2 ON issue (category_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE issue DROP CONSTRAINT FK_12AD233E12469DE2
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_12AD233E12469DE2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE issue ADD category VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE issue DROP category_id
        SQL);
    }
}
