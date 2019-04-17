## Lucy
*Lightweight PHP config parser with zero dependencies*

*Lucy* requires PHP version `>=7.0 <=7.3`. Created on PHP `7.3.4`

## Content

1. Introduction
2. Installation
3. Usage
4. API reference
5. Setting up tests

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
        ],
        'external_service' => [
            'api_key' => 'key',
            'secret' => 'secret',
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
        ->stepOut()
        ->cannotBeEmpty('external_service')
        ->isArray('external_service')
        ->stepInto('external_service')
            ->isString('api_key')->cannotBeEmpty('api_key')
            ->isString('secret')->cannotBeEmpty('secret')

```

Here, we have a much better example. Deeply nested arrays are stepped into with
the `stepInto` method. After that, you can validate the values that are within that
array entry. We also introduced the `isString` and `cannotBeEmpty` methods. All of
the public methods that *Lucy* has are chainable and you can use them in any order you wish.

Other methods include `keyHasToExist`, `isNumeric`, `isBoolean`, `isEnum` and `isAssociativeStringArray`.

There is also a `stepOut` method. With this method, you are going up in the tree. In our
example about, previously we stepped into the `configuration` and `database` entry so we
can validate the entries within them. After we are done validating the `database` entry,
we `stepOut` of it, and validate the `external_service` by stepping into that entry.

Use `stepInto` and `stepOut` methods to traverse the tree as you wish. It is also important to
say that *Lucy* does not know that there is a next entry to step into. Under the hood, it creates
instances of itself that become the children of the *Lucy* object that created them. In our example,
*Lucy* object validating the `database` entry is a child of the *Lucy* object created for the
`configuration` entry.

*Lucy* also defines conditional methods. For example, `isNumeric` has a method `isNumericIfExists`.
This method does exactly what it says. Only if an entry exists, this method will try to validate it.

Other methods are

```
isBooleanIfExists
isStringIfExists
cannotBeEmptyIfExists
isArrayIfExists
```

#### Callable validation

If you would like a specific validation on a certain element, you can use a callable validators.
Those validators accept a closure (anonymous function) and pass the node name and the current *Lucy*
object.

```
$lucy->applyCallback('configuration, function($nodeName, Lucy $lucy) {
    // $nodeName in this example is 'configuration' and
    // $lucy object can validate 'configuration' entry
    
    $lucy->cannotBeEmpty('configuration')->isArray('configuration');
});
```

If you want to apply a callback to all the subelement of a given entry, you can to that
with `applyToSubElements`. 

```
$lucy
    ->stepInto('configuration')
    ->applyToSubElementsOf('configuration, function($nodeName, Lucy $lucy) {
        // this method will be called twice, once for once for 'database' entry
        // and once for 'external_service' entry. Every time it is called,
        // you will get a Lucy object for those entries, so you can to something
        // like this
        
        if ($nodeName === 'database') {
            // validate 'database' entry here
        } else if ($nodeName === 'external_service') {
            // validate 'external_entry' here
        }
    });
```

`applyToSubElementsOfIfTheyExists` is also available if you don't know if the element will
actually exist.


## 5. Setting up tests

There are no production dependencies but there are development dependencies. After you clone this repo with 
`git@github.com:MarioLegenda/lucy.git`, run `composer install`. The tests are in the `/tests` directory





