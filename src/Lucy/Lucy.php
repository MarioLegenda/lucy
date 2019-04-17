<?php

namespace Lucy;

use Lucy\Exception\ConfigurationException;
use Lucy\Validator\Validator;

class Lucy implements \IteratorAggregate, \Countable
{
    /**
     * @var string $nodeName
     */
    private $nodeName;
    /**
     * @var bool $conditionalIgnore
     */
    private $conditionalIgnore = false;
    /**
     * @var array $nodeName
     */
    private $parentNode;
    /**
     * @var array $workingNode
     */
    private $workingNode;
    /**
     * @var Validator $validator
     */
    private $validator;
    /**
     * ArrayNode constructor.
     * @param string $rootNode
     * @param array $workingNode
     * @param Lucy|null $parentNode
     * @throws ConfigurationException
     *
     * Create a new node with the provided node name. Working node is an array that is
     * an element of $rootNode name. For example, given an array
     *
     * [
     *     'configuration' => [
     *          'elem1' => 'str1',
     *          'elem2' => 'str2',
     *     ]
     * ]
     *
     * the node name would be 'configuration' and the working node would be the entire
     * provided array, exactly like in the above example.
     *
     * If the $parentNode is provided, it is set as the parent node and can be entered in
     * the future.
     */
    public function __construct(string $rootNode, array $workingNode, Lucy $parentNode = null)
    {
        $this->nodeName = $rootNode;

        if (empty($workingNode)) {
            throw new ConfigurationException('Node has to be a non empty array for parent \''.$rootNode.'\'');
        }

        // this is what distinguishes the root node from the children
        // not parent and no parent as a parameter
        if (is_null($this->parentNode) and is_null($parentNode)) {
            $this->internalKeyExists($rootNode, $workingNode);
        }

        if ($this->parentNode instanceof Lucy) {
            $parent = $this->getParent();

            $this->internalKeyExists($rootNode, $parent);
        }

        $this->workingNode = $workingNode;
        $this->validator = Validator::create();

        if ($parentNode instanceof Lucy) {
            $this->parentNode = $parentNode;
        }
    }
    /**
     * @return string|string
     *
     * Returns the currently working node name
     */
    public function getNodeName() : string
    {
        return $this->nodeName;
    }
    /**
     * @param string $nodeName
     * @param string|null $errorMessage
     * @return $this|Lucy
     * @throws ConfigurationException
     *
     * Creates a new Lucy object from the provided node name. For example, given an array
     *
     * [
     *     'configuration' => [
     *         'inner_node' => []
     *     ]
     * ]
     *
     * a new Lucy object with be created with the value of 'inner_node' as the working node.
     *
     * Returns a new Lucy object.
     *
     * If the $nodeName does not exist, throws a ConfigurationException
     */
    public function stepInto(string $nodeName, string $errorMessage = null) : Lucy
    {
        if (!empty($this->workingNode)) {
            if (!array_key_exists($nodeName, $this->workingNode)) {
                if ($errorMessage) {
                    throw new ConfigurationException($errorMessage);
                }

                throw new ConfigurationException("Cannot step into '$nodeName'. '$nodeName' does not exist");
            }

            return new Lucy(
                $nodeName,
                $this->workingNode[$nodeName],
                new Lucy($nodeName, $this->workingNode)
            );
        }

        throw new ConfigurationException("Cannot step into '$nodeName'. '$nodeName' does not exist");
    }
    /**
     * @param string $nodeName
     * @return $this|Lucy
     * @throws ConfigurationException
     *
     * Does the same thing as Lucy::stepInto() but it does not throw an exception. If the node
     * does not exist, it returns the currently working node.
     *
     * This method also sets a 'conditional ignore' mechanism that makes the methods that usually throw
     * an exception, dont throw it.
     */
    public function stepIntoIfExists(string $nodeName)
    {
        if (!array_key_exists($nodeName, $this->workingNode)) {
            $this->conditionalIgnore = true;

            return $this;
        }

        if (array_key_exists($nodeName, $this->workingNode)) {
            return new Lucy(
                $nodeName,
                $this->workingNode[$nodeName],
                new Lucy($nodeName, $this->workingNode)
            );
        }

        return $this;
    }

    /**
     * @return $this|array|Lucy
     * @throws ConfigurationException
     *
     * Sets the parent as the currently working node. If the parent does not exist,
     * a ConfigurationException is thrown
     */
    public function stepOut()
    {
        if ($this->conditionalIgnore === true) {
            $this->conditionalIgnore = false;

            return $this;
        }

        if ($this->conditionalIgnore === false) {
            if (!$this->parentNode instanceof Lucy) {
                $message = sprintf(
                    'Cannot step out of \'%s\' because this node does not have a parent',
                    $this->getNodeName()
                );

                throw new ConfigurationException($message);
            }

            $parent = $this->getParent();

            if (!$parent instanceof Lucy) {
                $message = sprintf(
                    'Cannot step out of \'%s\' because this node does not have a parent',
                    $this->getNodeName()
                );

                throw new ConfigurationException($message);            }

            return $parent;
        }

        return $this;
    }
    /**
     * @param string $nodeName
     * @param \Closure $closure
     * @return $this
     *
     * Invokes a custom anonymous function on the given node. It only works on the
     * currently working node
     */
    public function closureValidator(string $nodeName, \Closure $closure) : Lucy
    {
        if ($this->conditionalIgnore === false) {
            $closure->__invoke($nodeName, $this);
        }

        return $this;
    }
    /**
     * @param array $childNodes
     * @param \Closure $closure
     * @return $this
     * @throws ConfigurationException
     *
     * Applies a callback to all child nodes
     */
    public function applyToSubElementsOf(array $childNodes, \Closure $closure) : Lucy
    {
        if ($this->conditionalIgnore === false) {
            foreach ($childNodes as $childNode) {
                if ($this->internalKeyExists($childNode, $this->workingNode)) {
                    $closure->__invoke($childNode, new Lucy($childNode, $this->workingNode[$childNode]));
                }
            }
        }

        return $this;
    }
    /**
     * @param array $childNodes
     * @param \Closure $closure
     * @return $this
     * @throws ConfigurationException
     */
    public function applyToSubElementsIfTheyExist(array $childNodes, \Closure $closure) : Lucy
    {
        if ($this->conditionalIgnore === false) {
            foreach ($childNodes as $childNode) {
                if (!array_key_exists($childNode, $this->workingNode)) {
                    continue;
                }

                $closure->__invoke($childNode, new Lucy($childNode, $this->workingNode[$childNode]));
            }
        }

        return $this;
    }
    /**
     * @param string $nodeName
     * @param string|null $errorMessage
     * @return Lucy
     * @throws ConfigurationException
     *
     * Checks if the key exists on the supplied $node parameter. If the parameter is empty,
     * it check the currently working node.
     *
     * If this method is called after Lucy::stepIntoIfExists() and that method returned a new Lucy
     * object (it found a node to traverse), this method will return the current Lucy object and NOT
     * throw an exception.
     *
     * If the conditional ignore is set to true, this method throw an exception if the $nodeName does
     * not exist
     */
    public function keyHasToExist(string $nodeName, string $errorMessage = null) : Lucy
    {
        if ($this->conditionalIgnore === false) {
            $this->validator->keyHasToExist()->validate(
                $nodeName,
                $this->workingNode,
                $this->parentNode,
                $errorMessage
            );
        }

        return $this;
    }
    /**
     * @param string $nodeName
     * @param string|null $errorMessage
     * @return $this
     * @throws ConfigurationException
     *
     * Given a $nodeName, check if the element under the $nodeName exists and is not empty.
     * A false value is empty by the empty() function, so additional check are added to check if
     * the value is a boolean.
     *
     * If the $nodeName value is empty, a ConfigurationException is thrown
     *
     * If a call to Lucy::stepIntoIfExists() and there was a node to be stepped into, then this
     * node will not throw an exception. Otherwise, it throws an exception
     */
    public function cannotBeEmpty(string $nodeName, string $errorMessage = null) : Lucy
    {
        if ($this->conditionalIgnore === false) {
            $this->validator->cannotBeEmpty()->validate(
                $nodeName,
                $this->workingNode,
                $this->parentNode,
                $errorMessage
            );
        }

        return $this;
    }
    /**
     * @param string $nodeName
     * @param string|null $errorMessage
     * @return $this
     * @throws ConfigurationException
     *
     * Check if a $nodeName is empty. Throws a ConfigurationException if the $nodeName is empty
     */
    public function cannotBeEmptyIfExists(string $nodeName, string $errorMessage = null) : Lucy
    {
        $this->validator->cannotBeEmptyIfExists()->validate(
            $nodeName,
            $this->workingNode,
            $this->parentNode,
            $errorMessage
        );

        return $this;
    }
    /**
     * @param string $nodeName
     * @param string|null $errorMessage
     * @return $this
     * @throws ConfigurationException
     *
     * Checks if the $nodeName is a string.
     *
     * Throws an exception if Lucy is in conditional ignore mode.
     *
     * Throws an exception if $nodeName does not exist
     */
    public function isString(string $nodeName, string $errorMessage = null) : Lucy
    {
        if ($this->conditionalIgnore === false) {
            $this->validator->isString()->validate(
                $nodeName,
                $this->workingNode,
                $this->parentNode,
                $errorMessage
            );
        }

        return $this;
    }
    /**
     * @param string $nodeName
     * @param string|null $errorMessage
     * @return $this
     * @throws ConfigurationException
     *
     * Check if a $nodeName is a string. Throws a ConfigurationException only if $nodeName
     * exists and is not a string. Silently ignores if the $nodeName does not exist.
     */
    public function isStringIfExists(string $nodeName, string $errorMessage = null) : Lucy
    {
        $this->validator->isStringIfExists()->validate(
            $nodeName,
            $this->workingNode,
            $this->parentNode,
            $errorMessage
        );

        return $this;
    }
    /**
     * @param string $nodeName
     * @param string|null $errorMessage
     * @return Lucy
     * @throws ConfigurationException
     *
     * Does the same thing as Lucy::isString() but for numbers. The check is done with is_numeric
     * function so a string '2.3' passes as a number.
     */
    public function isNumeric(string $nodeName, string $errorMessage = null): Lucy
    {
        if ($this->conditionalIgnore === false) {
            $this->validator->isNumeric()->validate(
                $nodeName,
                $this->workingNode,
                $this->parentNode,
                $errorMessage
            );
        }

        return $this;
    }
    /**
     * @param string $nodeName
     * @param string|null $errorMessage
     * @return Lucy
     * @throws ConfigurationException
     *
     * Does the same thing as Lucy::isNumeric() but for numbers and only if $nodeName exists.
     * If $nodeName does not exists, it does not throw an exception.
     *
     * The check is done with is_numeric function so a string '2.3' passes as a number.
     */
    public function isNumericIfExists(string $nodeName, string $errorMessage = null): Lucy
    {
        $this->validator->isNumericIfExists()->validate(
            $nodeName,
            $this->workingNode,
            $this->parentNode,
            $errorMessage
        );

        return $this;
    }
    /**
     * @param string $nodeName
     * @param string|null $errorMessage
     * @return $this
     * @throws ConfigurationException
     *
     * Does the same thing as Lucy::isString() but for arrays
     */
    public function isArray(string $nodeName, string $errorMessage = null) : Lucy
    {
        if ($this->conditionalIgnore === false) {
            $this->validator->isArray()->validate(
                $nodeName,
                $this->workingNode,
                $this->parentNode,
                $errorMessage
            );
        }

        return $this;
    }
    /**
     * @param string $nodeName
     * @param string|null $errorMessage
     * @return $this
     * @throws ConfigurationException
     *
     * Does the same thing as Lucy::isStringIfExists() but only for arrays
     */
    public function isArrayIfExists(string $nodeName, string $errorMessage = null) : Lucy
    {
        if ($this->conditionalIgnore === false) {
            if (array_key_exists($nodeName, $this->workingNode)) {
                if (!is_array($this->workingNode[$nodeName])) {
                    if ($errorMessage) throw new ConfigurationException($errorMessage);

                    $message = sprintf(
                        'If exists, \'%s\' has to be an array for parent \'%s\'',
                        $nodeName,
                        $this->getNodeName()
                    );

                    throw new ConfigurationException($message);
                }
            }
        }

        return $this;
    }
    /**
     * @param string $nodeName
     * @param string|null $errorMessage
     * @return $this
     * @throws ConfigurationException
     *
     * Does the same thing as Lucy::isString() but only for boolean values
     */
    public function isBoolean(string $nodeName, string $errorMessage = null) : Lucy
    {
        if ($this->conditionalIgnore === false) {
            $this->internalKeyExists($nodeName, $this->workingNode);

            if (!is_bool($this->workingNode[$nodeName])) {
                if ($errorMessage) throw new ConfigurationException($errorMessage);

                $message = sprintf(
                    '\'%s\' has to be a boolean',
                    $nodeName
                );

                throw new ConfigurationException($message);
            }
        }

        return $this;
    }
    /**
     * @param string $nodeName
     * @param string|null $errorMessage
     * @return $this
     * @throws ConfigurationException
     *
     * Does the same thing as Lucy::isStringIfExists() but only for booleans
     */
    public function isBooleanIfExists(string $nodeName, string $errorMessage = null) : Lucy
    {
        if ($this->conditionalIgnore === false) {
            if (array_key_exists($nodeName, $this->workingNode)) {
                if (!is_bool($this->workingNode[$nodeName])) {
                    if ($errorMessage) throw new ConfigurationException($errorMessage);

                    $message = sprintf(
                        'If exists, \'%s\' has to be a boolean for parent \'%s\'',
                        $nodeName,
                        $errorMessage
                    );

                    throw new ConfigurationException($message);
                }
            }
        }

        return $this;
    }
    /**
     * @param string $nodeName
     * @param string|null $errorMessage
     * @return $this
     * @throws ConfigurationException
     *
     * Check if an array is an associative string key array e.i. if all of its keys are strings.
     *
     * Throws a ConfigurationException if the $nodeName value is not an array
     *
     * Throws an exception if all of the keys are not strings
     */
    public function isAssociativeStringArray(string $nodeName, string $errorMessage = null) : Lucy
    {
        if ($this->conditionalIgnore === false) {
            if (!is_array($this->workingNode[$nodeName])) {
                $message = sprintf(
                    '\'%s\' has to be a array with string keys',
                    $nodeName
                );

                throw new ConfigurationException($message);
            }

            $keys = array_keys($this->workingNode[$nodeName]);

            foreach ($keys as $key) {
                if (!is_string($key)) {
                    if ($errorMessage) throw new ConfigurationException($errorMessage);

                    $message = sprintf(
                        '\'%s\' has to be a associative array with string keys. Key \'%s\' is not a string',
                        $nodeName,
                        $key
                    );

                    throw new ConfigurationException($message);
                }
            }
        }

        return $this;
    }
    /**
     * @param $nodeName
     * @param array $values
     * @param string|null $errorMessage
     * @return Lucy
     * @throws ConfigurationException
     */
    public function hasToBeOneOf($nodeName, array $values, string $errorMessage = null) : Lucy
    {
        if ($this->conditionalIgnore === false) {
            $this->internalKeyExists($nodeName, $this->workingNode);

            if (in_array($this->workingNode[$nodeName], $values) === false) {
                if ($errorMessage) throw new ConfigurationException($errorMessage);

                $message = sprintf(
                    'One of values \'%s\' in node \'%s\' has to be present',
                    implode(', ', $values),
                    $nodeName
                );

                throw new ConfigurationException($message);
            }
        }

        return $this;
    }
    /**
     * @return bool
     */
    public function isEmpty() : bool
    {
        return empty($this->workingNode);
    }
    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->workingNode);
    }
    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->workingNode);
    }
    /**
     * @param string $nodeName
     * @param array $node
     * @param string|null $errorMessage
     * @return $this
     * @throws ConfigurationException
     */
    private function internalKeyExists(string $nodeName, array $node, string $errorMessage = null)
    {
        if (!array_key_exists($nodeName, $node)) {
            if ($errorMessage) throw new ConfigurationException($errorMessage);

            throw new ConfigurationException('Invalid configuration. \''.$nodeName.'\' does not exist for parent node \''.$this->getNodeName().'\'');
        }

        return $this;
    }
    /**
     * @return array|Lucy|null
     */
    private function getParent()
    {
        return $this->parentNode;
    }
}