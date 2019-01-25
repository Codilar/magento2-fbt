<?php

/**
 * @package     M2FBT
 * @author      Codilar Technologies
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        http://www.codilar.com/
 */

namespace Codilar\Irp\Model\Source;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Eav\Api\AttributeManagementInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Option\ArrayInterface;

class ProductAttributes implements ArrayInterface
{
    /**
     * @var ProductFactory
     */
    private $productFactory;
    /**
     * @var AttributeManagementInterface
     */
    private $attributeManagement;

    /**
     * ProductAttributes constructor.
     * @param ProductFactory $productFactory
     * @param AttributeManagementInterface $attributeManagement
     */
    public function __construct(
        ProductFactory $productFactory,
        AttributeManagementInterface $attributeManagement
    )
    {
        $this->productFactory = $productFactory;
        $this->attributeManagement = $attributeManagement;
    }

    /**
     * Return the option array containing product data.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->getAttributes() as $attribute) {
            $requiredArray = $this->getRequiredArray();
            if (in_array($attribute->getAttributeCode(), $requiredArray)) {
                $options[] = [
                    "value" => $attribute->getAttributeCode(),
                    "label" => $attribute->getDefaultFrontendLabel()
                ];
            }
        }
        asort($options);
        return $options;
    }

    /**
     * Get attributes of the default attribute set.
     *
     * @return \Magento\Eav\Api\Data\AttributeInterface[]
     */
    protected function getAttributes()
    {
        try {
            return $this->attributeManagement->getAttributes(Product::ENTITY, $this->getDefaultAttributeSetId());
        } catch (NoSuchEntityException $e) {
            echo $e->getMessage();die;
        }
    }

    /**
     * Get the default product attribute set id.
     *
     * @return int
     */
    protected function getDefaultAttributeSetId()
    {
        return $this->productFactory->create()->getDefaultAttributeSetId();
    }

    /**
     * Static array containing not required attribute codes.
     *
     * @return array
     */
    protected function getNotRequiredAttributesArray()
    {
        return ["custom_design","custom_design_to", "image","price","sku_type","special_from_date","special_to_date","tax_class_id","sku", "price_type",'page_layout', 'custom_design_from', 'gift_message_available','media_gallery', 'custom_layout', 'tier_price', 'old_id', 'gallery', 'msrp',
            'msrp_display_actual_price_type', 'minimal_price', 'price_view', 'quantity_and_stock_status','required_options', 'has_options', 'news_from_date',
            'image_label', 'news_to_date', "links_purchased_separately","options_container", "shipment_type", "small_image", "special_price", "swatch_image", "status","custom_layout_update"];
    }

    /**
     * Static array containing the required attribute codes.
     *
     * @return array
     */
    protected function getRequiredArray()
    {
        return ["description", "meta_description", "meta_keyword", "meta_title", "name", "short_description"];
    }
}