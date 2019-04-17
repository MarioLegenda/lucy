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

final class Validator
{
    private function __construct() {}
    /**
     * @return ValidatorInterface
     */
    public function keyHasToExist(): ValidatorInterface
    {
        return Factory::createAndGet(KeyHasToExist::class);
    }
    /**
     * @return ValidatorInterface
     */
    public function cannotBeEmpty(): ValidatorInterface
    {
        return Factory::createAndGet(CannotBeEmpty::class);
    }
    /**
     * @return ValidatorInterface
     */
    public function cannotBeEmptyIfExists(): ValidatorInterface
    {
        return Factory::createAndGet(CannotBeEmptyIfExists::class);
    }
    /**
     * @return ValidatorInterface
     */
    public function isString(): ValidatorInterface
    {
        return Factory::createAndGet(IsString::class);
    }
    /**
     * @return ValidatorInterface
     */
    public function isStringIfExists(): ValidatorInterface
    {
        return Factory::createAndGet(IsStringIfExists::class);
    }
    /**
     * @return ValidatorInterface
     */
    public function isNumeric(): ValidatorInterface
    {
        return Factory::createAndGet(IsNumeric::class);
    }
    /**
     * @return ValidatorInterface
     */
    public function isNumericIfExists(): ValidatorInterface
    {
        return Factory::createAndGet(IsNumericIfExists::class);
    }
    /**
     * @return ValidatorInterface
     */
    public function isArray(): ValidatorInterface
    {
        return Factory::createAndGet(IsArray::class);
    }
    /**
     * @return ValidatorInterface
     */
    public function isArrayIfExists(): ValidatorInterface
    {
        return Factory::createAndGet(IsArrayIfExists::class);
    }
    /**
     * @return ValidatorInterface
     */
    public function isBoolean(): ValidatorInterface
    {
        return Factory::createAndGet(IsBoolean::class);
    }
    /**
     * @return ValidatorInterface
     */
    public function isBooleanIfExists(): ValidatorInterface
    {
        return Factory::createAndGet(IsBooleanIfExists::class);
    }
    /**
     * @return ValidatorInterface
     */
    public function isAssociativeStringArray(): ValidatorInterface
    {
        return Factory::createAndGet(IsAssociativeStringArray::class);
    }
    /**
     * @return Validator
     *
     * Create a new Validator
     */
    public static function create(): Validator
    {
        return new Validator();
    }
}