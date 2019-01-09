<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2019 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Css;

/**
 * Pop CSS selector class
 *
 * @category   Pop
 * @package    Pop\Css
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2019 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    1.0.7
 */
class Selector implements \ArrayAccess, \Countable, \IteratorAggregate
{

    /**
     * Selector name
     * @var string
     */
    protected $name = null;

    /**
     * Properties
     * @var array
     */
    protected $properties = [];

    /**
     * Tab size
     * @var int
     */
    protected $tabSize = 4;

    /**
     * Is ID selector flag
     * @var boolean
     */
    protected $isId = false;

    /**
     * Is class selector flag
     * @var boolean
     */
    protected $isClass = false;

    /**
     * Comments
     * @var array
     */
    protected $comments = [];

    /**
     * Minify flag
     * @var boolean
     */
    protected $minify = false;

    /**
     * Constructor
     *
     * Instantiate the CSS selector object
     *
     * @param string $name
     * @param int    $tabSize
     */
    public function __construct($name = null, $tabSize = 4)
    {
        if (null !== $name) {
            $this->setName($name);
        }
        $this->setTabSize($tabSize);
    }

    /**
     * Set name
     *
     * @param  string $name
     * @return self
     */
    public function setName($name)
    {
        if (strpos($name, '.') !== false) {
            $this->isClass = true;
        }
        if (strpos($name, '#') !== false) {
            $this->isId = true;
        }
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Check if is element selector
     *
     * @return boolean
     */
    public function isElementSelector()
    {
        return (!($this->isId) && !($this->isClass));
    }

    /**
     * Check if is ID selector
     *
     * @return boolean
     */
    public function isIdSelector()
    {
        return $this->isId;
    }

    /**
     * Check if is class selector
     *
     * @return boolean
     */
    public function isClassSelector()
    {
        return $this->isClass;
    }

    /**
     * Check if is multiple selector
     *
     * @return boolean
     */
    public function isMultipleSelector()
    {
        return (strpos($this->name, ',') !== false);
    }

    /**
     * Check if selector has a descendant
     *
     * @return boolean
     */
    public function hasDescendant()
    {
        return (strpos($this->name, '>') !== false);
    }

    /**
     * Set tab size
     *
     * @param  int $tabSize
     * @return self
     */
    public function setTabSize($tabSize)
    {
        $this->tabSize = (int)$tabSize;
        return $this;
    }

    /**
     * Get tab size
     *
     * @return int
     */
    public function getTabSize()
    {
        return $this->tabSize;
    }

    /**
     * Set property
     *
     * @param  string $property
     * @param  string $value
     * @return self
     */
    public function setProperty($property, $value)
    {
        $this->properties[$property] = $value;
        return $this;
    }

    /**
     * Set properties
     *
     * @param  array $properties
     * @return self
     */
    public function setProperties(array $properties)
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
     * @return boolean
     */
    public function hasProperty($property)
    {
        return isset($this->properties[$property]);
    }

    /**
     * Get properties
     *
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Get property
     *
     * @param  string $property
     * @return string
     */
    public function getProperty($property)
    {
        return (isset($this->properties[$property])) ? $this->properties[$property] : null;
    }

    /**
     * Remove property
     *
     * @param  string $property
     * @return self
     */
    public function removeProperty($property)
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
     * @return self
     */
    public function addComment(Comment $comment)
    {
        $this->comments[] = $comment;
        return $this;
    }

    /**
     * Get CSS comments
     *
     * @return array
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set minify flag
     *
     * @param  boolean $minify
     * @return self
     */
    public function minify($minify = true)
    {
        $this->minify = (bool)$minify;
        return $this;
    }

    /**
     * Check if minify flag is set
     *
     * @return boolean
     */
    public function isMinified()
    {
        return $this->minify;
    }

    /**
     * Method to iterate over the properties
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->properties);
    }

    /**
     * Method to get the count of properties
     *
     * @return int
     */
    public function count()
    {
        return count($this->properties);
    }

    /**
     * Method to render the selector CSS
     *
     * @return string
     */
    public function render()
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
    public function __toString()
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
    public function __set($name, $value)
    {
        $this->properties[$name] = $value;
    }

    /**
     * Magic method to return the value of $this->properties[$name]
     *
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        if ((strpos($name, 'margin-') !== false) && !isset($this->properties[$name]) && isset($this->properties['margin'])) {
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
        } else if ((strpos($name, 'padding-') !== false) && !isset($this->properties[$name]) && isset($this->properties['padding'])) {
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
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->properties[$name]);
    }

    /**
     * Magic method to unset $this->properties[$name]
     *
     * @param  string $name
     * @return void
     */
    public function __unset($name)
    {
        if (isset($this->properties[$name])) {
            unset($this->properties[$name]);
        }
    }

    /**
     * ArrayAccess offsetExists
     *
     * @param  mixed $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return $this->__isset($offset);
    }

    /**
     * ArrayAccess offsetGet
     *
     * @param  mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
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
    public function offsetSet($offset, $value)
    {
        $this->__set($offset, $value);
    }

    /**
     * ArrayAccess offsetUnset
     *
     * @param  mixed $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->__unset($offset);
    }

}