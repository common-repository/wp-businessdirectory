<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

use MVC\Utilities\ArrayHelper;

/**
 * Class JBusinessDirectoryControllerManageCompanies
 */
class JBusinessDirectoryControllerManageCompanies extends JControllerLegacy {
	/**
	 * Display the view
	 *
	 * @param   boolean            If true, the view output will be cached
	 * @param   array  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController        This object to support chaining.
	 * @since   1.6
	 */
	public function display($cachable = false, $urlparams = false) {
	}

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string $name   The model name. Optional.
	 * @param   string $prefix The class prefix. Optional.
	 * @param   array  $config Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'ManageCompanies', $prefix = 'JBusinessDirectoryModel', $config = array('ignore_request' => true)) {
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	public function back() {
		$this->setRedirect('index.php?option=com_jbusinessdirectory');
	}

	/**
	 * Removes an item
	 */
	public function delete() {
		// Check for request forgeries
		JSession::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$cid = $this->input->get('cid', array(), 'array');

		if (!is_array($cid) || count($cid) < 1) {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_JBUSINESSDIRECTORY_NO_COMPANY_SELECTED'), 'warning');
		} else {
			// Get the model.
			$model = $this->getModel("ManageCompany");

			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			ArrayHelper::toInteger($cid);

			// Remove the items.
			if (!$model->delete($cid)) {
				$this->setMessage($model->getError());
			} elseif (!empty($model->getErrors())){
                $implodeErrors = implode('<br />', $model->getErrors());
                $this->setMessage(JText::sprintf( 'COM_JBUSINESSDIRECTORY_DELETED_WARNING',$implodeErrors),'Warning');
            } else {
				$this->setMessage(JText::plural('COM_JBUSINESS_DIRECTORY_N_COMPANIES_DELETED', count($cid)));
			}
		}
		$menuId = JFactory::getApplication()->input->get('menuId');

		$this->setRedirect('index.php?option=com_jbusinessdirectory&view=managecompanies');
	}


	public function extendPeriod() {
		$model = $this->getModel('ManageCompany');
		$data  = JFactory::getApplication()->input->getArray();
		$orderId = $model->extendPeriod($data);

		$activeMenu = JFactory::getApplication()->getMenu()->getActive();
		$menuId="";
		if (isset($activeMenu)) {
			$menuId = "&Itemid=".$activeMenu->id;
		}

		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&task=businessuser.checkuser&orderId='.$orderId.$menuId));	
	}

	/**
	 * Retrieves the active package for a certain company by companyId, and returns
	 * the package object in json format
	 *
	 * @since 5.2.0
	 */
	public function getActivePackageAjax() {
		$companyId = JFactory::getApplication()->input->getInt('companyId');

		$model         = $this->getModel();
		$activePackage = $model->getActivePackage($companyId);

		if(!empty($activePackage->start_date)){
			$activePackage->start_date = JBusinessUtil::getDateGeneralFormat($activePackage->start_date);
		}

		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode($activePackage);
		exit;
	}

	/**
	 * Returns a json list of packages
	 *
	 * @since 5.2.0
	 */
	public function getPackageListAjax() {
		$model    = $this->getModel();
		$packages = $model->getPackages(false);

		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode($packages);
		exit;
	}

	/**
	 * Changes the package defined by the packageId, for a certain company (companyId).
	 * Returns message string
	 *
	 * @since 5.2.0
	 */
	public function changePackageAjax() {
		$companyId = JFactory::getApplication()->input->getInt('companyId');
		$packageId = JFactory::getApplication()->input->getInt('packageId');

		$model = $this->getModel('ManageCompany');
		$msg   = $model->changePackage($companyId, $packageId, UPDATE_TYPE_UPGRADE);

		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode($msg);
		exit;
	}

	/**
	 * Get all companies by string
	 *
	 * @throws Exception
	 * @since   5.3.1
	 */
	public function getCompaniesByStringAjax() {
		$str = JFactory::getApplication()->input->getString('term', null);
		$ignoreUser = JFactory::getApplication()->input->getString('ignore_user', null);
		$user = JBusinessUtil::getUser();
		$userId = $user->ID;
		if($ignoreUser){
			$userId = null;
		}
		header("Content-Type: application/json", true);
		echo json_encode(JBusinessUtil::getCompaniesByString($str, $userId));
		exit;
	}

	public function updateItemsByAvailability(){
		$month = date("Y-m");
		echo "Retrieving available members for current month - $month ..<br/>";
		$members = JBusinessUtil::getAvailableMembers($month);
		if(!empty($members)){
			$members = array_filter($members);
			$members = array_unique($members);
		}
		
		$nextMonth = date("Y-m", strtotime('+1 month'));
		echo "<br/><br/>Retrieving available members for next month - $nextMonth ..<br/>";
		$membersNextMonth = JBusinessUtil::getAvailableMembers($nextMonth);
		if(!empty($membersNextMonth)){
			$membersNextMonth = array_filter($membersNextMonth);
			$membersNextMonth = array_unique($membersNextMonth);
		}
		
		$members = array_merge($members, $membersNextMonth);
		if(!empty($members)){
			$members = array_filter($members);
			$members = array_unique($members);
		}

		echo "<br/><br/>Updating featured status ..<br/><br/>";
		JBusinessUtil::updateItemsFeaturedStatus($members);

		echo "Completed!<br/><br/>";
		exit;
	}

	public function cancelSubscription() {
		$app = JFactory::getApplication();

		$subscriptionId = $app->input->getInt('subscriptionId');
		try {
			$result = SubscriptionService::cancelSubscription($subscriptionId);

			if($result) {
				$this->setMessage(JText::_('LNG_SUBSCRIPTION_CANCELED'));
			} else {
				$this->setMessage(JText::_('LNG_SUBSCRIPTION_NOT_CANCELED'));
			}

		} catch (Exception $e) {
			$this->setMessage($e->getMessage());
		}

		$this->setRedirect('index.php?option=com_jbusinessdirectory&view=managecompanies');
	}
}
