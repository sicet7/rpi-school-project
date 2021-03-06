<?php

declare(strict_types=1);

namespace App\Utility;

use JsonException;
use Respect\Validation\Validator;

/**
 * Class Json
 * @package App\Utility
 */
class Json
{
    /**
     * @var int
     */
    private int $encodeOptions;

    /**
     * @var int
     */
    private int $decodeOptions;

    /**
     * Json constructor.
     * @param int $encodeOptions
     * @param int $decodeOptions
     */
    public function __construct(int $encodeOptions = 0, int $decodeOptions = 0)
    {
        $this->encodeOptions = $encodeOptions;
        $this->decodeOptions = $decodeOptions;
    }

    /**
     * @param array $data
     * @return string
     * @throws JsonException
     */
    public function encode(array $data): string
    {
        return json_encode(
            $data,
            $this->encodeOptions
        );
    }

    /**
     * @param string $json
     * @return array
     * @throws JsonException
     */
    public function decode(string $json): array
    {
        return json_decode(
            $json,
            true,
            512,
            $this->decodeOptions
        );
    }

    /**
     * @param string $json
     * @return bool
     */
    public function validate(string $json): bool
    {
        return Validator::json()->validate($json);
    }
}