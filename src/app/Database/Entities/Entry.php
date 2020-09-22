<?php

declare(strict_types=1);

namespace App\Database\Entities;

use App\Database\Repositories\EntryRepository;
use App\Database\UuidGenerator;
use App\Interfaces\EntityInterface;
use DateTimeInterface;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;
use Ramsey\Uuid\Uuid;

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
     * @var Token
     */
    private Token $token;

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

    public function __construct(Token $token)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->created_at = new \DateTimeImmutable('now');
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @return Entry
     */
    public function setData(array $data): Entry
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return Token
     */
    public function getToken(): Token
    {
        return $this->token;
    }

    /**
     * @param Token $token
     * @return Entry
     */
    public function setToken(Token $token): Entry
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function getCreatedAt(): DateTimeInterface
    {
        return $this->created_at;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updated_at;
    }

    /**
     * @return Entry
     */
    public function setUpdatedAt(): Entry
    {
        $this->updated_at = new \DateTimeImmutable('now');
        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getDeletedAt(): ?DateTimeInterface
    {
        return $this->deleted_at;
    }

    /**
     * @return Entry
     */
    public function setDeletedAt(): Entry
    {
        $this->deleted_at = new \DateTimeImmutable('now');
        return $this;
    }

    public static function loadMetadata(ClassMetadata $metadata): void
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setCustomRepositoryClass(EntryRepository::class);
        $builder->setTable('entries');

        $builder->createField('id', 'guid')
            ->makePrimaryKey()
            ->nullable(false)
            ->generatedValue('CUSTOM')
            ->setCustomIdGenerator(UuidGenerator::class)
            ->build();

        $builder->createField('data', 'json')
            ->nullable(false)
            ->build();

        $builder->addManyToOne('token', Token::class, 'id');

        $builder->createField('created_at', 'datetimetz_immutable')
            ->nullable(false)
            ->build();

        $builder->createField('updated_at', 'datetimetz_immutable')
            ->nullable(true)
            ->build();

        $builder->createField('deleted_at', 'datetimetz_immutable')
            ->nullable(true)
            ->build();

        $builder->addIndex(['deleted_at'], 'entries_is_deleted');
    }
}
