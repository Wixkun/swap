<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250209132934 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE agent (id UUID NOT NULL, id_user_id UUID NOT NULL, pseudo VARCHAR(100) NOT NULL, phone_number VARCHAR(20) NOT NULL, rating_global DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_268B9C9D79F37AE5 ON agent (id_user_id)');
        $this->addSql('COMMENT ON COLUMN agent.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN agent.id_user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE agent_skill (agent_id UUID NOT NULL, skill_id UUID NOT NULL, PRIMARY KEY(agent_id, skill_id))');
        $this->addSql('CREATE INDEX IDX_6793CC0F3414710B ON agent_skill (agent_id)');
        $this->addSql('CREATE INDEX IDX_6793CC0F5585C142 ON agent_skill (skill_id)');
        $this->addSql('COMMENT ON COLUMN agent_skill.agent_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN agent_skill.skill_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE conversation (id UUID NOT NULL, id_customer_id UUID NOT NULL, id_agent_id UUID NOT NULL, started_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8A8E26E98B870E04 ON conversation (id_customer_id)');
        $this->addSql('CREATE INDEX IDX_8A8E26E964CF9D9E ON conversation (id_agent_id)');
        $this->addSql('COMMENT ON COLUMN conversation.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN conversation.id_customer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN conversation.id_agent_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN conversation.started_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE customer (id UUID NOT NULL, id_user_id UUID NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, city VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_81398E0979F37AE5 ON customer (id_user_id)');
        $this->addSql('COMMENT ON COLUMN customer.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN customer.id_user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE message (id UUID NOT NULL, id_conversation_id UUID NOT NULL, id_user_id UUID NOT NULL, task_proposal_id UUID DEFAULT NULL, content TEXT NOT NULL, sent_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B6BD307FE0F2C01E ON message (id_conversation_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F79F37AE5 ON message (id_user_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307FB20C8D6A ON message (task_proposal_id)');
        $this->addSql('COMMENT ON COLUMN message.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN message.id_conversation_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN message.id_user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN message.task_proposal_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN message.sent_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE review (id UUID NOT NULL, id_agent_id UUID NOT NULL, rating INT DEFAULT NULL, comment TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_794381C664CF9D9E ON review (id_agent_id)');
        $this->addSql('COMMENT ON COLUMN review.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN review.id_agent_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN review.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE skill (id UUID NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN skill.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE tag (id UUID NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN tag.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE task (id UUID NOT NULL, owner_id UUID NOT NULL, title VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, image_paths JSON DEFAULT NULL, status VARCHAR(50) DEFAULT \'pending\' NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_527EDB257E3C61F9 ON task (owner_id)');
        $this->addSql('COMMENT ON COLUMN task.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN task.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN task.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE task_tag (task_id UUID NOT NULL, tag_id UUID NOT NULL, PRIMARY KEY(task_id, tag_id))');
        $this->addSql('CREATE INDEX IDX_6C0B4F048DB60186 ON task_tag (task_id)');
        $this->addSql('CREATE INDEX IDX_6C0B4F04BAD26311 ON task_tag (tag_id)');
        $this->addSql('COMMENT ON COLUMN task_tag.task_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN task_tag.tag_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE task_proposal (id UUID NOT NULL, agent_id UUID NOT NULL, task_id UUID NOT NULL, proposed_price DOUBLE PRECISION NOT NULL, status VARCHAR(50) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FF257E3E3414710B ON task_proposal (agent_id)');
        $this->addSql('CREATE INDEX IDX_FF257E3E8DB60186 ON task_proposal (task_id)');
        $this->addSql('COMMENT ON COLUMN task_proposal.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN task_proposal.agent_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN task_proposal.task_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN task_proposal.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "user" (id UUID NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, reset_token VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('COMMENT ON COLUMN "user".id IS \'(DC2Type:uuid)\'');
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
        $this->addSql('ALTER TABLE agent ADD CONSTRAINT FK_268B9C9D79F37AE5 FOREIGN KEY (id_user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE agent_skill ADD CONSTRAINT FK_6793CC0F3414710B FOREIGN KEY (agent_id) REFERENCES agent (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE agent_skill ADD CONSTRAINT FK_6793CC0F5585C142 FOREIGN KEY (skill_id) REFERENCES skill (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E98B870E04 FOREIGN KEY (id_customer_id) REFERENCES customer (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E964CF9D9E FOREIGN KEY (id_agent_id) REFERENCES agent (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E0979F37AE5 FOREIGN KEY (id_user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FE0F2C01E FOREIGN KEY (id_conversation_id) REFERENCES conversation (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F79F37AE5 FOREIGN KEY (id_user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FB20C8D6A FOREIGN KEY (task_proposal_id) REFERENCES task_proposal (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C664CF9D9E FOREIGN KEY (id_agent_id) REFERENCES agent (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB257E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task_tag ADD CONSTRAINT FK_6C0B4F048DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task_tag ADD CONSTRAINT FK_6C0B4F04BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task_proposal ADD CONSTRAINT FK_FF257E3E3414710B FOREIGN KEY (agent_id) REFERENCES agent (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task_proposal ADD CONSTRAINT FK_FF257E3E8DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE agent DROP CONSTRAINT FK_268B9C9D79F37AE5');
        $this->addSql('ALTER TABLE agent_skill DROP CONSTRAINT FK_6793CC0F3414710B');
        $this->addSql('ALTER TABLE agent_skill DROP CONSTRAINT FK_6793CC0F5585C142');
        $this->addSql('ALTER TABLE conversation DROP CONSTRAINT FK_8A8E26E98B870E04');
        $this->addSql('ALTER TABLE conversation DROP CONSTRAINT FK_8A8E26E964CF9D9E');
        $this->addSql('ALTER TABLE customer DROP CONSTRAINT FK_81398E0979F37AE5');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307FE0F2C01E');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307F79F37AE5');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307FB20C8D6A');
        $this->addSql('ALTER TABLE review DROP CONSTRAINT FK_794381C664CF9D9E');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT FK_527EDB257E3C61F9');
        $this->addSql('ALTER TABLE task_tag DROP CONSTRAINT FK_6C0B4F048DB60186');
        $this->addSql('ALTER TABLE task_tag DROP CONSTRAINT FK_6C0B4F04BAD26311');
        $this->addSql('ALTER TABLE task_proposal DROP CONSTRAINT FK_FF257E3E3414710B');
        $this->addSql('ALTER TABLE task_proposal DROP CONSTRAINT FK_FF257E3E8DB60186');
        $this->addSql('DROP TABLE agent');
        $this->addSql('DROP TABLE agent_skill');
        $this->addSql('DROP TABLE conversation');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE skill');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE task_tag');
        $this->addSql('DROP TABLE task_proposal');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
