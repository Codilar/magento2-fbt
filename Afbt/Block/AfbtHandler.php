<?php

/**
 * @package     M2FBT
 * @author      Codilar Technologies
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        http://www.codilar.com/
 */

namespace Codilar\Afbt\Block;

use Codilar\Afbt\Model\Config;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Block\Product\View;
use \Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Helper\Product;
use Magento\Catalog\Model\ProductTypes\ConfigInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Json\EncoderInterface as JsonEncoderInterface;
use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Framework\Url\EncoderInterface;

class AfbtHandler extends View
{
    /**
     * @var Config
     */
    private $config;

    /**
     * AfbtHandler constructor.
     *
     * @param Context $context
     * @param EncoderInterface $urlEncoder
     * @param JsonEncoderInterface $jsonEncoder
     * @param StringUtils $string
     * @param Product $productHelper
     * @param ConfigInterface $productTypeConfig
     * @param FormatInterface $localeFormat
     * @param Session $customerSession
     * @param $productRepository
     * @param PriceCurrencyInterface $priceCurrency
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        EncoderInterface $urlEncoder,
        JsonEncoderInterface $jsonEncoder,
        StringUtils $string,
        Product $productHelper,
        ConfigInterface $productTypeConfig,
        FormatInterface $localeFormat,
        Session $customerSession,
        ProductRepositoryInterface $productRepository,
        PriceCurrencyInterface $priceCurrency,
        Config $config,
        array $data = []
    )
    {
        parent::__construct($context, $urlEncoder, $jsonEncoder, $string, $productHelper, $productTypeConfig, $localeFormat, $customerSession, $productRepository, $priceCurrency, $data);
        $this->config = $config;
    }

    /**
     * Check if module is enabled.
     *
     * @return int
     */
    public function isEnabled()
    {
        return $this->config->isEnabled();
    }

    /**
     * Get Ajax fetch url.
     *
     * @return string
     */
    public function getFetchUrl()
    {
        return $this->getUrl("afbt/fetch/index");
    }

    /**
     * Get add to cart controller url for afbt.
     *
     * @return string
     */
    public function getCartAddUrl()
    {
        return $this->getUrl("afbt/cart/add");
    }

    /**
     * Get form key for add to cart.
     *
     * @return string
     */
    public function getFormKey()
    {
        return $this->getBlockHtml('form_key');
    }
}