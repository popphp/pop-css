<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Css;

/**
 * Abstract CSS class
 *
 * @category   Pop
 * @package    Pop\Css
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    1.0.8
 */
abstract class AbstractCss implements \ArrayAccess, \Countable, \IteratorAggregate
{

    /**
     * Selectors
     * @var array
     */
    protected $selectors = [];

    /**
     * Elements
     * @var array
     */
    protected $elements = [];

    /**
     * IDs
     * @var array
     */
    protected $ids = [];

    /**
     * Classes
     * @var array
     */
    protected $classes = [];

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
     * Add CSS selector
     *
     * @param  Selector $selector
     * @return self
     */
    public function addSelector(Selector $selector)
    {
        $this->selectors[$selector->getName()] = $selector;

        if ($selector->isElementSelector()) {
            $this->elements[] = $selector->getName();
        } else if ($selector->isIdSelector()) {
            $this->ids[] = $selector->getName();
        } else if ($selector->isClassSelector()) {
            $this->classes[] = $selector->getName();
        }

        return $this;
    }

    /**
     * Add CSS selectors
     *
     * @param  array $selectors
     * @return self
     */
    public function addSelectors(array $selectors)
    {
        foreach ($selectors as $selector) {
            $this->addSelector($selector);
        }
        return $this;
    }

    /**
     * Check if the object has CSS selector
     *
     * @param  string $selector
     * @return boolean
     */
    public function hasSelector($selector)
    {
        return (isset($this->selectors[$selector]));
    }

    /**
     * Get CSS selector
     *
     * @param  string $selector
     * @return Selector
     */
    public function getSelector($selector)
    {
        return (isset($this->selectors[$selector])) ? $this->selectors[$selector] : null;
    }

    /**
     * Get CSS selector
     *
     * @param  string $selector
     * @return self
     */
    public function removeSelector($selector)
    {
        if (isset($this->selectors[$selector])) {
            unset($this->selectors[$selector]);
            if (in_array($selector, $this->elements)) {
                unset($this->elements[array_search($selector, $this->elements)]);
            } else if (in_array($selector, $this->ids)) {
                unset($this->ids[array_search($selector, $this->ids)]);
            } else if (in_array($selector, $this->classes)) {
                unset($this->classes[array_search($selector, $this->classes)]);
            }
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
        return new \ArrayIterator($this->selectors);
    }

    /**
     * Method to get the count of properties
     *
     * @return int
     */
    public function count()
    {
        return count($this->selectors);
    }

    /**
     * ArrayAccess offsetExists
     *
     * @param  mixed $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return $this->hasSelector($offset);
    }

    /**
     * ArrayAccess offsetGet
     *
     * @param  mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->getSelector($offset);
    }

    /**
     * ArrayAccess offsetSet
     *
     * @param  mixed $offset
     * @param  mixed $value
     * @throws Exception
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (!($value instanceof Selector)) {
            throw new Exception('Error: The value passed must be an instance of Pop\Css\Selector');
        }
        $value->setName($offset);
        $this->addSelector($value);
    }

    /**
     * ArrayAccess offsetUnset
     *
     * @param  mixed $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->removeSelector($offset);
    }

}