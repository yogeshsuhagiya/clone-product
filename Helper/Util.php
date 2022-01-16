<?php
/**
 * Yogesh Suhagiya
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future. If you wish to customize this module for your needs.
 * Please contact Yogesh Suhagiya (yksuhagiya@gmail.com)
 *
 * @category    Practical
 * @package     Practical_CloneProduct
 * @author      Yogesh Suhagiya (yksuhagiya@gmail.com)
 * @copyright   Copyright (c) 2022
 * @license     https://github.com/yogeshsuhagiya/clone-product/blob/main/LICENSE
 */
namespace Practical\CloneProduct\Helper;

/**
 * Class Util
 */
class Util
{
    /**
     * Replace all occurrence of "New" keyword with specified replacement
     * 
     * @param $string
     * @param $replacement
     */
    public static function replaceNew($string, $replacement)
    {
        $pattern = '/(New|new|NEW)/i';
        return preg_replace(
            $pattern,
            $replacement,
            $string
        );
    }

    /**
     * Replace "New" keyword with "used"
     * 
     * @param $string
     */
    public static function toUsed($string)
    {
        return self::replaceNew($string, 'used');
    }

    /**
     * Replace "New" keyword with "refurbished"
     * 
     * @param $string
     */
    public static function toRefurbished($string)
    {
        return self::replaceNew($string, 'refurbished');
    }
}
