<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Css;

use ArrayIterator;

/**
 * Pop CSS selector class
 *
 * @category   Pop
 * @package    Pop\Css
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    2.0.0
 */
class Selector implements \ArrayAccess, \Countable, \IteratorAggregate
{

    /**
     * Selector name
     * @var ?string
     */
    protected ?string $name = null;

    /**
     * Properties
     * @var array
     */
    protected array $properties = [];

    /**
     * Tab size
     * @var int
     */
    protected int $tabSize = 4;

    /**
     * Is ID selector flag
     * @var bool
     */
    protected bool $isId = false;

    /**
     * Is class selector flag
     * @var bool
     */
    protected bool $isClass = false;

    /**
     * Comments
     * @var array
     */
    protected array $comments = [];

    /**
     * Minify flag
     * @var bool
     */
    protected bool $minify = false;

    /**
     * Constructor
     *
     * Instantiate the CSS selector object
     *
     * @param ?string $name
     * @param int    $tabSize
     */
    public function __construct(?string $name = null, int $tabSize = 4)
    {
        if ($name !== null) {
            $this->setName($name);
        }
        $this->setTabSize($tabSize);
    }

    /**
     * Set name
     *
     * @param  string $name
     * @return Selector
     */
    public function setName(string $name): Selector
    {
        if (str_contains($name, '.')) {
            $this->isClass = true;
        }
        if (str_contains($name, '#')) {
            $this->isId = true;
        }
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string|null
     */
    public function getName(): string|null
    {
        return $this->name;
    }

    /**
     * Check if is element selector
     *
     * @return bool
     */
    public function isElementSelector(): bool
    {
        return (!($this->isId) && !($this->isClass));
    }

    /**
     * Check if is ID selector
     *
     * @return bool
     */
    public function isIdSelector(): bool
    {
        return $this->isId;
    }

    /**
     * Check if is class selector
     *
     * @return bool
     */
    public function isClassSelector(): bool
    {
        return $this->isClass;
    }

    /**
     * Check if is multiple selector
     *
     * @return bool
     */
    public function isMultipleSelector(): bool
    {
        return (str_contains($this->name, ','));
    }

    /**
     * Check if selector has a descendant
     *
     * @return bool
     */
    public function hasDescendant(): bool
    {
        return (str_contains($this->name, '>'));
    }

    /**
     * Set tab size
     *
     * @param  int $tabSize
     * @return Selector
     */
    public function setTabSize(int $tabSize): Selector
    {
        $this->tabSize = $tabSize;
        return $this;
    }

    /**
     * Get tab size
     *
     * @return int
     */
    public function getTabSize(): int
    {
        return $this->tabSize;
    }

    /**
     * Set property
     *
     * @param  string $property
     * @param  string $value
     * @return Selector
     */
    public function setProperty(string $property, string $value): Selector
    {
        $this->properties[$property] = $value;
        return $this;
    }

    /**
     * Set properties
     *
     * @param  array $properties
     * @return Selector
     */
    public function setProperties(array $properties): Selector
    {
        foreach ($properties as $property => $value) {
            $this->setProperty($property, $value);
        }
        return $this;
    }

    /**
     * Check if selector has property
     *
     * @param  string $property
     * @return bool
     */
    public function hasProperty(string $property): bool
    {
        return isset($this->properties[$property]);
    }

    /**
     * Get properties
     *
     * @return array
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * Get property
     *
     * @param  string $property
     * @return string|null
     */
    public function getProperty(string $property): string|null
    {
        return $this->properties[$property] ?? null;
    }

    /**
     * Remove property
     *
     * @param  string $property
     * @return Selector
     */
    public function removeProperty(string $property): Selector
    {
        if (isset($this->properties[$property])) {
            unset($this->properties[$property]);
        }
        return $this;
    }

    /**
     * Add CSS comment
     *
     * @param  Comment $comment
     * @return Selector
     */
    public function addComment(Comment $comment): Selector
    {
        $this->comments[] = $comment;
        return $this;
    }

    /**
     * Get CSS comments
     *
     * @return array
     */
    public function getComments(): array
    {
        return $this->comments;
    }

    /**
     * Set minify flag
     *
     * @param  bool $minify
     * @return Selector
     */
    public function minify(bool $minify = true): Selector
    {
        $this->minify = $minify;
        return $this;
    }

    /**
     * Check if minify flag is set
     *
     * @return bool
     */
    public function isMinified(): bool
    {
        return $this->minify;
    }

    /**
     * Method to iterate over the properties
     *
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->properties);
    }

    /**
     * Method to get the count of properties
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->properties);
    }

    /**
     * Method to render the selector CSS
     *
     * @return string
     */
    public function render(): string
    {
        $css = '';

        if (!$this->minify) {
            foreach ($this->comments as $comment) {
                $css .= (string)$comment . PHP_EOL;
            }
        }

        if (!$this->minify) {
            $css .= $this->name . ' {' . PHP_EOL;

            foreach ($this->properties as $property => $value) {
                $css .= str_repeat(' ', $this->tabSize) . $property . ': ' . $value . ';' . PHP_EOL;
            }

            $css .= '}' . PHP_EOL;
        } else {
            $css .= $this->name . '{';
            foreach ($this->properties as $property => $value) {
                $css .= $property . ':' . $value . ';';
            }

            $css .= '}';
        }

        return $css;
    }

    /**
     * To string method
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }

    /**
     * Magic method to set the property to the value of $this->properties[$name]
     *
     * @param  string $name
     * @param  mixed $value
     * @return void
     */
    public function __set(string $name, mixed $value): void
    {
        $this->properties[$name] = $value;
    }

    /**
     * Magic method to return the value of $this->properties[$name]
     *
     * @param  string $name
     * @return mixed
     */
    public function __get(string $name): mixed
    {
        if ((str_contains($name, 'margin-')) && !isset($this->properties[$name]) && isset($this->properties['margin'])) {
            $values         = explode(' ', $this->properties['margin']);
            $position       = substr($name, strpos($name, '-') + 1);
            $positionValues = ['top' => null, 'right' => null, 'bottom' => null, 'left' => null];

            switch (count($values)) {
                case 4:
                    $positionValues = ['top' => $values[0], 'right' => $values[1], 'bottom' => $values[2], 'left' => $values[3]];
                    break;
                case 3:
                    $positionValues = ['top' => $values[0], 'right' => $values[1], 'bottom' => $values[2], 'left' => $values[1]];
                    break;
                case 2:
                    $positionValues = ['top' => $values[0], 'right' => $values[1], 'bottom' => $values[0], 'left' => $values[1]];
                    break;
                case 1:
                    $positionValues = ['top' => $values[0], 'right' => $values[0], 'bottom' => $values[0], 'left' => $values[0]];
                    break;
            }

            return $positionValues[$position];
        } else if ((str_contains($name, 'padding-')) && !isset($this->properties[$name]) && isset($this->properties['padding'])) {
            $values         = explode(' ', $this->properties['padding']);
            $position       = substr($name, strpos($name, '-') + 1);
            $positionValues = ['top' => null, 'right' => null, 'bottom' => null, 'left' => null];

            switch (count($values)) {
                case 4:
                    $positionValues = ['top' => $values[0], 'right' => $values[1], 'bottom' => $values[2], 'left' => $values[3]];
                    break;
                case 3:
                    $positionValues = ['top' => $values[0], 'right' => $values[1], 'bottom' => $values[2], 'left' => $values[1]];
                    break;
                case 2:
                    $positionValues = ['top' => $values[0], 'right' => $values[1], 'bottom' => $values[0], 'left' => $values[1]];
                    break;
                case 1:
                    $positionValues = ['top' => $values[0], 'right' => $values[0], 'bottom' => $values[0], 'left' => $values[0]];
                    break;
            }

            return $positionValues[$position];
        } else {
            return (isset($this->properties[$name])) ? $this->properties[$name] : null;
        }
    }

    /**
     * Magic method to return the isset value of $this->properties[$name]
     *
     * @param  string $name
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return isset($this->properties[$name]);
    }

    /**
     * Magic method to unset $this->properties[$name]
     *
     * @param  string $name
     * @return void
     */
    public function __unset(string $name): void
    {
        if (isset($this->properties[$name])) {
            unset($this->properties[$name]);
        }
    }

    /**
     * ArrayAccess offsetExists
     *
     * @param  mixed $offset
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return $this->__isset($offset);
    }

    /**
     * ArrayAccess offsetGet
     *
     * @param  mixed $offset
     * @return mixed
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->__get($offset);
    }

    /**
     * ArrayAccess offsetSet
     *
     * @param  mixed $offset
     * @param  mixed $value
     * @return void
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->__set($offset, $value);
    }

    /**
     * ArrayAccess offsetUnset
     *
     * @param  mixed $offset
     * @return void
     */
    public function offsetUnset(mixed $offset): void
    {
        $this->__unset($offset);
    }

}