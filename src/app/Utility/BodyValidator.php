<?php

declare(strict_types=1);

namespace App\Utility;

use Respect\Validation\Validator;

class BodyValidator
{

    public static function validatePost(array $data): void
    {
        $data = array_change_key_case($data, CASE_LOWER);
        Validator::allOf(
            Validator::arrayType(),
            Validator::notEmpty(),
            Validator::each(Validator::allOf(
                Validator::stringType(),
                Validator::numericVal()
            )),
            Validator::allOf(
                Validator::key('sound'),
                Validator::key('temp'),
                Validator::key('light'),
                Validator::key('humidity'),
                Validator::key('celsius'),
                Validator::key('fahrenheit'),
                Validator::key('kelvin')
            ),
            Validator::noneOf(
                Validator::key('id'),
                Validator::key('token'),
                Validator::key('created_at'),
                Validator::key('updated_at'),
            )
        )->check($data);
    }

    public static function validatePut(array $data): void
    {
        static::validatePost($data);
    }

    public static function validatePatch(array $data): void
    {
        $data = array_change_key_case($data, CASE_LOWER);
        Validator::allOf(
            Validator::arrayType(),
            Validator::notEmpty(),
            Validator::each(Validator::allOf(
                Validator::stringType(),
                Validator::numericVal()
            )),
            Validator::anyOf(
                Validator::key('sound'),
                Validator::key('temp'),
                Validator::key('light'),
                Validator::key('humidity'),
                Validator::key('celsius'),
                Validator::key('fahrenheit'),
                Validator::key('kelvin')
            ),
            Validator::noneOf(
                Validator::key('id'),
                Validator::key('token'),
                Validator::key('created_at'),
                Validator::key('updated_at'),
            )
        )->check($data);
    }
}