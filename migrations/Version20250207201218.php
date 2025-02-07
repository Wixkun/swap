<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250207201218 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE skill_agent (skill_id UUID NOT NULL, agent_id UUID NOT NULL, PRIMARY KEY(skill_id, agent_id))');
        $this->addSql('CREATE INDEX IDX_3A30FA15585C142 ON skill_agent (skill_id)');
        $this->addSql('CREATE INDEX IDX_3A30FA13414710B ON skill_agent (agent_id)');
        $this->addSql('COMMENT ON COLUMN skill_agent.skill_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN skill_agent.agent_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE skill_agent ADD CONSTRAINT FK_3A30FA15585C142 FOREIGN KEY (skill_id) REFERENCES skill (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE skill_agent ADD CONSTRAINT FK_3A30FA13414710B FOREIGN KEY (agent_id) REFERENCES agent (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE agent_skill DROP CONSTRAINT fk_6793cc0f3414710b');
        $this->addSql('ALTER TABLE agent_skill DROP CONSTRAINT fk_6793cc0f5585c142');
        $this->addSql('DROP TABLE agent_skill');
        $this->addSql('ALTER TABLE agent DROP description');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE agent_skill (agent_id UUID NOT NULL, skill_id UUID NOT NULL, PRIMARY KEY(agent_id, skill_id))');
        $this->addSql('CREATE INDEX idx_6793cc0f5585c142 ON agent_skill (skill_id)');
        $this->addSql('CREATE INDEX idx_6793cc0f3414710b ON agent_skill (agent_id)');
        $this->addSql('COMMENT ON COLUMN agent_skill.agent_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN agent_skill.skill_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE agent_skill ADD CONSTRAINT fk_6793cc0f3414710b FOREIGN KEY (agent_id) REFERENCES agent (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE agent_skill ADD CONSTRAINT fk_6793cc0f5585c142 FOREIGN KEY (skill_id) REFERENCES skill (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE skill_agent DROP CONSTRAINT FK_3A30FA15585C142');
        $this->addSql('ALTER TABLE skill_agent DROP CONSTRAINT FK_3A30FA13414710B');
        $this->addSql('DROP TABLE skill_agent');
        $this->addSql('ALTER TABLE agent ADD description TEXT DEFAULT NULL');
    }
}
