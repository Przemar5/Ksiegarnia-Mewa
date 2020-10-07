<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200913155222 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE order_book');
        $this->addSql('ALTER TABLE "order" ADD name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE "order" ADD surname VARCHAR(70) NOT NULL');
        $this->addSql('ALTER TABLE "order" ADD country VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE "order" ADD city VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE "order" ADD address VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE "order" ADD postal_code VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE "order" ADD phone VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE "order" ADD additional_phone VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "order" ADD products JSON NOT NULL');
        $this->addSql('ALTER TABLE "order" ADD price DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE order_book (order_id INT NOT NULL, book_id INT NOT NULL, PRIMARY KEY(order_id, book_id))');
        $this->addSql('CREATE INDEX idx_861499268d9f6d38 ON order_book (order_id)');
        $this->addSql('CREATE INDEX idx_8614992616a2b381 ON order_book (book_id)');
        $this->addSql('ALTER TABLE order_book ADD CONSTRAINT fk_861499268d9f6d38 FOREIGN KEY (order_id) REFERENCES "order" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_book ADD CONSTRAINT fk_8614992616a2b381 FOREIGN KEY (book_id) REFERENCES book (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "order" DROP name');
        $this->addSql('ALTER TABLE "order" DROP surname');
        $this->addSql('ALTER TABLE "order" DROP country');
        $this->addSql('ALTER TABLE "order" DROP city');
        $this->addSql('ALTER TABLE "order" DROP address');
        $this->addSql('ALTER TABLE "order" DROP postal_code');
        $this->addSql('ALTER TABLE "order" DROP phone');
        $this->addSql('ALTER TABLE "order" DROP additional_phone');
        $this->addSql('ALTER TABLE "order" DROP products');
        $this->addSql('ALTER TABLE "order" DROP price');
    }
}
