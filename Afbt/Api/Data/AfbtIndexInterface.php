<?php

/**
 * @package     M2FBT
 * @author      Codilar Technologies
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        http://www.codilar.com/
 */
namespace Codilar\Afbt\Api\Data;

interface AfbtIndexInterface
{

    /**
     * Get parent product id.
     *
     * @return integer
     */
    public function getPpId();

    /**
     * Get associated product ids.
     *
     * @return string
     */
    public function getAspIds();

    /**
     * Get associated product ids array.
     *
     * @return array|null
     */
    public function getAspIdsArray();

    /**
     * Set parent product id.
     *
     * @param int $ppId
     * @return $this
     */
    public function setPpId($ppId);

    /**
     * Set associated product ids.
     *
     * @param int $aspIds
     * @return $this
     */
    public function setAspIds($aspIds);

    /**
     * Get the data object.
     *
     * @return array|string|int
     */
    public function getData();
}