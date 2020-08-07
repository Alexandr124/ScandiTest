<?php
/**
 * Created by PhpStorm.
 * User: alexandrsemenchenko
 * Date: 2020-08-05
 * Time: 15:39
 */

namespace Vendor\Test\Console;

use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Magento\Framework\App\Config\Storage\WriterInterface;

/**
 * Class ChangeColor
 *
 * Task 2
 * We are using magento 2 core module (module-theme), which will add our custom styles to head, depending on the store.
 * The command whill add styles to config, than standart module will use it.
 *
 * @package Vendor\Test\Console
 */
class ChangeColor extends Command
{
    /**
     *Color option
     */
    const COLOR = 'color';
    /**
     *Store option
     */
    const STORE = 'store';
    /**
     *css class responsible for primary buttons.
     * (Possible to add .action.secondary to change color for secondary buttons)
     */
    const CLASS_OF_BUTTON = '.action.primary';
    /**
     *Path in core_config_data
     */
    const PATH_TO_CONFIG_STYLE = 'design/head/includes';
    /**
     * Closing tag. (To be replaced in case we already have any styles)
     */
    const TAG_FOR_CHANGES = "</style>";
    /**
     * @var WriterInterface
     */
    protected $configWriter;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * ChangeColor constructor.
     * @param \Magento\Backend\Block\Template\Context\Proxy $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param WriterInterface $configWriter
     * @param StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context\Proxy $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        WriterInterface $configWriter,
        StoreManagerInterface $storeManager,
        array $data = []
         )
    {
        $this->scopeConfig = $scopeConfig;
        $this->configWriter = $configWriter;
        $this->storeManager = $storeManager;
        parent::__construct(null, $context, $data);
    }

    /**
     *
     */
    protected function configure()
    {
        $this->setName('scandiweb:color-change');
        $this->setDescription('Changing buttons color');
        $this->addOption(
            self::COLOR,
            'c',
            InputOption::VALUE_REQUIRED,
            'Color'
        );
        $this->addOption(
            self::STORE,
            's',
            InputOption::VALUE_REQUIRED,
            'Store'
        );
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        if ($color = $input->getOption(self::COLOR)) {
            $output->writeln('<info>Provided color is `' . $color . '`</info>');
        }
        if ($storeId = $input->getOption(self::STORE)) {
            $output->writeln('<info>Provided store code is `' . $storeId . '`</info>');
        }
        $style = $this->getStyle($storeId, '#' . $color);
        if (($this->validateStoreId($storeId)) && ($this->checkColor($color)) != 0) {
            $this->saveStyle($style, $storeId);
            $output->writeln('<info>Color was changed</info>');
        } else {
            $output->writeln('<info>Color was NOT changed</info>');
        }
    }

    /**
     * Validating store. Checking if we have the one.
     *
     * @param $storeId
     * @return bool
     */
    protected function validateStoreId($storeId)
    {

        $stores = $this->storeManager->getStores();
        foreach ($stores as $store) {

            if ($store->getId() == $storeId) {
                echo("Store is valid \n");

                return true;
            }
        }
        echo("Store is NOT valid \n");

        return false;
    }

    /**
     *
     * Validating color
     *
     * @param $color
     * @return false|int
     */
    protected function checkColor($color)
    {
        switch (strlen($color)) {
            case 6:
                return preg_match('/[[:xdigit:]]{6}/', $color);
            case 3:
                return preg_match('/[[:xdigit:]]{3}/', $color);
        }
        echo("Color is NOT valid \n");

        return 0;
    }

    /**
     *
     * Checking if we already have any styles in config. (To be sure that we won't replace other changes)
     * In case config data is blank - writing our styles
     * In case any other styles are present - adding our one to the and of the tag
     *
     * @param $storeId
     * @param $color
     * @return mixed|string
     */
    protected function getStyle($storeId, $color)
    {
        $style = self::CLASS_OF_BUTTON . "{background: $color;}</style>";
        $headValue = $this->scopeConfig->getValue(
            self::PATH_TO_CONFIG_STYLE,
            'stores',
            $storeId
        );
        if ($headValue === null) {
            return '<style>' . $style;
        }
        if (stripos($headValue, self::CLASS_OF_BUTTON)) {
            $stringStart = stripos($headValue, self::CLASS_OF_BUTTON);
            $stringEnd = stripos($headValue, '}', $stringStart) + 1;
            $headValue = mb_substr($headValue, 0, $stringStart) . mb_substr($headValue, $stringEnd);
        }
        if (stripos($headValue, self::TAG_FOR_CHANGES)) {
            $headValue = str_replace(self::TAG_FOR_CHANGES, $style, $headValue);

            return $headValue;
        }

        return '<style>' . $style;
    }

    /**
     * @param $style
     * @param $storeId
     */
    protected function saveStyle($style, $storeId)
    {
        $this->configWriter->save(self::PATH_TO_CONFIG_STYLE, $style, ScopeInterface::SCOPE_STORES, $storeId);
    }

}
