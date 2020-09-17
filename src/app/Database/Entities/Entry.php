<?php

declare(strict_types=1);

namespace App\Database\Entities;

use App\Database\Repositories\EntryRepository;
use App\Database\UuidGenerator;
use App\Interfaces\EntityInterface;
use DateTimeInterface;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * Class Entry
 * @package App\Database\Entities
 */
class Entry implements EntityInterface
{
    /**
     * @var string
     */
    private string $id;

    /**
     * @var array
     */
    private array $data;

    /**
     * @var DateTimeInterface
     */
    private DateTimeInterface $created_at;

    /**
     * @var DateTimeInterface|null
     */
    private ?DateTimeInterface $updated_at = null;

    /**
     * @var DateTimeInterface|null
     */
    private ?DateTimeInterface $deleted_at = null;

    public function __construct()
    {
        if (!isset($this->created_at)) {
            $this->created_at = new \DateTimeImmutable('now');
        }
    }

    public static function loadMetadata(ClassMetadata $metadata): void
    {
        (new ClassMetadataBuilder($metadata))
            ->setCustomRepositoryClass(EntryRepository::class)
            ->setTable('entries')
            ->createField('id', 'guid')
            ->makePrimaryKey()
            ->nullable(false)
            ->generatedValue('CUSTOM')
            ->setCustomIdGenerator(UuidGenerator::class)
            ->build()
            ->createField('data', 'json')
            ->nullable(false)
            ->build()
            ->createField('created_at', 'datetimetz_immutable')
            ->nullable(false)
            ->build()
            ->createField('updated_at', 'datetimetz_immutable')
            ->nullable(true)
            ->build()
            ->createField('deleted_at', 'datetimetz_immutable')
            ->nullable(true)
            ->build()
            ->addIndex(['deleted_at'], 'entries_is_deleted');
    }
}
