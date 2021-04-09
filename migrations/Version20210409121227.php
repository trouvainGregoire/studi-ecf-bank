<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210409121227 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE recipient_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE recipient (id INT NOT NULL, client_id INT DEFAULT NULL, status VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, account_identifier VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6804FB4919EB6921 ON recipient (client_id)');
        $this->addSql('ALTER TABLE recipient ADD CONSTRAINT FK_6804FB4919EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE recipient_id_seq CASCADE');
        $this->addSql('DROP TABLE recipient');
    }
}
