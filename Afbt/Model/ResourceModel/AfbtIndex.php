<?php

/**
 * @package     M2FBT
 * @author      Codilar Technologies
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        http://www.codilar.com/
 */
namespace Codilar\Afbt\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class AfbtIndex extends AbstractDb
{
    protected $_idFieldName = "afbt_id";

    protected function _construct()
    {
        $this->_init('codilar_afbt_index','afbt_id');
    }
}
