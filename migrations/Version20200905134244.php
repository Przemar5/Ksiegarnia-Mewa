<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200905134244 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE author ALTER born TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE author ALTER born DROP DEFAULT');
        $this->addSql('ALTER TABLE author ALTER died TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE author ALTER died DROP DEFAULT');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE author ALTER born TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE author ALTER born DROP DEFAULT');
        $this->addSql('ALTER TABLE author ALTER died TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE author ALTER died DROP DEFAULT');
    }
}
