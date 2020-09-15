<?php

namespace App\Database\Entities;

use App\Database\Repositories\TokenRepository;
use App\Database\UuidGenerator;
use App\Interfaces\EntityInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use DateTimeInterface;

class TokenEntity implements EntityInterface
{

    private string $id;
    private string $value;
    private DateTimeInterface $created_at;
    private ?DateTimeInterface $updated_at = null;
    private ?DateTimeInterface $deleted_at = null;

    public function __construct()
    {
        if (!isset($this->created_at)) {
            $this->created_at = new \DateTimeImmutable('now');
        }
    }

    public static function loadMetadata(ClassMetadata $metadata)
    {
        $metadata->setCustomRepositoryClass(TokenRepository::class);
        $metadata->setPrimaryTable(['name' => 'tokens']);
        $metadata->mapField([
            'fieldName' => 'id',
            'type' => 'guid',

        ]);
        $builder = new ClassMetadataBuilder($metadata);
        $builder->setCustomRepositoryClass(TokenRepository::class)
            ->setTable('tokens')
            ->createField('id', 'guid')
            ->makePrimaryKey()
            ->nullable(false)
            ->generatedValue('custom')
            ->setCustomIdGenerator(UuidGenerator::class)
            ->build()
            ->createField('value', 'text')
            ->nullable(false)
            ->unique(true)
            ->build()
            ->createField('created_at', 'datetimetz')
            ->nullable(false)
            ->build()
            ->createField('updated_at', 'datetimetz')
            ->nullable(true)

    }
}