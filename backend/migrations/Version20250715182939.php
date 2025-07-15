<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250715182939 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invitation DROP CONSTRAINT fk_f11d61a2a7b4a7e3');
        $this->addSql('ALTER TABLE invitation DROP CONSTRAINT fk_f11d61a2c58dad6e');
        $this->addSql('DROP INDEX idx_f11d61a2a7b4a7e3');
        $this->addSql('DROP INDEX idx_f11d61a2c58dad6e');
        $this->addSql('ALTER TABLE invitation ADD sender_id UUID NOT NULL');
        $this->addSql('ALTER TABLE invitation ADD receiver_id UUID NOT NULL');
        $this->addSql('ALTER TABLE invitation DROP invited_by_id');
        $this->addSql('ALTER TABLE invitation DROP invited_user_id');
        $this->addSql('ALTER TABLE invitation RENAME COLUMN invited_at TO sent_at');
        $this->addSql('COMMENT ON COLUMN invitation.sender_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN invitation.receiver_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE invitation ADD CONSTRAINT FK_F11D61A2F624B39D FOREIGN KEY (sender_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invitation ADD CONSTRAINT FK_F11D61A2CD53EDB6 FOREIGN KEY (receiver_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_F11D61A2F624B39D ON invitation (sender_id)');
        $this->addSql('CREATE INDEX IDX_F11D61A2CD53EDB6 ON invitation (receiver_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE invitation DROP CONSTRAINT FK_F11D61A2F624B39D');
        $this->addSql('ALTER TABLE invitation DROP CONSTRAINT FK_F11D61A2CD53EDB6');
        $this->addSql('DROP INDEX IDX_F11D61A2F624B39D');
        $this->addSql('DROP INDEX IDX_F11D61A2CD53EDB6');
        $this->addSql('ALTER TABLE invitation ADD invited_by_id UUID NOT NULL');
        $this->addSql('ALTER TABLE invitation ADD invited_user_id UUID NOT NULL');
        $this->addSql('ALTER TABLE invitation DROP sender_id');
        $this->addSql('ALTER TABLE invitation DROP receiver_id');
        $this->addSql('ALTER TABLE invitation RENAME COLUMN sent_at TO invited_at');
        $this->addSql('COMMENT ON COLUMN invitation.invited_by_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN invitation.invited_user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE invitation ADD CONSTRAINT fk_f11d61a2a7b4a7e3 FOREIGN KEY (invited_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invitation ADD CONSTRAINT fk_f11d61a2c58dad6e FOREIGN KEY (invited_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_f11d61a2a7b4a7e3 ON invitation (invited_by_id)');
        $this->addSql('CREATE INDEX idx_f11d61a2c58dad6e ON invitation (invited_user_id)');
    }
}
