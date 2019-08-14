<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * Lockout install script
 *
 * @category    Filact
 * @package     Filact_Accountshield
 * @copyright   Copyright (c) 2014 Filact (http://www.filact.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @var $installer Mage_Core_Model_Resource_Setup
 */
$installer = $this;

/**
 * Creating table accountshield_lockout
 */
$tableName = $installer->getTable('accountshield/lockout'); 

if ($installer->getConnection()->isTableExists($tableName) != true) {
	$table = $installer->getConnection()
		->newTable($installer->getTable('accountshield/lockout'))
		->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'unsigned' => true,
			'identity' => true,
			'nullable' => false,
			'primary'  => true,
		), 'Primary Id')
		->addColumn('username', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
			'nullable' => false,
		), 'User name')
		->addColumn('lognum', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'nullable' => false,
			'default'  => 0,
		), 'Total login count')
		->addColumn('failures_num', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'nullable' => false,
			'default'  => 0,
		), 'Total failure count')
		->addColumn('cur_failure_num', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'nullable' => false,
			'default'  => 0,
		), 'Current failure count')
		->addColumn('last_failure_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
			'nullable' => false
		), 'last failure time')
		->addColumn('type', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
			'nullable' => false,
			'default'  => 0,
		), 'login type')
		->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'nullable' => false,
			'default'  => 0,
		), 'Website id')
		->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
			'nullable' => true,
			'default'  => null,
		), 'Created time')
		->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
			'nullable' => true,
			'default'  => null,
		), 'Updated time')
		
		->addIndex($installer->getIdxName(
				$installer->getTable('accountshield/lockout'),
				array('id'),
				Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
			),
			array('id'),
			array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
		)
		->setComment('Account lockout');
	
	$installer->getConnection()->createTable($table);
}