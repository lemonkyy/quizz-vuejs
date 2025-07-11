<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250710194732 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE friend_request (id UUID NOT NULL, sender_id UUID NOT NULL, receiver_id UUID NOT NULL, sent_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, accepted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, revoked_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, denied_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F284D94F624B39D ON friend_request (sender_id)');
        $this->addSql('CREATE INDEX IDX_F284D94CD53EDB6 ON friend_request (receiver_id)');
        $this->addSql('COMMENT ON COLUMN friend_request.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN friend_request.sender_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN friend_request.receiver_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN friend_request.sent_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN friend_request.accepted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN friend_request.revoked_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN friend_request.denied_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE profile_picture (id UUID NOT NULL, file_name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C5659115D7DF1668 ON profile_picture (file_name)');
        $this->addSql('COMMENT ON COLUMN profile_picture.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE room_player (id UUID NOT NULL, player_id UUID NOT NULL, room_id UUID NOT NULL, score INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D957BCA499E6F5DF ON room_player (player_id)');
        $this->addSql('CREATE INDEX IDX_D957BCA454177093 ON room_player (room_id)');
        $this->addSql('COMMENT ON COLUMN room_player.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN room_player.player_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN room_player.room_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE friend_request ADD CONSTRAINT FK_F284D94F624B39D FOREIGN KEY (sender_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE friend_request ADD CONSTRAINT FK_F284D94CD53EDB6 FOREIGN KEY (receiver_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE room_player ADD CONSTRAINT FK_D957BCA499E6F5DF FOREIGN KEY (player_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE room_player ADD CONSTRAINT FK_D957BCA454177093 FOREIGN KEY (room_id) REFERENCES room (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE room_users DROP CONSTRAINT fk_5e3f044254177093');
        $this->addSql('ALTER TABLE room_users DROP CONSTRAINT fk_5e3f0442a76ed395');
        $this->addSql('DROP TABLE room_users');
        $this->addSql('ALTER TABLE room ADD code VARCHAR(20) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_729F519B77153098 ON room (code)');
        $this->addSql('ALTER TABLE "user" ADD profile_picture_id UUID NOT NULL');
        $this->addSql('COMMENT ON COLUMN "user".profile_picture_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D649292E8AE2 FOREIGN KEY (profile_picture_id) REFERENCES profile_picture (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8D93D649292E8AE2 ON "user" (profile_picture_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D649292E8AE2');
        $this->addSql('CREATE TABLE room_users (room_id UUID NOT NULL, user_id UUID NOT NULL, PRIMARY KEY(room_id, user_id))');
        $this->addSql('CREATE INDEX idx_5e3f044254177093 ON room_users (room_id)');
        $this->addSql('CREATE INDEX idx_5e3f0442a76ed395 ON room_users (user_id)');
        $this->addSql('COMMENT ON COLUMN room_users.room_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN room_users.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE room_users ADD CONSTRAINT fk_5e3f044254177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE room_users ADD CONSTRAINT fk_5e3f0442a76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE friend_request DROP CONSTRAINT FK_F284D94F624B39D');
        $this->addSql('ALTER TABLE friend_request DROP CONSTRAINT FK_F284D94CD53EDB6');
        $this->addSql('ALTER TABLE room_player DROP CONSTRAINT FK_D957BCA499E6F5DF');
        $this->addSql('ALTER TABLE room_player DROP CONSTRAINT FK_D957BCA454177093');
        $this->addSql('DROP TABLE friend_request');
        $this->addSql('DROP TABLE profile_picture');
        $this->addSql('DROP TABLE room_player');
        $this->addSql('DROP INDEX IDX_8D93D649292E8AE2');
        $this->addSql('ALTER TABLE "user" DROP profile_picture_id');
        $this->addSql('DROP INDEX UNIQ_729F519B77153098');
        $this->addSql('ALTER TABLE room DROP code');
    }
}
