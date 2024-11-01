<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controllerform');

/**
 * The Company Controller
 *
 */
class JBusinessDirectoryControllerBillingDetails extends JControllerForm {
	/**
	 * Dummy method to redirect back to standard controller
	 *
	 */
	public function display($cachable = false, $urlparams = false) {

		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=billingdetails', false));
	}

	public function add() {
		$app = JFactory::getApplication();
		$context = 'com_jbusinessdirectory.edit.billingdetails';
	
		$result = parent::add();
		if ($result) {
			$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=billingdetails&id=0'. $this->getRedirectToItemAppend(), false));
		}
	
		return $result;
	}
	
	
	/**
	 * Method to cancel an edit.
	 *
	 * @param   string  $key  The name of the primary key of the URL variable.
	 *
	 * @return  boolean  True if access level checks pass, false otherwise.

	 */
	public function cancel($key = null) {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
	
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=useroptions', false));
		$result;
	}
	
	/**
	 * Method to edit an existing record.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key
	 * (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if access level check and checkout passes, false otherwise.
	 *
	 */
	public function edit($key = null, $urlVar = null) {
		$app = JFactory::getApplication();
		$result = parent::edit();
	
		return true;
	}

	/**
	 * check if billing details exist and redirect to payment or edit view	 
	 *   
	 */
	public function checkBillingDetails () {		        
        $jinput = JFactory::getApplication()->input;
        $orderId = $jinput->get('orderId');
        $packageId = $jinput->get('filter_package');
        $packageType = $jinput->get('packageType');
		$menuItemId = JBusinessUtil::getActiveMenuItem(); 

        $model = $this->getModel('billingdetails');		
		$billingDetails = $model->getItem();

		if(!isset($orderId)) {
			$packageTable = JTable::getInstance("Package", "JTable");
			$package = $packageTable->getPackage($packageId);
			if($package->package_type == PACKAGE_TYPE_USER){
				$orderId = OrderService::createUserPackageOrder($packageId);
			}
		}

        if(!empty($billingDetails->id)) {
			$countryId = !empty($billingDetails->country)?$billingDetails->country->id:null;

			OrderService::updateOrderVAT($orderId, $countryId);
	        try {
				TaxService::updateOrderTaxes($orderId, $countryId);
	        } catch (Exception $e) {
		        $this->setMessage($e->getMessage(), 'warning');
	        }
	        $this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=payment&orderId='.$orderId, false));
        } else {
            $this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=billingdetails&layout=edit'.$menuItemId.'&orderId='.$orderId, false));
        }
       
	}
	
	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	public function save($key = null, $urlVar = null) {
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$jinput = JFactory::getApplication()->input;

		$app      = JFactory::getApplication();
		$model = $this->getModel('billingdetails');
		$data = $jinput->post->getArray();
		$context  = 'com_jbusinessdirectory.edit.billingdetails';
		$task     = $this->getTask();
		$recordId = $jinput->getInt('id');

		if (!$model->save($data)) {
			// Save the data in the session.
			$app->setUserState('com_jbusinessdirectory.edit.billingdetails.data', $data);
			
			// Redirect back to the edit screen.
			$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'warning');
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId), false));
			
			return false;
		} elseif (!empty($model->getErrors())){
            $implodeErrors = implode('<br />', $model->getErrors());
            $this->setMessage(JText::sprintf( 'COM_JBUSINESSDIRECTORY_SAVED_WARNING',$implodeErrors),'Warning');
        } else {
            $this->setMessage(JText::_('COM_JBUSINESSDIRECTORY_OFFER_SAVE_SUCCESS'));
        }

		
		// Redirect the user and adjust session state based on the chosen task.
		switch ($task) {
			case 'apply':
				// Set the row data in the session.
				$recordId = $model->getState($this->context . '.id');
				$this->holdEditId($context, $recordId);
				$app->setUserState('com_jbusinessdirectory.edit.billingdetails.data', null);
			
				// Redirect back to the edit screen.
				$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId), false));
				break;

			default:
				// Clear the row id and data in the session.
				$this->releaseEditId($context, $recordId);
				$app->setUserState('com_jbusinessdirectory.edit.billingdetails.data', null);
				$appSettings = JBusinessUtil::getApplicationSettings();
				$ssl = 0;
				if ($appSettings->enable_https_payment) {
					$ssl = 1;
				}
				
				if (!empty($data["orderId"])) {
					$activeMenu = JFactory::getApplication()->getMenu()->getActive();
					$menuId="";
					if (isset($activeMenu)) {
						$menuId = "&Itemid=".$activeMenu->id;
					}

					OrderService::updateOrderVAT($data["orderId"], $data["country"]);
					try {
						TaxService::updateOrderTaxes($data["orderId"], $data["country"]->id);
					} catch (Exception $e) {
						$this->setMessage($e->getMessage(), 'warning');
					}

					$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=payment&orderId='.$data["orderId"].$menuId, false, $ssl));
				} else {
					// Redirect to the list screen.
					$this->setMessage(JText::_('COM_JBUSINESSDIRECTORY_BILLING_DETAILS_SAVE_SUCCESS'));
					$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=billingoverview', false));
				}
				break;
		}
		
		return;
	}
}
