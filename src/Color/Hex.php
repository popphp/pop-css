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
 * Pop CSS Hex color class
 *
 * @category   Pop
 * @package    Pop\Css
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2019 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    1.0.7
 */
class Hex implements \ArrayAccess, ColorInterface
{

    /**
     * R value
     * @var string
     */
    protected $r = null;

    /**
     * G value
     * @var string
     */
    protected $g = null;

    /**
     * B value
     * @var string
     */
    protected $b = null;

    /**
     * Hex value
     * @var string
     */
    protected $hex = null;

    /**
     * Constructor
     *
     * Instantiate the CSS hex color object
     *
     * @param string $hex
     */
    public function __construct($hex)
    {
        $this->setHex($hex);
    }

    /**
     * Set hex value
     *
     * @param  string $hex
     * @throws \OutOfRangeException
     * @return self
     */
    public function setHex($hex)
    {
        $hex = strtolower($hex);
        $hex = (substr($hex, 0, 1) == '#') ? substr($hex, 1) : $hex;

        if ((strlen($hex) != 3) && (strlen($hex) != 6)) {
            throw new \OutOfRangeException('Error: The hex string was not the correct length.');
        }
        if (!$this->isValid($hex)) {
            throw new \OutOfRangeException('Error: The hex string was out of range.');
        }

        if (strlen($hex) == 3) {
            $this->setR(substr($hex, 0, 1));
            $this->setG(substr($hex, 1, 1));
            $this->setB(substr($hex, 2, 1));
        } else {
            $this->setR(substr($hex, 0, 2));
            $this->setG(substr($hex, 2, 2));
            $this->setB(substr($hex, 4, 2));
        }

        $this->hex = $hex;

        return $this;
    }

    /**
     * Set R value
     *
     * @param  string $r
     * @throws \OutOfRangeException
     * @return self
     */
    public function setR($r)
    {
        if (!$this->isValid($r)) {
            throw new \OutOfRangeException('Error: The $r hex string was out of range.');
        }
        $this->r = $r;
        return $this;
    }

    /**
     * Set G value
     *
     * @param  string $g
     * @throws \OutOfRangeException
     * @return self
     */
    public function setG($g)
    {
        if (!$this->isValid($g)) {
            throw new \OutOfRangeException('Error: The $g hex string was out of range.');
        }
        $this->g = $g;
        return $this;
    }

    /**
     * Set B value
     *
     * @param  string $b
     * @throws \OutOfRangeException
     * @return self
     */
    public function setB($b)
    {
        if (!$this->isValid($b)) {
            throw new \OutOfRangeException('Error: The $b hex string was out of range.');
        }
        $this->b = $b;
        return $this;
    }

    /**
     * Get hex value
     *
     * @return string
     */
    public function getHex()
    {
        return $this->hex;
    }

    /**
     * Get R value
     *
     * @return string
     */
    public function getR()
    {
        return $this->r;
    }

    /**
     * Get G value
     *
     * @return string
     */
    public function getG()
    {
        return $this->g;
    }

    /**
     * Get B value
     *
     * @return string
     */
    public function getB()
    {
        return $this->b;
    }

    /**
     * Method to determine if the hex value is valid
     *
     * @param  string $hex
     * @return boolean
     */
    public function isValid($hex)
    {
        $valid     = true;
        $hexValues = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f'];
        $hexAry    = str_split($hex);

        foreach ($hexAry as $h) {
            if (!in_array($h, $hexValues)) {
                $valid = false;
                break;
            }
        }

        return $valid;
    }

    /**
     * Convert to RGB
     *
     * @return Rgb
     */
    public function toRgb()
    {
        $hexR  = $this->r;
        $hexG  = $this->g;
        $hexB  = $this->b;

        if (strlen($hexR) == 1) {
            $hexR .= $hexR;
        }
        if (strlen($hexG) == 1) {
            $hexG .= $hexG;
        }
        if (strlen($hexB) == 1) {
            $hexB .= $hexB;
        }

        $r = base_convert($hexR, 16, 10);
        $g = base_convert($hexG, 16, 10);
        $b = base_convert($hexB, 16, 10);

        return new Rgb($r, $g, $b);
    }

    /**
     * Convert to HSL
     *
     * @return Hsl
     */
    public function toHsl()
    {
        return $this->toRgb()->toHsl();
    }

    /**
     * Convert to array
     *
     * @param  boolean $assoc
     * @return array
     */
    public function toArray($assoc = true)
    {
        $hex = [];

        if ($assoc) {
            $hex['hex'] = '#' . $this->hex;
            $hex['r']   = $this->r;
            $hex['g']   = $this->g;
            $hex['b']   = $this->b;
        } else {
            $hex[] = '#' . $this->hex;
            $hex[] = $this->r;
            $hex[] = $this->g;
            $hex[] = $this->b;
        }

        return $hex;
    }

    /**
     * Convert to CSS-formatted string
     *
     * @return string
     */
    public function render()
    {
        return '#' . $this->hex;
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
        return (($offset == 'r') || ($offset == 'g') || ($offset == 'b') || ($offset == 'hex'));
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
            case 'hex':
                return $this->getHex();
                break;
            default:
                throw new Exception("Error: You can only use 'r', 'g', 'b' or 'hex'.");
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
            case 'hex':
                $this->setHex($value);
                break;
            default:
                throw new Exception("Error: You can only use 'r', 'g', 'b' or 'hex'.");
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