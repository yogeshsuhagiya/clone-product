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
namespace Practical\CloneProduct\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Practical\CloneProduct\Api\Duplicate\ProductInterface;

/**
 * Class Product
 */
class Product extends Command
{

    /**
     * Field name for argument
     */
    const ARGUMENT = 'sku';

    /**
     * @var \Practical\CloneProduct\Helper\Config
     */
    private $config;

    /**
     * @var \Practical\CloneProduct\Logger\Logger
     */
    private $logger;

    /**
     * @var ProductInterface
     */
    private $product;

    /**
     * Profit constructor.
     *
     * @param \Practical\CloneProduct\Helper\Config $config
     * 
     * @throws LogicException When the command name is empty
     */
    public function __construct(
        \Practical\CloneProduct\Helper\Config $config,
        \Practical\CloneProduct\Logger\Logger $logger,
        ProductInterface $product
    ) {
        $this->config = $config;
        $this->logger = $logger;
        $this->product = $product;

        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('clone:product')
            ->setDescription('Command line utility/admin module.')
            ->setDefinition([
                new InputArgument(
                    self::ARGUMENT,
                    InputArgument::OPTIONAL,
                    __('The product for which you want to make set of 2 (Used and Refurbished) (eg: 123). Can be a product SKU.')
                )
            ]);

        parent::configure();
    }

    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return null|int
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->config->isEnable()) {
            throw new \Exception(__("Enable First !! Goto: STORES > Configuration > Practical By Yogesh > General Settings"));
        }

        if ($id = $input->getArgument(self::ARGUMENT)) {
            $output->writeln('<info>Provided product SKU is `' . $id . '`</info>');
        }

        $this->product->setOutput($output);
        $this->product->generate($id);
    }
}
