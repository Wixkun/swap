<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250206191200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE conversation DROP CONSTRAINT FK_8A8E26E98B870E04');
        $this->addSql('ALTER TABLE conversation DROP CONSTRAINT FK_8A8E26E964CF9D9E');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E98B870E04 FOREIGN KEY (id_customer_id) REFERENCES customer (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E964CF9D9E FOREIGN KEY (id_agent_id) REFERENCES agent (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307FE0F2C01E');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307F79F37AE5');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FE0F2C01E FOREIGN KEY (id_conversation_id) REFERENCES conversation (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F79F37AE5 FOREIGN KEY (id_user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE review DROP CONSTRAINT FK_794381C664CF9D9E');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C664CF9D9E FOREIGN KEY (id_agent_id) REFERENCES agent (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT FK_527EDB257E3C61F9');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB257E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task_proposal DROP CONSTRAINT FK_FF257E3E3414710B');
        $this->addSql('ALTER TABLE task_proposal DROP CONSTRAINT FK_FF257E3E8DB60186');
        $this->addSql('ALTER TABLE task_proposal ADD CONSTRAINT FK_FF257E3E3414710B FOREIGN KEY (agent_id) REFERENCES agent (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task_proposal ADD CONSTRAINT FK_FF257E3E8DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT fk_b6bd307fe0f2c01e');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT fk_b6bd307f79f37ae5');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT fk_b6bd307fe0f2c01e FOREIGN KEY (id_conversation_id) REFERENCES conversation (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT fk_b6bd307f79f37ae5 FOREIGN KEY (id_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE review DROP CONSTRAINT fk_794381c664cf9d9e');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT fk_794381c664cf9d9e FOREIGN KEY (id_agent_id) REFERENCES agent (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task_proposal DROP CONSTRAINT fk_ff257e3e3414710b');
        $this->addSql('ALTER TABLE task_proposal DROP CONSTRAINT fk_ff257e3e8db60186');
        $this->addSql('ALTER TABLE task_proposal ADD CONSTRAINT fk_ff257e3e3414710b FOREIGN KEY (agent_id) REFERENCES agent (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task_proposal ADD CONSTRAINT fk_ff257e3e8db60186 FOREIGN KEY (task_id) REFERENCES task (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE conversation DROP CONSTRAINT fk_8a8e26e98b870e04');
        $this->addSql('ALTER TABLE conversation DROP CONSTRAINT fk_8a8e26e964cf9d9e');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT fk_8a8e26e98b870e04 FOREIGN KEY (id_customer_id) REFERENCES customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT fk_8a8e26e964cf9d9e FOREIGN KEY (id_agent_id) REFERENCES agent (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT fk_527edb257e3c61f9');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT fk_527edb257e3c61f9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
