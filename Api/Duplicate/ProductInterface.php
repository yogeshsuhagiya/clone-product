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
namespace Practical\CloneProduct\Api\Duplicate;

/**
 * Interface ProductInterface
 */
interface ProductInterface
{

    /**
     * Load product by SKU
     */
    public function load($sku);

    /**
     * Retrieve list of products
     */
    public function getList();

    /**
     * @param string $sku
     */
    public function process($sku);

    /**
     * @return array
     */
    public function getConfig();

    /**
     * @param string $path 
     * @param string $value
     * @return void
     */
    public function saveConfig($path, $value);

    /**
     * @return OutputInterface
     */
    public function getOutput();

    /**
     * @param OutputInterface $output
     */
    public function setOutput($output);

    /**
     * Generate clone of a product based on SKU
     */
    public function generate($sku);
    
}
