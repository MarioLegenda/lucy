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
example above, previously we stepped into the `configuration` and `database` entry so we
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

If you would like a specific custom validation on a certain element, you can use a callable validators.
Those validators accept a closure (anonymous function) and pass the node name and the current *Lucy*
object.

```
$lucy->applyCallback('configuration', function($nodeName, Lucy $lucy) {
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
    ->applyToSubElementsOf(['database', 'external_service'], function($nodeName, Lucy $lucy) {
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

`applyToSubElementsOfIfTheyExists` is also available if you don't know if the elements will
actually exist. In that case, this method will call the callback only on the elements that do
exist and skip the rest.

#### 4. API reference

#### `Lucy::__construct(string $nodeName, array $node, Lucy $parentNode = null)`

Create a new instance of *Lucy*. `$parentNode` is meant for internal *Lucy* purposes and
is not meant to be used in client code. 

If the `$nodeName` does not exist in `$node`, throws a `Lucy\Exception\ConfigurationException`.

#### `Lucy::getNodeName(): string`

Returns a current node name.

#### `Lucy::stepInto(string $nodeName, string $errorMessage)`

Steps into `$nodeName` and creates a new *Lucy* object to be validated on. 

Accepts an optional `$errorMessage` that is replaced with the original error message for easier debugging.

If the `$nodeName` does not exist, throws a `Lucy\Exception\ConfigurationException`.

#### `Lucy::stepIntoIfExists(string $nodeName)`

Does the same thing as `stepInto` but does not throw exception if the node does not exist.

#### `Lucy::stepOut()`

Steps out of the current *Lucy* object and returns to the parent.

#### `Lucy::applyCallback(string $nodeName, Closure $closure)`

Applies a user defined callback to `$nodeName`. The callback will receive a string `$nodeName` and
a current *Lucy* object if the object is an array, otherwise, it will receive the value that is the index
of `$nodeName`

If the `$nodeName` does not exist, throws a `Lucy\Exception\ConfigurationException`.

#### `Lucy::applyToSubElement(array $childNodes, Closure $closure)`

Applies a user defined callback to all `$childNodes`. It receives the `$nodeName` of the
currently traversed node and a *Lucy* object if the entry under it is an array, any other value
otherwise. 

If any of the child nodes does not exist, throws a `Lucy\Configuration\ConfigurationException`.

#### `Lucy::applyToSubElementIfTheyExist(array $childNodes, Closure $closure)`

Does the same thing as `applyToSubElements` but does not throw exception if one of the
child nodes does not exist. If a child node doesn't exist, it skips it.

#### `Lucy::keyHasToExist(string $nodeName, string $errorMessage = null)`

Validates that the key `$nodeName` exists. 

Accepts an optional `$errorMessage` that is replaced with the original error message for easier debugging.

If the `$nodeName` does not exist, throws a `Lucy\Exception\ConfigurationException`.

#### `Lucy::cannotBeEmpty(string $nodeName, string $errorMessage = null)`

Validates that a entry under `$nodeName` cannot be empty. It uses a simple `empty()` PHP
function. 

If the `$nodeName` does not exist, throws a `Lucy\Exception\ConfigurationException`.

Accepts an optional `$errorMessage` that is replaced with the original error message for easier debugging.

#### `Lucy::cannotBeEmptyIfExists(string $nodeName, string $errorMessage = null)`

Does the same thing as `cannotBeEmpty` but does not throw an exception if the `$nodeName` does
not exist.

#### `Lucy::isString(string $nodeName, string $errorMessage = null)`

Validates that a entry under `$nodeName` is a string. A string is considered a string
even if it is empty ''.

If the `$nodeName` does not exist, throws a `Lucy\Exception\ConfigurationException`.

Accepts an optional `$errorMessage` that is replaced with the original error message for easier debugging.

#### `Lucy::isStringIfExists(string $nodeName, string $errorMessage = null)`

Does the same thing as `isString` but it does not throw an exception if `$nodeName` does
not exist.

#### `Lucy::isNumeric(string $nodeName, string $errorMessage = null)`

Validates that a entry under `$nodeName` a numeric expression. Both '7' and 7 are considered
numeric. Under the hood, it uses the `is_numeric` PHP function.

If the `$nodeName` does not exist, throws a `Lucy\Exception\ConfigurationException`.

Accepts an optional `$errorMessage` that is replaced with the original error message for easier debugging.

#### `Lucy::isNumericIfExists(string $nodeName, string $errorMessage = null)`

Does the same thing as `isNumeric` but it does not throw an exception if `$nodeName` 
does not exist.

#### `Lucy::isArray(string $nodeName, string $errorMessage = null)`

Validates that an entry under `$nodeName` is an array.

If the `$nodeName` does not exist, throws a `Lucy\Exception\ConfigurationException`.

Accepts an optional `$errorMessage` that is replaced with the original error message for easier debugging.

#### `Lucy::isArrayIfExists(string $nodeName, string $errorMessage = null)`

Does the sam thing as `isArray` but does not throw an exception.

#### `Lucy::isBoolean(string $nodeName, string $errorMessage = null)`

Validates that the value under `$nodeName` is a boolean. Only `true` or `false` values
are considered as be a valid boolean.

If the `$nodeName` does not exist, throws a `Lucy\Exception\ConfigurationException`.

Accepts an optional `$errorMessage` that is replaced with the original error message for easier debugging.

#### `Lucy::isBooleanIfExists(string $nodeName, string $errorMessage = null)`

Does the same thing as `isBoolean` but does not throw an exception if `$nodeName` does
not exist.

#### `Lucy::isAssociativeStringArray(string $nodeName, string $errorMessage = null)`

Validates that all keys in a hashmap are strings. If any of the keys are not strings, it throws
a `Lucy\Exception\ConfigurationException`.

If the `$nodeName` does not exist, throws a `Lucy\Exception\ConfigurationException`.

Accepts an optional `$errorMessage` that is replaced with the original error message for easier debugging.

#### `Lucy::isEnum(string $nodeName, array $values, string $errorMessage = null)`

Validates that values in the configuration contains at least one value from `$values`.

For example...

```
$configuration = [
    'configuration' => [
        'one' => 1,
        'two' => 2,
        'three' => 3
    ]
]

$lucy = new Lucy('configuration', $configuration);

$lucy->isEnum('configuration', ['one']
```

The above code check that the array under `configuration` contains at leas one value in the `$values`
array. 

If the `$nodeName` does not exist, throws a `Lucy\Exception\ConfigurationException`.

Accepts an optional `$errorMessage` that is replaced with the original error message for easier debugging.

## 5. Setting up tests

There are no production dependencies but there are development dependencies. After you clone this repo with 
`git@github.com:MarioLegenda/lucy.git`, run `composer install`. The tests are in the `/tests` directory





