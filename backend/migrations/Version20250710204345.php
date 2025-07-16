<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250710204345 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE room_player ALTER player_id DROP NOT NULL');
        $this->addSql('ALTER TABLE room_player ALTER room_id DROP NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD room_player_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN "user".room_player_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D64954F15B6 FOREIGN KEY (room_player_id) REFERENCES room_player (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64954F15B6 ON "user" (room_player_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE room_player ALTER player_id SET NOT NULL');
        $this->addSql('ALTER TABLE room_player ALTER room_id SET NOT NULL');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D64954F15B6');
        $this->addSql('DROP INDEX UNIQ_8D93D64954F15B6');
        $this->addSql('ALTER TABLE "user" DROP room_player_id');
    }
}
