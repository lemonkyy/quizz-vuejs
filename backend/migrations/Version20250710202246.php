<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250710202246 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_friends (user_id UUID NOT NULL, friend_user_id UUID NOT NULL, PRIMARY KEY(user_id, friend_user_id))');
        $this->addSql('CREATE INDEX IDX_79E36E63A76ED395 ON user_friends (user_id)');
        $this->addSql('CREATE INDEX IDX_79E36E6393D1119E ON user_friends (friend_user_id)');
        $this->addSql('COMMENT ON COLUMN user_friends.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN user_friends.friend_user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE user_friends ADD CONSTRAINT FK_79E36E63A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_friends ADD CONSTRAINT FK_79E36E6393D1119E FOREIGN KEY (friend_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE user_friends DROP CONSTRAINT FK_79E36E63A76ED395');
        $this->addSql('ALTER TABLE user_friends DROP CONSTRAINT FK_79E36E6393D1119E');
        $this->addSql('DROP TABLE user_friends');
    }
}
