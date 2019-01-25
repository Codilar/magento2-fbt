<?php

/**
 * @package     M2FBT
 * @author      Codilar Technologies
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        http://www.codilar.com/
 */
namespace Codilar\Afbt\Model;

use Codilar\Afbt\Api\Data\AfbtIndexInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class AfbtIndex extends AbstractModel implements AfbtIndexInterface, IdentityInterface
{
    const CACHE_TAG = 'codilar_afbt_index';
    CONST PP_ID = "pp_id";
    CONST ASP_IDS = "asp_ids";

    protected function _construct()
    {
        $this->_init('Codilar\Afbt\Model\ResourceModel\AfbtIndex');
    }

    /**
     * @return array|string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return integer
     */
    public function getPpId()
    {
        return $this->getData(self::PP_ID);
    }

    /**
     * @return string
     */
    public function getAspIds()
    {
        return $this->getData(self::ASP_IDS);
    }


    /**
     * @return array|null
     */
    public function getAspIdsArray()
    {
        if ($this->getAspIds()) {
            return explode(",", $this->getAspIds());
        } else {
            return null;
        }
    }


    /**
     * @param int $ppId
     * @return $this
     */
    public function setPpId($ppId)
    {
        $this->setData(self::PP_ID, $ppId);
        return $this;
    }

    /**
     * @param int $aspIds
     * @return $this
     */
    public function setAspIds($aspIds)
    {
        $this->setData(self::ASP_IDS, $aspIds);
        return $this;
    }

    /**
     * @param string $key
     * @param null $index
     * @return array|string|int
     */
    public function getData($key = '', $index = null)
    {
        return parent::getData($key, $index);
    }
}
