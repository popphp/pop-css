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
 * Abstract CSS class
 *
 * @category   Pop
 * @package    Pop\Css
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    2.0.0
 */
abstract class AbstractCss implements \ArrayAccess, \Countable, \IteratorAggregate
{

    /**
     * Selectors
     * @var array
     */
    protected array $selectors = [];

    /**
     * Elements
     * @var array
     */
    protected array $elements = [];

    /**
     * IDs
     * @var array
     */
    protected array $ids = [];

    /**
     * Classes
     * @var array
     */
    protected array $classes = [];

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
     * Add CSS selector
     *
     * @param  Selector $selector
     * @return AbstractCss
     */
    public function addSelector(Selector $selector): AbstractCss
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
     * @return AbstractCss
     */
    public function addSelectors(array $selectors): AbstractCss
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
     * @return bool
     */
    public function hasSelector(string $selector): bool
    {
        return (isset($this->selectors[$selector]));
    }

    /**
     * Get CSS selector
     *
     * @param  string $selector
     * @return Selector|null
     */
    public function getSelector(string $selector): Selector|null
    {
        return $this->selectors[$selector] ?? null;
    }

    /**
     * Get CSS selector
     *
     * @param  string $selector
     * @return AbstractCss
     */
    public function removeSelector(string $selector): AbstractCss
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
     * @return AbstractCss
     */
    public function addComment(Comment $comment): AbstractCss
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
     * @return AbstractCss
     */
    public function minify(bool $minify = true): AbstractCss
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
        return new ArrayIterator($this->selectors);
    }

    /**
     * Method to get the count of properties
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->selectors);
    }

    /**
     * ArrayAccess offsetExists
     *
     * @param  mixed $offset
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return $this->hasSelector($offset);
    }

    /**
     * ArrayAccess offsetGet
     *
     * @param  mixed $offset
     * @return mixed
     */
    public function offsetGet(mixed $offset): mixed
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
    public function offsetSet(mixed $offset, mixed $value): void
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
    public function offsetUnset(mixed $offset): void
    {
        $this->removeSelector($offset);
    }

}