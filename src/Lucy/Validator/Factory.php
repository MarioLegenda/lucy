<?php


namespace Lucy\Validator;

use Lucy\Validator\Implementation\CannotBeEmpty;
use Lucy\Validator\Implementation\CannotBeEmptyIfExists;
use Lucy\Validator\Implementation\IsString;
use Lucy\Validator\Implementation\KeyHasToExist;

final class Factory
{
    /**
     * @var array $validators
     */
    private static $validators = [];
    /**
     * Factory constructor.
     *
     * Factory is private because it is used by client code and should never be
     * instantiated by client code. The only method that the client code can use shoule be
     * either Factory::createAndGet() or Factory::addValidator().
     *
     * Only one instance of Factory should exist in the system to avoid creating validators
     * multiple times and recreating custom validators from client code every time the user
     * wants to use a custom validator. The nature of Lucy is a self creating linked list and
     * thus, some areas of it have to be static and non changing.
     */
    private function __construct()
    {
        // has to be initialised in the constructor because expressions are not
        // allowed as property values in PHP
        static::$validators[KeyHasToExist::class] = function() { return new KeyHasToExist(); };
        static::$validators[CannotBeEmpty::class] = function() { return new CannotBeEmpty(); };
        static::$validators[CannotBeEmptyIfExists::class] = function() { return new CannotBeEmptyIfExists(); };
        static::$validators[IsString::class] = function() { return new IsString(); };
    }
    /**
     * @param string $name
     * @return ValidatorInterface
     */
    public static function createAndGet(string $name): ValidatorInterface
    {
        static::createThinAirSelf();

        if (!isset(static::$validators[$name])) {
            $message = sprintf(
                'Internal Lucy error. Validator %s is not registered as a validator and cannot be created',
                $name
            );

            throw new \InvalidArgumentException($message);
        }

        /** @var ValidatorInterface $validator */
        $validator = static::$validators[$name]->__invoke();

        if (!$validator instanceof ValidatorInterface) {
            $message = sprintf(
                'Internal Lucy error. Validator %s has been created but it does not implement %s. Every validator has to implement %s',
                $name,
                ValidatorInterface::class,
                ValidatorInterface::class
            );

            throw new \InvalidArgumentException($message);
        }

        return $validator;
    }

    private static function createThinAirSelf(): void
    {
        new Factory();
    }
}