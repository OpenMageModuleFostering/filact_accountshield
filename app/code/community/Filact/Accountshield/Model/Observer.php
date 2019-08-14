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
 * Lockout observer model
 *
 * @category    Filact
 * @package     Filact_Accountshield
 * @copyright   Copyright (c) 2014 Filact (http://www.filact.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Filact_Accountshield_Model_Observer
{
	
	/**
     * Frontend login attempts log and lockout process
	 *
	 * @param Varien_Event_Observer
	 * @return Filact_Accountshield_Model_Lockout
     */
	public function accountLock(Varien_Event_Observer $observer) {
		
		$controller = $observer->getControllerAction();
		
		$sourceModel = Mage::getModel('accountshield/lockout');
		if (!$sourceModel->isEnabled())
			return $this;
			
		if ($controller->getFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH))
			return $this;
		
		$session = Mage::getSingleton('customer/session');
		$customer = Mage::getModel('customer/customer')
            ->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
			
		if (Mage::app()->getRequest()->isPost()) {
			$login = Mage::app()->getRequest()->getPost('login');
			
            if (!empty($login['username']) && !empty($login['password'])) {
			
				// Customer exists check
				$checkCustomer = $customer->loadByEmail($login['username']);
				if (!$checkCustomer->getId())
					return $this;
				
				$lockoutModel = Mage::getModel('accountshield/lockout')->getCollection()
									->addFieldToFilter('username', $login['username'])
									->addFieldToFilter('website_id', Mage::app()->getStore()->getWebsiteId())
									->getFirstItem();
									
				$lastFailureAt = $lockoutModel->getLastFailureAt();
				
				if (($lockoutModel->getCurFailureNum() >= $lockoutModel->getMaxLimit()) && $lockoutModel->remTime($lastFailureAt)) {
					Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('*/*'));
					Mage::getSingleton('customer/session')->addError(Mage::helper('core')->__('Your account has been locked!. Please try again after %d Mins', ceil($lockoutModel->remTime($lastFailureAt)/60)));
					Mage::app()->getResponse()->sendResponse();
					exit;
				}
				
				try {
					$customer->authenticate($login['username'], $login['password']);
				} catch (Mage_Core_Exception $e) {
						
					$failureNum = $lockoutModel->getFailuresNum() + 1;					
					$curFailureNum = $lockoutModel->getCurFailureNum() + 1;
					
					// Reset again
					if (($lockoutModel->getCurFailureNum() >= $lockoutModel->getMaxLimit()) && !$lockoutModel->remTime($lastFailureAt))
						$curFailureNum = 1;
					
					$lockoutModel->setUsername($login['username']);
					$lockoutModel->setFailuresNum($failureNum);
					$lockoutModel->setCurFailureNum($curFailureNum);
					$lockoutModel->setLastFailureAt(Varien_Date::now());
					$lockoutModel->setType($lockoutModel->getFrontendLoginId());
					$lockoutModel->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
					$lockoutModel->save();
				}
			}
		}
		
		return $this;
	}
	
	/**
     * Release frontend account lock
	 *
	 * @param Varien_Event_Observer
	 * @return Filact_Accountshield_Model_Lockout
     */
	public function accountLockRelease(Varien_Event_Observer $observer) {
		
		$sourceModel = Mage::getModel('accountshield/lockout');
		if (!$sourceModel->isEnabled())
			return $this;
		
		$customer = $observer->getCustomer();
		
		if ($customer->getEmail()) {
			$lockoutModel = Mage::getModel('accountshield/lockout')->getCollection()
								->addFieldToFilter('username', $customer->getEmail())
								->addFieldToFilter('website_id', Mage::app()->getStore()->getWebsiteId())
								->getFirstItem();
			
			if ($lockoutModel->getId()) {
				$lognum = $lockoutModel->getLognum() + 1;
				
				$lockoutModel->setLognum($lognum);
				$lockoutModel->setCurFailureNum(0);
				
				$lockoutModel->save();
			}
		}
		
		return $this;
	}
	
	/**
     * Adminhtml login attempts log and lockout process
	 *
	 * @param Varien_Event_Observer
	 * @return Filact_Accountshield_Model_Lockout
     */
	public function adminAccountLock(Varien_Event_Observer $observer) {
		
		$sourceModel = Mage::getModel('accountshield/lockout');
		if (!$sourceModel->isEnabled())
			return $this;
		
		$username = $observer->getUsername();
		$result = $observer->getResult();
		
		// Check admin user exists
		$adminUser = Mage::getSingleton('admin/user')->loadByUsername($username);
		if (!$adminUser->getId())
			return $this;
		
		$lockoutModel = Mage::getModel('accountshield/lockout')->getCollection()
								->addFieldToFilter('username', $username)
								->addFieldToFilter('type', 2)
								->getFirstItem();
								
		$lastFailureAt = $lockoutModel->getLastFailureAt();
		
		if (($lockoutModel->getCurFailureNum() >= $lockoutModel->getMaxLimit()) && $lockoutModel->remTime($lastFailureAt)) {
			Mage::throwException(Mage::helper('accountshield')->__('Your account has been locked!. Please try again after %d Mins', ceil($lockoutModel->remTime($lastFailureAt)/60)));
		}
								
		if (!$result) {
			$failureNum = $lockoutModel->getFailuresNum() + 1;					
			$curFailureNum = $lockoutModel->getCurFailureNum() + 1;
			
			// Reset again
			if (($lockoutModel->getCurFailureNum() >= $lockoutModel->getMaxLimit()) && !$lockoutModel->remTime($lastFailureAt))
				$curFailureNum = 1;
			
			$lockoutModel->setUsername($username);
			$lockoutModel->setFailuresNum($failureNum);
			$lockoutModel->setCurFailureNum($curFailureNum);
			$lockoutModel->setLastFailureAt(Varien_Date::now());
			$lockoutModel->setType($lockoutModel->getAdminhtmlLoginId());
			$lockoutModel->setWebstieId(Mage::app()->getStore()->getWebsiteId());
			$lockoutModel->save();
		}
		
		return $this;
	}
	
	/**
     * Release Adminhtml account lock
	 *
	 * @param Varien_Event_Observer
	 * @return Filact_Accountshield_Model_Lockout
     */
	public function adminAccountLockRelease(Varien_Event_Observer $observer) {
		
		$sourceModel = Mage::getModel('accountshield/lockout');
		if (!$sourceModel->isEnabled())
			return $this;
		
		$customer = $observer->getUser();

		if ($customer->getUsername()) {
			$lockoutModel = Mage::getModel('accountshield/lockout')->getCollection()
								->addFieldToFilter('username', $customer->getUsername())
								->addFieldToFilter('type', 2)
								->getFirstItem();
			
			if ($lockoutModel->getId()) {
				$lognum = $lockoutModel->getLognum() + 1;
				
				$lockoutModel->setLognum($lognum);
				$lockoutModel->setCurFailureNum(0);
				
				$lockoutModel->save();
			}
		}
		
		return $this;
	}
}