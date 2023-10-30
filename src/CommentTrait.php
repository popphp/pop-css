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
 * CSS comment trait
 *
 * @category   Pop
 * @package    Pop\Css
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
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
     * @param  Comment $comment
     * @return static
     */
    public function addComment(Comment $comment): static
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
     * Has CSS comments
     *
     * @return bool
     */
    public function hasComments(): bool
    {
        return !empty($this->comments);
    }

}