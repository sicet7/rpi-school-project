<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20200917144032
 * @package Migrations
 */
final class Version20200917144032 extends AbstractMigration
{
    private const TABLE_NAME = 'tokens';

    /**
     * @return string
     */
    public function getDescription() : string
    {
        return 'Create initial tokens table.';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $table = $schema->createTable(self::TABLE_NAME);
        $table->addColumn('id', 'guid')
            ->setNotnull(true);
        $table->addColumn('value', 'text')
            ->setNotnull(true);
        $table->addColumn('created_at', 'datetimetz_immutable')
            ->setNotnull(true);
        $table->addColumn('updated_at', 'datetimetz_immutable')
            ->setDefault(null)
            ->setNotnull(false);
        $table->addColumn('deleted_at', 'datetimetz_immutable')
            ->setDefault(null)
            ->setNotnull(false);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['value']);
        $table->addIndex(['deleted_at']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        $schema->dropTable(self::TABLE_NAME);
    }
}
