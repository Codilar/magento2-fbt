<?php

/**
 * @package     M2FBT
 * @author      Codilar Technologies
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        http://www.codilar.com/
 */

namespace Codilar\Afbt\Model;

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
    protected function getValue($field, $group = "general", $section = "codilar_afbt")
    {
        return $this->scopeConfig->getValue($section."/".$group."/".$field);
    }

    /**
     * Get no. of combo products to show on frontend.
     *
     * @return int
     */
    public function getNoOfCombos()
    {
        return $this->getValue("no_of_combo");
    }

    /**
     * Check if module is enabled or not.
     * 
     * @return int
     */
    public function isEnabled()
    {
        return $this->getValue("enabled");
    }
}