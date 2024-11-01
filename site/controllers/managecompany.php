<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

JTable::addIncludePath(DS.'components'.'com_jbusinessdirectory'.DS.'models');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'controllers'.DS.'company.php');

class JBusinessDirectoryControllerManageCompany extends JBusinessDirectoryControllerCompany {
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	 
	public function __construct() {
		parent::__construct();
		$this->registerTask('duplicate', 'save');
	}
	
	/**
	 * Dummy method to redirect back to standard controller
	 *
	 */
	public function display($cachable = false, $urlparams = false) {
		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanies', false));
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
			$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompany&id=0'. $this->getRedirectToItemAppend(), false));
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
			$model = $this->getModel('managecompany');
			if (!$model->deleteSecondaryLocation($data['identifier'])) {
				$this->setMessage(JText::sprintf('COM_JBUSINESSDIRECTORY_COMPANY_LOCATION_FAILED_DELETE', $model->getError()), 'warning');
			}
		}
	
		$app = JFactory::getApplication();
		$context = 'com_jbusinessdirectory.edit.company';
		$result = parent::cancel();

		return $result;
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
		JSession::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));
		
		$app = JFactory::getApplication();
		$result = parent::edit();

		return true;
	}

	/**
	 * Redirect to add a new listing page
	 *
	 * @return void
	 */ 
	public function addListing(){

		$filterParam = "";
		$filter_package = JFactory::getApplication()->input->get("filter_package");

		if (!empty($filter_package)) {
			$filterParam .="&filter_package=".$filter_package;
		}
		
		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompany&showSteps=true&layout=edit'.$filterParam, false));

		return;
	}

	/**
	 * Create order for the claimed listing
	 *
	 * @return void
	 */
	public function createOrder() {
		$packageId = JFactory::getApplication()->input->get("filter_package");
		$claimListingId = JFactory::getApplication()->input->get("claim_listing_id");
		$model = $this->getModel('managecompany');

		$model->setPackageId($claimListingId, $packageId);
		$orderId =  $model->createOrder($claimListingId, $packageId, UPDATE_TYPE_NEW);
		
		$menuItemId = JBusinessUtil::getActiveMenuItem();
		
		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=billingdetails&layout=edit&orderId='.$orderId.'&companyId='.$claimListingId.$menuItemId, false));
	}
	
	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	public function save($key = null, $urlVar = null) {
	
		// Check for request forgeries.
		//JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$jinput = JFactory::getApplication()->input;
		$appSettings = JBusinessUtil::getApplicationSettings();
		$app      = JFactory::getApplication();
		$model = $this->getModel('managecompany');
		$data = $jinput->post->getArray();
		$data['description'] = $jinput->get('description', '', 'RAW');
		$data['custom_tab_content'] = $jinput->get('custom_tab_content', '', 'RAW');
		$context  = 'com_jbusinessdirectory.edit.managecompany';
		$task     = $this->getTask();
		$listingId = $recordId = $jinput->getInt('id');
		
		if($task == 'duplicate') {
			$data['id'] = 0;
			$listingId = $recordId = $jinput->set('id',0);
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

		if (!empty($data['website'])) {
			if (!preg_match("~^(?:f|ht)tps?://~i", $data['website'])) {
				$data['website'] = "http://" . $data['website'];
			}
			$data['website'] =  str_replace(array('\'', '"'), '', $data['website']);
		}
		if (!empty($data['facebook'])) {
			if (!preg_match("~^(?:f|ht)tps?://~i", $data['facebook'])) {
				$data['facebook'] = "http://" . $data['facebook'];
			}
			$data['facebook'] =  str_replace(array('\'', '"'), '', $data['facebook']);
		}
		if (!empty($data['twitter'])) {
			if (!preg_match("~^(?:f|ht)tps?://~i", $data['twitter'])) {
				$data['twitter'] = "http://" . $data['twitter'];
			}
			$data['twitter'] =  str_replace(array('\'', '"'), '', $data['twitter']);
		}
		
		if (!empty($data['linkedin'])) {
			if (!preg_match("~^(?:f|ht)tps?://~i", $data['linkedin'])) {
				$data['linkedin'] = "http://" . $data['linkedin'];
			}
			$data['linkedin'] =  str_replace(array('\'', '"'), '', $data['linkedin']);
		}
		
		if (!empty($data['youtube'])) {
			if (!preg_match("~^(?:f|ht)tps?://~i", $data['youtube'])) {
				$data['youtube'] = "http://" . $data['youtube'];
			}
			$data['youtube'] =  str_replace(array('\'', '"'), '', $data['youtube']);
		}
		if (!empty($data['instagram'])) {
			if (!preg_match("~^(?:f|ht)tps?://~i", $data['instagram'])) {
				$data['instagram'] = "http://" . $data['instagram'];
			}
			$data['instagram'] =  str_replace(array('\'', '"'), '', $data['instagram']);
		}
		if (!empty($data['pinterest'])) {
			if (!preg_match("~^(?:f|ht)tps?://~i", $data['pinterest'])) {
				$data['pinterest'] = "http://" . $data['pinterest'];
			}
			$data['pinterest'] =  str_replace(array('\'', '"'), '', $data['pinterest']);
		}
		
		//save images
		$pictures = array();
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
			$app->setUserState('com_jbusinessdirectory.edit.managecompany.data', $data);
				
			// Redirect back to the edit screen.
			$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'warning');
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId), false));
				
			return false;
		} elseif (!empty($model->getErrors())){
            $implodeErrors = implode('<br />', $model->getErrors());
            $this->setMessage(JText::sprintf( 'COM_JBUSINESSDIRECTORY_SAVED_WARNING',$implodeErrors),'Warning');
        } else {
            $this->setMessage(JText::_('COM_JBUSINESSDIRECTORY_COMPANY_SAVE_SUCCESS'));
		}
	
		// Redirect the user and adjust session state based on the chosen task.
		switch ($task) {
			case 'apply':
				$recordId = $model->getState("company.id");
				// Set the row data in the session.
				$this->holdEditId($context, $recordId);
				$app->setUserState('com_jbusinessdirectory.edit.managecompany.data', null);
				// Redirect back to the edit screen.
				$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId), false));
				break;
			
			case 'duplicate':
					$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $this->getRedirectToListAppend(), false));
				break;
			default:
				// Clear the row id and data in the session.
				$this->releaseEditId($context, $recordId);
				$app->setUserState('com_jbusinessdirectory.edit.managecompany.data', null);
					
				$redirect = $model->getState()->get("company.redirect.payment");
				$orderId = $model->getState()->get("company.redirect.orderId");
				
				$isNew = $model->getState()->get("company.isNew");

				$createdUserId = $model->getState('created.user.id',0);
				if(!empty($createdUserId)){
					//login user
					UserService::loginUser($createdUserId);
                }

				$activeMenu = JFactory::getApplication()->getMenu()->getActive();
				$menuId="";
				if (isset($activeMenu)) {
					$menuId = "&Itemid=".$activeMenu->id;
				}

				$onlyContribute = $data["only_contribute"];
				if($isNew && (!empty($onlyContribute) || (empty($orderId) && $appSettings->allow_user_creation))){
					if(empty($listingId)){
						$this->setMessage("");
						$listingId = $model->getState("company.id");
					}

					$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=listingconfirmation&listing_id='.$listingId.'&only_contribute='.$onlyContribute.'&user_created='.$createdUserId.$menuId), false);
					return;
				}

				$user = JBusinessUtil::getUser();

				//if there is no user created and we need to create one
				if(empty($createdUserId) && empty($user->ID)){
					if(!empty($orderId)){
						$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&task=businessuser.checkuser&orderId='.$orderId.$menuId, false));
					}else{
						$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&task=businessuser.checkuser'.$menuId, false));
					}
					return;
				}

				if ($redirect=="1") {
					$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=billingdetails&layout=edit&orderId='.$orderId.'&userCreated='.$createdUserId.$menuId, false));
					return;
				} 
				
				$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $this->getRedirectToListAppend(), false));
				
				break;
		}
	}

	public function listingautosave() {
		$app      = JFactory::getApplication();
		$model = $this->getModel('managecompany');
		
		$jinput = $app->input;
		
		$data = $jinput->get->getArray();
		
		$data['description'] = $jinput->get('description', '', 'RAW');
		$data['custom_tab_content'] = $jinput->get('custom_tab_content', '', 'RAW');
		
		$context  = 'com_jbusinessdirectory.edit.company';
		
		//save images
		$pictures					= array();
		$extraPictures				= array();
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
		$data['pictures'] 		= $pictures;
		$data['extra_pictures'] = $extraPictures;
		
		$data["autosave"] = 1;
		
		$id= $model->save($data);
		
		$errorFlag="0";
		$message= JText::_('COM_JBUSINESSDIRECTORY_COMPANY_SAVE_SUCCESS');

        $timezone = $jinput->getInt('user-timezone');
        $timezone = $timezone * -1;


		// This method always sends a JSON response
		$app = JFactory::getApplication();
		$app->mimeType = 'application/json';

		// clean warnings
		ob_end_clean();

		// Send the JSON response.
		$app->setHeader('Content-Type', $app->mimeType . '; charset=' . $app->charSet);
		$app->sendHeaders();

        $response = array();
        $response["company_id"] = $id;
        $response["time"] = JBusinessUtil::getCurrentTime($timezone);
		
		echo json_encode($response);
		
		$app->close();
	}
	
	public function saveLocation() {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$app      = JFactory::getApplication();
		$model = $this->getModel('managecompany');
		$data = JFactory::getApplication()->input->post->getArray();
		;

		if ($data["company_id"]==0) {
			$data["company_id"]=-1;
		}
	
		if (!($locationId = $model->saveLocation($data))) {
			// Save the data in the session.
			$app->setUserState('com_jbusinessdirectory.edit.companylocation.data', $data);
	
			// Redirect back to the edit screen.
			$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'warning');
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&tmpl=component&layout=locations&view=managecompany'. $this->getRedirectToItemAppend($recordId).'&locationId='.$locationId, false));
	
			return false;
		} elseif (!empty($model->getErrors())){
            $implodeErrors = implode('<br />', $model->getErrors());
            $this->setMessage(JText::sprintf( 'COM_JBUSINESSDIRECTORY_SAVED_WARNING',$implodeErrors),'Warning');
        } else {
            $this->setMessage(JText::_('COM_JBUSINESSDIRECTORY_LOCATION_SAVE_SUCCESS'));
        }
	
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&tmpl=component&layout=locations&view=managecompany' . $this->getRedirectToListAppend().'&locationId='.$locationId, false));
	}
	
	public function deleteLocation() {
		$errorFlag = false;
		$locationId =JFactory::getApplication()->input->get('locationId');
		$model = $this->getModel('managecompany');
	
		$result = $model->deleteLocation($locationId);
		$message="";
	
		echo '<?xml version="1.0" encoding="utf-8" ?>';
		echo '<category_statement>';
		echo '<answer error="'.(!$result ? "0" : "1").'" errorMessage="'.$message.'" locationId="'.$locationId.'"';
		echo '</category_statement>';
		echo '</xml>';
		exit;
	}
	
	public function changeState() {
		$model = $this->getModel('ManageCompany');
		$msg ="";
		if (!$model->changeState()) {
			$msg = JText::_('LNG_ERROR_CHANGE_STATE');
		}
	
		$this->setMessage($msg);
		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanies', false));
	}
	
	/**
	 * Method to retrieve attributes by ajax
	 */
	public function getAttributesAjax() {
		$categoryId = JFactory::getApplication()->input->get('categoryId');
		$companyId = JFactory::getApplication()->input->get('companyId');
		$packageId = JFactory::getApplication()->input->get('packageId');

		$model = $this->getModel('ManageCompany');
		$result = $model->getAttributesAjax($categoryId, $companyId, $packageId);

		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;
	}

	/**
	 * Extend listing period
	 *
	 * @return void
	 */
	public function extendPeriod() {
		$model = $this->getModel('ManageCompany');
		$data = JFactory::getApplication()->input->post->getArray();
		$model ->extendPeriod($data);
		$this->setMessage(JText::_('COM_JBUSINESSDIRECTORY_EXTENDED_NEW_ORDER_CREATED'));
		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view==managecompany&layout=edit&id='.$data["id"], false));
	}

	/**
	 * Send an invitation email
	 *
	 * @return void
	 */
	public function sendEditorInvitationAjax(){
		$input = JFactory::getApplication()->input;
		$companyId = $input->getInt('companyId');
		$email = $input->getString('email');

		$company = JBusinessUtil::getCompany($companyId);
		$link = JRoute::_("index.php?index.php?option=com_jbusinessdirectory&task==managecompany.acceptinvitation&email=".$email."&editor_listing_id=".$companyId, false);
		$link = "<a href='$link'>$link</a>";
		$result = EmailService::sendEditorInvitation($company, $email, $link);

		$response          = new stdClass();
		$response->data    = null;

		if($result){
			$response->status  = RESPONSE_STATUS_SUCCESS;
			$response->message = JText::_("LNG_INVITATION_SENT_SUCCESFULLY");
		}else{
			$response->status  = RESPONSE_STATUS_ERROR;
			$response->message = JText::_("LNG_INVITATION_SENT_FAILURE");
		}

		JBusinessUtil::sendJsonResponse($response, RESPONSE_STATUS_SUCCESS, '');
	}

	/**
	 * Check if the user has an account or accept the invitation
	 *
	 * @return void
	 */
	public function acceptInvitation(){
		$user = JBusinessUtil::getUser();

		$input = JFactory::getApplication()->input;
		$companyId = $input->getInt('editor_listing_id');
		$email = $input->getString('email');

		//if there is no user created and we need to create one
		if(empty($user->ID)){
			$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&task=businessuser.checkuser&editor_listing_id='.$companyId, false));
		}else{
			$model = $this->getModel('ManageCompany');
			if(!empty($companyId)){
				if($model->addCompanyEditor($companyId, $user->ID) !== $user->ID){
					$company = JBusinessUtil::getCompany($companyId);
					$message = JText::sprintf(JText::_('LNG_EDITOR_INVITATION_ACCEPTED_MESSAGE'),$email, $company->name);
					EmailService::sendUserMessage(JText::_('LNG_EDITOR_INVITATION_ACCEPTED_SUBJECT'), $message, $user->ID, $company->email);
					$this->setMessage(JText::_('LNG_EDITOR_ASSIGNED_SUCCESSFULLY'));
				}else{
				 	$this->setMessage(JText::_('LNG_EDITOR_ALREADY_ASSIGNED'));				
				}
			}else{
				$this->setMessage(JText::_('LNG_EDITOR_ASSIGN_ERROR'));
			}
			$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=useroptions', false));
		}

		return;
	}	

	/**
     * Start the profile trial period
     *
     * @return void
     */
    public function startTrial(){
        $input = JFactory::getApplication()->input;
        $companyId = $input->getInt('id');

        $model = $this->getModel('ManageCompany');
        $model->startTrial($companyId);

        $this->setMessage(JText::_('LNG_TRIAL_PERIOD_HAS_STARTED'));
        $this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanies'));
    }
}
