<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230213024602 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game_state (id INT AUTO_INCREMENT NOT NULL, game_id INT DEFAULT NULL, phase VARCHAR(255) NOT NULL, round INT NOT NULL, leader_index INT NOT NULL, results LONGTEXT NOT NULL, UNIQUE INDEX UNIQ_91A0AB74E48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game_state ADD CONSTRAINT FK_91A0AB74E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_state DROP FOREIGN KEY FK_91A0AB74E48FD905');
        $this->addSql('DROP TABLE game_state');
    }
}
