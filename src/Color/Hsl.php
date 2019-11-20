<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Css\Color;

/**
 * Pop CSS HSL color class
 *
 * @category   Pop
 * @package    Pop\Css
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    1.0.8
 */
class Hsl implements \ArrayAccess, ColorInterface
{

    /**
     * H value
     * @var int
     */
    protected $h = 0;

    /**
     * S value
     * @var int
     */
    protected $s = 0;

    /**
     * L value
     * @var int
     */
    protected $l = 0;

    /**
     * Alpha value
     * @var int
     */
    protected $a = null;

    /**
     * Constructor
     *
     * Instantiate the CSS HSL color object
     *
     * @param int   $h
     * @param int   $s
     * @param int   $l
     * @param float $a
     */
    public function __construct($h, $s, $l, $a = null)
    {
        $this->setH($h);
        $this->setS($s);
        $this->setL($l);
        if (null !== $a) {
            $this->setA($a);
        }
    }

    /**
     * Set H value
     *
     * @param  int $h
     * @throws \OutOfRangeException
     * @return self
     */
    public function setH($h)
    {
        $h = (int)$h;
        if (($h > 360) || ($h < 0)) {
            throw new \OutOfRangeException('Error: The value of $h must be between 0 and 360.');
        }
        $this->h = $h;
        return $this;
    }

    /**
     * Set S value
     *
     * @param  int $s
     * @throws \OutOfRangeException
     * @return self
     */
    public function setS($s)
    {
        $s = (int)$s;
        if (($s > 100) || ($s < 0)) {
            throw new \OutOfRangeException('Error: The value of $s must be between 0 and 100.');
        }
        $this->s = $s;
        return $this;
    }

    /**
     * Set L value
     *
     * @param  int $l
     * @throws \OutOfRangeException
     * @return self
     */
    public function setL($l)
    {
        $l = (int)$l;
        if (($l > 100) || ($l < 0)) {
            throw new \OutOfRangeException('Error: The value of $l must be between 0 and 100.');
        }
        $this->l = $l;
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
     * Get H value
     *
     * @return int
     */
    public function getH()
    {
        return $this->h;
    }

    /**
     * Get S value
     *
     * @return int
     */
    public function getS()
    {
        return $this->s;
    }

    /**
     * Get L value
     *
     * @return int
     */
    public function getL()
    {
        return $this->l;
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
     * Convert to RGB
     *
     * @return Rgb
     */
    public function toRgb()
    {
        $s = $this->s / 100;
        $v = $this->l / 100;

        if ($this->s == 0) {
            $r = round($v * 255);
            $g = round($v * 255);
            $b = round($v * 255);
        } else {
            $h = $this->h / 360;
            $h = $h * 6;
            if ($h == 6) {
                $h = 0;
            }

            $i  = floor($h);
            $v1 = $v * (1 - $s);
            $v2 = $v * (1 - ($s * ($h - $i)));
            $v3 = $v * (1 - ($s * (1 - ($h - $i))));

            switch ($i) {
                case 0:
                    $r = $v;
                    $g = $v3;
                    $b = $v1;
                    break;
                case 1:
                    $r = $v2;
                    $g = $v;
                    $b = $v1;
                    break;
                case 2:
                    $r = $v1;
                    $g = $v;
                    $b = $v3;
                    break;
                case 3:
                    $r = $v1;
                    $g = $v;
                    $b = $v3;
                    break;
                case 4:
                    $r = $v3;
                    $g = $v1;
                    $b = $v;
                    break;
                default:
                    $r = $v;
                    $g = $v1;
                    $b = $v2;
            }

            $r = round($r * 255);
            $g = round($g * 255);
            $b = round($b * 255);
        }

        return new Rgb($r, $g, $b, $this->a);
    }

    /**
     * Convert to hex
     *
     * @return Hex
     */
    public function toHex()
    {
        return $this->toRgb()->toHex();
    }

    /**
     * Convert to array
     *
     * @param  boolean $assoc
     * @return array
     */
    public function toArray($assoc = true)
    {
        $hsl = [];

        if ($assoc) {
            $hsl['h'] = $this->h;
            $hsl['s'] = $this->s . '%';
            $hsl['l'] = $this->l . '%';
            if (null !== $this->a) {
                $hsl['a'] = $this->a;
            }
        } else {
            $hsl[] = $this->h;
            $hsl[] = $this->s . '%';
            $hsl[] = $this->l . '%';
            if (null !== $this->a) {
                $hsl[] = $this->a;
            }
        }

        return $hsl;
    }

    /**
     * Convert to CSS-formatted string
     *
     * @return string
     */
    public function render()
    {
        return ((null !== $this->a) ? 'hsla(' : 'hsl(') . implode(', ', $this->toArray()) . ')';
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
        return (($offset == 'h') || ($offset == 's') || ($offset == 'l') || ($offset == 'a'));
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
            case 'h':
                return $this->getH();
                break;
            case 's':
                return $this->getS();
                break;
            case 'l':
                return $this->getL();
                break;
            case 'a':
                return $this->getA();
                break;
            default:
                throw new Exception("Error: You can only use 'h', 's', 'l' or 'a'.");
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
            case 'h':
                $this->setH($value);
                break;
            case 's':
                $this->setS($value);
                break;
            case 'l':
                $this->setL($value);
                break;
            case 'a':
                $this->setA($value);
                break;
            default:
                throw new Exception("Error: You can only use 'h', 's', 'l' or 'a'.");
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