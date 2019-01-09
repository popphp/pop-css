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
 * Pop CSS media class
 *
 * @category   Pop
 * @package    Pop\Css
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2019 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    1.0.7
 */
class Media extends AbstractCss
{

    /**
     * Media type
     * @var string
     */
    protected $type = null;

    /**
     * Media condition
     * @var string
     */
    protected $condition = null;

    /**
     * Media features
     * @var array
     */
    protected $features = [];

    /**
     * Tab size
     * @var int
     */
    protected $tabSize = 4;

    /**
     * Constructor
     *
     * Instantiate the CSS media object
     *
     * @param string $type
     * @param array  $features
     * @param string $condition
     * @param int    $tabSize
     */
    public function __construct($type = null, array $features = null, $condition = null, $tabSize = 4)
    {
        if (null !== $type) {
            $this->setType($type);
        }
        if (null !== $features) {
            $this->setFeatures($features);
        }
        if (null !== $condition) {
            $this->setCondition($condition);
        }
        $this->setTabSize($tabSize);
    }

    /**
     * Set type
     *
     * @param  string $type
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set condition
     *
     * @param  string $condition
     * @return self
     */
    public function setCondition($condition = null)
    {
        $this->condition = $condition;
        return $this;
    }

    /**
     * Get condition
     *
     * @return string
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * Set tab size
     *
     * @param  int $tabSize
     * @return self
     */
    public function setTabSize($tabSize)
    {
        $this->tabSize = (int)$tabSize;
        return $this;
    }

    /**
     * Get tab size
     *
     * @return int
     */
    public function getTabSize()
    {
        return $this->tabSize;
    }

    /**
     * Set features
     *
     * @param  array $features
     * @return self
     */
    public function setFeatures(array $features)
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
    public function getFeatures()
    {
        return $this->features;
    }

    /**
     * Set feature
     *
     * @param  string $feature
     * @param  string $value
     * @return self
     */
    public function setFeature($feature, $value)
    {
        $this->features[$feature] = $value;
        return $this;
    }

    /**
     * Get feature
     *
     * @param  string $feature
     * @return string
     */
    public function getFeature($feature)
    {
        return (isset($this->features[$feature])) ? $this->features[$feature] : null;
    }

    /**
     * Does media query have feature
     *
     * @param  string $feature
     * @return boolean
     */
    public function hasFeature($feature)
    {
        return isset($this->features[$feature]);
    }

    /**
     * Method to render the selector CSS
     *
     * @return string
     */
    public function render()
    {
        $css = '';

        if (!$this->minify) {
            foreach ($this->comments as $comment) {
                $css .= (string)$comment . PHP_EOL;
            }
        }

        $css .= ($this->minify) ? ' @media': '@media';

        if (null !== $this->condition) {
            $css .= ' ' . $this->condition;
        }
        if (null !== $this->type) {
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
    public function __toString()
    {
        return $this->render();
    }

}