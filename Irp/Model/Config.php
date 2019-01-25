<?php

/**
 * @package     M2FBT
 * @author      Codilar Technologies
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        http://www.codilar.com/
 */

namespace Codilar\Irp\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Config constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param $field
     * @param string $group
     * @param string $section
     * @return mixed
     */
    protected function getValue($field, $group = "general", $section = "codilar_irp")
    {
        return $this->scopeConfig->getValue($section."/".$group."/".$field);
    }

    /**
     * Get no. of products to show in related products block.
     *
     * @return int
     */
    public function getNoOfProducts()
    {
        return $this->getValue("no_of_products");
    }

    /**
     * Get module is enabled from config.
     *
     * @return int
     */
    public function isEnabled()
    {
        return $this->getValue("enabled");
    }

    /**
     * Get allowed attributes from config.
     *
     * @return string
     */
    public function getAllowedAttributes()
    {
        return $this->getValue("allowed_attributes");
    }
}