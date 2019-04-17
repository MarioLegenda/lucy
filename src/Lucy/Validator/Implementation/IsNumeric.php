<?php


namespace Lucy\Validator\Implementation;


use Lucy\Exception\ConfigurationException;
use Lucy\Lucy;
use Lucy\Validator\KeyExistsTrait;
use Lucy\Validator\ValidatorInterface;

class IsNumeric implements ValidatorInterface
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
        $this->internalKeyExists($name, $value);

        if (!is_numeric($value[$name])) {
            if ($errorMessage) throw new ConfigurationException($errorMessage);

            $message = sprintf(
                '\'%s\' should be a numeric value',
                $name
            );

            if ($parent instanceof Lucy) {
                $message = sprintf(
                    '\'%s\' should be a numeric value for parent node \'%s\'',
                    $name,
                    $parent->getNodeName()
                );
            }

            throw new ConfigurationException($message);
        }
    }
}