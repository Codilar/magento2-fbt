<?php

/**
 * @package     M2FBT
 * @author      Codilar Technologies
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        http://www.codilar.com/
 */
namespace Codilar\Afbt\Model;

use Codilar\Afbt\Api\AfbtIndexRepositoryInterface;
use Codilar\Afbt\Api\Data\AfbtIndexInterface;
use Codilar\Afbt\Model\AfbtIndexFactory;
use Codilar\Afbt\Model\ResourceModel\AfbtIndex as ObjectResourceModel;
use Codilar\Afbt\Model\ResourceModel\AfbtIndex\CollectionFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\SearchResultsInterfaceFactory;

class AfbtIndexRepository implements AfbtIndexRepositoryInterface
{
    /** @var \Codilar\Afbt\Model\AfbtIndexFactory  */
    protected $objectFactory;

    /** @var ObjectResourceModel  */
    protected $objectResourceModel;

    /** @var CollectionFactory  */
    protected $collectionFactory;

    /** @var SearchResultsInterfaceFactory  */
    protected $searchResultsFactory;

    /**
     * AfbtIndexRepository constructor.
     *
     * @param \Codilar\Afbt\Model\AfbtIndexFactory $objectFactory
     * @param ObjectResourceModel $objectResourceModel
     * @param CollectionFactory $collectionFactory
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        AfbtIndexFactory $objectFactory,
        ObjectResourceModel $objectResourceModel,
        CollectionFactory $collectionFactory,
        SearchResultsInterfaceFactory $searchResultsFactory       
    ) {
        $this->objectFactory        = $objectFactory;
        $this->objectResourceModel  = $objectResourceModel;
        $this->collectionFactory    = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * @param AfbtIndexInterface $object
     * @return AfbtIndexInterface
     * @throws CouldNotSaveException
     */
    public function save(AfbtIndexInterface $object)
    {
        try {
            $this->objectResourceModel->save($object);
        } catch(\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }
        return $object;
    }

    /**
     * @param $id
     * @param null $field
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getById($id, $field = null)
    {
        $object = $this->objectFactory->create();
        if ($field) {
            $this->objectResourceModel->load($object, $id, $field);
        } else {
            $this->objectResourceModel->load($object, $id);
        }
        if (!$object->getId()) {
            throw new NoSuchEntityException(__('Object with id "%1" does not exist.', $id));
        }
        return $object;        
    }

    /**
     * @param AfbtIndexInterface $object
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(AfbtIndexInterface $object)
    {
        try {
            $this->objectResourceModel->delete($object);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;    
    }

    /**
     * @param $id
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($id)
    {
        return $this->delete($this->getById($id));
    }

    /**
     * @param SearchCriteriaInterface $criteria
     * @return mixed
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);  
        $collection = $this->collectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                $fields[] = $filter->getField();
                $conditions[] = [$condition => $filter->getValue()];
            }
            if ($fields) {
                $collection->addFieldToFilter($fields, $conditions);
            }
        }  
        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        $objects = [];                                     
        foreach ($collection as $objectModel) {
            $objects[] = $objectModel;
        }
        $searchResults->setItems($objects);
        return $searchResults;        
    }

    /**
     * @return ObjectResourceModel\Collection
     */
    public function getCollection()
    {
        return $this->collectionFactory->create();
    }
}
