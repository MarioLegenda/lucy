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
}