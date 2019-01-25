<?php

/**
 * @package     M2FBT
 * @author      Codilar Technologies
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        http://www.codilar.com/
 */

namespace Codilar\Irp\Helper;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Catalog\Helper\ImageFactory;
use Magento\Framework\Exception\NoSuchEntityException;

class Data extends AbstractHelper
{
    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;
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
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ProductCollectionFactory $productCollectionFactory
     * @param ProductFactory $productFactory
     * @param ProductResource $productResource
     * @param ImageFactory $imageFactory
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        Context $context,
        ProductCollectionFactory $productCollectionFactory,
        ProductFactory $productFactory,
        ProductResource $productResource,
        ImageFactory $imageFactory,
        CategoryRepositoryInterface $categoryRepository
    )
    {
        parent::__construct($context);
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productFactory = $productFactory;
        $this->productResource = $productResource;
        $this->imageFactory = $imageFactory;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Get product data collection.
     *
     * @return ProductResource\Collection
     */
    public function getProductCollection()
    {
        return $this->productCollectionFactory->create();
    }

    /**
     * Get the product model for the specified product id.
     *
     * @param $id
     * @return Product
     */
    public function getProduct($id)
    {
        $product = $this->productFactory->create();
        $this->productResource->load($product, $id);
        return $product;
    }

    /**
     * Get the product image url for the specified product model.
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

    /**
     * Get the category model for the specified category id.
     *
     * @param $categoryId
     * @return \Magento\Catalog\Api\Data\CategoryInterface|null
     */
    public function getCategory($categoryId)
    {
        try {
            return $this->categoryRepository->get($categoryId);
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }
}