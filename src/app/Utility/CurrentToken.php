<?php

declare(strict_types=1);

namespace App\Utility;

use App\Database\Entities\Token;

/**
 * Class CurrentToken
 * @package App\Utility
 */
class CurrentToken
{

    private Token $token;

    /**
     * @param Token $token
     * @return CurrentToken
     */
    public function set(Token $token): CurrentToken
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return Token|null
     */
    public function get(): ?Token
    {
        if (isset($this->token)) {
            return $this->token;
        }
        return null;
    }

    /**
     * @return bool
     */
    public function isset(): bool
    {
        return $this->get() instanceof Token;
    }

    /**
     * @return CurrentToken
     */
    public function unset(): CurrentToken
    {
        unset($this->token);
        return $this;
    }
}