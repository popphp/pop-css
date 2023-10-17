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
 * Pop CSS class
 *
 * @category   Pop
 * @package    Pop\Css
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    2.0.0
 */
class Css extends AbstractCss
{

    /**
     * Media queries
     * @var array
     */
    protected array $media = [];

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
        if (isset($this->media[(int)$i])) {
            unset($this->media[(int)$i]);
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

        if (isset($matches[0]) && isset($matches[0][0])) {
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
                    $mediaComment = substr($mediaComment, strrpos($mediaComment, '/*'));
                    $mediaComment = explode(PHP_EOL, $mediaComment);
                    foreach ($mediaComment as $key => $line) {
                        $mediaComment[$key] = trim(str_replace(['/*', '*/', '*'], ['', '', ''], $line));
                    }
                }

                $cssString      = str_replace($match[0], '', $cssString);
                $mediaType      = null;
                $mediaCondition = null;
                $mediaFeatures  = [];
                $mediaQuery     = substr($match[0], 6);
                $mediaQuery     = trim(substr($mediaQuery, 0, strpos($mediaQuery, ' {')));
                $mediaQueryCss  = substr($match[0], (strpos($match[0], '{') + 1));
                $mediaQueryCss  = trim(substr($mediaQueryCss, 0, strrpos($match[0], '}')));

                if (str_contains($mediaQuery, 'all')) {
                    $mediaType = 'all';
                } else if (str_contains($mediaQuery, 'print')) {
                    $mediaType = 'print';
                } else if (str_contains($mediaQuery, 'screen')) {
                    $mediaType = 'screen';
                } else if (str_contains($mediaQuery, 'speech')) {
                    $mediaType = 'speech';
                }

                if (str_contains($mediaQuery, 'not')) {
                    $mediaCondition = 'not';
                }
                if (str_contains($mediaQuery, 'only')) {
                    $mediaCondition = 'only';
                }

                if ((str_contains($mediaQuery, '(')) && (str_contains($mediaQuery, ')'))) {
                    $features = substr($mediaQuery, strpos($mediaQuery, '('));
                    $features = substr($features, 0, (strrpos($features, ')') + 1));
                    $features = explode('and', $features);
                    foreach ($features as $feature) {
                        $feature = str_replace(['(', ')'], ['', ''], $feature);
                        $feature = explode(':', $feature);
                        if (count($feature) == 2) {
                            $mediaFeatures[trim($feature[0])] = trim($feature[1]);
                        }
                    }
                }
                $media = new Media($mediaType, $mediaFeatures, $mediaCondition);
                if ($mediaComment !== null) {
                    $media->addComment(new Comment(trim(implode(PHP_EOL, $mediaComment))));
                }

                $commentsMatches = [];
                preg_match_all('!/\*.*?\*/!s', $mediaQueryCss, $commentsMatches, PREG_OFFSET_CAPTURE);

                if (isset($commentsMatches[0]) && isset($commentsMatches[0][0])) {
                    foreach ($commentsMatches[0] as $match) {
                        $selectorName = substr($mediaQueryCss, $match[1]);
                        $selectorName = substr($selectorName, 0, strpos($selectorName, '{'));
                        $selectorName = trim(substr($selectorName, (strpos($selectorName, '*/') + 2)));
                        $comment = explode(PHP_EOL, $match[0]);
                        foreach ($comment as $key => $line) {
                            $comment[$key] = trim(str_replace(['/*', '*/', '*'], ['', '', ''], $line));
                        }
                        $mediaComments[$selectorName] = new Comment(trim(implode(PHP_EOL, $comment)));
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

        if (isset($matches[0]) && isset($matches[0][0])) {
            foreach ($matches[0] as $match) {
                $selectorName = null;
                if ($match[1] != 0) {
                    $selectorName = substr($cssString, $match[1]);
                    $selectorName = substr($selectorName, 0, strpos($selectorName, '{'));
                    $selectorName = trim(substr($selectorName, (strpos($selectorName, '*/') + 2)));
                }
                $comment = explode(PHP_EOL, $match[0]);
                foreach ($comment as $key => $line) {
                    $comment[$key] = trim(str_replace(['/*', '*/', '*'], ['', '', ''], $line));
                }
                if ($selectorName === null) {
                    $this->addComment(new Comment(trim(implode(PHP_EOL, $comment))));
                } else {
                    $comments[$selectorName] = new Comment(trim(implode(PHP_EOL, $comment)));
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
     * @return Css
     */
    public function parseCssUri(string $cssUri): Css
    {
        return $this->parseCss(file_get_contents($cssUri));
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
        foreach ($this->elements as $element) {
            if (isset($this->selectors[$element])) {
                $selector = $this->selectors[$element];
                $selector->minify($this->minify);
                $css .= (string)$selector;
                if (!$this->minify) {
                    $css .= PHP_EOL;
                }
            }
        }
        foreach ($this->ids as $id) {
            if (isset($this->selectors[$id])) {
                $selector = $this->selectors[$id];
                $selector->minify($this->minify);
                $css .= (string)$selector;
                if (!$this->minify) {
                    $css .= PHP_EOL;
                }
            }
        }
        foreach ($this->classes as $class) {
            if (isset($this->selectors[$class])) {
                $selector = $this->selectors[$class];
                $selector->minify($this->minify);
                $css .= (string)$selector;
                if (!$this->minify) {
                    $css .= PHP_EOL;
                }
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

        if (isset($matches[0]) && isset($matches[0][0])) {
            $curPos = 0;
            foreach ($matches[0] as $match) {
                $selectorName = trim(substr($cssString, $curPos, $match[1]));
                if (strpos($selectorName, '{') !== false) {
                    $selectorName = trim(substr($selectorName, 0, strpos($selectorName, '{')));
                }
                $rules    = explode(';', trim(str_replace(['{', '}'], ['', ''], trim($match[0]))));
                $cssRules = [];
                foreach ($rules as $key => $value) {
                    if (!empty($value)) {
                        $value = trim($value);
                        $v = explode(':', $value);
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

}