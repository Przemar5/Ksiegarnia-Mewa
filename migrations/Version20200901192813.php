<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200901192813 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "user" ADD birth TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD address VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" DROP street');
        $this->addSql('ALTER TABLE "user" ALTER city DROP NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER postal_code DROP NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER country DROP NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" ADD street VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE "user" DROP birth');
        $this->addSql('ALTER TABLE "user" DROP address');
        $this->addSql('ALTER TABLE "user" ALTER country SET NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER city SET NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER postal_code SET NOT NULL');
    }
}
