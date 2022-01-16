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
namespace Practical\CloneProduct\Model\Duplicate\Product;

use Practical\CloneProduct\Helper\Util;

/**
 * Class Refurbished
 */
class Refurbished extends AbstractProduct
{
    /**
     * Get new product name
     */
    public function getName($name)
    {
        return Util::toRefurbished($name);
    }
    
    /**
     * Get new product SKU
     */
    public function getSku($sku)
    {
        $sku = Util::toRefurbished($sku);
        return strtoupper($sku);
    }
    
    /**
     * Get condition type
     */
    public function getCondition($condition)
    {
        return Util::toRefurbished($condition);
    }
    
    /**
     * Generate new URL key
     */
    public function getUrlKey($url)
    {
        return Util::toRefurbished($url);
    }
    
    /**
     * Get new product meta title
     */
    public function getMetaTitle($title)
    {
        return Util::toRefurbished($title);
    }

    /**
     * Collect data and create new product
     */
    public function create()
    {
        $data = $this->getProduct()->getData();
        
        $data['name'] = $this->getName($data['name']);
        $data['sku'] = $this->getSku($data['sku']);
        $data['url_key'] = $this->getUrlKey($data['url_key']);
        
        if (isset($data['condition']))
            $data['condition'] = $this->getCondition($data['condition']);
        
        if (isset($data['meta_title']))
            $data['meta_title'] = $this->getMetaTitle($data['meta_title']);

        if (isset($data['meta_keyword']))
            $data['meta_keyword'] = $this->getMetaTitle($data['meta_keyword']);

        if (isset($data['meta_description']))
            $data['meta_description'] = $this->getMetaTitle($data['meta_description']);
        
        echo "\tRefurbished SKU :: " . $data['sku'] . "\n";
        $this->duplicate(
            $data
        );
    }
}