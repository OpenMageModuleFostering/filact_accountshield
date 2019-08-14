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
 * Adminhtml manage lockout grid container
 *
 * @category    Filact
 * @package     Filact_Accountshield
 * @copyright   Copyright (c) 2014 Filact (http://www.filact.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Filact_Accountshield_Block_Adminhtml_Lockout extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Block constructor - Adminhtml Manage Lockout
     */
    public function __construct()
    {
        $this->_blockGroup = 'accountshield';
        $this->_controller = 'adminhtml_lockout';
        $this->_headerText = Mage::helper('accountshield')->__('Manage Account Lockout');
		
		parent::__construct();
		$this->_addButton('delete_all', array(
                'label'   => Mage::helper('adminhtml')->__('Delete All'),
                'onclick' => "
					if (confirm('". Mage::helper('accountshield')->__('Are you sure to delete all locks?') ."')) { 
						setLocation('". $this->getUrl('*/*/deleteAll') ."') 
				}",
                'class'   => 'task',
				'confirm' => Mage::helper('accountshield')->__('Are you sure?')
            ), -100);
		$this->_removeButton('add');
    }
}