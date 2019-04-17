<?php


namespace Lucy\Validator\Implementation;


use Lucy\Exception\ConfigurationException;
use Lucy\Lucy;
use Lucy\Validator\ValidatorInterface;

class CannotBeEmpty implements ValidatorInterface
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
        if (!array_key_exists($name, $value)) {
            $message = sprintf(
                'Node \'%s\' does not exist so Lucy cannot check if it can be empty',
                $name
            );

            if ($parent instanceof Lucy) {
                $message = sprintf(
                    'Node \'%s\' does not exist so Lucy cannot check if it can be empty for parent node \'%s\'',
                    $name,
                    $parent->getNodeName()
                );
            }

            throw new ConfigurationException($message);
        }

        if (is_bool($value[$name])) {
            return;
        }

        if (empty($value[$name])) {
            if ($errorMessage) throw new ConfigurationException($errorMessage);

            $message = sprintf(
                'Node \'%s\' cannot be empty',
                $name,
            );

            if ($parent instanceof Lucy) {
                $message = sprintf(
                    'Node \'%s\' cannot be empty for parent node \'%s\'',
                    $name,
                    $parent->getNodeName()
                );
            }

            throw new ConfigurationException($message);
        }
    }
}