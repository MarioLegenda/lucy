<?php


namespace Lucy\Validator;

use Lucy\Validator\Implementation\CannotBeEmpty;
use Lucy\Validator\Implementation\CannotBeEmptyIfExists;
use Lucy\Validator\Implementation\IsArray;
use Lucy\Validator\Implementation\IsArrayIfExists;
use Lucy\Validator\Implementation\IsAssociativeStringArray;
use Lucy\Validator\Implementation\IsBoolean;
use Lucy\Validator\Implementation\IsBooleanIfExists;
use Lucy\Validator\Implementation\IsNumeric;
use Lucy\Validator\Implementation\IsNumericIfExists;
use Lucy\Validator\Implementation\IsString;
use Lucy\Validator\Implementation\IsStringIfExists;
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
        static::$validators[KeyHasToExist::class]            = function(): ValidatorInterface { return new KeyHasToExist(); };
        static::$validators[CannotBeEmpty::class]            = function(): ValidatorInterface { return new CannotBeEmpty(); };
        static::$validators[CannotBeEmptyIfExists::class]    = function(): ValidatorInterface { return new CannotBeEmptyIfExists(); };
        static::$validators[IsString::class]                 = function(): ValidatorInterface { return new IsString(); };
        static::$validators[IsStringIfExists::class]         = function(): ValidatorInterface { return new IsStringIfExists(); };
        static::$validators[IsNumeric::class]                = function(): ValidatorInterface { return new IsNumeric(); };
        static::$validators[IsNumericIfExists::class]        = function(): ValidatorInterface { return new IsNumericIfExists(); };
        static::$validators[IsArray::class]                  = function(): ValidatorInterface { return new IsArray(); };
        static::$validators[IsArrayIfExists::class]          = function(): ValidatorInterface { return new IsArrayIfExists(); };
        static::$validators[IsBoolean::class]                = function(): ValidatorInterface { return new IsBoolean(); };
        static::$validators[IsBooleanIfExists::class]        = function(): ValidatorInterface { return new IsBooleanIfExists(); };
        static::$validators[IsAssociativeStringArray::class] = function(): ValidatorInterface { return new IsAssociativeStringArray(); };
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