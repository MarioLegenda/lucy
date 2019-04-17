<?php


namespace Lucy\Validator\Implementation;


use Lucy\Exception\ConfigurationException;
use Lucy\Lucy;
use Lucy\Validator\ValidatorInterface;

class IsAssociativeStringArray implements ValidatorInterface
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
        if (!is_array($value[$name])) {
            $message = sprintf(
                '\'%s\' has to be a array with string keys',
                $name
            );

            if ($parent instanceof Lucy) {
                $message = sprintf(
                    '\'%s\' has to be a array with string keys for parent node \'%s\'',
                    $name,
                    $parent->getNodeName()
                );
            }

            throw new ConfigurationException($message);
        }

        $keys = array_keys($value[$name]);

        foreach ($keys as $key) {
            if (!is_string($key)) {
                if ($errorMessage) throw new ConfigurationException($errorMessage);

                $message = sprintf(
                    '\'%s\' has to be a associative array with string keys. Key \'%s\' is not a string',
                    $name,
                    $key
                );

                if ($parent instanceof Lucy) {
                    $message = sprintf(
                        '\'%s\' has to be a associative array with string keys. Key \'%s\' is not a string for parent node \'%s\'',
                        $name,
                        $key,
                        $parent->getNodeName()
                    );
                }

                throw new ConfigurationException($message);
            }
        }
    }
}