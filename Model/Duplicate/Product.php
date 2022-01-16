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
namespace Practical\CloneProduct\Model\Duplicate;

use Practical\CloneProduct\Api\Duplicate\ProductInterface;
use Practical\CloneProduct\Model\Duplicate\Product\Used;
use Practical\CloneProduct\Model\Duplicate\Product\Refurbished;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as Collection;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Practical\CloneProduct\Logger\Logger;

/**
 * Class Product
 */
class Product implements ProductInterface
{

    const DEFAULT_LIMIT_OFFSET = ['offset' => 0, 'limit' => 10];
    const XML_PATH_OFFSET_TRACKER = 'practical/clone_product/limit_tracker';

    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @var Used
     */
    private $used;

    /**
     * @var Refurbished
     */
    private $refurbished;

    /**
     * @var Collection
     */
    private $collection;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var Json
     */
    private $json;
    
    /**
     * @var WriterInterface
     */
    private $configWriter;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * Product constructor
     * 
     * @param ProductFactory $productFactory
     * @param Used $used
     * @param Refurbished $refurbished
     * @param Collection $collection
     * @param ResourceConnection $resourceConnection
     * @param Json $json
     * @param Logger $logger
     */
    public function __construct(
        ProductFactory $productFactory,
        Used $used,
        Refurbished $refurbished,
        Collection $collection,
        ResourceConnection $resourceConnection,
        Json $json,
        WriterInterface $configWriter,
        Logger $logger
    ) {
        $this->used = $used;
        $this->refurbished = $refurbished;
        $this->productFactory = $productFactory;
        $this->collection = $collection;
        $this->resourceConnection = $resourceConnection;
        $this->json = $json;
        $this->configWriter = $configWriter;
        $this->logger = $logger;
    }

    /**
     * Load product by SKU
     */
    public function load($sku)
    {
        $product = $this->productFactory->create()
            ->loadByAttribute('sku', $sku);
        return $product;
    }

    /**
     * Retrieve list of products
     */
    public function getList()
    {
        $config = $this->getConfig();
        list($offset, $limit) = array_values(
            $this->json->unserialize($config[self::XML_PATH_OFFSET_TRACKER])
        );

        $collection = $this->collection->create();
        $collection->setPageSize($limit)
            ->addAttributeToFilter('type_id', \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
            ->setCurPage($offset)
            ->setOrder('entity_id ASC')
            ->load();

        $items = $collection->getItems();

        $this->saveConfig(
            self::XML_PATH_OFFSET_TRACKER,
            $this->json->serialize([
                'offset' => $offset + 1,
                'limit' => $limit
            ])
        );

        return $items;
    }

    /**
     * Generate clone of a product based on SKU
     */
    public function generate($sku = '')
    {
        if(!empty($sku)) {
            $this->process($sku);
        } else {
            $products = $this->getList();
            foreach ($products as $product) {
                try {
                    $this->process($product->getSku());
                } catch (\Exception $e) {
                    echo $e->getMessage(); die;
                    continue;
                }
            }
        }
    }

    /**
     * Start to process and create new duplicate products
     */
    public function process($sku)
    {
        $product = $this->load($sku);
        $this->print('SKU :: '.$product->getSku());
        
        try {
            $this->print("    Used :: Start");
            $this->used->setProduct($product);
            $this->used->create();
            $this->print("    Used :: Finish");
        } catch (\Exception $e) {
            $this->getOutput()->writeln("<error>    ".$e->getMessage()."</error>");
        }

        try {
            $this->print("    Refurbished :: Start");
            $this->refurbished->setProduct($product);
            $this->refurbished->create();
            $this->print("    Refurbished :: Finish");
        } catch (\Exception $e) {
            $this->getOutput()->writeln("<error>    ".$e->getMessage()."</error>");
        }

        $this->print('-------------------------------------');
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        $connection = $this->resourceConnection->getConnection();
        $configTable = $this->resourceConnection->getTableName('core_config_data');

        $query = $connection->select()
            ->from(['config' => $configTable], ['path', 'value'])
            ->where('path in (?)', [self::XML_PATH_OFFSET_TRACKER]);

        $result = $connection->fetchAll($query);
        $data = array_column($result, 'value', 'path');

        return [
            self::XML_PATH_OFFSET_TRACKER => $data[self::XML_PATH_OFFSET_TRACKER] ?? $this->json->serialize(
                self::DEFAULT_LIMIT_OFFSET
            )
        ];
    }

    /**
     * Store config value
     * 
     * @param $path
     * @param $value
     * @return void
     */
    public function saveConfig($path, $value)
    {
        $this->configWriter->save($path, $value, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
    }

    /**
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param OutputInterface $output
     */
    public function setOutput($output)
    {
        $this->output = $output;
    }

    /**
     * Print message in log file and console
     */
    public function print($message)
    {
        $this->getOutput()->writeln("<info>$message</info>");
        $this->logger->info($message);
    }
}