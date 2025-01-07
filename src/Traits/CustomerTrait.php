<?php

namespace Altostrat\Tools\Traits;

trait CustomerTrait
{
    public function getAuthIdentifierName(): void
    {
        //
    }

    public function getAuthIdentifier(): string
    {
        return $this->attributes['id'];
    }

    public function getAuthPassword(): void
    {
        //
    }

    public function getRememberToken(): void
    {
        //
    }

    public function setRememberToken($value): void
    {
        //
    }

    public function getRememberTokenName(): void
    {
        //
    }
}
