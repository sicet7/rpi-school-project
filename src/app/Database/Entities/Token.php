<?php

namespace App\Database\Entities;

use App\Database\Repositories\TokenRepository;
use App\Database\UuidGenerator;
use App\Interfaces\EntityInterface;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use DateTimeInterface;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * Class Token
 * @package App\Database\Entities
 */
class Token implements EntityInterface
{

    /**
     * @var string
     */
    private string $id;

    /**
     * @var string
     */
    private string $value;

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

    /**
     * Token constructor.
     */
    public function __construct()
    {
        if (!isset($this->created_at)) {
            $this->created_at = new \DateTimeImmutable('now');
        }
    }

    public static function loadMetadata(ClassMetadata $metadata): void
    {
        (new ClassMetadataBuilder($metadata))
            ->setCustomRepositoryClass(TokenRepository::class)
            ->setTable('tokens')
            ->createField('id', 'guid')
            ->makePrimaryKey()
            ->nullable(false)
            ->generatedValue('CUSTOM')
            ->setCustomIdGenerator(UuidGenerator::class)
            ->build()
            ->createField('value', 'text')
            ->nullable(false)
            ->unique(true)
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
            ->addIndex(['deleted_at'], 'tokens_is_deleted');
    }
}
