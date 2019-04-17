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
    }
}