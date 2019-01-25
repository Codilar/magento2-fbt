<?php

/**
 * @package     eat
 * @author      Codilar Technologies
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        http://www.codilar.com/
 */

namespace Codilar\Core\Helper;

use Codilar\Api\Helper\Emulator;
use Magento\Catalog\Helper\ImageFactory;
use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\App\Area;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Catalog\Block\Product\ImageBuilder;
use Magento\Store\Model\StoreManagerInterface;

class Product extends AbstractHelper
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var ProductModel\ImageFactory
     */
    private $imageFactory;

    /**
     * @var ImageBuilder
     */
    private $imageBuilder;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Product constructor.
     * @param Context $context
     * @param EavSetupFactory $eavSetupFactory
     * @param ImageFactory $imageFactory
     * @param ImageBuilder $imageBuilder
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        EavSetupFactory $eavSetupFactory,
        ImageFactory $imageFactory,
        ImageBuilder $imageBuilder,
        StoreManagerInterface $storeManager
    )
    {
        parent::__construct($context);
        $this->eavSetupFactory = $eavSetupFactory;
        $this->imageFactory = $imageFactory;
        $this->imageBuilder = $imageBuilder;
        $this->storeManager = $storeManager;
    }

    /**
     * @param ProductModel $_product
     * @return int
     */
    public function getDiscount($_product)
    {
        /**
         * @var $_product \Magento\Catalog\Model\Product
         */
        $originalPrice = $_product->getPrice();
        $finalPrice = $_product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();

        $percentage = 0;
        if ($originalPrice > $finalPrice) {
            $percentage = intval(($originalPrice - $finalPrice) * 100 / $originalPrice);
        }
        if ($percentage) {
            return $percentage;
        } else {
            return 0;
        }
    }

    /**
     * @param ProductModel $_product
     * @return string
     */
    public function getDiscountLabel($_product) {
        $discount = $this->getDiscount($_product);
        return $discount ? $discount."% Off" : "";
    }

    /**
     * @param ProductModel $_product
     * @return string
     */
    public function getDiscountHtml($_product) {
        $discount = $this->getDiscount($_product);
        return $discount ? "<div class='product-discount'>".$discount."% Off</div>" : "";
    }


    /**
     * @param $product
     * @param string $imageId
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function getProductImage($product, $imageId = "product_page_image_large")
    {
        return $this->imageBuilder->setProduct($product)->setImageId($imageId)->create();
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductPriceHtml($product)
    {
        $currency = $this->storeManager->getStore()->getCurrentCurrencyCode();
        $specialPrice = $product->getPriceInfo()->getPrice('final_price')->getValue();
        $oldPrice = $product->getPriceInfo()->getPrice('regular_price')->getValue();
        $normalPriceHtml = "<div class='price-box price-final_price' data-role='priceBox' data-product-id='".$product->getId()."'><span class='price-container price-final_price tax weee'>
                            <span id='product-price-".$product->getId()."' data-price-amount=".$oldPrice." data-price-type='finalPrice' class='price-wrapper'>
                            <span class='price'>".$currency. " ". $oldPrice."</span></span></span></div>";
        $specialPriceHtml = "<div class='price-box price-final_price' data-role='priceBox' data-product-id='".$product->getId()."'>
                            <span class='special-price'>
                            <span class='price-container price-final_price tax weee'>
                            <span class='price-label'>Special Price</span>
                            <span id='product-price-".$product->getId()."' data-price-amount='".$specialPrice."' data-price-type='finalPrice' class='price-wrapper '>
                            <span class='price'>".$currency ." ". $specialPrice."</span></span>
                            </span>
                            </span>
                            <span class='old-price'>
                            <span class='price-container price-final_price tax weee'>
                            <span class='price-label'>Regular Price</span>
                            <span id='old-price".$product->getId()."' data-price-amount='".$specialPrice."' data-price-type='oldPrice' class='price-wrapper '>
                            <span class='price'>".$currency ." ". $oldPrice."</span></span>
                            </span>
                            </span>
                            </div>";
        return $specialPrice != $oldPrice?$specialPriceHtml:$normalPriceHtml;
    }


    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param bool $formatted
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPrice($product, $formatted = false)
    {
        $specialPrice = $product->getPriceInfo()->getPrice('final_price')->getValue();
        $oldPrice = $product->getPriceInfo()->getPrice('regular_price')->getValue();
        $price = $specialPrice != $oldPrice?$specialPrice:$oldPrice;
        if ($formatted) {
            $price = $this->storeManager->getStore()->getCurrentCurrencyCode(). " ".$price;
        }
        return $price;
    }

    /**
     * @param $price
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFormattedPrice($price)
    {
        $currency = $this->storeManager->getStore()->getCurrentCurrencyCode();
        return $currency." ". $price;
    }
}