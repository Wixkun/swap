<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250204155047 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "user" ADD id_agent_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD id_customer_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN "user".id_agent_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "user".id_customer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D64964CF9D9E FOREIGN KEY (id_agent_id) REFERENCES agent (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D6498B870E04 FOREIGN KEY (id_customer_id) REFERENCES customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64964CF9D9E ON "user" (id_agent_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6498B870E04 ON "user" (id_customer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D64964CF9D9E');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D6498B870E04');
        $this->addSql('DROP INDEX UNIQ_8D93D64964CF9D9E');
        $this->addSql('DROP INDEX UNIQ_8D93D6498B870E04');
        $this->addSql('ALTER TABLE "user" DROP id_agent_id');
        $this->addSql('ALTER TABLE "user" DROP id_customer_id');
    }
}
