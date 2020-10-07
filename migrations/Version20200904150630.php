<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200904150630 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE genre ALTER started_at TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE genre ALTER started_at DROP DEFAULT');
        $this->addSql('ALTER TABLE genre ALTER ended_at TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE genre ALTER ended_at DROP DEFAULT');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE genre ALTER started_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE genre ALTER started_at DROP DEFAULT');
        $this->addSql('ALTER TABLE genre ALTER ended_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE genre ALTER ended_at DROP DEFAULT');
    }
}
