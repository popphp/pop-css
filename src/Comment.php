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
 * Pop CSS comment class
 *
 * @category   Pop
 * @package    Pop\Css
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2019 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    1.0.7
 */
class Comment
{

    /**
     * Comment string
     * @var string
     */
    protected $comment = null;

    /**
     * Constructor
     *
     * Instantiate the CSS comment object
     *
     * @param string $comment
     */
    public function __construct($comment)
    {
        $this->setComment($comment);
    }

    /**
     * Set comment
     *
     * @param  string $comment
     * @return self
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Method to render the selector CSS
     *
     * @return string
     */
    public function render()
    {
        $comment = '/**' . PHP_EOL;

        if (strpos($this->comment, PHP_EOL) !== false) {
            $commentAry = explode(PHP_EOL, $this->comment);
        } else {
            $commentAry = (strlen($this->comment) > 80) ?
                explode(PHP_EOL, wordwrap($this->comment, 80, PHP_EOL)) : [$this->comment];
        }

        foreach ($commentAry as $line) {
            $comment .= ' * ' . $line . PHP_EOL;
        }

        $comment .= ' */' . PHP_EOL;

        return $comment;
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

}