<?php

/**
 * @package     eat
 * @author      Codilar Technologies
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        http://www.codilar.com/
 */

namespace Codilar\Core\Helper;

use Magento\Customer\Model\Customer as CustomerModel;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Eav\Model\Config;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use \Magento\Eav\Model\ResourceModel\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set;
use \Magento\Customer\Setup\CustomerSetupFactory;




class Customer extends AbstractHelper
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
    /**
     * @var Config
     */
    private $eavConfig;
    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;
    /**
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * Product constructor.
     * @param Context $context
     * @param EavSetupFactory $eavSetupFactory
     * @param Config $eavConfig
     * @param AttributeSetFactory $attributeSetFactory
     * @param CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(
        Context $context,
        EavSetupFactory $eavSetupFactory,
        Config $eavConfig,
        AttributeSetFactory $attributeSetFactory,
        CustomerSetupFactory $customerSetupFactory
    )
    {
        parent::__construct($context);
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->customerSetupFactory = $customerSetupFactory;
    }


    /**
     * @param $attributeCode
     * @param $attributeLabel
     * @param $attributeType
     * @param $attributeInputType
     * @param bool $required
     * @param bool $visible
     * @param int $sortOrder
     * @param int $position
     * @param string $source
     * @param array $usedInForms
     * @param bool $isUsedInGrid
     * @param bool $isVisibleInGrid
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createCustomerAttribute(
        $attributeCode,
        $attributeLabel,
        $attributeType,
        $attributeInputType,
        $required = true,
        $visible = true,
        $sortOrder = 1000,
        $position = 1000,
        $source = "",
        $usedInForms = ['adminhtml_customer', 'customer_account_create', 'customer_account_edit', 'checkout_register'],
        $isUsedInGrid = true,
        $isVisibleInGrid = true
    )
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create();
        $customerEntity = $eavSetup->getEntityType(CustomerModel::ENTITY);
        $eavSetup->addAttribute(
            $customerEntity,
            $attributeCode,
            [
                'type' => $attributeType,
                'label' => $attributeLabel,
                'input' => $attributeInputType,
                'source' => $source,
                'required' => $required,
                'visible' => $visible,
                'user_defined' => false,
                'sort_order' => $sortOrder,
                'position' => $position,
                'system' => 0
            ]
        );

        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create();
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();
        /** @var Set $attributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);
        $attribute = $customerSetup->getEavConfig()->getAttribute($customerEntity, $attributeCode)
            ->addData([
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => $usedInForms,
                'is_used_in_grid' => $isUsedInGrid,
                'is_visible_in_grid' => $isVisibleInGrid
            ]);
        $attribute->save();
    }
}