## Lucy
*Lightweight PHP config parser with zero dependencies*

*Lucy* requires PHP version `>=7.0 <=7.3`. Created on PHP `7.3.4`

## Content

1. Introduction
2. Installation
3. Usage
4. Setting up tests

## 1. Introduction

*Lucy* is a config parser similar to Symfony config component but much more lightweight (no dependencies).
I created it because I haven't seen a competition tool for it so I created it. I don't think that Lucy is better
than Symfony config component. I just like to have more than one solution to a single problem.

*Lucy* is basically a self creating linked list. When you create the entire config tree, it creates an instances of 
itself for every node in the array (tree) and validates that part of the tree. It does that on demand so the next node 
is created only when it needs to validate the next value in the tree. I haven't done any benchmarks to compare symfony config
component and *Lucy*, but they are on their way. I hope you like it.

## 2. Installation

`composer require mario-legenda/lucy`

## 3. Usage

We will start with the most simple configuration array possible.

```
use Lucy;

$configuration = [
    'configuration' => []
];

$lucy = new Lucy('configuration', $configuration);

$lucy->isArray('configuration');

```

This simple code will just validate that the `configuration` entry is an array.
If the entry is not an array, a `Lucy\Exception\ConfigurationException` will be thrown
so if you expect an invalid value in your configuration, wrap it in a `try/catch` block.

So, lets expand on our example.

```

use Lucy;

$configuration = [
    'configuration' => [
        'database' => [
            'host' => 'localhost',
            'database_name' => 'db_name',
            'password' => 'password',
            'user' => 'user'
        ]
    ]
]

$lucy = new Lucy('configuration', $configuration);

$lucy
    ->isArray('configuration')
    ->cannotBeEmpty('configuration')
    ->stepInto('configuration')
        ->isArray('database')
        ->cannotBeEmpty('database')
        ->stepInto('database')
            ->isString('host')->cannotBeEmpty('host')
            ->isString('database_name')->cannotBeEmpty('host')
            ->isString('password')->cannotBeEmpty('password')
            ->isString('user')->cannotBeEmpty('user')

```

Here, we have a much better example. Deeply nested arrays are stepped into with
the `stepInto` method. After that, you can validate the values that are within that
array entry. We also introduced the `isString` and `cannotBeEmpty` methods. All of
the public methods that *Lucy* has are chainable and you can use them in any order you wish.



## 4. Setting up tests

There are no production dependencies but there are development dependencies. After you clone this repo with 
`git@github.com:MarioLegenda/lucy.git`, run `composer install`. The tests are in the `/tests` directory





