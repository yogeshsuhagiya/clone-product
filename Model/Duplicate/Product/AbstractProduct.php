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

use Magento\Catalog\Model\ProductFactory;
use Practical\CloneProduct\Logger\Logger;
use Magento\Framework\App\State;

/**
 * Class AbstractProduct
 */
class AbstractProduct
{

    private $product;

    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var array
     */
    private $websiteIds;

    /**
     * @var array
     */
    private $categoryIds;

    /**
     * @var State
     */
    protected $appState;

    /**
     * AbstractProduct constructor
     * 
     * @param ProductFactory $productFactory
     * @param Logger $logger
     * @param State $appState
     */
    public function __construct(
        ProductFactory $productFactory,
        Logger $logger,
        State $appState
    ) {
        $this->productFactory = $productFactory;
        $this->logger = $logger;
        
        try {
            $this->appState = $appState;
            $this->appState->setAreaCode('adminhtml');
        } catch (\Exception $e) {            
        }
    }

    /**
     * Get product object
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set product object
     */
    public function setProduct($product)
    {
        $this->product = $product;

        $this->websiteIds = $product->getWebsiteIds();
        $this->categoryIds = $product->getCategoryIds();
    }

    /**
     * Generate duplicate product
     */
    public function duplicate($data)
    {
        try {

            $duplicate = $this->productFactory->create();
            $duplicate->setData($data);
            
            $duplicate->setId(null);
            $duplicate->setCreatedAt(null);
            $duplicate->setUpdatedAt(null);
            $duplicate->setIsDuplicate(true);
            $duplicate->setWebsiteIds($this->websiteIds);
            $duplicate->setCategoryIds($this->categoryIds);
            $duplicate->setStockData(
                array(
                    'use_config_manage_stock' => 0,
                    'manage_stock' => 1,
                    'is_in_stock' => 1,
                    'qty' => 1
                )
            );

            $duplicate->save();
            
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }
}