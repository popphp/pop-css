<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2017 NOLA Interactive, LLC. (http://www.nolainteractive.com)
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
 * @copyright  Copyright (c) 2009-2017 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.0.0
 */
abstract class AbstractCss
{

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
        if ($selector->isElementSelector()) {
            $this->elements[$selector->getName()] = $selector;
        } else if ($selector->isIdSelector()) {
            $this->ids[$selector->getName()] = $selector;
        } else if ($selector->isClassSelector()) {
            $this->classes[$selector->getName()] = $selector;
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
        return (isset($this->elements[$selector]) || isset($this->ids[$selector]) || isset($this->classes[$selector]));
    }

    /**
     * Get CSS selector
     *
     * @param  string $selector
     * @return Selector
     */
    public function getSelector($selector)
    {
        $result = null;

        if (isset($this->elements[$selector])) {
            $result = $this->elements[$selector];
        } else if (isset($this->ids[$selector])) {
            $result = $this->ids[$selector];
        } else if (isset($this->classes[$selector])) {
            $result = $this->classes[$selector];
        }

        return $result;
    }

    /**
     * Get CSS selector
     *
     * @param  string $selector
     * @return self
     */
    public function removeSelector($selector)
    {
        if (isset($this->elements[$selector])) {
            unset($this->elements[$selector]);
        } else if (isset($this->ids[$selector])) {
            unset($this->ids[$selector]);
        } else if (isset($this->classes[$selector])) {
            unset($this->classes[$selector]);
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

}