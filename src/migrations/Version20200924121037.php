<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200924121037 extends AbstractMigration
{
    private const TABLE = 'entries';

    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf(!$schema->hasTable(self::TABLE), self::TABLE . 'dos\'nt exists.');
        $this->addSql('ALTER TABLE entries ADD sound NUMERIC(15, 10) NOT NULL');
        $this->addSql('ALTER TABLE entries ADD temp NUMERIC(15, 10) NOT NULL');
        $this->addSql('ALTER TABLE entries ADD light NUMERIC(15, 10) NOT NULL');
        $this->addSql('ALTER TABLE entries ADD humidity NUMERIC(15, 10) NOT NULL');
        $this->addSql('ALTER TABLE entries ADD celsius NUMERIC(15, 10) NOT NULL');
        $this->addSql('ALTER TABLE entries ADD fahrenheit NUMERIC(15, 10) NOT NULL');
        $this->addSql('ALTER TABLE entries ADD kelvin NUMERIC(15, 10) NOT NULL');
        $schema->getTable('entries')->dropColumn('data');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(!$schema->hasTable(self::TABLE), self::TABLE . 'dos\'nt exists.');
        $schema->getTable(self::TABLE)->addColumn('data', 'json', [
            'Notnull' => true
        ]);
        $schema->getTable(self::TABLE)->dropColumn('sound');
        $schema->getTable(self::TABLE)->dropColumn('temp');
        $schema->getTable(self::TABLE)->dropColumn('light');
        $schema->getTable(self::TABLE)->dropColumn('humidity');
        $schema->getTable(self::TABLE)->dropColumn('celsius');
        $schema->getTable(self::TABLE)->dropColumn('fahrenheit');
        $schema->getTable(self::TABLE)->dropColumn('kelvin');
    }
}
