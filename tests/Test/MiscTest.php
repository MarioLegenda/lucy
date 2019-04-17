<?php


namespace Test;


use Lucy\Lucy;
use PHPUnit\Framework\TestCase;

class MiscTest extends TestCase
{
    public function testMisc()
    {
        $configuration = ['configuration' => []];

        $lucy = new Lucy('configuration', $configuration);

        static::assertEquals('configuration', $lucy->getNodeName());
        static::assertEquals(1, count($lucy));
        static::assertFalse($lucy->isEmpty());
    }
}