<?php

/**
 * @package     M2FBT
 * @author      Codilar Technologies
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        http://www.codilar.com/
 */

namespace Codilar\Afbt\Model\Source;

use Codilar\Afbt\Helper\Data;
use Magento\Catalog\Model\Product;

class Products implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * YesNo constructor.
     * @param Data $helper
     */
    public function __construct(
        Data $helper
    )
    {
        $this->helper = $helper;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $collection = $this->getProductCollection()->addAttributeToSelect(["name", "entity_id"]);
        $products = [];
        /** @var Product $item */
        foreach ($collection as $item) {
            $products[] = [
                "value" => $item->getEntityId(),
                "label" => $item->getName()
            ];
        }
        return $products;
    }



    protected function getProductCollection()
    {
        return $this->helper->getProductCollection();
    }
}
