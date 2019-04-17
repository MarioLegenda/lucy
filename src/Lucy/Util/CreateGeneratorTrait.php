<?php


namespace Lucy\Util;


trait CreateGeneratorTrait
{
    /**
     * @param array $values
     * @return \Generator
     */
    public function createGenerator(array $values): \Generator
    {
        foreach ($values as $key => $value) {
            yield [
                'key' => $key,
                'value' => $value
            ];
        }
    }
}