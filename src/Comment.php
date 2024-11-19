<?php
/**
 * Pop PHP Framework (https://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
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
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
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
     * Comment wrap
     * @var int
     */
    protected int $wrap = 80;

    /**
     * Trailing new line
     * @var bool
     */
    protected bool $trailingNewLine = true;

    /**
     * Constructor
     *
     * Instantiate the CSS comment object
     *
     * @param string $comment
     * @param int    $wrap
     * @param bool   $trailingNewLine
     */
    public function __construct(string $comment, int $wrap = 80, bool $trailingNewLine = false)
    {
        $this->setComment($comment);
        $this->setWrap($wrap);
        $this->setTrailingNewLine($trailingNewLine);
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
     * Set wrap
     *
     * @param  int $wrap
     * @return Comment
     */
    public function setWrap(int $wrap): Comment
    {
        $this->wrap = $wrap;
        return $this;
    }

    /**
     * Get wrap
     *
     * @return int
     */
    public function getWrap(): int
    {
        return $this->wrap;
    }

    /**
     * Set trailing new line
     *
     * @param  bool $trailingNewLine
     * @return Comment
     */
    public function setTrailingNewLine(bool $trailingNewLine): Comment
    {
        $this->trailingNewLine = $trailingNewLine;
        return $this;
    }

    /**
     * Has trailing new line
     *
     * @return bool
     */
    public function hasTrailingNewLine(): bool
    {
        return $this->trailingNewLine;
    }

    /**
     * Method to render the selector CSS
     *
     * @return string
     */
    public function render(): string
    {
        $comment = '/*';

        if ($this->wrap == 0) {
            $comment .= ' ' . $this->comment;
        } else {
            $comment .= PHP_EOL;
            if (str_contains($this->comment, PHP_EOL)) {
                $commentAry = explode(PHP_EOL, $this->comment);
            } else {
                $commentAry = (strlen($this->comment) > $this->wrap) ?
                    explode(PHP_EOL, wordwrap($this->comment, $this->wrap, PHP_EOL)) : [$this->comment];
            }

            foreach ($commentAry as $line) {
                $comment .= ' * ' . $line . PHP_EOL;
            }
        }

        $comment .= ' */';

        if ($this->trailingNewLine) {
            $comment .= PHP_EOL;
        }

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
