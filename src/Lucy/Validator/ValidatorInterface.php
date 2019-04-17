<?php


namespace Lucy\Validator;

use Lucy\Exception\ConfigurationException;
use Lucy\Lucy;

interface ValidatorInterface
{
    /**
     * @param string $name
     * @param array $value
     * @param Lucy|null $parent
     * @param string|null $errorMessage
     * @throws ConfigurationException
     */
    public function validate(
        string $name,
        array $value,
        Lucy $parent = null,
        string $errorMessage = null
    ): void;
}