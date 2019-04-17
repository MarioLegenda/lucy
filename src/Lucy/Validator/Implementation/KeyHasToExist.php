<?php

namespace Lucy\Validator\Implementation;

use Lucy\Lucy;
use Lucy\Validator\KeyExistsTrait;
use Lucy\Validator\ValidatorInterface;

class KeyHasToExist implements ValidatorInterface
{
    use KeyExistsTrait;
    /**
     * @inheritDoc
     */
    public function validate(
        string $name,
        array $value,
        Lucy $parent = null,
        string $errorMessage = null
    ): void {
        $this->internalKeyExists($name, $value, $parent, $errorMessage);
    }
}