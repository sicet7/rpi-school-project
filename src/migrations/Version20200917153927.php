<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20200917153927
 * @package Migrations
 */
final class Version20200917153927 extends AbstractMigration
{
    private const TABLE_NAME = 'entries';

    /**
     * @return string
     */
    public function getDescription() : string
    {
        return 'Create entries table.';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $table = $schema->createTable(static::TABLE_NAME);
        $table->addColumn('id', 'guid')
            ->setNotnull(true);
        $table->addColumn('data', 'json')
            ->setNotnull(true);
        $table->addColumn('created_at', 'datetimetz_immutable')
            ->setNotnull(true);
        $table->addColumn('updated_at', 'datetimetz_immutable')
            ->setNotnull(false)
            ->setDefault(null);
        $table->addColumn('deleted_at', 'datetimetz_immutable')
            ->setNotnull(false)
            ->setDefault(null);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['deleted_at']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        $schema->dropTable(static::TABLE_NAME);
    }
}
