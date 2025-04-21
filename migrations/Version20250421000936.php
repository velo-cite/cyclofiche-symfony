<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250421000936 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE "Moderator" (id SERIAL NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, email VARCHAR(180) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, phone VARCHAR(15) DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL_MODERATOR ON "Moderator" (email)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE issue ADD moderator_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE issue ADD comment_moderator TEXT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE issue ADD CONSTRAINT FK_12AD233ED0AFA354 FOREIGN KEY (moderator_id) REFERENCES "Moderator" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_12AD233ED0AFA354 ON issue (moderator_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE issue DROP CONSTRAINT FK_12AD233ED0AFA354
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "Moderator"
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_12AD233ED0AFA354
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE issue DROP moderator_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE issue DROP comment_moderator
        SQL);
    }
}
