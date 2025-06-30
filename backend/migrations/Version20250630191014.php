<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250630191014 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invitation DROP CONSTRAINT fk_f11d61a2fe54d947');
        $this->addSql('CREATE TABLE rooms (id UUID NOT NULL, owner_id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, is_public BOOLEAN NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7CA11A967E3C61F9 ON rooms (owner_id)');
        $this->addSql('COMMENT ON COLUMN rooms.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN rooms.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN rooms.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN rooms.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE room_users (room_id UUID NOT NULL, user_id UUID NOT NULL, PRIMARY KEY(room_id, user_id))');
        $this->addSql('CREATE INDEX IDX_5E3F044254177093 ON room_users (room_id)');
        $this->addSql('CREATE INDEX IDX_5E3F0442A76ED395 ON room_users (user_id)');
        $this->addSql('COMMENT ON COLUMN room_users.room_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN room_users.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE rooms ADD CONSTRAINT FK_7CA11A967E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE room_users ADD CONSTRAINT FK_5E3F044254177093 FOREIGN KEY (room_id) REFERENCES rooms (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE room_users ADD CONSTRAINT FK_5E3F0442A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE group_users DROP CONSTRAINT fk_44af8e8ea76ed395');
        $this->addSql('ALTER TABLE group_users DROP CONSTRAINT fk_44af8e8efe54d947');
        $this->addSql('ALTER TABLE "group" DROP CONSTRAINT fk_6dc044c57e3c61f9');
        $this->addSql('DROP TABLE group_users');
        $this->addSql('DROP TABLE "group"');
        $this->addSql('DROP INDEX idx_f11d61a2fe54d947');
        $this->addSql('ALTER TABLE invitation RENAME COLUMN group_id TO room_id');
        $this->addSql('ALTER TABLE invitation ADD CONSTRAINT FK_F11D61A254177093 FOREIGN KEY (room_id) REFERENCES rooms (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_F11D61A254177093 ON invitation (room_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE invitation DROP CONSTRAINT FK_F11D61A254177093');
        $this->addSql('CREATE TABLE group_users (group_id UUID NOT NULL, user_id UUID NOT NULL, PRIMARY KEY(group_id, user_id))');
        $this->addSql('CREATE INDEX idx_44af8e8ea76ed395 ON group_users (user_id)');
        $this->addSql('CREATE INDEX idx_44af8e8efe54d947 ON group_users (group_id)');
        $this->addSql('COMMENT ON COLUMN group_users.group_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN group_users.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE "group" (id UUID NOT NULL, owner_id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, is_public BOOLEAN NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_6dc044c57e3c61f9 ON "group" (owner_id)');
        $this->addSql('COMMENT ON COLUMN "group".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "group".owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "group".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "group".deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE group_users ADD CONSTRAINT fk_44af8e8ea76ed395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE group_users ADD CONSTRAINT fk_44af8e8efe54d947 FOREIGN KEY (group_id) REFERENCES "group" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "group" ADD CONSTRAINT fk_6dc044c57e3c61f9 FOREIGN KEY (owner_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE rooms DROP CONSTRAINT FK_7CA11A967E3C61F9');
        $this->addSql('ALTER TABLE room_users DROP CONSTRAINT FK_5E3F044254177093');
        $this->addSql('ALTER TABLE room_users DROP CONSTRAINT FK_5E3F0442A76ED395');
        $this->addSql('DROP TABLE rooms');
        $this->addSql('DROP TABLE room_users');
        $this->addSql('DROP INDEX IDX_F11D61A254177093');
        $this->addSql('ALTER TABLE invitation RENAME COLUMN room_id TO group_id');
        $this->addSql('ALTER TABLE invitation ADD CONSTRAINT fk_f11d61a2fe54d947 FOREIGN KEY (group_id) REFERENCES "group" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_f11d61a2fe54d947 ON invitation (group_id)');
    }
}
