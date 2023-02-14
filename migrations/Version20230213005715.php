<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230213005715 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game_player_role (id INT AUTO_INCREMENT NOT NULL, player_id INT DEFAULT NULL, role VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_2B26985B99E6F5DF (player_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game_player_role ADD CONSTRAINT FK_2B26985B99E6F5DF FOREIGN KEY (player_id) REFERENCES game_player (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_player_role DROP FOREIGN KEY FK_2B26985B99E6F5DF');
        $this->addSql('DROP TABLE game_player_role');
    }
}
