<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250723233549 extends AbstractMigration
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
        $this->addSql('CREATE TABLE invitation (id UUID NOT NULL, room_id UUID NOT NULL, sender_id UUID NOT NULL, receiver_id UUID NOT NULL, sent_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, accepted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, revoked_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, denied_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F11D61A254177093 ON invitation (room_id)');
        $this->addSql('CREATE INDEX IDX_F11D61A2F624B39D ON invitation (sender_id)');
        $this->addSql('CREATE INDEX IDX_F11D61A2CD53EDB6 ON invitation (receiver_id)');
        $this->addSql('COMMENT ON COLUMN invitation.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN invitation.room_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN invitation.sender_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN invitation.receiver_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN invitation.sent_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN invitation.accepted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN invitation.revoked_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN invitation.denied_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE profile_picture (id UUID NOT NULL, file_name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C5659115D7DF1668 ON profile_picture (file_name)');
        $this->addSql('COMMENT ON COLUMN profile_picture.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE quizz_questions (id UUID NOT NULL, quizz_id UUID NOT NULL, question_text TEXT NOT NULL, correct_answer TEXT NOT NULL, options JSON DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_79E4F161BA934BCD ON quizz_questions (quizz_id)');
        $this->addSql('COMMENT ON COLUMN quizz_questions.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN quizz_questions.quizz_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE quizzes (id UUID NOT NULL, title VARCHAR(100) DEFAULT NULL, content_json TEXT NOT NULL, created_by VARCHAR(255) DEFAULT NULL, ready BOOLEAN DEFAULT false NOT NULL, time_per_question INT DEFAULT NULL, count INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN quizzes.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE room (id UUID NOT NULL, owner_id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, is_public BOOLEAN NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, code VARCHAR(20) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_729F519B77153098 ON room (code)');
        $this->addSql('CREATE INDEX IDX_729F519B7E3C61F9 ON room (owner_id)');
        $this->addSql('COMMENT ON COLUMN room.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN room.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN room.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN room.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE room_player (id UUID NOT NULL, player_id UUID DEFAULT NULL, room_id UUID DEFAULT NULL, score INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D957BCA499E6F5DF ON room_player (player_id)');
        $this->addSql('CREATE INDEX IDX_D957BCA454177093 ON room_player (room_id)');
        $this->addSql('COMMENT ON COLUMN room_player.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN room_player.player_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN room_player.room_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE "user" (id UUID NOT NULL, profile_picture_id UUID NOT NULL, email VARCHAR(100) NOT NULL, username VARCHAR(20) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, totp_secret VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON "user" (username)');
        $this->addSql('CREATE INDEX IDX_8D93D649292E8AE2 ON "user" (profile_picture_id)');
        $this->addSql('COMMENT ON COLUMN "user".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "user".profile_picture_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE user_friends (user_id UUID NOT NULL, friend_user_id UUID NOT NULL, PRIMARY KEY(user_id, friend_user_id))');
        $this->addSql('CREATE INDEX IDX_79E36E63A76ED395 ON user_friends (user_id)');
        $this->addSql('CREATE INDEX IDX_79E36E6393D1119E ON user_friends (friend_user_id)');
        $this->addSql('COMMENT ON COLUMN user_friends.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN user_friends.friend_user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE friend_request ADD CONSTRAINT FK_F284D94F624B39D FOREIGN KEY (sender_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE friend_request ADD CONSTRAINT FK_F284D94CD53EDB6 FOREIGN KEY (receiver_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invitation ADD CONSTRAINT FK_F11D61A254177093 FOREIGN KEY (room_id) REFERENCES room (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invitation ADD CONSTRAINT FK_F11D61A2F624B39D FOREIGN KEY (sender_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invitation ADD CONSTRAINT FK_F11D61A2CD53EDB6 FOREIGN KEY (receiver_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quizz_questions ADD CONSTRAINT FK_79E4F161BA934BCD FOREIGN KEY (quizz_id) REFERENCES quizzes (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B7E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE room_player ADD CONSTRAINT FK_D957BCA499E6F5DF FOREIGN KEY (player_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE room_player ADD CONSTRAINT FK_D957BCA454177093 FOREIGN KEY (room_id) REFERENCES room (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D649292E8AE2 FOREIGN KEY (profile_picture_id) REFERENCES profile_picture (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_friends ADD CONSTRAINT FK_79E36E63A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_friends ADD CONSTRAINT FK_79E36E6393D1119E FOREIGN KEY (friend_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE friend_request DROP CONSTRAINT FK_F284D94F624B39D');
        $this->addSql('ALTER TABLE friend_request DROP CONSTRAINT FK_F284D94CD53EDB6');
        $this->addSql('ALTER TABLE invitation DROP CONSTRAINT FK_F11D61A254177093');
        $this->addSql('ALTER TABLE invitation DROP CONSTRAINT FK_F11D61A2F624B39D');
        $this->addSql('ALTER TABLE invitation DROP CONSTRAINT FK_F11D61A2CD53EDB6');
        $this->addSql('ALTER TABLE quizz_questions DROP CONSTRAINT FK_79E4F161BA934BCD');
        $this->addSql('ALTER TABLE room DROP CONSTRAINT FK_729F519B7E3C61F9');
        $this->addSql('ALTER TABLE room_player DROP CONSTRAINT FK_D957BCA499E6F5DF');
        $this->addSql('ALTER TABLE room_player DROP CONSTRAINT FK_D957BCA454177093');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D649292E8AE2');
        $this->addSql('ALTER TABLE user_friends DROP CONSTRAINT FK_79E36E63A76ED395');
        $this->addSql('ALTER TABLE user_friends DROP CONSTRAINT FK_79E36E6393D1119E');
        $this->addSql('DROP TABLE friend_request');
        $this->addSql('DROP TABLE invitation');
        $this->addSql('DROP TABLE profile_picture');
        $this->addSql('DROP TABLE quizz_questions');
        $this->addSql('DROP TABLE quizzes');
        $this->addSql('DROP TABLE room');
        $this->addSql('DROP TABLE room_player');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE user_friends');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
