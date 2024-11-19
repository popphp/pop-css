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
 * Pop CSS media class
 *
 * @category   Pop
 * @package    Pop\Css
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    2.0.0
 */
class Media extends AbstractCss
{

    /**
     * Media type
     * @var ?string
     */
    protected ?string $type = null;

    /**
     * Media condition
     * @var ?string
     */
    protected ?string $condition = null;

    /**
     * Media features
     * @var array
     */
    protected array $features = [];

    /**
     * Tab size
     * @var int
     */
    protected int $tabSize = 4;

    /**
     * Constructor
     *
     * Instantiate the CSS media object
     *
     * @param ?string $type
     * @param ?array  $features
     * @param ?string $condition
     * @param int     $tabSize
     */
    public function __construct(?string $type = null, ?array $features = null, ?string $condition = null, $tabSize = 4)
    {
        if ($type !== null) {
            $this->setType($type);
        }
        if ($features !== null) {
            $this->setFeatures($features);
        }
        if ($condition !== null) {
            $this->setCondition($condition);
        }
        $this->setTabSize($tabSize);
    }

    /**
     * Set type
     *
     * @param  string $type
     * @return Media
     */
    public function setType(string $type): Media
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return string|null
     */
    public function getType(): string|null
    {
        return $this->type;
    }

    /**
     * Set condition
     *
     * @param  ?string $condition
     * @return Media
     */
    public function setCondition(?string $condition = null): Media
    {
        $this->condition = $condition;
        return $this;
    }

    /**
     * Get condition
     *
     * @return string|null
     */
    public function getCondition(): string|null
    {
        return $this->condition;
    }

    /**
     * Set tab size
     *
     * @param  int $tabSize
     * @return Media
     */
    public function setTabSize(int $tabSize): Media
    {
        $this->tabSize = $tabSize;
        return $this;
    }

    /**
     * Get tab size
     *
     * @return int
     */
    public function getTabSize(): int
    {
        return $this->tabSize;
    }

    /**
     * Set features
     *
     * @param  array $features
     * @return Media
     */
    public function setFeatures(array $features): Media
    {
        foreach ($features as $feature => $value) {
            $this->setFeature($feature, $value);
        }
        return $this;
    }

    /**
     * Get features
     *
     * @return array
     */
    public function getFeatures(): array
    {
        return $this->features;
    }

    /**
     * Set feature
     *
     * @param  string $feature
     * @param  string $value
     * @return Media
     */
    public function setFeature(string $feature, string $value): Media
    {
        $this->features[$feature] = $value;
        return $this;
    }

    /**
     * Get feature
     *
     * @param  string $feature
     * @return string|null
     */
    public function getFeature(string $feature): string|null
    {
        return $this->features[$feature] ?? null;
    }

    /**
     * Does media query have feature
     *
     * @param  string $feature
     * @return bool
     */
    public function hasFeature(string $feature): bool
    {
        return isset($this->features[$feature]);
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
                $css .= $comment . PHP_EOL;
            }
        }

        $css .= ($this->minify) ? ' @media': '@media';

        if ($this->condition !== null) {
            $css .= ' ' . $this->condition;
        }
        if ($this->type !== null) {
            $css .= ' ' . $this->type;
        }

        if (!empty($this->condition) || !empty($this->type)) {
            $css .= ' and';
        }

        if (count($this->features) > 0) {
            $features = [];
            foreach ($this->features as $feature => $value) {
                $features[] = '(' . $feature . ': ' . $value .')';
            }
            $css .= ' ' . implode(' and ', $features);
        }

        $css .= ' {';
        if (!$this->minify) {
            $css .= PHP_EOL;
        }

        foreach ($this->elements as $element) {

            if (isset($this->selectors[$element])) {
                $selector = $this->selectors[$element];
                $selector->minify($this->minify);
                $elementCss = (string)$selector;

                if (!$this->minify) {
                    $elementCssAry = explode(PHP_EOL, $elementCss);
                    foreach ($elementCssAry as $key => $value) {
                        $elementCssAry[$key] = str_repeat(' ', $this->tabSize) . $value;
                    }
                    $elementCss = implode(PHP_EOL, $elementCssAry);
                }

                $css .= $elementCss;
                if (!$this->minify) {
                    $css .= PHP_EOL;
                }
            }
        }
        foreach ($this->ids as $id) {
            if (isset($this->selectors[$id])) {
                $selector = $this->selectors[$id];
                $selector->minify($this->minify);
                $idCss = (string)$selector;

                if (!$this->minify) {
                    $idCssAry = explode(PHP_EOL, $idCss);
                    foreach ($idCssAry as $key => $value) {
                        $idCssAry[$key] = str_repeat(' ', $this->tabSize) . $value;
                    }
                    $idCss = implode(PHP_EOL, $idCssAry);
                }

                $css .= $idCss;
                if (!$this->minify) {
                    $css .= PHP_EOL;
                }
            }
        }
        foreach ($this->classes as $class) {
            if (isset($this->selectors[$class])) {
                $selector = $this->selectors[$class];
                $selector->minify($this->minify);
                $classCss = (string)$selector;

                if (!$this->minify) {
                    $classCssAry = explode(PHP_EOL, $classCss);
                    foreach ($classCssAry as $key => $value) {
                        $classCssAry[$key] = str_repeat(' ', $this->tabSize) . $value;
                    }
                    $classCss = implode(PHP_EOL, $classCssAry);
                }

                $css .= $classCss;
                if (!$this->minify) {
                    $css .= PHP_EOL;
                }
            }
        }

        // Clean up end new lines
        if (str_ends_with($css, PHP_EOL . str_repeat(' ', $this->tabSize) . PHP_EOL)) {
            $css = substr($css, 0, 0 - ($this->tabSize + 1));
        }

        $css .= '}';

        if (!$this->minify) {
            $css .= PHP_EOL;
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

}
