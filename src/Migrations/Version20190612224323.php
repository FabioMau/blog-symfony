<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190612224323 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_9474526CB03A8386');
        $this->addSql('DROP INDEX IDX_9474526C4B89032C');
        $this->addSql('CREATE TEMPORARY TABLE __temp__comment AS SELECT id, post_id, created_by_id, text, created_at FROM comment');
        $this->addSql('DROP TABLE comment');
        $this->addSql('CREATE TABLE comment (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, post_id INTEGER DEFAULT NULL, created_by_id INTEGER DEFAULT NULL, text CLOB NOT NULL COLLATE BINARY, created_at DATETIME NOT NULL, CONSTRAINT FK_9474526C4B89032C FOREIGN KEY (post_id) REFERENCES post (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9474526CB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO comment (id, post_id, created_by_id, text, created_at) SELECT id, post_id, created_by_id, text, created_at FROM __temp__comment');
        $this->addSql('DROP TABLE __temp__comment');
        $this->addSql('CREATE INDEX IDX_9474526CB03A8386 ON comment (created_by_id)');
        $this->addSql('CREATE INDEX IDX_9474526C4B89032C ON comment (post_id)');
        $this->addSql('DROP INDEX IDX_5A8A6C8DB03A8386');
        $this->addSql('CREATE TEMPORARY TABLE __temp__post AS SELECT id, created_by_id, title, text, created_at FROM post');
        $this->addSql('DROP TABLE post');
        $this->addSql('CREATE TABLE post (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, created_by_id INTEGER NOT NULL, title VARCHAR(255) NOT NULL COLLATE BINARY, text CLOB NOT NULL COLLATE BINARY, created_at DATETIME NOT NULL, CONSTRAINT FK_5A8A6C8DB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO post (id, created_by_id, title, text, created_at) SELECT id, created_by_id, title, text, created_at FROM __temp__post');
        $this->addSql('DROP TABLE __temp__post');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DB03A8386 ON post (created_by_id)');
        $this->addSql('DROP INDEX UNIQ_8D93D649F85E0677');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, username, roles, password, email FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username VARCHAR(180) NOT NULL COLLATE BINARY, roles CLOB NOT NULL COLLATE BINARY --(DC2Type:json)
        , password VARCHAR(255) NOT NULL COLLATE BINARY, email VARCHAR(255) NOT NULL COLLATE BINARY)');
        $this->addSql('INSERT INTO user (id, username, roles, password, email) SELECT id, username, roles, password, email FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_9474526C4B89032C');
        $this->addSql('DROP INDEX IDX_9474526CB03A8386');
        $this->addSql('CREATE TEMPORARY TABLE __temp__comment AS SELECT id, post_id, created_by_id, text, created_at FROM comment');
        $this->addSql('DROP TABLE comment');
        $this->addSql('CREATE TABLE comment (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, text CLOB NOT NULL, created_at DATETIME NOT NULL, post_id INTEGER NOT NULL, created_by_id INTEGER NOT NULL)');
        $this->addSql('INSERT INTO comment (id, post_id, created_by_id, text, created_at) SELECT id, post_id, created_by_id, text, created_at FROM __temp__comment');
        $this->addSql('DROP TABLE __temp__comment');
        $this->addSql('CREATE INDEX IDX_9474526C4B89032C ON comment (post_id)');
        $this->addSql('CREATE INDEX IDX_9474526CB03A8386 ON comment (created_by_id)');
        $this->addSql('DROP INDEX IDX_5A8A6C8DB03A8386');
        $this->addSql('CREATE TEMPORARY TABLE __temp__post AS SELECT id, created_by_id, title, text, created_at FROM post');
        $this->addSql('DROP TABLE post');
        $this->addSql('CREATE TABLE post (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, created_by_id INTEGER NOT NULL, title VARCHAR(255) NOT NULL, text CLOB NOT NULL, created_at DATETIME NOT NULL)');
        $this->addSql('INSERT INTO post (id, created_by_id, title, text, created_at) SELECT id, created_by_id, title, text, created_at FROM __temp__post');
        $this->addSql('DROP TABLE __temp__post');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DB03A8386 ON post (created_by_id)');
        $this->addSql('DROP INDEX UNIQ_8D93D649F85E0677');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, username, roles, password, email FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO user (id, username, roles, password, email) SELECT id, username, roles, password, email FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)');
    }
}
