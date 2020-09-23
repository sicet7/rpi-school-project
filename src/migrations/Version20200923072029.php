<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200923072029 extends AbstractMigration
{
    private const ID = 'f2a3fbfb-912a-42ec-8c1c-7c6aec99a678';

    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf(!$schema->hasTable('tokens'), '\'tokens\' table is required to run this migration.');
        $qb = $this->connection->createQueryBuilder()
            ->insert('tokens')
            ->values([
                'id' => '?',
                'value' => '?',
                'created_at' => '?'
            ])->setParameter(0, self::ID)
            ->setParameter(1, '0123456789')
            ->setParameter(2, date(\DateTimeInterface::ISO8601));
        $qb->execute();
    }

    public function down(Schema $schema) : void
    {
        if ($schema->hasTable('tokens')) {
            $qb = $this->connection->createQueryBuilder();
            $qb->delete('tokens', 'e');
            $qb->where('id = ?');
            $qb->setParameter(0, self::ID);
            $qb->execute();
        } else {
            $this->warnIf(true, '\'tokens\' table missing, value not deleted.');
        }
    }
}
