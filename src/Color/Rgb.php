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
namespace Pop\Css\Color;

/**
 * Pop CSS RGB color class
 *
 * @category   Pop
 * @package    Pop\Css
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2019 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    1.0.7
 */
class Rgb implements \ArrayAccess, ColorInterface
{

    /**
     * R value
     * @var int
     */
    protected $r = 0;

    /**
     * G value
     * @var int
     */
    protected $g = 0;

    /**
     * B value
     * @var int
     */
    protected $b = 0;

    /**
     * Alpha value
     * @var int
     */
    protected $a = null;

    /**
     * Constructor
     *
     * Instantiate the CSS RGB color object
     *
     * @param int   $r
     * @param int   $g
     * @param int   $b
     * @param float $a
     */
    public function __construct($r, $g, $b, $a = null)
    {
        $this->setR($r);
        $this->setG($g);
        $this->setB($b);
        if (null !== $a) {
            $this->setA($a);
        }
    }

    /**
     * Set R value
     *
     * @param  int $r
     * @throws \OutOfRangeException
     * @return self
     */
    public function setR($r)
    {
        $r = (int)$r;
        if (($r > 255) || ($r < 0)) {
            throw new \OutOfRangeException('Error: The value of $r must be between 0 and 255.');
        }
        $this->r = $r;
        return $this;
    }

    /**
     * Set G value
     *
     * @param  int $g
     * @throws \OutOfRangeException
     * @return self
     */
    public function setG($g)
    {
        $g = (int)$g;
        if (($g > 255) || ($g < 0)) {
            throw new \OutOfRangeException('Error: The value of $g must be between 0 and 255.');
        }
        $this->g = $g;
        return $this;
    }

    /**
     * Set B value
     *
     * @param  int $b
     * @throws \OutOfRangeException
     * @return self
     */
    public function setB($b)
    {
        $b = (int)$b;
        if (($b > 255) || ($b < 0)) {
            throw new \OutOfRangeException('Error: The value of $b must be between 0 and 255.');
        }
        $this->b = $b;
        return $this;
    }

    /**
     * Set A value
     *
     * @param  float $a
     * @throws \OutOfRangeException
     * @return self
     */
    public function setA($a)
    {
        $a = (float)$a;
        if (($a > 1) || ($a < 0)) {
            throw new \OutOfRangeException('Error: The value of $l must be between 0 and 1.');
        }
        $this->a = $a;
        return $this;
    }

    /**
     * Get R value
     *
     * @return int
     */
    public function getR()
    {
        return $this->r;
    }

    /**
     * Get G value
     *
     * @return int
     */
    public function getG()
    {
        return $this->g;
    }

    /**
     * Get B value
     *
     * @return int
     */
    public function getB()
    {
        return $this->b;
    }

    /**
     * Get A value
     *
     * @return float
     */
    public function getA()
    {
        return $this->a;
    }

    /**
     * Determine if the color object has an alpha value
     *
     * @return boolean
     */
    public function hasA()
    {
        return (null !== $this->a);
    }

    /**
     * Determine if the color object has an alpha value (alias)
     *
     * @return boolean
     */
    public function hasAlpha()
    {
        return (null !== $this->a);
    }

    /**
     * Convert to HSL
     *
     * @return Hsl
     */
    public function toHsl()
    {
        $r = $this->getR();
        $g = $this->getG();
        $b = $this->getB();

        $min = min($r, min($g, $b));
        $max = max($r, max($g, $b));
        $delta = $max - $min;
        $h = 0;

        if ($delta > 0) {
            if ($max == $r && $max != $g) $h += ($g - $b) / $delta;
            if ($max == $g && $max != $b) $h += (2 + ($b - $r) / $delta);
            if ($max == $b && $max != $r) $h += (4 + ($r - $g) / $delta);
            $h /= 6;
        }

        // Calculate the saturation and brightness.
        $r = $this->getR() / 255;
        $g = $this->getG() / 255;
        $b = $this->getB() / 255;

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);

        $l = $max;
        $d = $max - $min;
        $s = ($d == 0) ? 0 : $d / $max;

        return new Hsl(round($h * 360), round($s * 100), round($l * 100), $this->a);
    }

    /**
     * Convert to hex
     *
     * @return Hex
     */
    public function toHex()
    {
        $hex = dechex($this->r) . dechex($this->g) . dechex($this->b);
        return new Hex($hex);
    }

    /**
     * Convert to array
     *
     * @param  boolean $assoc
     * @return array
     */
    public function toArray($assoc = true)
    {
        $rgb = [];

        if ($assoc) {
            $rgb['r'] = $this->r;
            $rgb['g'] = $this->g;
            $rgb['b'] = $this->b;
            if (null !== $this->a) {
                $rgb['a'] = $this->a;
            }
        } else {
            $rgb[] = $this->r;
            $rgb[] = $this->g;
            $rgb[] = $this->b;
            if (null !== $this->a) {
                $rgb[] = $this->a;
            }
        }

        return $rgb;
    }

    /**
     * Convert to CSS-formatted string
     *
     * @return string
     */
    public function render()
    {
        return ((null !== $this->a) ? 'rgba(' : 'rgb(') . implode(', ', $this->toArray()) . ')';
    }

    /**
     * Return CSS-formatted string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Magic method to set the color value
     *
     * @param  string $name
     * @param  mixed $value
     * @return void
     */
    public function __set($name, $value)
    {
        $this->offsetSet($name, $value);
    }

    /**
     * Magic method to return the color value
     *
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->offsetGet($name);
    }

    /**
     * Magic method to return whether the color value exists
     *
     * @param  string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return $this->offsetExists($name);
    }

    /**
     * Magic method to unset color value
     *
     * @param  string $name
     * @throws Exception
     * @return void
     */
    public function __unset($name)
    {
        throw new Exception('You cannot unset the properties of this color object.');
    }

    /**
     * ArrayAccess offsetExists
     *
     * @param  mixed $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return (($offset == 'r') || ($offset == 'g') || ($offset == 'b') || ($offset == 'a'));
    }

    /**
     * ArrayAccess offsetGet
     *
     * @param  mixed $offset
     * @throws Exception
     * @return mixed
     */
    public function offsetGet($offset)
    {
        switch ($offset) {
            case 'r':
                return $this->getR();
                break;
            case 'g':
                return $this->getG();
                break;
            case 'b':
                return $this->getB();
                break;
            case 'a':
                return $this->getA();
                break;
            default:
                throw new Exception("Error: You can only use 'r', 'g', 'b' or 'a'.");
        }
    }

    /**
     * ArrayAccess offsetSet
     *
     * @param  mixed $offset
     * @param  mixed $value
     * @throws Exception
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        switch ($offset) {
            case 'r':
                $this->setR($value);
                break;
            case 'g':
                $this->setG($value);
                break;
            case 'b':
                $this->setB($value);
                break;
            case 'a':
                $this->setA($value);
                break;
            default:
                throw new Exception("Error: You can only use 'r', 'g', 'b' or 'a'.");
        }
    }

    /**
     * ArrayAccess offsetUnset
     *
     * @param  mixed $offset
     * @throws Exception
     * @return void
     */
    public function offsetUnset($offset)
    {
        throw new Exception('You cannot unset the properties of this color object.');
    }

}