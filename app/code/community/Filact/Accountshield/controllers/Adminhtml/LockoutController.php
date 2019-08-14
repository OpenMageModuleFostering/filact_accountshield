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
 * Adminhtml account lockout controller
 *
 * @category    Filact
 * @package     Filact_Accountshield
 * @copyright   Copyright (c) 2014 Filact (http://www.filact.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Filact_Accountshield_Adminhtml_LockoutController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Init actions
     *
     * @return Filact_Accountshield_Adminhtml_LockoutController
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('accountshield')
            ->_addBreadcrumb(
                  Mage::helper('accountshield')->__('Lockouts'),
                  Mage::helper('accountshield')->__('Lockouts')
              )
            ->_addBreadcrumb(
                  Mage::helper('accountshield')->__('Manage Lockouts'),
                  Mage::helper('accountshield')->__('Manage Lockouts')
              )
        ;
        return $this;
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_title(Mage::helper('accountshield')->__('Lockouts'))
             ->_title(Mage::helper('accountshield')->__('Manage Lockouts'));

        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        $itemId = $this->getRequest()->getParam('id');
        if ($itemId) {
            try {
                
                $model = Mage::getModel('accountshield/lockout');
                $model->load($itemId);
                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('accountshield')->__('Unable to find a lockout item.'));
                }
                $model->delete();

                // display success message
                $this->_getSession()->addSuccess(
                    Mage::helper('accountshield')->__('A lockout item has been deleted.')
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('accountshield')->__('An error occurred while deleting a lockout item.')
                );
            }
        }

        // go to grid
        $this->_redirect('*/*/');
    }
	
	/**
	 * Delete all account locks
	 *
	 */
	public function deleteAllAction() {
		try {
			$collection = Mage::getModel('accountshield/lockout')->getCollection();
			if (!$collection->getSize()) {
				Mage::throwException(Mage::helper('accountshield')->__('There are no items to delete.'));
			}
			
			foreach ($collection as $lockout) {
				$lockout->delete();
			}

			// display success message
			$this->_getSession()->addSuccess(
				Mage::helper('accountshield')->__('All lockouts have been deleted.')
			);
		} catch (Mage_Core_Exception $e) {
			$this->_getSession()->addError($e->getMessage());
		} catch (Exception $e) {
			$this->_getSession()->addException($e,
				Mage::helper('accountshield')->__('An error occurred while deleting the lockouts.')
			);
		}
		
		// go to grid
        $this->_redirect('*/*/');
	}
	
	/**
	 * Unlock an User
	 *
	 **/
	 public function unlockAction() {
	 	$itemId = $this->getRequest()->getParam('id');
        if ($itemId) {
            try {
                
                $model = Mage::getModel('accountshield/lockout');
                $model->load($itemId);
                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('accountshield')->__('Unable to find a lockout item.'));
                }
                $model->setCurFailureNum(0);
				$model->save();

                // display success message
                $this->_getSession()->addSuccess(
                    Mage::helper('accountshield')->__('An User has been unlocked.')
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('accountshield')->__('An error occurred while unlocking an User.')
                );
            }
        }

        // go to grid
        $this->_redirect('*/*/');
	 }

    /**
     * Grid ajax action
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
	
	/**
     * Check ACL
     */
	protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'unlock':
                return Mage::getSingleton('admin/session')->isAllowed('accountshield/manage/unlock');
                break;
			case 'delete':
            case 'deleteall':
                return Mage::getSingleton('admin/session')->isAllowed('accountshield/manage/delete');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('accountshield/manage');
                break;
        }
    }
}