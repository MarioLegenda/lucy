<?php


namespace Lucy\Validator\Implementation;

use Lucy\Exception\ConfigurationException;
use Lucy\Lucy;
use Lucy\Validator\ValidatorInterface;

class CannotBeEmptyIfExists implements ValidatorInterface
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
            if (is_bool($value[$name])) {
                return;
            }

            if (empty($value[$name])) {
                if ($errorMessage) throw new ConfigurationException($errorMessage);

                $message = sprintf(
                    '\'%s\' cannot be empty if exists',
                    $name
                );

                if ($parent instanceof Lucy) {
                    $message = sprintf(
                        '\'%s\' cannot be empty if exists for parent node \'%s\'',
                        $name,
                        $parent->getNodeName()
                    );
                }

                throw new ConfigurationException($message);
            }
        }
    }
}