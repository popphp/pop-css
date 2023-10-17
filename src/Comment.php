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

/**
 * Pop CSS comment class
 *
 * @category   Pop
 * @package    Pop\Css
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    2.0.0
 */
class Comment
{

    /**
     * Comment string
     * @var ?string
     */
    protected ?string $comment = null;

    /**
     * Constructor
     *
     * Instantiate the CSS comment object
     *
     * @param string $comment
     */
    public function __construct(string $comment)
    {
        $this->setComment($comment);
    }

    /**
     * Set comment
     *
     * @param  string $comment
     * @return Comment
     */
    public function setComment(string $comment): Comment
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * Get comment
     *
     * @return string|null
     */
    public function getComment(): string|null
    {
        return $this->comment;
    }

    /**
     * Method to render the selector CSS
     *
     * @return string
     */
    public function render(): string
    {
        $comment = '/**' . PHP_EOL;

        if (str_contains($this->comment, PHP_EOL)) {
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
    public function __toString(): string
    {
        return $this->render();
    }

}