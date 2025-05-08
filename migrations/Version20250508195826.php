<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250508195826 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP SEQUENCE area_id_seq CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE organisation_user (id SERIAL NOT NULL, organisation_id INT DEFAULT NULL, roles JSON NOT NULL, email VARCHAR(180) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, password VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_CFD7D6519E6B1585 ON organisation_user (organisation_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL_ORGA_USER ON organisation_user (email)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE organisation_user ADD CONSTRAINT FK_CFD7D6519E6B1585 FOREIGN KEY (organisation_id) REFERENCES organisation (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE organisation_area DROP CONSTRAINT fk_95d03d709e6b1585
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE organisation_area DROP CONSTRAINT fk_95d03d70bd0f409c
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE area
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE organisation_area
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            CREATE SEQUENCE area_id_seq INCREMENT BY 1 MINVALUE 1 START 1
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE area (id SERIAL NOT NULL, libelle VARCHAR(255) NOT NULL, coordinates TEXT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE organisation_area (organisation_id INT NOT NULL, area_id INT NOT NULL, PRIMARY KEY(organisation_id, area_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_95d03d70bd0f409c ON organisation_area (area_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_95d03d709e6b1585 ON organisation_area (organisation_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE organisation_area ADD CONSTRAINT fk_95d03d709e6b1585 FOREIGN KEY (organisation_id) REFERENCES organisation (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE organisation_area ADD CONSTRAINT fk_95d03d70bd0f409c FOREIGN KEY (area_id) REFERENCES area (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE organisation_user DROP CONSTRAINT FK_CFD7D6519E6B1585
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE organisation_user
        SQL);
    }
}
