<?php


namespace Lucy\Validator;


use Lucy\Exception\ConfigurationException;
use Lucy\Lucy;

trait KeyExistsTrait
{
    /**
     * @param string $nodeName
     * @param array $node
     * @param Lucy|null $parent
     * @param string|null $errorMessage
     * @throws ConfigurationException
     */
    protected function internalKeyExists(
        string $nodeName,
        array $node,
        Lucy $parent = null,
        string $errorMessage = null
    ): void {
        if (!array_key_exists($nodeName, $node)) {
            if ($errorMessage) throw new ConfigurationException($errorMessage);

            $message = sprintf(
                'Invalid configuration. \'%s\' does not exist',
                $nodeName
            );

            if ($parent instanceof Lucy) {
                $message = sprintf(
                    'Invalid configuration. \'%s\' does not exist for parent node \'%s\'',
                    $nodeName,
                    $parent->getNodeName()
                );
            }

            throw new ConfigurationException($message);
        }
    }
}