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
 * CSS comment trait
 *
 * @category   Pop
 * @package    Pop\Css
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    2.0.0
 */
trait CommentTrait
{

    /**
     * Comments
     * @var array
     */
    protected array $comments = [];

    /**
     * Add CSS comment
     *
     * @param  Comment|string $comment
     * @param  int            $wrap
     * @param  bool           $trailingNewLine
     * @return static
     */
    public function addComment(Comment|string $comment, int $wrap = 80, bool $trailingNewLine = false): static
    {
        if (is_string($comment)) {
            $comment = new Comment($comment, $wrap, $trailingNewLine);
        }
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
     * Has CSS comments
     *
     * @return bool
     */
    public function hasComments(): bool
    {
        return !empty($this->comments);
    }

}
