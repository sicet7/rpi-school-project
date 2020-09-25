<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200925132626 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE entries ALTER sound TYPE INT');
        $this->addSql('ALTER TABLE entries ALTER sound DROP DEFAULT');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE entries ALTER sound TYPE NUMERIC(15, 10)');
        $this->addSql('ALTER TABLE entries ALTER sound DROP DEFAULT');
    }
}
