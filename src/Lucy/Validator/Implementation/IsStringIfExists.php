<?php


namespace Lucy\Validator\Implementation;

use Lucy\Exception\ConfigurationException;
use Lucy\Lucy;
use Lucy\Validator\ValidatorInterface;

class IsStringIfExists implements ValidatorInterface
{
    /**
     * @inheritDoc
     */
    public function validate(
        string $name,
        array $value,
        Lucy $parent = null,
        string $errorMessage = null
    ): void {
        if (array_key_exists($name, $value)) {
            if (!is_string($value[$name])) {
                if ($errorMessage) throw new ConfigurationException($errorMessage);

                $message = sprintf(
                    'If \'%s\' exists, it has to be a string',
                    $name
                );

                if ($parent instanceof Lucy) {
                    $message = sprintf(
                        'If \'%s\' exists, it has to be a string for parent node \'%s\'',
                        $name,
                        $parent->getNodeName()
                    );
                }

                throw new ConfigurationException($message);
            }
        }
    }
}