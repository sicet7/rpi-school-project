<?php

declare(strict_types=1);

namespace App\Database\Entities;

use App\Database\Repositories\TokenRepository;
use App\Database\UuidGenerator;
use App\Interfaces\EntityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use DateTimeInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Ramsey\Uuid\Uuid;

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
     * @var Collection
     */
    private Collection $entries;

    /**
     * Token constructor.
     * @param string|null $value
     */
    public function __construct(?string $value = null)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->created_at = new \DateTimeImmutable('now');
        $this->entries = new ArrayCollection();
        if ($value !== null) {
            $this->value = $value;
        }
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return Token
     */
    public function setValue(string $value): Token
    {
        $this->value = $value;
        $this->setUpdatedAt();
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
     * @return Token
     */
    public function setUpdatedAt(): Token
    {
        $this->updated_at = new \DateTimeImmutable('now');
        return $this;
    }

    /**
     * @return Collection
     */
    public function getEntries(): Collection
    {
        return $this->entries;
    }

    /**
     * @inheritDoc
     */
    public static function loadMetadata(ClassMetadata $metadata): void
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setCustomRepositoryClass(TokenRepository::class);
        $builder->setTable('tokens');

        $builder->createField('id', 'guid')
            ->makePrimaryKey()
            ->nullable(false)
            ->generatedValue('CUSTOM')
            ->setCustomIdGenerator(UuidGenerator::class)
            ->build();

        $builder->createField('value', 'text')
            ->nullable(false)
            ->build();

        $builder->createField('created_at', 'datetimetz_immutable')
            ->nullable(false)
            ->build();

        $builder->createField('updated_at', 'datetimetz_immutable')
            ->nullable(true)
            ->build();

        $builder->addOneToMany('entries', Entry::class, 'token');
        $builder->addUniqueConstraint(['value'], 'value_unique');
    }
}
