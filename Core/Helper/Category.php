<?php

/**
 * @package     eat
 * @author      Codilar Technologies
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        http://www.codilar.com/
 */

namespace Codilar\Core\Helper;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

class Category extends AbstractHelper
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * Category constructor.
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        CategoryRepositoryInterface $categoryRepository
    )
    {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Get the parent categories fot top menu.
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getParentCategories()
    {
        $rootCategoryId = $this->storeManager->getStore(1)->getRootCategoryId();
        $rootCategory = $this->categoryRepository->get($rootCategoryId);
        $subCategoryIds = $rootCategory->getChildren();
        $subCategoryIds = explode(',',$subCategoryIds);
        $limit = 6;
        $data = [];
        $i = 0;
        foreach ($subCategoryIds as $subCategory) {

            try {
                $category = $this->categoryRepository->get($subCategory);
            } catch (NoSuchEntityException $e) {
                $category = false;
            }

            if ($category) {
                if ($category->getIncludeInMenu()) {
                    $i++;
                    $data[] = [
                        'id'    => $category->getId(),
                        'name'  => ucwords($category->getName()),
                        'url'   => $category->getUrl()
                    ];
                    if ($i >= $limit) {
                        break;
                    }
                }
            }

        }
        return $data;
    }

}