<?php
/**
 * @package     M2FBT
 * @author      Codilar Technologies
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        http://www.codilar.com/
 */
namespace Codilar\Afbt\Plugins;

use Codilar\Afbt\Model\Constants;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type\AbstractType;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Magento\Quote\Model\Quote as Subject;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Quote\Model\ResourceModel\Quote\Item;

class Quote{

    const LAST_ADDED_PRODUCT_REGISTRY_KEY = "_codilar_last_added_product";
    const ASSOCIATED_SIMPLE_PRODUCT = "_codilar_associated_simple_product";

    protected $configurableProductInstance;

    protected $productRepository;
    /**
     * @var Registry
     */
    private $registry;
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var Item
     */
    private $quoteItemResource;

    /**
     * Quote constructor.
     *
     * @param Configurable $configurable
     * @param ProductRepository $productRepository
     * @param Registry $registry
     * @param RequestInterface $request
     * @param Item $quoteItemResource
     */
    public function __construct(
        Configurable $configurable,
        ProductRepository $productRepository,
        Registry $registry,
        RequestInterface $request,
        Item $quoteItemResource
    )
    {
        $this->configurableProductInstance = $configurable;
        $this->productRepository = $productRepository;
        $this->registry = $registry;
        $this->request = $request;
        $this->quoteItemResource = $quoteItemResource;
    }

    /**
     * Add configurable product if simple supplied.
     *
     * @param Subject $subject
     * @param Product $product
     * @param null $request
     * @param string $processMode
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeAddProduct(Subject $subject, Product $product, $request = null, $processMode = AbstractType::PROCESS_MODE_FULL){
        $parentId = $this->configurableProductInstance->getParentIdsByChild($product->getId());

        /** Is a simple product with a configurable parent product */
        if(count($parentId)){

            $parent = $this->productRepository->getById($parentId[0]);

            /** Retrieve all configurable attributes of parent product */
            $attributes = $parent->getTypeInstance(true)->getConfigurableAttributes($parent);

            $superAttribute = [];

            foreach($attributes as $attribute){
                $attributeId = $attribute->getData('product_attribute')->getId();
                $attributeCode = $attribute->getData('product_attribute')->getData('attribute_code');
                $superAttribute[$attributeId] = $product->getData($attributeCode);
            }

            /** Replace child simple product with configurable */
            $this->registry->register(self::LAST_ADDED_PRODUCT_REGISTRY_KEY, $parent);
            $this->registry->register(self::ASSOCIATED_SIMPLE_PRODUCT, $product);
            $product = $parent;

            /** Set necessary request data */
            $request->setData("product", $parent->getId());
            $request->setData("super_attribute", $superAttribute);
        }
        return [$product, $request, $processMode];
    }

    /**
     * Add from_afbt value if added from afbt block.
     *
     * @param Subject $subject
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     */
    public function afterAddProduct(Subject $subject, $quoteItem)
    {
        $data = array_key_exists(Constants::FROM_AFBT,$this->request->getParams());
        if ($data) {
            try {
                $quoteItem->setData("from_afbt", 1);
                $this->quoteItemResource->save($quoteItem);
            } catch (\Exception $e) {
            }
        }
    }
}