<?php

/**
 * @package     M2FBT
 * @author      Codilar Technologies
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        http://www.codilar.com/
 */
namespace Codilar\Afbt\Api;

use Codilar\Afbt\Api\Data\AfbtIndexInterface;
use Codilar\Afbt\Model\ResourceModel\AfbtIndex\Collection;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;

interface AfbtIndexRepositoryInterface 
{
    /**
     * Save afbtIndex model.
     *
     * @param AfbtIndexInterface $page
     * @return AfbtIndexInterface
     */
    public function save(AfbtIndexInterface $page);

    /**
     * Get afbtIndex by specified id and field.
     *
     * @param int $id
     * @param string|null $field
     * @return AfbtIndexInterface
     * @throws NoSuchEntityException
     */
    public function getById($id, $field);

    /**
     * Get AfbtIndex list based on specified search criteria.
     *
     * @param SearchCriteriaInterface $criteria
     * @return mixed
     */
    public function getList(SearchCriteriaInterface $criteria);

    /**
     * Delete afbtIndex row.
     *
     * @param AfbtIndexInterface $page
     * @return mixed
     */
    public function delete(AfbtIndexInterface $page);

    /**
     * Delete AfbtIndex row based on specified id.
     *
     * @param $id
     * @return mixed
     */
    public function deleteById($id);

    /**
     * Get AfbtIndex Collection.
     *
     * @return Collection
     */
    public function getCollection();
}
