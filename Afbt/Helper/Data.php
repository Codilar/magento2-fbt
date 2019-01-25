<?php

/**
 * @package     M2FBT
 * @author      Codilar Technologies
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        http://www.codilar.com/
 */

namespace Codilar\Afbt\Helper;

use Codilar\Afbt\Model\AfbtIndex;
use Codilar\Afbt\Model\AfbtIndexFactory;
use Codilar\Afbt\Model\ResourceModel\AfbtIndex as AfbtIndexResource;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory as QuoteItemCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory as OrderItemCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Item\Collection as OrderItemCollection;
use Psr\Log\LoggerInterface;
use Magento\Catalog\Helper\ImageFactory;


class Data extends AbstractHelper
{
    /**
     * @var QuoteItemCollectionFactory
     */
    private $quoteItemCollectionFactory;
    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;
    /**
     * @var AfbtIndexFactory
     */
    private $afbtIndexFactory;
    /**
     * @var AfbtIndexResource
     */
    private $afbtIndexResource;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ProductFactory
     */
    private $productFactory;
    /**
     * @var ProductResource
     */
    private $productResource;
    /**
     * @var ImageFactory
     */
    private $imageFactory;
    /**
     * @var OrderItemCollectionFactory
     */
    private $orderItemCollectionFactory;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param QuoteItemCollectionFactory $quoteItemCollectionFactory
     * @param OrderItemCollectionFactory $orderItemCollectionFactory
     * @param ProductCollectionFactory $productCollectionFactory
     * @param AfbtIndexFactory $afbtIndexFactory
     * @param AfbtIndexResource $afbtIndexResource
     * @param LoggerInterface $logger
     * @param ProductFactory $productFactory
     * @param ProductResource $productResource
     * @param ImageFactory $imageFactory
     */
    public function __construct(
        Context $context,
        QuoteItemCollectionFactory $quoteItemCollectionFactory,
        OrderItemCollectionFactory $orderItemCollectionFactory,
        ProductCollectionFactory $productCollectionFactory,
        AfbtIndexFactory $afbtIndexFactory,
        AfbtIndexResource $afbtIndexResource,
        LoggerInterface $logger,
        ProductFactory $productFactory,
        ProductResource $productResource,
        ImageFactory $imageFactory
    )
    {
        parent::__construct($context);
        $this->quoteItemCollectionFactory = $quoteItemCollectionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->afbtIndexFactory = $afbtIndexFactory;
        $this->afbtIndexResource = $afbtIndexResource;
        $this->logger = $logger;
        $this->productFactory = $productFactory;
        $this->productResource = $productResource;
        $this->imageFactory = $imageFactory;
        $this->orderItemCollectionFactory = $orderItemCollectionFactory;
    }

    /**
     * Get Quote Items collection factory.
     *
     * @return \Magento\Quote\Model\ResourceModel\Quote\Item\Collection
     */
    public function getQuoteItemCollectionFactory()
    {
        return $this->quoteItemCollectionFactory->create();
    }

    /**
     * Get order items collection factory.
     *
     * @return OrderItemCollection
     */
    public function getOrderItemCollectionFactory()
    {
        return $this->orderItemCollectionFactory->create();
    }

    /**
     * Get product collection.
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getProductCollection()
    {
        return $this->productCollectionFactory->create();
    }

    /**
     * Sort and return array based on number of occurrences.
     *
     * @param $unsortedArray
     * @return array
     */
    public function getWeightSortedArray($unsortedArray)
    {
        $array = array_count_values($unsortedArray); //get all occurrences of each values
        arsort($array);
        $sortedArray = [];

        foreach($array as $key=>$val){ // iterate over occurrences array
            for($i=0;$i<$val;$i++){ //apply loop based on occurrences number
                $sortedArray[] = $key; // assign same name to the final array
            }
        }
        return array_unique($sortedArray);
    }

    /**
     * Create or update index table.
     *
     * @param $productId
     * @param $associatedProducts
     * @return bool|int
     */
    public function createOrUpdateIndexRow($productId, $associatedProducts)
    {
        /** @var AfbtIndex $afbtIndex */
        $afbtIndex = $this->afbtIndexFactory->create();
        $this->afbtIndexResource->load($afbtIndex, $productId, "pp_id");
        $afbtIndex->setPpId($productId);
        $afbtIndex->setAspIds($associatedProducts);
        try {
            $this->afbtIndexResource->save($afbtIndex);
            return $afbtIndex->getId();
        } catch (AlreadyExistsException $e) {
            $this->logger->error("AFBT ERROR: ". $e->getMessage());
            return false;
        } catch (\Exception $e) {
            $this->logger->error("AFBT ERROR: ". $e->getMessage());
            return false;
        }
    }

    /**
     * Get product model based on specified id.
     *
     * @param $id
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct($id)
    {
        $product = $this->productFactory->create();
        $this->productResource->load($product, $id);
        return $product;
    }

    /**
     * Get product image for specified product.
     *
     * @param Product $product
     * @param string $imageId
     * @return string
     */
    public function getProductImage($product, $imageId = "category_page_list")
    {
        $image = $this->imageFactory->create()->init($product, $imageId)
            ->setImageFile($product->getFile());
        $imageUrl = $image->getUrl();
        return (string)$imageUrl;
    }


}