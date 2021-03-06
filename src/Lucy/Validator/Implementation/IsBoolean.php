<?php


namespace Lucy\Validator\Implementation;


use Lucy\Exception\ConfigurationException;
use Lucy\Lucy;
use Lucy\Util\KeyExistsTrait;
use Lucy\Validator\ValidatorInterface;

class IsBoolean implements ValidatorInterface
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

        if (!is_bool($value[$name])) {
            if ($errorMessage) throw new ConfigurationException($errorMessage);

            $message = sprintf(
                '\'%s\' has to be a boolean',
                $name
            );

            if ($parent instanceof Lucy) {
                $message = sprintf(
                    '\'%s\' has to be a boolean for parent node \'%s\'',
                    $name,
                    $parent->getNodeName()
                );
            }

            throw new ConfigurationException($message);
        }
    }
}