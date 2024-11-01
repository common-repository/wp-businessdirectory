<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JBusinessDirectoryControllerBusinessUser extends JControllerLegacy {
	public $appSettings;

	public function __construct() {
		parent::__construct();
		$this->appSettings = JBusinessUtil::getApplicationSettings();
	}

	public function checkUser() {
		$user = JBusinessUtil::getUser();
		$filterParam = "";
		$filter_package     = JFactory::getApplication()->input->getInt("filter_package");
		$packageType        = JFactory::getApplication()->input->getInt("packageType",0);
		$claimListingId     = JFactory::getApplication()->input->getInt("claim_listing_id");
		$editorListingId     = JFactory::getApplication()->input->getInt("editor_listing_id");
		$serviceType        = JFactory::getApplication()->input->getInt("serviceType");
		$orderId            = JFactory::getApplication()->input->getInt("orderId");

		if (!empty($filter_package)) {			
			$filterParam .="&filter_package=".$filter_package;
		}

		if (!empty($claimListingId)) {
			$filterParam .="&claim_listing_id=".$claimListingId;
		}

		if (!empty($serviceType)) {
			$filterParam .="&serviceType=".$serviceType;
		}

		if (!empty($editorListingId)) {
			$filterParam .="&editor_listing_id=".$editorListingId;
		}

		if ($user->ID == 0 && ($this->appSettings->allow_user_creation==0 || $packageType == PACKAGE_TYPE_USER || !empty($serviceType) || !empty($claimListingId))) {
			$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=businessuser'.$filterParam, false));
		}elseif(!empty($orderId)){
			$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&task=billingdetails.checkBillingDetails&orderId='.$orderId, false));
		} else {
			if (!empty($claimListingId)) {
				$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=claimlisting'.$filterParam, false));
			}else if(!empty($editorListingId)) {
				$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&task=managecompany.acceptInvitation'.$filterParam, false));
			}else if(!empty($filter_package) && empty($claimListingId)){
				if($packageType == PACKAGE_TYPE_USER) {
					$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&task=billingdetails.checkBillingDetails'.$filterParam, false));
				} else {
					$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompany&showSteps=true&layout=edit'.$filterParam, false));
				}
			} elseif (!empty($serviceType)) {
				$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view='.$serviceType.'&layout=edit', false));
			} 
		}

		return;
	}
	
	public function loginUser() {
		$this->checkToken('post');

		$app    = JFactory::getApplication();
		$input  = $app->input;
		$method = $input->getMethod();

		$filterParam = "";
		$filter_package = $input->get("filter_package");
		$packageType = $input->get("packageType");
		$claimListingId = $input->get("claim_listing_id");
		$editorListingId = $input->getInt("editor_listing_id");
		$serviceType = $input->get("serviceType");
		$orderId = $app->getUserState("user.order_id");
		$companyId = $app->getUserState("user.company_id");

		if (!empty($filter_package)) {
			$filterParam .="&filter_package=".$filter_package;
		}

		if (!empty($claimListingId)) {
			$filterParam .="&claim_listing_id=".$claimListingId;
		}

		if (!empty($serviceType)) {
			$filterParam .="&serviceType=".$serviceType;
		}

		if (!empty($orderId)) {
			$filterParam .="&orderId=".$orderId;
		}
		
		if (!empty($editorListingId)) {
			$filterParam .="&editor_listing_id=".$editorListingId;
		}

		// Populate the data array:
		$credentials = array();
		$credentials['user_login']  = $input->$method->get('display_name', '', 'USERNAME');
		$credentials['log']  = $input->$method->get('display_name', '', 'USERNAME');
		$credentials['user_password']  = $input->$method->get('password', '', 'RAW');
		$credentials['remember'] = true;
		
		// Perform the log in.
		if (true !== UserService::loginUserWithCredentials($credentials)) {
			// Login failed !
			$this->setMessage("Could not login user", 'warning');
			$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&showOnlyLogin=1&view=businessuser'.$filterParam, false));
			return;
		}
		
		if (!empty($claimListingId)) {
			$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=claimlisting'.$filterParam, false));
		}else if(!empty($editorListingId)) {
			$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&task=managecompany.acceptInvitation'.$filterParam, false));
		}else if(!empty($filter_package) && empty($claimListingId)){
			if($packageType == PACKAGE_TYPE_USER) {
				$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&task=billingdetails.checkBillingDetails'.$filterParam, false));
			} else {
				$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompany&showSteps=true&layout=edit'.$filterParam, false));
			}		
		} elseif (!empty($serviceType)) {
			$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view='.$serviceType.'&layout=edit', false));
		} elseif (!empty($companyId)) {
			$user = JBusinessUtil::getUser();
			
			if(!empty($companyId )){
				if(JBusinessUtil::updateCompanyUser($companyId, $user->ID)){
					$app->setUserState('user.order_id',null);
					$app->setUserState('user.company_id',null);
				}
				if (!empty($orderId)) {
					$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=billingdetails&layout=edit&'.$filterParam, false));
				} else{
					$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=listingconfirmation&listing_id='.$companyId, false));
				}
			}else{
				$this->setMessage("Could not update listing user", 'warning');
				$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanies'.$filterParam, false));
			}
		}
	}

	public function addUser() {
		// Check for request forgeries.
		//$this->checkToken();
		
		$app    = JFactory::getApplication();
		$input  = $app->input;
		$method = $input->getMethod();
		
		$filterParam = "";
		$filter_package = $input->get("filter_package");
		$packageType = $input->get("packageType");
		$claimListingId = $input->get("claim_listing_id");
		$editorListingId = $input->getInt("editor_listing_id");
		$serviceType        = $input->get("serviceType");
		$orderId = $app->getUserState("user.order_id");
		$companyId = $app->getUserState("user.company_id");

		if (!empty($filter_package)) {			
			$filterParam .="&filter_package=".$filter_package;
		}
		
		if (!empty($claimListingId)) {
			$filterParam .="&claim_listing_id=".$claimListingId;
		}

		if (!empty($serviceType)) {
			$filterParam .="&serviceType=".$serviceType;
		}

		if (!empty($orderId)) {
			$filterParam .="&orderId=".$orderId;
		}
		
		if (!empty($editorListingId)) {
			$filterParam .="&editor_listing_id=".$editorListingId;
		}

		$appSettings = JBusinessUtil::getApplicationSettings();
		$data = JFactory::getApplication()->input->post->getArray();
		if ($appSettings->captcha) {
			$captchaAnswer = !empty($data['recaptcha_response_field'])?$data['recaptcha_response_field']:$data['g-recaptcha-response'];
			try {
				$namespace="jbusinessdirectory.contact";
				$captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));
				if (!$captcha->checkAnswer($captchaAnswer)) {
					$error = $captcha->getError();
					$this->setMessage("Captcha error!", 'warning');
					$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=businessuser'.$filterParam, false));
					return;
				}
			} catch (Exception $e) {
				$error = $captcha->getError();
				$this->setMessage("Captcha error!", 'warning');
				$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=businessuser'.$filterParam, false));
				return;
			}
		}
		
		$app   = JFactory::getApplication();
		
		// Attempt to save the data.
		$return = UserService::addNewUser($data);
		
		// Check for errors. 
		// TODO enable check again - for now it is causing some issues
		if ($return === false) {
			
			// Redirect back to the edit screen.
			$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=businessuser'.$filterParam, false));
			
			return false;
		}
				
		// Flush the data from the session.
		$app->setUserState('com_users.registration.data', null);
		
		if(is_numeric($return)) {
			
			$userId = $return;

			// if the user select the business owner checkbox it will be associated with the business owner usergroup
			$isBusinessOwner = $app->input->get("business_owner");
			if($isBusinessOwner){
				if (!empty($userId) && empty($this->appSettings->business_usergroup)) {
					JUserHelper::addUserToGroup($userId, $this->appSettings->business_usergroup);
				}
			}else{
				//Add default usergroup to the user
				if (!empty($userId)) {
					JUserHelper::addUserToGroup($userId, $this->appSettings->usergroup);
				}
			}

			UserService::loginUser($userId);

			if(!empty($data["jform"]) && !empty($data["jform"]["com_fields"])){
				$data["com_fields"]=$data["jform"]["com_fields"];
				$user = JBusinessUtil::getUser();			
				JFactory::getApplication()->triggerEvent('onContentAfterSave', ['com_users.registration', $user, true, $data]);
			}
			
			// Redirect url provided only from user module registration
			if(!empty($app->input->get("registration_redirect"))) {
				$this->setRedirect(JRoute::_(base64_decode($app->input->get("registration_redirect")), false));
			} else if (!empty($claimListingId)) {
				$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=claimlisting'.$filterParam, false));
			}else if(!empty($editorListingId)) {
				$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&task=managecompany.acceptInvitation'.$filterParam, false));
			}else if(!empty($filter_package) && empty($claimListingId)){
				if($packageType == PACKAGE_TYPE_USER) {
					$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&task=billingdetails.checkBillingDetails'.$filterParam, false));
				} else {
					$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompany&showSteps=true&layout=edit'.$filterParam, false));
				}		
			} elseif (!empty($serviceType)) {
				$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view='.$serviceType.'&layout=edit', false));
			} elseif (!empty($companyId)) {
				$user = JBusinessUtil::getUser();
				
				if(!empty($companyId )){
					if(JBusinessUtil::updateCompanyUser($companyId, $user->ID)){
						$app->setUserState('user.order_id',null);
						$app->setUserState('user.company_id',null);
					}
					if (!empty($orderId)) {
						$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=billingdetails&layout=edit&'.$filterParam, false));
					} else{
						$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=listingconfirmation&listing_id='.$companyId, false));
					}
				}else{
					$this->setMessage("Could not update listing user", 'warning');
					$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanies'.$filterParam, false));
				}
			}else{
				
				if($isBusinessOwner){
					$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=useroptions', false));
				}else{
					$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=userdashboard', false));
				}
				
			}
		}

		return true;
	}



	/**
	 * Verify the email address
	 *
	 * @return void
	 */
	public function verifyEmail(){

		$model = $this->getModel("businessuser");	
		
		if($model->verifyEmail()){
			$this->setMessage(JText::_('LNG_EMAIL_VERIFIED_SUCCESFULLY'));
		}else{
			$this->setMessage(JText::_('LNG_EMAIL_ACVITATION_CODE_INVALID'));
		}

		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=useroptions', false));

		return true;
	}

	/**
	 * Resend the verification email
	 *
	 * @return void
	 */
	public function resendVerficationEmail(){
		$model = $this->getModel("businessuser");
		
		if($model->resendVerificationEmail()){ 
			$this->setMessage(JText::_('LNG_EMAIL_VERIFICATION_SENT'));
		}else{
			$this->setMessage(JText::_('LNG_EMAIL_VERIFICATION_NOT_SENT'));
		}

		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=useroptions', false));

		return true;
	}

	public function checkUserByUsernameAjax(){
		$input  = JFactory::getApplication()->input;
		$display_name = $input->getString('display_name');

		$model = $this->getModel("businessuser");
		$result = $model->getUserByUsername($display_name);
		$response = new stdClass;
		$response->message = '';
		if($result>0){
			$response->status = $result;
			$response->message = JText::_('LNG_USERNAME_ALREADY_EXIST');
		}

		// Send as JSON
		header("Content-Type: application/json", true);
		echo json_encode($response);
		exit;
	}
	
	public function checkUserByEmailAjax(){
		$input  = JFactory::getApplication()->input;
		$email = $input->getString('email');
		
		$model = $this->getModel("businessuser");
		$result = $model->getUserByEmail($email);
		$response = new stdClass;
		$response->message = '';
		if($result>0){
			$response->status = $result;
			$response->message = JText::_('LNG_EMAIL_ALREADY_EXIST');
		}

		// Send as JSON
		header("Content-Type: application/json", true);
		echo json_encode($response);
		exit;
	}

	public function checkUserLoginCredentials(){
		$input  = JFactory::getApplication()->input;
		$email = $input->getString('email');
		$password = $input->getString('password');
		$user = UserService::getUserByEmail($email);
		$model = $this->getModel("businessuser");
		$result = $model->getUserByEmail($email);
		$response = new stdClass;

		if($result>0){ // user exists
			$credentials = array();
			$credentials['display_name']  = $user->display_name;
			$credentials['password']  = $password;
			$loginSuccess = UserService::loginUserWithCredentials($credentials);
			if($loginSuccess) {
				$response->message = JText::_('LNG_LOGIN_SUCCESSFUL');
				$response->status = 1;
			} else {
				$response->message = JText::_('LNG_USER_DOESNT_MATCH');
				$response->status = 0;
			}
		} else {
			$response->message = JText::_('LNG_USER_NOT_EXIST');
			$response->status = 2;
		}

		// Send as JSON
		header("Content-Type: application/json", true);
		echo json_encode($response);
		exit;
	}
}
