<?php
/**
 * @package     eat
 * @author      Codilar Technologies
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        http://www.codilar.com/
 */

namespace Codilar\Afbt\Setup;


use Codilar\Afbt\Model\Constants;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{


    /**
     * Add codilar_afbt_index table in db.
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $afbtTable = $installer->getTable('codilar_afbt_index');
        if(!$installer->tableExists($afbtTable)) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable($afbtTable)
            )
            ->addColumn(
                'afbt_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'pp_id',
                Table::TYPE_INTEGER,
                10,
                ['nullable' => false, 'unsigned' => true],
                'Parent Product Id'
            )->addColumn(
                'asp_ids',
                Table::TYPE_TEXT,
                10,
                ['nullable' => false],
                'Associated Product Ids'
            )->addForeignKey(
                'codilar_afbt_product_id_catalog_entity_id',
                'pp_id',
                $installer->getTable('catalog_product_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )->setComment(
            'Codilar Advanced Frequently Bought Together Table'
            );
            $installer->getConnection()->createTable($table);
        }

        $this->addTableColumnIfNotExists($setup, $setup->getTable('sales_order_item'), Constants::FROM_AFBT, [
            'type' => Table::TYPE_INTEGER,
            'size' => 10,
            'nullable' => false,
            'default' => 0,
            'comment' => 'Added From AFBT'
        ]);

        $this->addTableColumnIfNotExists($setup, $setup->getTable('quote_item'), Constants::FROM_AFBT, [
            'type' => Table::TYPE_INTEGER,
            'size' => 10,
            'nullable' => false,
            'default' => 0,
            'comment' => 'Added From AFBT'
        ]);
        $installer->endSetup();
    }

    /**
     * Add table column if not exists.
     *
     * @param SchemaSetupInterface $setup
     * @param string $table
     * @param string $columnName
     * @param array $data
     */
    protected function addTableColumnIfNotExists(SchemaSetupInterface $setup,  $table,  $columnName, $data = []) {
        if (!$setup->getConnection()->tableColumnExists($table, $columnName)) {
            $setup->getConnection()->addColumn($table, $columnName, $data);
        }
    }
}