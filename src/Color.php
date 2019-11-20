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
namespace Pop\Css;

/**
 * Pop CSS color class
 *
 * @category   Pop
 * @package    Pop\Css
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    1.0.8
 */
class Color
{

    /**
     * Instantiate an RGB color object
     *
     * @param  int   $r
     * @param  int   $g
     * @param  int   $b
     * @param  float $a
     * @return Color\Rgb
     */
    public static function rgb($r, $g, $b, $a = null)
    {
        return new Color\Rgb($r, $g, $b, $a);
    }

    /**
     * Instantiate an RGB color object
     *
     * @param  int   $h
     * @param  int   $s
     * @param  int   $l
     * @param  float $a
     * @return Color\Hsl
     */
    public static function hsl($h, $s, $l, $a = null)
    {
        return new Color\Hsl($h, $s, $l, $a);
    }

    /**
     * Instantiate an RGB color object
     *
     * @param  string $hex
     * @return Color\Hex
     */
    public static function hex($hex)
    {
        return new Color\Hex($hex);
    }

    /**
     * Parse CSS color from string
     *
     * @param  string $colorString
     * @throws Color\Exception
     * @return Color\ColorInterface|object
     */
    public static function parse($colorString)
    {
        $colorString = strtolower($colorString);

        if (substr($colorString, 0, 3) == 'rgb') {
            $params = self::parseColorValues($colorString);
            return (new \ReflectionClass('Pop\Css\Color\Rgb'))->newInstanceArgs($params);
        } else if (substr($colorString, 0, 3) == 'hsl') {
            $params = self::parseColorValues($colorString);
            return (new \ReflectionClass('Pop\Css\Color\Hsl'))->newInstanceArgs($params);
        } else if (substr($colorString, 0, 1) == '#') {
            return new Color\Hex($colorString);
        } else {
            throw new Color\Exception('Error: The string was not in the correct color format.');
        }
    }

    /**
     * Parse CSS color values from string
     *
     * @param  string $colorString
     * @throws Color\Exception
     * @return array
     */
    public static function parseColorValues($colorString)
    {
        if ((strpos($colorString, '(') === false) || (strpos($colorString, ')') === false)) {
            throw new Color\Exception('Error: The string was not in the correct color format.');
        }
        $colorString = substr($colorString, (strpos($colorString, '(') + 1));
        $colorString = substr($colorString, 0, strpos($colorString, ')'));
        $values      = explode(',' , $colorString);

        foreach ($values as $key => $value) {
            $values[$key] = trim($value);
        }

        return $values;
    }

}