<?php


namespace Test;

use Lucy\Exception\ConfigurationException;
use Lucy\Lucy;
use PHPUnit\Framework\TestCase;

class LucyIndividualValidatorsTest extends TestCase
{
    /**
     * @var array $deepArray
     */
    private $deepArray = [];

    public function setUp()
    {
        $this->deepArray = [
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

        parent::setUp();
    }

    public function testKeyHasToExist()
    {
        $lucy = new Lucy('configuration', $this->deepArray);

        $enteredException = false;
        try {
            $lucy->keyHasToExist('configuration');
        } catch (ConfigurationException $e) {
            $enteredException = true;
        }

        static::assertFalse($enteredException);

        $enteredException = false;
        try {
            $lucy->keyHasToExist('invalid');
        } catch (ConfigurationException $e) {
            $enteredException = true;
        }

        static::assertTrue($enteredException);
    }

    public function testCannotBeEmpty()
    {
        $lucy = new Lucy('configuration', $this->deepArray);

        $enteredException = false;
        try {
            $lucy->cannotBeEmpty('configuration');
        } catch (ConfigurationException $e) {
            $enteredException = true;
        }

        static::assertFalse($enteredException);

        $lucy = new Lucy('configuration', $this->deepArray);

        $enteredException = false;
        try {
            $lucy->cannotBeEmpty('invalid');
        } catch (ConfigurationException $e) {
            $enteredException = true;
        }

        static::assertTrue($enteredException);
    }

    public function testCannotBeEmptyIfExists()
    {
        $lucy = new Lucy('configuration', $this->deepArray);

        $enteredException = false;
        try {
            $lucy->cannotBeEmptyIfExists('configuration');
        } catch (ConfigurationException $e) {
            $enteredException = true;
        }

        static::assertFalse($enteredException);

        $configuration = [
            'configuration' => null
        ];

        $lucy = new Lucy('configuration', $configuration);

        $enteredException = false;
        try {
            $lucy->cannotBeEmptyIfExists('configuration');
        } catch (ConfigurationException $e) {
            $enteredException = true;
        }

        static::assertTrue($enteredException);
    }

    public function testIsString()
    {
        $configuration = [
            'isString' => 'string'
        ];

        $lucy = new Lucy('isString', $configuration);

        $enteredException = false;
        try {
            $lucy->isString('isString');
        } catch (ConfigurationException $e) {
            $enteredException = true;
        }

        static::assertFalse($enteredException);

        $configuration = [
            'notString' => null
        ];

        $lucy = new Lucy('notString', $configuration);

        $enteredException = false;
        try {
            $lucy->isString('notString');
        } catch (ConfigurationException $e) {
            $enteredException = true;
        }

        static::assertTrue($enteredException);
    }

    public function testIsStringIfExists()
    {
        $configuration = ['configuration' => 'string'];

        $lucy = new Lucy('configuration', $configuration);

        $enteredException = false;
        try {
            $lucy->isStringIfExists('configuration');
        } catch (ConfigurationException $e) {
            $enteredException = true;
        }

        static::assertFalse($enteredException);

        $configuration = [
            'configuration' => null
        ];

        $lucy = new Lucy('configuration', $configuration);

        $enteredException = false;
        try {
            $lucy->isStringIfExists('configuration');
        } catch (ConfigurationException $e) {
            $enteredException = true;
        }

        static::assertTrue($enteredException);
    }

    public function testIsNumeric()
    {
        $configuration = [
            'isNumeric' => '7'
        ];

        $lucy = new Lucy('isNumeric', $configuration);

        $enteredException = false;
        try {
            $lucy->isNumeric('isNumeric');
        } catch (ConfigurationException $e) {
            $enteredException = true;
        }

        static::assertFalse($enteredException);

        $configuration = [
            'isNumeric' => 7
        ];

        $lucy = new Lucy('isNumeric', $configuration);

        $enteredException = false;
        try {
            $lucy->isNumeric('isNumeric');
        } catch (ConfigurationException $e) {
            $enteredException = true;
        }

        static::assertFalse($enteredException);

        $configuration = [
            'isNumeric' => null
        ];

        $lucy = new Lucy('isNumeric', $configuration);

        $enteredException = false;
        try {
            $lucy->isNumeric('isNumeric');
        } catch (ConfigurationException $e) {
            $enteredException = true;
        }

        static::assertTrue($enteredException);
    }

    public function testIsNumericIfExists()
    {
        $configuration = ['configuration' => '7'];

        $lucy = new Lucy('configuration', $configuration);

        $enteredException = false;
        try {
            $lucy->isNumericIfExists('configuration');
        } catch (ConfigurationException $e) {
            $enteredException = true;
        }

        static::assertFalse($enteredException);

        $configuration = ['configuration' => 7];

        $lucy = new Lucy('configuration', $configuration);

        $enteredException = false;
        try {
            $lucy->isNumericIfExists('configuration');
        } catch (ConfigurationException $e) {
            $enteredException = true;
        }

        static::assertFalse($enteredException);

        $configuration = ['configuration' => 7];

        $lucy = new Lucy('configuration', $configuration);

        $enteredException = false;
        try {
            $lucy->isNumericIfExists('not_exists');
        } catch (ConfigurationException $e) {
            $enteredException = true;
        }

        static::assertFalse($enteredException);

        $configuration = [
            'configuration' => null
        ];

        $lucy = new Lucy('configuration', $configuration);

        $enteredException = false;
        try {
            $lucy->isNumericIfExists('configuration');
        } catch (ConfigurationException $e) {
            $enteredException = true;
        }

        static::assertTrue($enteredException);
    }

    public function testIsArray()
    {
        $configuration = ['configuration' => []];

        $lucy = new Lucy('configuration', $configuration);

        $enteredException = false;
        try {
            $lucy->isArray('configuration');
        } catch (ConfigurationException $e) {
            $enteredException = true;
        }

        static::assertFalse($enteredException);

        $configuration = [
            'configuration' => null
        ];

        $lucy = new Lucy('configuration', $configuration);

        $enteredException = false;
        try {
            $lucy->isArray('configuration');
        } catch (ConfigurationException $e) {
            $enteredException = true;
        }

        static::assertTrue($enteredException);
    }

    public function isArrayIfExists()
    {
        $configuration = ['configuration' => '7'];

        $lucy = new Lucy('configuration', $configuration);

        $enteredException = false;
        try {
            $lucy->isNumericIfExists('configuration');
        } catch (ConfigurationException $e) {
            $enteredException = true;
        }

        static::assertFalse($enteredException);

        $configuration = ['configuration' => []];

        $lucy = new Lucy('configuration', $configuration);

        $enteredException = false;
        try {
            $lucy->isArrayIfExists('configuration');
        } catch (ConfigurationException $e) {
            $enteredException = true;
        }

        static::assertFalse($enteredException);

        $configuration = ['configuration' => null];

        $lucy = new Lucy('configuration', $configuration);

        $enteredException = false;
        try {
            $lucy->isArrayIfExists('not_exists');
        } catch (ConfigurationException $e) {
            $enteredException = true;
        }

        static::assertFalse($enteredException);

        $configuration = [
            'configuration' => null
        ];

        $lucy = new Lucy('configuration', $configuration);

        $enteredException = false;
        try {
            $lucy->isArrayIfExists('configuration');
        } catch (ConfigurationException $e) {
            $enteredException = true;
        }

        static::assertTrue($enteredException);
    }

    public function testIsBoolean()
    {
        $configuration = ['configuration' => true];

        $lucy = new Lucy('configuration', $configuration);

        $enteredException = false;
        try {
            $lucy->isBoolean('configuration');
        } catch (ConfigurationException $e) {
            $enteredException = true;
        }

        static::assertFalse($enteredException);

        $configuration = [
            'configuration' => null
        ];

        $lucy = new Lucy('configuration', $configuration);

        $enteredException = false;
        try {
            $lucy->isBoolean('configuration');
        } catch (ConfigurationException $e) {
            $enteredException = true;
        }

        static::assertTrue($enteredException);
    }

    public function isBooleanIfExists()
    {
        $configuration = ['configuration' => true];

        $lucy = new Lucy('configuration', $configuration);

        $enteredException = false;
        try {
            $lucy->isBooleanIfExists('configuration');
        } catch (ConfigurationException $e) {
            $enteredException = true;
        }

        static::assertFalse($enteredException);

        $configuration = ['configuration' => null];

        $lucy = new Lucy('configuration', $configuration);

        $enteredException = false;
        try {
            $lucy->isBooleanIfExists('not_exists');
        } catch (ConfigurationException $e) {
            $enteredException = true;
        }

        static::assertFalse($enteredException);

        $configuration = [
            'configuration' => null
        ];

        $lucy = new Lucy('configuration', $configuration);

        $enteredException = false;
        try {
            $lucy->isBooleanIfExists('configuration');
        } catch (ConfigurationException $e) {
            $enteredException = true;
        }

        static::assertTrue($enteredException);
    }

    public function testIsAssociativeStringArray()
    {
        $configuration = ['configuration' => [
                'entry1' => 1,
                'entry2' => 2,
            ]
        ];

        $lucy = new Lucy('configuration', $configuration);

        $enteredException = false;
        try {
            $lucy->isAssociativeStringArray('configuration');
        } catch (ConfigurationException $e) {
            $enteredException = true;
        }

        static::assertFalse($enteredException);

        $configuration = [
            'configuration' => null
        ];

        $lucy = new Lucy('configuration', $configuration);

        $enteredException = false;
        try {
            $lucy->isAssociativeStringArray('configuration');
        } catch (ConfigurationException $e) {
            $enteredException = true;
        }

        static::assertTrue($enteredException);

        $configuration = ['configuration' => [
            'entry1' => 1,
            0 => 2,
        ]
        ];

        $lucy = new Lucy('configuration', $configuration);

        $enteredException = false;
        try {
            $lucy->isAssociativeStringArray('configuration');
        } catch (ConfigurationException $e) {
            $enteredException = true;
        }

        static::assertTrue($enteredException);
    }

    public function testIsEnum()
    {
        $configuration = [
            'configuration' => [
                'enum1',
                'enum2',
                'enum3',
            ]
        ];

        $lucy = new Lucy('configuration', $configuration);


        $enteredException = false;
        try {
            $lucy
                ->isEnum('configuration', [
                    'enum1',
                    'enum2',
                    'enum3',
                ]);
        } catch (ConfigurationException $e) {
            $enteredException = true;
        }

        static::assertFalse($enteredException);

        $enteredException = false;
        try {
            $lucy
                ->isEnum('configuration', [
                    'non_existent1',
                    'non_existent2',
                    'non_existent3',
                ]);
        } catch (ConfigurationException $e) {
            $enteredException = true;
        }

        static::assertTrue($enteredException);
    }

    public function testClosureValidator()
    {
        $lucy = new Lucy('configuration', $this->deepArray);

        $closureEntered = false;
        $that = $lucy;
        $lucy->applyClosure('configuration', function(string $name, Lucy $node) use(&$closureEntered, $that) {
            $closureEntered = true;
            static::assertEquals('configuration', $name);

            static::assertEquals($that, $node);

            static::assertTrue($that == $node);
        });

        static::assertTrue($closureEntered);
    }

    public function testApplyToSubElementsOf()
    {
        $configuration = [
            'configuration' => [
                'childNode1' => 1,
                'childNode2' => 2,
                'childNode3' => 3,
                'childNode4' => 4,
            ]
        ];

        $lucy = new Lucy('configuration', $configuration);

        $enteredCount = 0;
        $lucy
            ->stepInto('configuration')
            ->applyToSubElements([
                'childNode1',
                'childNode2',
                'childNode3',
                'childNode4',
        ], function(string $name, int $value) use (&$enteredCount) {
            $enteredCount++;

            static::assertEquals('childNode'.$enteredCount, $name);
            static::assertEquals($enteredCount, $value);
        });

        static::assertEquals(4, $enteredCount);
    }

    public function testApplyToSubElementsOfIfTheyExist()
    {
        $configuration = [
            'configuration' => [
                'not_exists1' => 1,
                'not_exists2' => 2,
                'childNode3' => 3,
                'childNode4' => 4,
            ]
        ];

        $lucy = new Lucy('configuration', $configuration);

        $enteredCount = 0;
        $lucy
            ->stepInto('configuration')
            ->applyToSubElementsIfTheyExist([
                'childNode1',
                'childNode2',
                'childNode3',
                'childNode4',
            ], function(string $name, int $value) use (&$enteredCount) {
                $enteredCount++;

                static::assertEquals('childNode'.$value, $name);
            });

        static::assertEquals(2, $enteredCount);
    }
}