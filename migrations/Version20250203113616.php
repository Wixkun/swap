<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250203113616 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE agent (id SERIAL NOT NULL, id_user_id INT NOT NULL, id_agent UUID NOT NULL, pseudo VARCHAR(100) NOT NULL, phone_number VARCHAR(20) NOT NULL, rating_global DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_268B9C9D79F37AE5 ON agent (id_user_id)');
        $this->addSql('CREATE TABLE conversation (id SERIAL NOT NULL, id_customer_id INT NOT NULL, id_agent_id INT NOT NULL, id_conversation UUID NOT NULL, started_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8A8E26E98B870E04 ON conversation (id_customer_id)');
        $this->addSql('CREATE INDEX IDX_8A8E26E964CF9D9E ON conversation (id_agent_id)');
        $this->addSql('COMMENT ON COLUMN conversation.started_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE customer (id SERIAL NOT NULL, id_user_id INT NOT NULL, id_customer UUID NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_81398E0979F37AE5 ON customer (id_user_id)');
        $this->addSql('CREATE TABLE message (id SERIAL NOT NULL, id_conversation_id INT NOT NULL, id_user_id INT NOT NULL, id_message UUID NOT NULL, content TEXT NOT NULL, sent_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B6BD307FE0F2C01E ON message (id_conversation_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F79F37AE5 ON message (id_user_id)');
        $this->addSql('COMMENT ON COLUMN message.sent_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE review (id SERIAL NOT NULL, id_agent_id INT NOT NULL, id_review UUID NOT NULL, rating INT DEFAULT NULL, comment TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_794381C664CF9D9E ON review (id_agent_id)');
        $this->addSql('COMMENT ON COLUMN review.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE skill (id SERIAL NOT NULL, id_skill UUID NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE skill_agent (skill_id INT NOT NULL, agent_id INT NOT NULL, PRIMARY KEY(skill_id, agent_id))');
        $this->addSql('CREATE INDEX IDX_3A30FA15585C142 ON skill_agent (skill_id)');
        $this->addSql('CREATE INDEX IDX_3A30FA13414710B ON skill_agent (agent_id)');
        $this->addSql('CREATE TABLE tag (id SERIAL NOT NULL, id_tag UUID NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE tag_task (tag_id INT NOT NULL, task_id INT NOT NULL, PRIMARY KEY(tag_id, task_id))');
        $this->addSql('CREATE INDEX IDX_BC716493BAD26311 ON tag_task (tag_id)');
        $this->addSql('CREATE INDEX IDX_BC7164938DB60186 ON tag_task (task_id)');
        $this->addSql('CREATE TABLE task (id SERIAL NOT NULL, id_task UUID NOT NULL, title VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, status VARCHAR(50) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN task.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE task_proposal (id SERIAL NOT NULL, id_agent_id INT NOT NULL, id_task_id INT NOT NULL, id_task_proposal UUID NOT NULL, proposed_price DOUBLE PRECISION NOT NULL, status VARCHAR(50) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FF257E3E64CF9D9E ON task_proposal (id_agent_id)');
        $this->addSql('CREATE INDEX IDX_FF257E3E532BA8F6 ON task_proposal (id_task_id)');
        $this->addSql('COMMENT ON COLUMN task_proposal.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, id_user UUID NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN "user".roles IS \'(DC2Type:array)\'');
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
        $this->addSql('ALTER TABLE agent ADD CONSTRAINT FK_268B9C9D79F37AE5 FOREIGN KEY (id_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E98B870E04 FOREIGN KEY (id_customer_id) REFERENCES customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E964CF9D9E FOREIGN KEY (id_agent_id) REFERENCES agent (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E0979F37AE5 FOREIGN KEY (id_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FE0F2C01E FOREIGN KEY (id_conversation_id) REFERENCES conversation (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F79F37AE5 FOREIGN KEY (id_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C664CF9D9E FOREIGN KEY (id_agent_id) REFERENCES agent (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE skill_agent ADD CONSTRAINT FK_3A30FA15585C142 FOREIGN KEY (skill_id) REFERENCES skill (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE skill_agent ADD CONSTRAINT FK_3A30FA13414710B FOREIGN KEY (agent_id) REFERENCES agent (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tag_task ADD CONSTRAINT FK_BC716493BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tag_task ADD CONSTRAINT FK_BC7164938DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task_proposal ADD CONSTRAINT FK_FF257E3E64CF9D9E FOREIGN KEY (id_agent_id) REFERENCES agent (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task_proposal ADD CONSTRAINT FK_FF257E3E532BA8F6 FOREIGN KEY (id_task_id) REFERENCES task (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE agent DROP CONSTRAINT FK_268B9C9D79F37AE5');
        $this->addSql('ALTER TABLE conversation DROP CONSTRAINT FK_8A8E26E98B870E04');
        $this->addSql('ALTER TABLE conversation DROP CONSTRAINT FK_8A8E26E964CF9D9E');
        $this->addSql('ALTER TABLE customer DROP CONSTRAINT FK_81398E0979F37AE5');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307FE0F2C01E');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307F79F37AE5');
        $this->addSql('ALTER TABLE review DROP CONSTRAINT FK_794381C664CF9D9E');
        $this->addSql('ALTER TABLE skill_agent DROP CONSTRAINT FK_3A30FA15585C142');
        $this->addSql('ALTER TABLE skill_agent DROP CONSTRAINT FK_3A30FA13414710B');
        $this->addSql('ALTER TABLE tag_task DROP CONSTRAINT FK_BC716493BAD26311');
        $this->addSql('ALTER TABLE tag_task DROP CONSTRAINT FK_BC7164938DB60186');
        $this->addSql('ALTER TABLE task_proposal DROP CONSTRAINT FK_FF257E3E64CF9D9E');
        $this->addSql('ALTER TABLE task_proposal DROP CONSTRAINT FK_FF257E3E532BA8F6');
        $this->addSql('DROP TABLE agent');
        $this->addSql('DROP TABLE conversation');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE skill');
        $this->addSql('DROP TABLE skill_agent');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE tag_task');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE task_proposal');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
