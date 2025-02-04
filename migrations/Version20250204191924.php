<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250204191924 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE agent DROP CONSTRAINT FK_268B9C9D79F37AE5');
        $this->addSql('ALTER TABLE agent ADD CONSTRAINT FK_268B9C9D79F37AE5 FOREIGN KEY (id_user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE customer DROP CONSTRAINT fk_81398e096b3ca4b');
        $this->addSql('DROP INDEX uniq_81398e096b3ca4b');
        $this->addSql('ALTER TABLE customer ADD id_user_id UUID NOT NULL');
        $this->addSql('ALTER TABLE customer DROP id_user');
        $this->addSql('COMMENT ON COLUMN customer.id_user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E0979F37AE5 FOREIGN KEY (id_user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_81398E0979F37AE5 ON customer (id_user_id)');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT fk_8d93d64964cf9d9e');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT fk_8d93d6498b870e04');
        $this->addSql('DROP INDEX uniq_8d93d6498b870e04');
        $this->addSql('DROP INDEX uniq_8d93d64964cf9d9e');
        $this->addSql('ALTER TABLE "user" DROP id_agent_id');
        $this->addSql('ALTER TABLE "user" DROP id_customer_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE customer DROP CONSTRAINT FK_81398E0979F37AE5');
        $this->addSql('DROP INDEX UNIQ_81398E0979F37AE5');
        $this->addSql('ALTER TABLE customer ADD id_user UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE customer DROP id_user_id');
        $this->addSql('COMMENT ON COLUMN customer.id_user IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT fk_81398e096b3ca4b FOREIGN KEY (id_user) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_81398e096b3ca4b ON customer (id_user)');
        $this->addSql('ALTER TABLE "user" ADD id_agent_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD id_customer_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN "user".id_agent_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "user".id_customer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT fk_8d93d64964cf9d9e FOREIGN KEY (id_agent_id) REFERENCES agent (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT fk_8d93d6498b870e04 FOREIGN KEY (id_customer_id) REFERENCES customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_8d93d6498b870e04 ON "user" (id_customer_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_8d93d64964cf9d9e ON "user" (id_agent_id)');
        $this->addSql('ALTER TABLE agent DROP CONSTRAINT fk_268b9c9d79f37ae5');
        $this->addSql('ALTER TABLE agent ADD CONSTRAINT fk_268b9c9d79f37ae5 FOREIGN KEY (id_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
