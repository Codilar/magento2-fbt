<?php

/**
 * @package     eat
 * @author      Codilar Technologies
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        http://www.codilar.com/
 */

namespace Codilar\Afbt\Ui\DataProvider\Analytics;

use Magento\Framework\App\ObjectManager;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;


class Report extends AbstractDataProvider
{
    /** @var mixed  */
    private $modifiersPool;

    /**
     * Blocks constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ProductCollectionFactory $collectionFactory
     * @param array $meta
     * @param array $data
     * @param PoolInterface|null $modifiersPool
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ProductCollectionFactory $collectionFactory,
        array $meta = [],
        array $data = [],
        PoolInterface $modifiersPool = null
    )
    {
        $this->modifiersPool = $modifiersPool ?: ObjectManager::getInstance()->get(PoolInterface::class);
        $this->collection = $collectionFactory->create();
        $this->collection->getSelect()->join("quote_item", "quote_item.product_id = e.entity_id and quote_item.from_afbt = 1", ["added_to_cart" => "SUM(quote_item.from_afbt)"]);
        $this->collection->getSelect()->joinLeft("sales_order_item", "sales_order_item.product_id = e.entity_id and quote_item.from_afbt = 1", ["ordered" => "SUM(sales_order_item.from_afbt)"]);
        $this->collection->getSelect()->group("quote_item.product_id");
        $this->collection->getSelect()->order("added_to_cart DESC");
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @return array
     */
    public function getData()
    {
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->load();
        }
        $items = $this->getCollection()->toArray();

        $data = [
            'totalRecords' => count($this->getCollection()->getData()),
            'items' => array_values($items),
        ];

        /** @var ModifierInterface $modifier */
        foreach ($this->modifiersPool->getModifiersInstances() as $modifier) {
            $data = $modifier->modifyData($data);
        }
        return $data;
    }
}