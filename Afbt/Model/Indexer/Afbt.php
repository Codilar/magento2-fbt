<?php

/**
 * @package     M2FBT
 * @author      Codilar Technologies
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        http://www.codilar.com/
 */

namespace Codilar\Afbt\Model\Indexer;

use Codilar\Afbt\Helper\Data;
use Codilar\Afbt\Model\Config;
use Magento\Framework\Indexer\ActionInterface;
use Magento\Framework\Mview\ActionInterface as MViewActionInterface;
use Magento\Sales\Model\Order\Item as OrderItem;

class Afbt implements ActionInterface, MViewActionInterface
{

    /**
     * @var Data
     */
    private $helper;
    /**
     * @var Config
     */
    private $config;

    /**
     * Afbt constructor.
     * @param Data $helper
     * @param Config $config
     */
    public function __construct(
        Data $helper,
        Config $config
    )
    {
        $this->helper = $helper;
        $this->config = $config;
    }

    /**
     * Execute full indexation
     *
     * @return void
     */
    public function executeFull()
    {
        $this->execute(null);
    }

    /**
     * Execute partial indexation by ID list
     *
     * @param int[] $ids
     * @return void
     */
    public function executeList(array $ids)
    {
        $this->execute($ids);
    }

    /**
     * Execute partial indexation by ID
     *
     * @param int $id
     * @return void
     */
    public function executeRow($id)
    {
        $this->execute([$id]);
    }

    /**
     * Execute materialization on ids entities
     *
     * @param int[] $ids
     * @return void
     * @api
     */
    public function execute($ids)
    {
        /** process if module is enabled */
        if ($this->config->isEnabled()) {
            $helper = $this->helper;
            $indexIds = [];
            if (!$ids) {
                $ids = $helper->getProductCollection()->getAllIds();
            }
            foreach ($ids as $pid) {
                /** Get Quote Ids for the pid */
                $quoteItems = $helper->getOrderItemCollectionFactory();
                $quoteItems->addFieldToFilter("product_id", $pid);
                $quoteItems->getSelect()->group("order_id");
                $quoteIds = [];
                if ($quoteItems->getSize()) {
                    /** @var OrderItem $quoteItem */
                    foreach ($quoteItems as $quoteItem) {
                        $quoteIds[] = $quoteItem->getOrderId();
                    }
                }
                /** get quotes product ids */
                $quoteItems = $helper->getOrderItemCollectionFactory();
                $quoteItems->addFieldToFilter("order_id", ["in" => $quoteIds]);
                $quoteProductIds = [];
                if ($quoteItems->getSize()) {
                    foreach ($quoteItems as $quoteItem) {
                        $quoteProductIds[] = $quoteItem->getProductId();
                    }
                }

                /** sort $quoteProductIds by number of occurrences */
                /** group by value (make it unique) */
                $quoteProductIds = $helper->getWeightSortedArray($quoteProductIds);
                /** if $pid is in this array, then remove it */
                if (in_array($pid, $quoteProductIds)) {
                    if (($key = array_search($pid, $quoteProductIds)) !== false) {
                        unset($quoteProductIds[$key]);
                    }
                }
                /** check config for no. of items and restrict array*/
                $noOfCombos = intval($this->config->getNoOfCombos());
                if (count($quoteProductIds) > $noOfCombos) {
                    foreach ($quoteProductIds as $k => $v) {
                        if ($k >= $noOfCombos) {
                            unset($quoteProductIds[$k]);
                        }
                    }
                }
                /** implode array with coma */
                $associatedProductIds = null;
                if ($quoteProductIds) {
                    $associatedProductIds = implode(',', $quoteProductIds);
                }

                /** Insert or update into db */

                if ($pid && $associatedProductIds) {
                    $indexIds[] = $helper->createOrUpdateIndexRow($pid, $associatedProductIds);
                }
            }
        }
    }
}