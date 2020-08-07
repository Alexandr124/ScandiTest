<?php

namespace Vendor\Test\Plugin\App;

use Magento\Framework\App\Response\Http;
use Magento\Framework\View\LayoutInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Response
 * @package Vendor\Test\Plugin\App
 */
class Response
{
    /**
     * @var LayoutInterface
     */
    private $layout;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Response constructor.
     *
     * @param LayoutInterface $layout
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(LayoutInterface $layout, StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
        $this->layout = $layout;
    }

    /**
     * @param Http $subject
     * @param $value
     *
     * @return array
     */
    public function beforeAppendBody(Http $subject, $value): array
    {
        $value = $this->setMetaTag($value);

        return [$value];
    }

    /**
     * @param $value
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function setMetaTag($value)
    {
        $cmsBlock = $this->layout->getBlock('cms_page');
        if (!$cmsBlock) {
            return $value;
        }
        if (count($cmsBlock->getPage()->getStores()) <= 1) {
            return $value;
        }
        $customLink = '<link rel="alternate" hreflang="' . $this->storeManager->getStore()
                ->getCode() . '" href="' . $cmsBlock->getUrl('*/*/*', [
                '_current' => true,
                '_use_rewrite' => true
            ]) . '" />';
        $value = str_replace('</head>', $customLink . '</head>', $value);

        return $value;
    }
}
