<?php

/**
 * @package     M2FBT
 * @author      Codilar Technologies
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        http://www.codilar.com/
 */

namespace Codilar\Afbt\Controller\Fetch;

use Codilar\Afbt\Helper\Data;
use Codilar\Core\Helper\Product;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Block\Product\AbstractProduct;

class PhpMl extends Action
{
    /**
     * @var Data
     */
    private $helper;
    /**
     * @var Product
     */
    private $productHelper;
    /**
     * @var AbstractProduct
     */
    private $abstractProduct;
    /**
     * @var JsonFactory
     */
    private $jsonFactory;

    /**
     * Index constructor.
     *
     * @param Context $context
     * @param Data $helper
     * @param Product $productHelper
     * @param AbstractProduct $abstractProduct
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        Data $helper,
        Product $productHelper,
        AbstractProduct $abstractProduct,
        JsonFactory $jsonFactory
    )
    {
        $this->helper = $helper;
        $this->productHelper = $productHelper;
        $this->abstractProduct = $abstractProduct;
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
    }

    /**
     * Execute action based on request and return result
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $parentProduct = $this->helper->getProduct($id);
        $numberOfRelatedProducts = 5;
        $attributeArray = array('name','color','activity','style_bags','material','strap_bags','features_bags','category_gear','size','eco_collection','gender',
            'performance_fabric','erin_recommends','format','style_bottom','style_general','sleeve','collar','pattern','climate');
        $productCollection = $this->helper->getProductCollection()
            ->addAttributeToSelect($attributeArray,true)
            ->addAttributeToFilter(\Magento\Catalog\Model\Product::ATTRIBUTE_SET_ID, $parentProduct->getData('attribute_set_id'))
            ->addAttributeToFilter(\Magento\Catalog\Model\Product::VISIBILITY, Visibility::VISIBILITY_BOTH);
        $rankArray = array();
        foreach ($productCollection as $product){
            $match = 0;
            foreach ($attributeArray as $attribute){
                $match = $match + $this->getNumberOfWordMatch($parentProduct->getData($attribute),$product->getData($attribute));
            }
            $rankArray[$product->getId()] = $match;
        }
        unset($rankArray[$id]);
        arsort($rankArray);
        $relatedProductIds = array_keys(array_slice( $rankArray, 0, $numberOfRelatedProducts ,true));
        var_dump($relatedProductIds);
        die;
    }

    /**
     * @param $source
     * @param $destination
     * @return int
     */
    protected function getNumberOfWordMatch($source, $destination) {
        $words_to_count_source = strip_tags($source);
        $pattern = "/[^(\w|\d|\'|\"|\.|\!|\?|;|,|\\|\/|\-\-|:|\&|@)]+/";
        $words_to_count_source = preg_replace ($pattern, " ", $words_to_count_source);
        $words_to_count_source = trim($words_to_count_source);
        $total_words_source = explode(" ",$words_to_count_source);
        $words_to_count_destination = strip_tags($destination);
        $words_to_count_destination = preg_replace ($pattern, " ", $words_to_count_destination);
        $words_to_count_destination = trim($words_to_count_destination);
        $total_words_destination = explode(" ",$words_to_count_destination);
        $result=count(array_intersect($total_words_source,$total_words_destination));
        return $result;
    }
}