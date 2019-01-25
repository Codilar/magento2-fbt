<?php

/**
 * @package     eat
 * @author      Codilar Technologies
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        http://www.codilar.com/
 */

namespace Codilar\Core\Model\Plugins;

use Magento\Store\Model\StoreManager as Subject;

class StoreManager extends Subject
{
    public function aroundGetStore(Subject $storeManager, callable  $proceed, $storeId = null)
    {
        if (!isset($storeId) || '' === $storeId || $storeId === true) {
            if (null === $this->currentStoreId) {
                \Magento\Framework\Profiler::start('store.resolve');
                $this->currentStoreId = $this->storeResolver->getCurrentStoreId();
                \Magento\Framework\Profiler::stop('store.resolve');
            }
            $storeId = $this->currentStoreId;
        }
        if ($storeId instanceof \Magento\Store\Api\Data\StoreInterface) {
            return $storeId;
        }
        try {
            $store = is_numeric($storeId)
                ? $this->storeRepository->getById($storeId)
                : $this->storeRepository->get($storeId);
        } catch (\Exception $e) {
            $store = $this->getDefaultStoreView();
        }
        return $store;
    }
}