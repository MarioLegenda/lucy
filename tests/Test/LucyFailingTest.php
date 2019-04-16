<?php


namespace Test;

use Lucy\Exception\ConfigurationException;
use PHPUnit\Framework\TestCase;
use Lucy\Lucy;

class LucyFailingTest extends TestCase
{
    public function testLucyRootNodeFail()
    {
        $testArray = ['configuration' => []];

        $exceptionEntered = false;
        try {
            new Lucy('invalidKey', $testArray);
        } catch (ConfigurationException $e) {
            $exceptionEntered = true;
        }

        static::assertTrue($exceptionEntered);
    }

    public function testLucySubtreeTypeFail()
    {
        $testValidArray = [
            'configuration' => [
                'trueBoolean' => 'not boolean',
                'falseBoolean' => false,
                'aString' => 3940,
                'anEmptyArray' => 'not array',
                'aNonEmptyArray' => [],
                'anAssociativeArray' => ['first key is string' => 1, 2, 3, 'last key is string' => 4],
            ]
        ];

        $exceptionEntered = false;
        try {
            $lucy = new Lucy('configuration', $testValidArray);

            $lucy
                ->stepInto('configuration')
                ->isBoolean('trueBoolean');
        } catch (ConfigurationException $e) {
            $exceptionEntered = true;
        }

        static:: assertTrue($exceptionEntered);

        $exceptionEntered = false;
        try {
            $lucy = new Lucy('configuration', $testValidArray);

            $lucy
                ->stepInto('configuration')
                ->isString('aString');
        } catch (ConfigurationException $e) {
            $exceptionEntered = true;
        }

        static:: assertTrue($exceptionEntered);

        static:: assertTrue($exceptionEntered);

        $exceptionEntered = false;
        try {
            $lucy = new Lucy('configuration', $testValidArray);

            $lucy
                ->stepInto('configuration')
                ->isArray('anEmptyArray');
        } catch (ConfigurationException $e) {
            $exceptionEntered = true;
        }

        static:: assertTrue($exceptionEntered);

        static:: assertTrue($exceptionEntered);

        $exceptionEntered = false;
        try {
            $lucy = new Lucy('configuration', $testValidArray);

            $lucy
                ->stepInto('configuration')
                ->isAssociativeStringArray('anAssociativeArray');
        } catch (ConfigurationException $e) {
            $exceptionEntered = true;
        }

        static:: assertTrue($exceptionEntered);
    }
}