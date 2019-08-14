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
 * Lockout model
 *
 * @category    Filact
 * @package     Filact_Accountshield
 * @copyright   Copyright (c) 2014 Filact (http://www.filact.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Filact_Accountshield_Model_Lockout extends Mage_Core_Model_Abstract
{
	/**
     * Frontend lockout flag
     *
     * @var integer
     */
	const FRONTEND_LOGIN = 1;
	
	/**
     * Adminhtml lockout flag
     *
     * @var integer
     */
	const ADMINHTML_LOGIN = 2;
	
	/**
     * Check lockout enabled or not - System Configuration
     *
     * @var boolean
     */
	const ENABLED = 'accountshield/account/enable';
	
	/**
     * Maximum allowed login attempts - System Configuration
     *
     * @var integer
     */
	const MAX_LIMIT = 'accountshield/account/max_limit';
	
	/**
     * Account lock duration in seconds - System Configuration
     *
     * @var integer
     */
	const INTERVAL = 'accountshield/account/interval';
	
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('accountshield/lockout');
    }
	
	/**
     * Assign created_at and updated_at fields
	 *
	 * @return Filact_Accountshield_Model_Lockout
     */
	protected function _beforeSave()
    {
        parent::_beforeSave();
		
        if ($this->isObjectNew()) {
            $this->setData('created_at', Varien_Date::now());
        } else {
			$this->setData('updated_at', Varien_Date::now());
		}
		
        return $this;
    }
	
	/**
     * Check lockout enabled or not - System Configuration
	 *
	 * @return boolean
     */
	public function isEnabled() {
		return Mage::getStoreConfig(self::ENABLED);
	}
	
	/**
     * Maximum allowed login attempts - System Configuration
	 *
	 * @return integer
     */
	public function getMaxLimit() {
		return is_numeric(Mage::getStoreConfig(self::MAX_LIMIT)) ? Mage::getStoreConfig(self::MAX_LIMIT) : 3;
	}
	
	/**
     * Account lock duration in seconds - System Configuration
	 *
	 * @return integer
     */
	public function getInterval() {
		return is_numeric(Mage::getStoreConfig(self::INTERVAL)) ? Mage::getStoreConfig(self::INTERVAL) : 900;
	}
	
	/**
     * Check whether a lockout time has been expired.
	 *
	 * @param timestamp $time
	 * @return boolean
     */
	public function isIntervalExceeds($time) {
		$isAllow = false;
		
		if ($time) {
			$timestamp = Mage::getModel('core/date')->timestamp($time);
			$now = Mage::getModel('core/date')->timestamp(Varien_Date::now());
			
			$isAllow = (($this->remTime($time)/60) == 0);
		}
		
		exit( 'int: '. ($this->remTime($time)/60));
		
		return $isAllow;
	}
	
	/**
     * Check whether a lockout has remaining time to expire.
	 *
	 * @param timestamp $time
	 * @return timestamp
     */
	public function remTime($time) {
		$rem = 0;
		
		if ($time) {
			$timestamp = Mage::getModel('core/date')->timestamp($time);
			$endTime = $timestamp + $this->getInterval();
			$now = Mage::getModel('core/date')->timestamp(Varien_Date::now());
			
			$rem = intval($endTime - $now);
			$rem = ($rem<0) ? 0 : $rem;
		}
		
		return $rem;
	}
	
	/**
     * Get a frontend lockout flag
	 *
	 * @return integer
     */
	public function getFrontendLoginId() {
		return self::FRONTEND_LOGIN;
	}
	
	/**
     * Get a Adminhtml lockout flag
	 *
	 * @return integer
     */
	public function getAdminhtmlLoginId() {
		return self::ADMINHTML_LOGIN;
	}
}