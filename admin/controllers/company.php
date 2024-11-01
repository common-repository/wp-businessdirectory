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
class JBusinessDirectoryControllerCompany extends JControllerForm {
	/**
	 * Dummy method to redirect back to standard controller
	 *
	 */
	public function display($cachable = false, $urlparams = false) {
		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=companies', false));
	}

	/**
	 * Method override to check if you can add a new record.
	 *
	 * @param   array  $data  An array of input data.
	 *
	 * @return  boolean
	 */
	
	protected function allowAdd($data = array()) {
		return true;
	}
	
	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key.
	 *
	 * @return  boolean
	 */
	protected function allowEdit($data = array(), $key = 'id') {
		return true;
	}
	
	public function add() {
		$app = JFactory::getApplication();
		$context = 'com_jbusinessdirectory.edit.company';
	
		$result = parent::add();
		if ($result) {
			$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=company'. $this->getRedirectToItemAppend(), false));
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
		$data = JFactory::getApplication()->input->post->getArray();

		if (empty($data['id'])) {
			$model = $this->getModel('company');
			if (!$model->deleteSecondaryLocation($data['identifier'])) {
				$this->setMessage(JText::sprintf('COM_JBUSINESSDIRECTORY_COMPANY_LOCATION_FAILED_DELETE', $model->getError()), 'warning');
			} elseif (!empty($model->getErrors())) {
				$implodeErrors = implode('<br />', $model->getErrors());
				$this->setMessage(JText::sprintf('COM_JBUSINESSDIRECTORY_CANCELED_WARNING', $implodeErrors), 'Warning');
			}
		}
	
		$app = JFactory::getApplication();
		$context = 'com_jbusinessdirectory.edit.company';
		$result = parent::cancel();
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
	 * save a record (and redirect to main page)
	 * @return void
	 */
	public function save($key = null, $urlVar = null) {
		
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		$app      = JFactory::getApplication();
		$model = $this->getModel('company');

		$jinput = $app->input;

		$data = $jinput->post->getArray();

		$data['description'] = $jinput->get('description', '', 'RAW');
		$data['custom_tab_content'] = $jinput->get('custom_tab_content', '', 'RAW');

		$context  = 'com_jbusinessdirectory.edit.company';
		$task     = $this->getTask();

		if ($task == 'save2copy') {
			$data['id'] = 0;
			foreach ($data['contact_id'] as &$contact_id) {
				$contact_id = '';
			}
		  
			foreach ($data['work_ids'] as &$work_id) {
				$work_id = '';
			}

			foreach ($data['testimonial_id'] as &$testimonial_id) {
				$testimonial_id = '';
			}
		  
			foreach ($data['service_id'] as &$service_id) {
				$service_id = '';
			}

			foreach ($data['zip_code_id'] as &$zip_code_id) {
				$zip_code_id = '';
			}
		}

		$recordId = $jinput->getInt('id');
		
		//save images
		$pictures					= array();
		$extraPictures			= array();
		foreach ($data as $key => $value) {
			if (strpos($key, 'picture_title') !== false
					||
					strpos($key, 'picture_info') !== false
					||
					strpos($key, 'picture_path') !== false
					||
					strpos($key, 'picture_enable') !== false
			) {
				foreach ($value as $k => $v) {
					if (!isset($pictures[$k])) {
						$pictures[$k] = array('picture_title' => '', 'picture_info'=>'', 'picture_path'=>'','picture_enable'=>1);
					}
					$pictures[$k][$key] = $v;
				}
			}

			if (strpos($key, 'image_title') !== false
					||
					strpos($key, 'image_info') !== false
					||
					strpos($key, 'image_path') !== false
					||
					strpos($key, 'image_enable') !== false
			) {
				foreach ($value as $k => $v) {
					if (!isset($extraPictures[$k])) {
						$extraPictures[$k] = array('image_title' => '', 'image_info'=>'', 'image_path'=>'','image_enable'=>1);
					}
					$extraPictures[$k][$key] = $v;
				}
			}
		}
		$data['pictures'] = $pictures;
		$data['extra_pictures'] = $extraPictures;
		
		if (!$model->save($data)) {
			// Save the data in the session.
			$app->setUserState('com_jbusinessdirectory.edit.company.data', $data);
			// Redirect back to the edit screen.
			$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'warning');
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId), false));
			
			return false;
		} elseif (!empty($model->getErrors())) {
			$implodeErrors = implode('<br />', $model->getErrors());
			$this->setMessage(JText::sprintf('COM_JBUSINESSDIRECTORY_SAVED_WARNING', $implodeErrors), 'Warning');
		} else {
			$this->setMessage(JText::_('COM_JBUSINESSDIRECTORY_COMPANY_SAVE_SUCCESS'));
		}
 
		// Redirect the user and adjust session state based on the chosen task.
		switch ($task) {
			case 'apply':
				$recordId = $model->getState("company.id");
				// Set the row data in the session.
				$this->holdEditId($context, $recordId);
				$app->setUserState('com_jbusinessdirectory.edit.company.data', null);
				// Redirect back to the edit screen.
				$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId), false));
				break;

			default:
				// Clear the row id and data in the session.
				$this->releaseEditId($context, $recordId);
				$app->setUserState('com_jbusinessdirectory.edit.company.data', null);
							
				// Redirect to the list screen.
				$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $this->getRedirectToListAppend(), false));
				break;
		}
	}
	
	public function listingautosave() {
		$app      = JFactory::getApplication();
		$model = $this->getModel('company');
		
		$jinput = $app->input;
		
		$data = $jinput->get->getArray();
		
		$data['description'] = $jinput->get('description', '', 'RAW');
		$data['custom_tab_content'] = $jinput->get('custom_tab_content', '', 'RAW');
		
		$context  = 'com_jbusinessdirectory.edit.company';
		
		//save images
		$pictures					= array();
		$extraPictures			= array();
		foreach ($data as $key => $value) {
			if (strpos($key, 'picture_title') !== false
				||
				strpos($key, 'picture_info') !== false
				||
				strpos($key, 'picture_path') !== false
				||
				strpos($key, 'picture_enable') !== false
				) {
				foreach ($value as $k => $v) {
					if (!isset($pictures[$k])) {
						$pictures[$k] = array('picture_title' => '', 'picture_info'=>'', 'picture_path'=>'','picture_enable'=>1);
					}
					$pictures[$k][$key] = $v;
				}
			}

			if (strpos($key, 'image_title') !== false
					||
					strpos($key, 'image_info') !== false
					||
					strpos($key, 'image_path') !== false
					||
					strpos($key, 'image_enable') !== false
			) {
				foreach ($value as $k => $v) {
					if (!isset($extraPictures[$k])) {
						$extraPictures[$k] = array('image_title' => '', 'image_info'=>'', 'image_path'=>'','image_enable'=>1);
					}
					$extraPictures[$k][$key] = $v;
				}
			}
		}
		$data['extra_pictures'] = $extraPictures;
		$data['pictures'] = $pictures;

		$id= $model->save($data);
		
		$errorFlag="0";
		$message= JText::_('COM_JBUSINESSDIRECTORY_COMPANY_SAVE_SUCCESS');
		

		$timezone = $jinput->getInt('user-timezone');
		$timezone = $timezone * -1;

		$response = array();
		$response["company_id"] = $id;
		$response["time"] = JBusinessUtil::getCurrentTime($timezone);
	   
		ob_clean();
		echo json_encode($response);
		
		exit;
	}
	
	public function saveLocation() {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$app      = JFactory::getApplication();
		$model = $this->getModel('company');
		$data = $app->input->post->getArray();
		
		if ($data["company_id"]==0) {
			$data["company_id"]=-1;
		}
		
		if (!($locationId = $model->saveLocation($data))) {
			// Save the data in the session.
			$app->setUserState('com_jbusinessdirectory.edit.companylocation.data', $data);
				
			// Redirect back to the edit screen.
			$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'warning');
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=company&tmpl=component&layout=locations&locationId='.$locationId, false));
				
			return false;
		} elseif (!empty($model->getErrors())) {
			$implodeErrors = implode('<br />', $model->getErrors());
			$this->setMessage(JText::sprintf('COM_JBUSINESSDIRECTORY_SAVED_WARNING', $implodeErrors), 'Warning');
		} else {
			$this->setMessage(JText::_('COM_JBUSINESSDIRECTORY_LOCATION_SAVE_SUCCESS'));
		}
		
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=company&tmpl=component&layout=locations&locationId='.$locationId, false));
	}
	
	public function deleteLocation() {
		$errorFlag = false;
		$locationId = JFactory::getApplication()->input->get('locationId');
		$model = $this->getModel('company');
		
		$result = $model->deleteLocation($locationId);
		$message="";
		
		echo '<?xml version="1.0" encoding="utf-8" ?>';
		echo '<category_statement>';
		echo '<answer error="'.(!$result ? "0" : "1").'" errorMessage="'.$message.'" locationId="'.$locationId.'"';
		echo '</category_statement>';
		echo '</xml>';
		exit;
	}
	
	
	public function extendPeriod() {
		$model = $this->getModel('Company');
		$data = JFactory::getApplication()->input->post->getArray();
		$model ->extendPeriod($data);
		$this->setMessage(JText::_('COM_JBUSINESSDIRECTORY_EXTENDED_NEW_ORDER_CREATED'));
		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view==company&layout=edit&id='.$data["id"], false));
	}
	
	public function aprove() {
		$data = JFactory::getApplication()->input->post->getArray();
		$model = $this->getModel('Company');
		$error= !$model->changeAprovalState(COMPANY_STATUS_APPROVED);
		
		$company = $model->getItem();
		
		EmailService::sendApprovalEmail($company);
		
		$cid   = $this->input->get('cid', array(), 'array');
		$ajax = $this->input->get('ajax');
		if (!empty($ajax)) {
			$response = array();
			$response["cid"] = $cid[0];
			$response["error"] = $error;
			
			echo json_encode($response);
			
			exit;
		}
		
		
		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=companies', false));
	}
	
	public function disaprove() {
		$data = JFactory::getApplication()->input->post->getArray();
		$model = $this->getModel('Company');
		$model->setState('company.id', $data['company_id']);
		$error= !$model ->changeAprovalState(COMPANY_STATUS_DISAPPROVED, $data["disapproval_text"]);

		$company = $model->getItem();
		EmailService::sendDisapprovalEmail($company);
		
		$cid   = $this->input->get('cid', array(), 'array');
		$ajax = $this->input->get('ajax');
		if (!empty($ajax)) {
			$response = array();
			$response["cid"] = $cid[0];
			$response["error"] = $error;
			
			echo json_encode($response);
			
			exit;
		}
		
		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=companies', false));
	}
	
	public function changeApprovalState() {
		$data = JFactory::getApplication()->input->post->getArray();
		$model = $this->getModel('Company');
		$status = $this->input->getString('status')=="true"?true:false;
		
		$company = $model->getItem();
		
		if ($status) {
			$error= !$model ->changeAprovalState(COMPANY_STATUS_APPROVED);
			EmailService::sendApprovalEmail($company);
		} else {
			$error= !$model ->changeAprovalState(COMPANY_STATUS_DISAPPROVED);
			EmailService::sendDisapprovalEmail($company);
		}
		
		$cid   = $this->input->get('cid', array(), 'array');
		$ajax = $this->input->get('ajax');
		if (!empty($ajax)) {
			$response = array();
			$response["cid"] = $cid[0];
			$response["error"] = $error;
			
			echo json_encode($response);
			
			exit;
		}
		
		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=companies', false));
	}
	
	
	public function aproveClaim() {
		$data = JFactory::getApplication()->input->post->getArray();
		$model = $this->getModel('Company');
		$model ->changeClaimAprovalState(1);
		$model ->changeAprovalState(COMPANY_STATUS_APPROVED);
		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=companies', false));
	}
	
	public function disaproveClaim() {
		$data = JFactory::getApplication()->input->post->getArray();
		$model = $this->getModel('Company');
		$model ->changeClaimAprovalState(-1);
		$model ->changeAprovalState(COMPANY_STATUS_APPROVED);
		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=companies', false));
	}

	public function resetClaim() {
		$data = JFactory::getApplication()->input->post->getArray();
		$model = $this->getModel('Company');
		$model ->changeClaimAprovalState(0);
		$model ->changeAprovalState(COMPANY_STATUS_CREATED);
		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=companies', false));
	}
	
	public function changeState() {
		$model = $this->getModel('Company');
		$msg ="";
		if (!$model->changeState()) {
			$msg = JText::_('LNG_ERROR_CHANGE_STATE');
		}
	
		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=companies', $msg));
	}
	
	/**
	 * Change featured state
	 *
	 * @return void
	 */
	public function changeFeaturedState() {
		$cid  = $this->input->post->get('cid', array(), 'array');
		
		if (!empty($cid)) {
			$model = $this->getModel('Company');
			$msg ="";
			if (!$model->changeFeaturedState($cid[0])) {
				$msg = JText::_('LNG_ERROR_CHANGE_STATE');
			}
		}
	
		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=companies', $msg));
	}

	/**
	 * Change recommended state
	 *
	 * @return void
	 */
	public function changeRecommendedState() {
		$cid  = $this->input->post->get('cid', array(), 'array');
		
		if (!empty($cid)) {
			$model = $this->getModel('Company');
			$msg ="";
			if (!$model->changeRecommendedState($cid[0])) {
				$msg = JText::_('LNG_ERROR_CHANGE_STATE');
			}
		}
	
		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=companies', $msg));
	}
	
	public function checkCompanyName() {
		$data = JFactory::getApplication()->input->post->getArray();
		$model = $this->getModel('company');
		$nr = $model->checkCompanyName(trim($data["companyName"]));
	
		$exists = $nr>0?1:0;
	
		echo '<?xml version="1.0" encoding="utf-8" ?>';
		echo '<company_statement>';
		echo '<answer exists="'.$exists.'"/>';
		echo '</company_statement>';
		echo '</xml>';
		exit;
	}
	
	/**
	 * Method to run batch operations.
	 *
	 * @param   object  $model  The model.
	 *
	 * @return  boolean   True if successful, false otherwise and internal error is set.
	 *
	 * @since   1.6
	 */
	public function batch($model = null) {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
	
		// Set the model
		$model = $this->getModel('Company', '', array());
	
		$vars = $this->input->post->get('batch', array(), 'array');
		$cid  = $this->input->post->get('cid', array(), 'array');
		
		// Attempt to run the batch operation.
		if ($model->batch($vars, $cid, null)) {
			$this->setMessage(JText::_('JLIB_APPLICATION_SUCCESS_BATCH'));
		} else {
			$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_FAILED', $model->getError()), 'warning');
		}
		
		// Preset the redirect
		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=companies' . $this->getRedirectToListAppend(), false));
	
		return parent::batch($model);
	}

	/**
	 * Method to retrieve attributes by ajax
	 */
	public function getAttributesAjax() {
		$jinput     = JFactory::getApplication()->input;
		$categoryId = $jinput->get('categoryId');
		$companyId  = $jinput->get('companyId');
		$packageId  = $jinput->get('packageId');

		$model  = $this->getModel('Company');
		$result = $model->getAttributesAjax($categoryId, $companyId, $packageId);

		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;
	}

	/**
     * Submit review for the profile
     *
     * @return void
     */
    function submitReview(){
        $model  = $this->getModel('Company');
        $result = $model->submitReview();
        
        if ($result) {
            $this->setMessage(JText::_('LNG_SUBMIT_REVIEW_SUCCESS'));
        } else {
            $this->setMessage(JText::_('LNG_SUBMIT_REVIEW_ERROR'));
        }
        
        $this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=companies' . $this->getRedirectToListAppend(), false));
    }
}
