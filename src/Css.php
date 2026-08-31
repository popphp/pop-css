<?php
declare(strict_types=1);
/**
 * Pop PHP Framework (https://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2026 Nick Sagona, III
 * @license    https://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Css;

/**
 * Pop CSS class
 *
 * @category   Pop
 * @package    Pop\Css
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2026 Nick Sagona, III
 * @license    https://www.popphp.org/license     New BSD License
 * @version    3.0.0
 */
class Css extends AbstractCss
{

    /**
     * Media queries
     * @var array
     */
    protected array $media = [];

    /**
     * CSS object constructor
     *
     */
    public function __construct()
    {
        $args = func_get_args();

        foreach ($args as $arg) {
            $this->loadArgument($arg);
        }
    }

    /**
     * Load argument
     *
     * @param  mixed $arg
     * @return void
     */
    protected function loadArgument(mixed $arg): void
    {
        if ($arg instanceof Selector) {
            $this->addSelector($arg);
        } else if (($arg instanceof Comment) || is_string($arg)) {
            $this->addComment($arg);
        } else if ($arg instanceof Media) {
            $this->addMedia($arg);
        } else if (is_array($arg)) {
            foreach ($arg as $a) {
                $this->loadArgument($a);
            }
        }
    }

    /**
     * Add media query
     *
     * @param  Media $media
     * @return Css
     */
    public function addMedia(Media $media): Css
    {
        $this->media[] = $media;
        return $this;
    }

    /**
     * Get media query by index
     *
     * @param  int $i
     * @return Media|null
     */
    public function getMedia(int $i): Media|null
    {
        return $this->media[$i] ?? null;
    }

    /**
     * Get all media queries
     *
     * @return array
     */
    public function getAllMedia(): array
    {
        return $this->media;
    }

    /**
     * Remove media query by index
     *
     * @param  int $i
     * @return Css
     */
    public function removeMedia(int $i): Css
    {
        if (isset($this->media[$i])) {
            unset($this->media[$i]);
            $this->media = array_values($this->media);
        }
        return $this;
    }

    /**
     * Remove all media queries
     *
     * @return Css
     */
    public function removeAllMedia(): Css
    {
        $this->media = [];
        return $this;
    }

    /**
     * Parse CSS string
     *
     * @param  string $cssString
     * @return Css
     */
    public static function parseString(string $cssString): Css
    {
        $css = new self();
        $css->parseCss($cssString);

        return $css;
    }

    /**
     * Parse CSS from file
     *
     * @param  string $cssFile
     * @throws Exception
     * @return Css
     */
    public static function parseFile(string $cssFile): Css
    {
        $css = new self();
        $css->parseCssFile($cssFile);

        return $css;
    }

    /**
     * Parse CSS from URI
     *
     * @param  string $cssUri
     * @return Css
     */
    public static function parseUri(string $cssUri): Css
    {
        $css = new self();
        $css->parseCssUri($cssUri);

        return $css;
    }

    /**
     * Parse CSS string
     *
     * @param  string $cssString
     * @return Css
     */
    public function parseCss(string $cssString): Css
    {

        // Parse media queries
        $origCssString = $cssString;
        $mediaComments = [];
        $matches       = [];
        preg_match_all('~@media\b[^{]*({((?:[^{}]+|(?1))*)})~', $cssString, $matches, PREG_OFFSET_CAPTURE);

        if (isset($matches[0][0])) {
            foreach ($matches[0] as $match) {
                // See if media query has a top-level comment
                $mediaComment = null;
                $char         = null;
                $pos          = $match[1];
                while (($char != '/') && ($char != '}') && ($pos != 0)) {
                    $pos--;
                    $char = $origCssString[$pos];
                }
                if ($char == '/') {
                    $mediaComment = substr($origCssString, 0, ($pos + 1));
                    $mediaComment = substr($mediaComment, strrpos($mediaComment, '/*') ?: 0);
                }

                $cssString     = str_replace($match[0], '', $cssString);
                $mediaQuery    = substr($match[0], 6);
                $mediaQuery    = trim(substr($mediaQuery, 0, strpos($mediaQuery, ' {') ?: 0));
                $mediaQueryCss = substr($match[0], (strpos($match[0], '{') + 1));
                $mediaQueryCss = trim(substr($mediaQueryCss, 0, strrpos($match[0], '}') ?: 0));

                $mediaType      = $this->detectMediaType($mediaQuery);
                $mediaCondition = $this->detectMediaCondition($mediaQuery);
                $mediaFeatures  = $this->parseMediaFeatures($mediaQuery);

                $media = new Media($mediaType, $mediaFeatures, $mediaCondition);
                if ($mediaComment !== null) {
                    $media->addComment($this->normalizeComment($mediaComment));
                }

                $commentsMatches = [];
                preg_match_all('!/\*.*?\*/!s', $mediaQueryCss, $commentsMatches, PREG_OFFSET_CAPTURE);

                if (isset($commentsMatches[0][0])) {
                    foreach ($commentsMatches[0] as $match) {
                        $selectorName = $this->extractSelectorNameAfterComment($mediaQueryCss, $match[1]);
                        $mediaComments[$selectorName] = $this->normalizeComment($match[0]);
                    }
                }

                $mediaQueryCss = preg_replace('!/\*.*?\*/!s', '', $mediaQueryCss);
                $mediaQueryCss = preg_replace('/\n\s*\n/', "\n", $mediaQueryCss);

                $selectors = $this->parseSelectors($mediaQueryCss);
                foreach ($selectors as $selector) {
                    $media->addSelector($selector);
                }

                if (count($mediaComments) > 0) {
                    foreach ($mediaComments as $selectorName => $comment) {
                        if ($media->hasSelector($selectorName)) {
                            $media->getSelector($selectorName)->addComment($comment);
                        }
                    }
                }

                $this->addMedia($media);
            }
        }

        // Parse comments
        $comments = [];
        $matches  = [];
        preg_match_all('!/\*.*?\*/!s', $cssString, $matches, PREG_OFFSET_CAPTURE);

        if (isset($matches[0][0])) {
            foreach ($matches[0] as $match) {
                $selectorName = null;
                if ($match[1] != 0) {
                    $selectorName = $this->extractSelectorNameAfterComment($cssString, $match[1]);
                }
                if ($selectorName === null) {
                    $this->addComment($this->normalizeComment($match[0]));
                } else {
                    $comments[$selectorName] = $this->normalizeComment($match[0]);
                }
            }
        }

        $cssString = preg_replace('!/\*.*?\*/!s', '', $cssString);
        $cssString = preg_replace('/\n\s*\n/', "\n", $cssString);

        // Parse everything else
        $selectors = $this->parseSelectors($cssString);
        foreach ($selectors as $selector) {
            $this->addSelector($selector);
        }

        if (count($comments) > 0) {
            foreach ($comments as $selectorName => $comment) {
                if ($this->hasSelector($selectorName)) {
                    $this->getSelector($selectorName)->addComment($comment);
                }
            }
        }

        return $this;
    }

    /**
     * Normalize a raw comment block into a Comment object, stripping the /*, *&#47; and leading *
     * markers from each line
     *
     * @param  string $rawComment
     * @return Comment
     */
    protected function normalizeComment(string $rawComment): Comment
    {
        $lines = explode(PHP_EOL, $rawComment);
        foreach ($lines as $key => $line) {
            $lines[$key] = trim(str_replace(['/*', '*/', '*'], ['', '', ''], $line));
        }

        return new Comment(trim(implode(PHP_EOL, $lines)));
    }

    /**
     * Extract the selector name that immediately follows a comment's closing '*​/' marker
     *
     * @param  string $source
     * @param  int    $pos
     * @return string
     */
    protected function extractSelectorNameAfterComment(string $source, int $pos): string
    {
        $selectorName = substr($source, $pos);
        $selectorName = substr($selectorName, 0, strpos($selectorName, '{') ?: 0);

        return trim(substr($selectorName, (strpos($selectorName, '*/') + 2)));
    }

    /**
     * Detect the media type (all, print, screen, speech) from a media query string
     *
     * @param  string $mediaQuery
     * @return string|null
     */
    protected function detectMediaType(string $mediaQuery): string|null
    {
        foreach (['all', 'print', 'screen', 'speech'] as $type) {
            if (str_contains($mediaQuery, $type)) {
                return $type;
            }
        }

        return null;
    }

    /**
     * Detect the media condition (not, only) from a media query string
     *
     * @param  string $mediaQuery
     * @return string|null
     */
    protected function detectMediaCondition(string $mediaQuery): string|null
    {
        if (str_contains($mediaQuery, 'not')) {
            return 'not';
        }
        if (str_contains($mediaQuery, 'only')) {
            return 'only';
        }

        return null;
    }

    /**
     * Parse the (feature: value) pairs out of a media query string
     *
     * @param  string $mediaQuery
     * @return array
     */
    protected function parseMediaFeatures(string $mediaQuery): array
    {
        $mediaFeatures = [];

        if (str_contains($mediaQuery, '(') && str_contains($mediaQuery, ')')) {
            $features = substr($mediaQuery, strpos($mediaQuery, '(') ?: 0);
            $features = substr($features, 0, (strrpos($features, ')') + 1));
            foreach (explode('and', $features) as $feature) {
                $feature = str_replace(['(', ')'], ['', ''], $feature);
                $feature = explode(':', $feature);
                if (count($feature) == 2) {
                    $mediaFeatures[trim($feature[0])] = trim($feature[1]);
                }
            }
        }

        return $mediaFeatures;
    }

    /**
     * Parse CSS string from file
     *
     * @param  string $cssFile
     * @throws Exception
     * @return Css
     */
    public function parseCssFile(string $cssFile): Css
    {
        if (!file_exists($cssFile)) {
            throw new Exception("Error: That file '" . $cssFile . "' does not exist.");
        }
        return $this->parseCss(file_get_contents($cssFile));
    }

    /**
     * Parse CSS string from URI
     *
     * @param  string $cssUri
     * @throws Exception
     * @return Css
     */
    public function parseCssUri(string $cssUri): Css
    {
        $cssString = @file_get_contents($cssUri);
        if ($cssString === false) {
            throw new Exception("Error: Unable to fetch CSS from the URI '" . $cssUri . "'.");
        }

        return $this->parseCss($cssString);
    }

    /**
     * Method to write CSS to file
     *
     * @param  string $filename
     * @return void
     */
    public function writeToFile(string $filename): void
    {
        file_put_contents($filename, $this->render());
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
        foreach ($this->selectors as $selector) {
            $selector->minify($this->minify);
            $css .= (string)$selector;
            if (!$this->minify) {
                $css .= PHP_EOL;
            }
        }
        foreach ($this->media as $media) {
            $media->minify($this->minify);
            $css .= (string)$media;
            if (!$this->minify) {
                $css .= PHP_EOL;
            }
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
     * Method to parse the CSS selectors from a string
     *
     * @param  string $cssString
     * @return array
     */
    protected function parseSelectors(string $cssString): array
    {
        $selectors = [];

        $matches = [];
        preg_match_all('/\{\s*([^}]*?)\s*}/m', $cssString, $matches, PREG_OFFSET_CAPTURE);

        if (isset($matches[0][0])) {
            $curPos = 0;
            foreach ($matches[0] as $match) {
                $selectorName = trim(substr($cssString, $curPos, $match[1]));
                if (strpos($selectorName, '{') !== false) {
                    $selectorName = trim(substr($selectorName, 0, strpos($selectorName, '{')));
                }
                $rules    = $this->splitDeclarations(trim(str_replace(['{', '}'], ['', ''], trim($match[0]))));
                $cssRules = [];
                foreach ($rules as $key => $value) {
                    if (!empty($value)) {
                        $value = trim($value);
                        $v = explode(':', $value, 2);
                        if (count($v) == 2) {
                            $cssRules[trim($v[0])] = trim($v[1]);
                        }
                    }
                }

                $selector = new Selector($selectorName);
                $selector->setProperties($cssRules);
                $selectors[] = $selector;
                $curPos = $match[1] + strlen($match[0]);
            }
        }

        return $selectors;
    }

    /**
     * Split a rule block into individual declarations, respecting parentheses so a semicolon
     * inside a value like url(data:image/png;base64,...) doesn't split one declaration into two
     *
     * @param  string $rulesBlock
     * @return array
     */
    protected function splitDeclarations(string $rulesBlock): array
    {
        $declarations = [];
        $current      = '';
        $depth        = 0;

        foreach (str_split($rulesBlock) as $char) {
            if ($char === '(') {
                $depth++;
            } else if (($char === ')') && ($depth > 0)) {
                $depth--;
            }

            if (($char === ';') && ($depth === 0)) {
                $declarations[] = $current;
                $current        = '';
            } else {
                $current .= $char;
            }
        }

        $declarations[] = $current;

        return $declarations;
    }

}
