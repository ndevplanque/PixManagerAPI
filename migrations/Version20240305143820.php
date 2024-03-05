<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240305143820 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE album DROP CONSTRAINT fk_39986e439d86650f');
        $this->addSql('DROP INDEX idx_39986e439d86650f');
        $this->addSql('ALTER TABLE album RENAME COLUMN user_id_id TO owner_id');
        $this->addSql('ALTER TABLE album ADD CONSTRAINT FK_39986E437E3C61F9 FOREIGN KEY (owner_id) REFERENCES app_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_39986E437E3C61F9 ON album (owner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE album DROP CONSTRAINT FK_39986E437E3C61F9');
        $this->addSql('DROP INDEX IDX_39986E437E3C61F9');
        $this->addSql('ALTER TABLE album RENAME COLUMN owner_id TO user_id_id');
        $this->addSql('ALTER TABLE album ADD CONSTRAINT fk_39986e439d86650f FOREIGN KEY (user_id_id) REFERENCES app_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_39986e439d86650f ON album (user_id_id)');
    }
}
