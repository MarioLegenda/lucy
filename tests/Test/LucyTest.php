<?php

namespace Test;

use Lucy\Exception\ConfigurationException;
use Lucy\Lucy;
use PHPUnit\Framework\TestCase;

class LucyTest extends TestCase
{
    public function testLucyRootNode()
    {
        $testArray = ['configuration' => []];

        $lucy = new Lucy('configuration', $testArray);

        static::assertEquals('configuration', $lucy->getNodeName());
        static::assertEquals(1, count($lucy));
        static::assertFalse($lucy->isEmpty());

        $exceptionEntered = false;
        try {
            $lucy->stepInto('non_existing_element');
        } catch (ConfigurationException $e) {
            $exceptionEntered = true;
        }

        static::assertTrue($exceptionEntered);

        static::assertEquals($lucy->stepIntoIfExists('not_exists'), $lucy);
        static::assertTrue($lucy === $lucy->stepIntoIfExists('not_exists'));
    }

    public function testLucySubtreeTypeCheck()
    {
        $testValidArray = [
            'configuration' => [
                'trueBoolean' => true,
                'falseBoolean' => false,
                'aString' => 'string',
                'anEmptyArray' => [],
                'aNonEmptyArray' => [1, 2, 3],
                'anAssociativeArray' => ['value1' => 0, 'value2' => 1, 'value3' => 2],
            ]
        ];

        $exceptionEntered = false;
        try {
            $lucy = new Lucy('configuration', $testValidArray);

            $lucy
                ->stepInto('configuration')
                ->isBoolean('trueBoolean')
                ->isBoolean('falseBoolean')
                ->isString('aString')
                ->isArray('anEmptyArray')
                ->isArray('aNonEmptyArray')
                ->isAssociativeStringArray('anAssociativeArray');

            $lucy
                ->stepIntoIfExists('configuration')
                ->isBoolean('trueBoolean')
                ->isBoolean('falseBoolean')
                ->isString('aString')
                ->isArray('anEmptyArray')
                ->isArray('aNonEmptyArray')
                ->isAssociativeStringArray('anAssociativeArray');
        } catch (ConfigurationException $e) {
            $exceptionEntered = true;
        }

        static::assertFalse($exceptionEntered);
    }

    public function testDeepSubtree()
    {
        $testValidArray = [
            'configuration' => [
                'element-depth-1.0' => [
                    'aString' => 'string',
                    'anInteger' => 4,
                    'aNumber' => '2.3',
                    'element-depth-2' => [
                        'anArray' => [1, 2, 3, 4]
                    ]
                ],
                'element-depth-1.1' => [
                ]
            ]
        ];

        $lucy = new Lucy('configuration', $testValidArray);

        $exceptionEntered = false;
        try {
            $lucy
                ->cannotBeEmpty('configuration')
                ->isAssociativeStringArray('configuration')
                    ->stepInto('configuration')
                    ->cannotBeEmpty('element-depth-1.0')
                    ->isAssociativeStringArray('element-depth-1.0')
                        ->stepInto('element-depth-1.0')
                        ->isString('aString')
                        ->isNumeric('anInteger')
                        ->isNumeric('aNumber')
                        ->cannotBeEmpty('element-depth-2')
                        ->isAssociativeStringArray('element-depth-2')
                            ->stepInto('element-depth-2')
                            ->isArray('anArray');
        } catch (ConfigurationException $e) {
            $exceptionEntered = true;
        }

        static::assertFalse($exceptionEntered);
    }
}