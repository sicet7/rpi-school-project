<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200922180143 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE entries (id UUID NOT NULL, token_id UUID DEFAULT NULL, data JSON NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2DF8B3C541DEE7B9 ON entries (token_id)');
        $this->addSql('COMMENT ON COLUMN entries.created_at IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('COMMENT ON COLUMN entries.updated_at IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('CREATE TABLE tokens (id UUID NOT NULL, value TEXT NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX value_unique ON tokens (value)');
        $this->addSql('COMMENT ON COLUMN tokens.created_at IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('COMMENT ON COLUMN tokens.updated_at IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('ALTER TABLE entries ADD CONSTRAINT FK_2DF8B3C541DEE7B9 FOREIGN KEY (token_id) REFERENCES tokens (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE entries DROP CONSTRAINT FK_2DF8B3C541DEE7B9');
        $this->addSql('DROP TABLE entries');
        $this->addSql('DROP TABLE tokens');
    }
}
