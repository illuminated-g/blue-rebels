<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230213235413 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_state ADD p1_vote TINYINT(1) DEFAULT NULL, ADD p2_vote TINYINT(1) DEFAULT NULL, ADD p3_vote TINYINT(1) DEFAULT NULL, ADD p4_vote TINYINT(1) DEFAULT NULL, ADD p5_vote TINYINT(1) DEFAULT NULL, ADD p6_vote TINYINT(1) DEFAULT NULL, ADD p7_vote TINYINT(1) DEFAULT NULL, ADD p8_vote TINYINT(1) DEFAULT NULL, ADD p9_vote TINYINT(1) DEFAULT NULL, ADD p10_vote TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_state DROP p1_vote, DROP p2_vote, DROP p3_vote, DROP p4_vote, DROP p5_vote, DROP p6_vote, DROP p7_vote, DROP p8_vote, DROP p9_vote, DROP p10_vote');
    }
}
