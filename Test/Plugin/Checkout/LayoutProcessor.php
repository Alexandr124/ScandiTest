<?php
/**
 * Created by PhpStorm.
 * User: alexandrsemenchenko
 * Date: 2020-08-07
 * Time: 10:18
 */

namespace Vendor\Test\Plugin\Checkout;


/**
 * Class LayoutProcessor
 * @package Vendor\Test\Plugin\Checkout
 */
class LayoutProcessor
{
    /**
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array $jsLayout
    )
    {
        foreach (
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']['children'] as &$children) {
            if (isset($children['label'])) {
                $children['label'] = strrev($children['label']);
            }
        }

        return $jsLayout;
    }
}
