<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230705090105 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__book AS SELECT id, title, author, released_at, isbn, price, cover, plot, editor FROM book');
        $this->addSql('DROP TABLE book');
        $this->addSql('CREATE TABLE book (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, created_by_id INTEGER DEFAULT NULL, title VARCHAR(255) NOT NULL, author VARCHAR(255) DEFAULT NULL, released_at DATE NOT NULL --(DC2Type:date_immutable)
        , isbn VARCHAR(20) NOT NULL, price INTEGER DEFAULT NULL, cover VARCHAR(255) NOT NULL, plot CLOB NOT NULL, editor VARCHAR(255) NOT NULL, CONSTRAINT FK_CBE5A331B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO book (id, title, author, released_at, isbn, price, cover, plot, editor) SELECT id, title, author, released_at, isbn, price, cover, plot, editor FROM __temp__book');
        $this->addSql('DROP TABLE __temp__book');
        $this->addSql('CREATE INDEX IDX_CBE5A331B03A8386 ON book (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__book AS SELECT id, title, author, released_at, isbn, price, cover, plot, editor FROM book');
        $this->addSql('DROP TABLE book');
        $this->addSql('CREATE TABLE book (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, author VARCHAR(255) DEFAULT NULL, released_at DATE NOT NULL --(DC2Type:date_immutable)
        , isbn VARCHAR(20) NOT NULL, price INTEGER DEFAULT NULL, cover VARCHAR(255) NOT NULL, plot CLOB NOT NULL, editor VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO book (id, title, author, released_at, isbn, price, cover, plot, editor) SELECT id, title, author, released_at, isbn, price, cover, plot, editor FROM __temp__book');
        $this->addSql('DROP TABLE __temp__book');
    }
}
