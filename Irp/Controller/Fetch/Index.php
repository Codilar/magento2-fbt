<?php
/**
 * @package     M2FBT
 * @author      Codilar Technologies
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        http://www.codilar.com/
 */
namespace Codilar\Irp\Controller\Fetch;

use Codilar\Irp\Helper\Data;
use Codilar\Irp\Model\Config;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Codilar\Core\Helper\Product as ProductHelper;

class Index extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;
    /**
     * @var Config
     */
    private $config;
    /**
     * @var Data
     */
    private $helper;
    /**
     * @var JsonFactory
     */
    private $jsonFactory;
    /**
     * @var ProductHelper
     */
    private $productHelper;

    /**
     * Index constructor.
     * @param Context $context
     * @param Config $config
     * @param Data $helper
     * @param JsonFactory $jsonFactory
     * @param ProductHelper $productHelper
     */
    public function __construct(
        Context $context,
        Config $config,
        Data $helper,
        JsonFactory $jsonFactory,
        ProductHelper $productHelper
    )
    {
        parent::__construct($context);
        $this->config = $config;
        $this->helper = $helper;
        $this->jsonFactory = $jsonFactory;
        $this->productHelper = $productHelper;
    }

    /**
     * Fetch related products using JNearestNeighbour algorithm.
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if ($this->config->isEnabled()) {
            try {
                $data = $this->getRequest()->getParams();
                $pid = $data['product_id'];
                $parentProduct = $this->helper->getProduct($pid);
                $numberOfRelatedProducts = ($this->config->getNoOfProducts())?:10;
                $attributeArray = $this->config->getAllowedAttributes();
                if (!is_array($attributeArray)) {
                    $attributeArray = explode(",", $attributeArray);
                }
                if (!$attributeArray) {
                    $attributeArray = ["name", "description", "short_description"];
                }
                $productCollection = $this->helper->getProductCollection();
                $productCollection->addAttributeToSelect($attributeArray, true);
                $productCollection->addAttributeToFilter(\Magento\Catalog\Model\Product::ATTRIBUTE_SET_ID, $parentProduct->getData('attribute_set_id'));
                $productCollection->addAttributeToFilter(\Magento\Catalog\Model\Product::VISIBILITY, Visibility::VISIBILITY_BOTH);
                $rankArray = [];
                foreach ($productCollection as $product) {
                    $match = 0;
                    foreach ($attributeArray as $attribute) {
                        $match = $match + $this->getNumberOfWordMatch($parentProduct->getData($attribute),$product->getData($attribute));
                    }
                    $rankArray[$product->getId()] = $match;
                }
                unset($rankArray[$pid]);
                arsort($rankArray);
                $relatedProductIds = array_keys(array_slice( $rankArray, 0, $numberOfRelatedProducts ,true));
                $productsData = [];
                if ($relatedProductIds) {
                    foreach ($relatedProductIds as $productId) {
                        $product = $this->helper->getProduct($productId);
                        $productsData[] = [
                            "id" => $productId,
                            "name" => $product->getName(),
                            "url" => $product->getProductUrl(),
                            "image" => $this->helper->getProductImage($product),
                            "price_html" => $this->productHelper->getProductPriceHtml($product),
                            "price" => $this->productHelper->getPrice($product)
                        ];
                    }

                }
                return $this->jsonFactory->create()->setData(["status" => true, "products" => $productsData]);
            } catch (\Exception $e) {
                return $this->jsonFactory->create()->setData(["status" => false, "message" => $e->getMessage()]);
            }
        }
    }

    /**
     * Get no. of word match.
     *
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
        $result = count(array_intersect($total_words_source,$total_words_destination));
        return $result;
    }
}
