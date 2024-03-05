<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240305135600 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE album_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE app_user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE label_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE photo_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE album (id INT NOT NULL, user_id_id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_39986E439D86650F ON album (user_id_id)');
        $this->addSql('COMMENT ON COLUMN album.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE app_user (id INT NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, is_admin BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE app_user_album (app_user_id INT NOT NULL, album_id INT NOT NULL, PRIMARY KEY(app_user_id, album_id))');
        $this->addSql('CREATE INDEX IDX_8D413E674A3353D8 ON app_user_album (app_user_id)');
        $this->addSql('CREATE INDEX IDX_8D413E671137ABCF ON app_user_album (album_id)');
        $this->addSql('CREATE TABLE label (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE label_photo (label_id INT NOT NULL, photo_id INT NOT NULL, PRIMARY KEY(label_id, photo_id))');
        $this->addSql('CREATE INDEX IDX_86A9321633B92F39 ON label_photo (label_id)');
        $this->addSql('CREATE INDEX IDX_86A932167E9E4C8C ON label_photo (photo_id)');
        $this->addSql('CREATE TABLE photo (id INT NOT NULL, album_id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_14B784181137ABCF ON photo (album_id)');
        $this->addSql('COMMENT ON COLUMN photo.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE album ADD CONSTRAINT FK_39986E439D86650F FOREIGN KEY (user_id_id) REFERENCES app_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_user_album ADD CONSTRAINT FK_8D413E674A3353D8 FOREIGN KEY (app_user_id) REFERENCES app_user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_user_album ADD CONSTRAINT FK_8D413E671137ABCF FOREIGN KEY (album_id) REFERENCES album (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE label_photo ADD CONSTRAINT FK_86A9321633B92F39 FOREIGN KEY (label_id) REFERENCES label (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE label_photo ADD CONSTRAINT FK_86A932167E9E4C8C FOREIGN KEY (photo_id) REFERENCES photo (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B784181137ABCF FOREIGN KEY (album_id) REFERENCES album (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE album_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE app_user_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE label_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE photo_id_seq CASCADE');
        $this->addSql('ALTER TABLE album DROP CONSTRAINT FK_39986E439D86650F');
        $this->addSql('ALTER TABLE app_user_album DROP CONSTRAINT FK_8D413E674A3353D8');
        $this->addSql('ALTER TABLE app_user_album DROP CONSTRAINT FK_8D413E671137ABCF');
        $this->addSql('ALTER TABLE label_photo DROP CONSTRAINT FK_86A9321633B92F39');
        $this->addSql('ALTER TABLE label_photo DROP CONSTRAINT FK_86A932167E9E4C8C');
        $this->addSql('ALTER TABLE photo DROP CONSTRAINT FK_14B784181137ABCF');
        $this->addSql('DROP TABLE album');
        $this->addSql('DROP TABLE app_user');
        $this->addSql('DROP TABLE app_user_album');
        $this->addSql('DROP TABLE label');
        $this->addSql('DROP TABLE label_photo');
        $this->addSql('DROP TABLE photo');
    }
}
