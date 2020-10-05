<?php

declare(strict_types=1);

namespace App\DTO;

class EntryAverage
{
    private int $amount;
    private int $sound;
    private string $temp;
    private string $light;
    private string $humidity;
    private string $celsius;
    private string $fahrenheit;
    private string $kelvin;
    private string $date;

    public function __construct(
        int $amount,
        int $sound,
        string $temp,
        string $light,
        string $humidity,
        string $celsius,
        string $fahrenheit,
        string $kelvin,
        string $date
    ) {
        $this->amount = $amount;
        $this->sound = $sound;
        $this->temp = $temp;
        $this->light = $light;
        $this->humidity = $humidity;
        $this->celsius = $celsius;
        $this->fahrenheit = $fahrenheit;
        $this->kelvin = $kelvin;
        $this->date = $date;
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @return int
     */
    public function getSound(): int
    {
        return $this->sound;
    }

    /**
     * @return string
     */
    public function getTemp(): string
    {
        return $this->temp;
    }

    /**
     * @return string
     */
    public function getLight(): string
    {
        return $this->light;
    }

    /**
     * @return string
     */
    public function getHumidity(): string
    {
        return $this->humidity;
    }

    /**
     * @return string
     */
    public function getCelsius(): string
    {
        return $this->celsius;
    }

    /**
     * @return string
     */
    public function getFahrenheit(): string
    {
        return $this->fahrenheit;
    }

    /**
     * @return string
     */
    public function getKelvin(): string
    {
        return $this->kelvin;
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * @return string[]
     */
    public function toArray(): array
    {
        return [
            'amount' => (string) $this->getAmount(),
            'sound' => (string) ($this->getSound()/65535),
            'temp' => (string) $this->getTemp(),
            'light' => (string) $this->getLight(),
            'humidity' => (string) $this->getHumidity(),
            'celsius' => (string) $this->getCelsius(),
            'fahrenheit' => (string) $this->getFahrenheit(),
            'kelvin' => (string) $this->getKelvin(),
            'date' => (string) $this->getDate(),
        ];
    }
}