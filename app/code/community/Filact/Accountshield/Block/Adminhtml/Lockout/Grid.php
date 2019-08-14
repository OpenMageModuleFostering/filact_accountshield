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
 * Adminhtml manage lockout grid
 *
 * @category    Filact
 * @package     Filact_Accountshield
 * @copyright   Copyright (c) 2014 Filact (http://www.filact.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Filact_Accountshield_Block_Adminhtml_Lockout_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Init Grid default properties
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('account_lockout_list');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Prepare collection for Grid
     *
     * @return Filact_Accountshield_Block_Adminhtml_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('accountshield/lockout')->getResourceCollection();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare Grid columns
     *
     * @return Mage_Adminhtml_Block_Catalog_Search_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    => Mage::helper('accountshield')->__('ID'),
            'width'     => '50px',
            'index'     => 'id',
        ));

        $this->addColumn('username', array(
            'header'    => Mage::helper('accountshield')->__('Username'),
            'index'     => 'username',
        ));
		
		$this->addColumn('lognum', array(
            'header'    => Mage::helper('accountshield')->__('Total Login Times'),
            'width'     => '50px',
            'index'     => 'lognum',
        ));
		
		$this->addColumn('failures_num', array(
            'header'    => Mage::helper('accountshield')->__('Total Failure Times'),
            'width'     => '50px',
            'index'     => 'failures_num',
        ));
		
		$this->addColumn('cur_failure_num', array(
            'header'    => Mage::helper('accountshield')->__('Current Failure Attempts'),
            'width'     => '50px',
            'index'     => 'cur_failure_num',
        ));

        $this->addColumn('last_failure_at', array(
            'header'   => Mage::helper('accountshield')->__('Last Failure At'),
            'sortable' => true,
            'width'    => '170px',
            'index'    => 'last_failure_at',
            'type'     => 'datetime',
        ));

		$this->addColumn('website_id', array(
			'header'    => Mage::helper('accountshield')->__('Website'),
			'align'     => 'center',
			'width'     => '100px',
			'type'      => 'options',
			'options'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(true),
			'index'     => 'website_id',
		));
		
		$this->addColumn('is_locked', array(
            'header'   => Mage::helper('accountshield')->__('Is Locked?'),
            'sortable' => true,
            'width'    => '170px',
            'index'    => 'is_locked',
			'filter'    => false,
            'sortable'  => false,
            'renderer' => 'Filact_Accountshield_Block_Adminhtml_Lockout_Grid_Renderer_Islocked',
        ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('accountshield')->__('Action'),
                'width'     => '100px',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(array(
                    'caption' => Mage::helper('accountshield')->__('Unlock'),
                    'url'     => array('base' => '*/*/unlock'),
                    'field'   => 'id',
					'confirm' => Mage::helper('accountshield')->__('Are you sure to unlock this User?')
                ), array(
                    'caption' => Mage::helper('accountshield')->__('Delete'),
                    'url'     => array('base' => '*/*/delete'),
                    'field'   => 'id',
					'confirm' => Mage::helper('accountshield')->__('Are you sure to delete this lock?')
                )),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'lockout',
        ));

        return parent::_prepareColumns();
    }

    /**
     * Return row URL for js event handlers
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*', array('id' => $row->getId()));
    }

    /**
     * Grid url getter
     *
     * @return string current grid url
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
}