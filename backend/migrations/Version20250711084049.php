<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250711084049 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE room_player ADD player_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN room_player.player_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE room_player ADD CONSTRAINT FK_D957BCA499E6F5DF FOREIGN KEY (player_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D957BCA499E6F5DF ON room_player (player_id)');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT fk_8d93d64954f15b6');
        $this->addSql('DROP INDEX uniq_8d93d64954f15b6');
        $this->addSql('ALTER TABLE "user" DROP room_player_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE room_player DROP CONSTRAINT FK_D957BCA499E6F5DF');
        $this->addSql('DROP INDEX UNIQ_D957BCA499E6F5DF');
        $this->addSql('ALTER TABLE room_player DROP player_id');
        $this->addSql('ALTER TABLE "user" ADD room_player_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN "user".room_player_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT fk_8d93d64954f15b6 FOREIGN KEY (room_player_id) REFERENCES room_player (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_8d93d64954f15b6 ON "user" (room_player_id)');
    }
}
