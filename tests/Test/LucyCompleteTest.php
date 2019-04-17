<?php

namespace Test;

use Lucy\Exception\ConfigurationException;
use Lucy\Lucy;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

class LucyCompleteTest extends TestCase
{
    public function testLucyComplete()
    {
        $file = __DIR__.'/config/config.yml';
        $config = Yaml::parseFile($file);

        $lucy = new Lucy('configuration', $config);

        $exceptionEntered = false;
        try {
            $lucy
                ->cannotBeEmpty('configuration')
                ->isArray('configuration')
                ->stepInto('configuration')
                ->isArrayIfExists('connection')
                ->stepIntoIfExists('connection')
                ->isString('host')
                ->isString('database_name')
                ->isString('user')
                ->isString('password')
                ->isBooleanIfExists('persistent')
                ->stepOut()
                ->isStringIfExists('sql_import')
                ->cannotBeEmptyIfExists('simple')
                ->stepIntoIfExists('simple')
                ->isArray('select')
                ->isArray('insert')
                ->isArray('update')
                ->isArray('delete')
                ->applyToSubElements(array('select', 'insert', 'update', 'delete'), function($nodeName, Lucy $lucy) {
                    foreach ($lucy as $nodeName => $nodeValue) {
                        $lucy->isAssociativeStringArray($nodeName);
                    }
                });
        } catch (ConfigurationException $e) {
            $exceptionEntered = true;
        }

        static::assertFalse($exceptionEntered);
    }
}