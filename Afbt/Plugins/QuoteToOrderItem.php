<?php

/**
 * @package     M2FBT
 * @author      Codilar Technologies
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        http://www.codilar.com/
 */

namespace Codilar\Afbt\Plugins;

use Codilar\Afbt\Model\Constants;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\Quote\Model\Quote\Item\ToOrderItem as Subject;

class QuoteToOrderItem
{
    /**
     *
     * @param Subject $subject
     * @param callable $proceed
     * @param AbstractItem $item
     * @param array $additional
     * @return \Magento\Sales\Model\Order\Item
     */
    public function aroundConvert(
        Subject $subject,
        callable $proceed,
        AbstractItem $item,
        $additional = []
    )
    {
        /** @var $orderItem \Magento\Sales\Model\Order\Item */
        $orderItem = $proceed($item, $additional);
        $orderItem->setData(Constants::FROM_AFBT, $item->getData(Constants::FROM_AFBT));
        return $orderItem;
    }
}