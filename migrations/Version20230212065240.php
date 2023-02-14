<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230212065240 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE session_user (id INT AUTO_INCREMENT NOT NULL, default_name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game_player ADD session_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE game_player ADD CONSTRAINT FK_E52CD7ADB5B651CF FOREIGN KEY (session_user_id) REFERENCES session_user (id)');
        $this->addSql('CREATE INDEX IDX_E52CD7ADB5B651CF ON game_player (session_user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_player DROP FOREIGN KEY FK_E52CD7ADB5B651CF');
        $this->addSql('DROP TABLE session_user');
        $this->addSql('DROP INDEX IDX_E52CD7ADB5B651CF ON game_player');
        $this->addSql('ALTER TABLE game_player DROP session_user_id');
    }
}
