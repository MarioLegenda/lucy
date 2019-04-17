<?php

namespace Lucy\Validator;

use Lucy\Validator\Implementation\CannotBeEmpty;
use Lucy\Validator\Implementation\CannotBeEmptyIfExists;
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
     * @return Validator
     *
     * Create a new Validator
     */
    public static function create(): Validator
    {
        return new Validator();
    }
}