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
     * @var string
     */
    private string $sound;

    /**
     * @var string
     */
    private string $temp;

    /**
     * @var string
     */
    private string $light;

    /**
     * @var string
     */
    private string $humidity;

    /**
     * @var string
     */
    private string $celsius;

    /**
     * @var string
     */
    private string $fahrenheit;

    /**
     * @var string
     */
    private string $kelvin;

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

    public function __construct(Token $token)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->token = $token;
        $this->setCreatedAt();
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
    public function getSound(): string
    {
        return $this->sound;
    }

    /**
     * @param string $sound
     * @return Entry
     */
    public function setSound(string $sound): Entry
    {
        $this->sound = $sound;
        $this->setUpdatedAt();
        return $this;
    }

    /**
     * @return string
     */
    public function getTemp(): string
    {
        return $this->temp;
    }

    /**
     * @param string $temp
     * @return Entry
     */
    public function setTemp(string $temp): Entry
    {
        $this->temp = $temp;
        $this->setUpdatedAt();
        return $this;
    }

    /**
     * @return string
     */
    public function getLight(): string
    {
        return $this->light;
    }

    /**
     * @param string $light
     * @return Entry
     */
    public function setLight(string $light): Entry
    {
        $this->light = $light;
        $this->setUpdatedAt();
        return $this;
    }

    /**
     * @return string
     */
    public function getHumidity(): string
    {
        return $this->humidity;
    }

    /**
     * @param string $humidity
     * @return Entry
     */
    public function setHumidity(string $humidity): Entry
    {
        $this->humidity = $humidity;
        $this->setUpdatedAt();
        return $this;
    }

    /**
     * @return string
     */
    public function getCelsius(): string
    {
        return $this->celsius;
    }

    /**
     * @param string $celsius
     * @return Entry
     */
    public function setCelsius(string $celsius): Entry
    {
        $this->celsius = $celsius;
        $this->setUpdatedAt();
        return $this;
    }

    /**
     * @return string
     */
    public function getFahrenheit(): string
    {
        return $this->fahrenheit;
    }

    /**
     * @param string $fahrenheit
     * @return Entry
     */
    public function setFahrenheit(string $fahrenheit): Entry
    {
        $this->fahrenheit = $fahrenheit;
        $this->setUpdatedAt();
        return $this;
    }

    /**
     * @return string
     */
    public function getKelvin(): string
    {
        return $this->kelvin;
    }

    /**
     * @param string $kelvin
     * @return Entry
     */
    public function setKelvin(string $kelvin): Entry
    {
        $this->kelvin = $kelvin;
        $this->setUpdatedAt();
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
        $this->setUpdatedAt();
        return $this;
    }

    /**
     * @param bool $format
     * @return DateTimeInterface|string
     */
    public function getCreatedAt(bool $format = false)
    {
        if ($format) {
            return $this->formatDate($this->created_at);
        }
        return $this->created_at;
    }

    /**
     * @param DateTimeInterface|null $dateTime
     * @return Entry
     */
    public function setCreatedAt(?DateTimeInterface $dateTime = null): Entry
    {
        if ($dateTime === null) {
            $this->created_at = new \DateTimeImmutable('now');
        } else {
            $this->created_at = $dateTime;
        }
        return $this;
    }

    /**
     * @param bool $format
     * @return DateTimeInterface|string|null
     */
    public function getUpdatedAt(bool $format = false)
    {
        if ($format) {
            return $this->formatDate($this->updated_at);
        }
        return $this->updated_at;
    }

    /**
     * @param DateTimeInterface|null $dateTime
     * @return Entry
     */
    public function setUpdatedAt(?DateTimeInterface $dateTime = null): Entry
    {
        if ($dateTime === null) {
            $this->updated_at = new \DateTimeImmutable('now');
        } else {
            $this->updated_at = $dateTime;
        }
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

        $builder->addManyToOne('token', Token::class, 'id');

        $builder->createField('sound', 'decimal')
            ->nullable(false)
            ->build();

        $builder->createField('temp', 'decimal')
            ->nullable(false)
            ->build();

        $builder->createField('light', 'decimal')
            ->nullable(false)
            ->build();

        $builder->createField('humidity', 'decimal')
            ->nullable(false)
            ->build();

        $builder->createField('celsius', 'decimal')
            ->nullable(false)
            ->build();

        $builder->createField('fahrenheit', 'decimal')
            ->nullable(false)
            ->build();

        $builder->createField('kelvin', 'decimal')
            ->nullable(false)
            ->build();

        $builder->createField('created_at', 'datetimetz_immutable')
            ->nullable(false)
            ->build();

        $builder->createField('updated_at', 'datetimetz_immutable')
            ->nullable(true)
            ->build();
    }

    /**
     * @param \DateTimeInterface|null $dateTime
     * @return string|null
     */
    private function formatDate(?\DateTimeInterface $dateTime = null): ?string
    {
        if ($dateTime instanceof \DateTimeInterface) {
            return $dateTime->format(\DateTimeInterface::ISO8601);
        }
        return null;
    }
}
