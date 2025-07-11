<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250710204915 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE room_player DROP CONSTRAINT fk_d957bca499e6f5df');
        $this->addSql('DROP INDEX idx_d957bca499e6f5df');
        $this->addSql('ALTER TABLE room_player DROP player_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE room_player ADD player_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN room_player.player_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE room_player ADD CONSTRAINT fk_d957bca499e6f5df FOREIGN KEY (player_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_d957bca499e6f5df ON room_player (player_id)');
    }
}
