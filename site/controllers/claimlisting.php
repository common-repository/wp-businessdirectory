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
class JBusinessDirectoryControllerClaimListing extends JControllerForm {
	/**
	 * Dummy method to redirect back to standard controller
	 *
	 */
	public function display($cachable = false, $urlparams = false) {
		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=claimlisting', false));
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
	
		$companyId = JFactory::getApplication()->input->get('claim_listing_id');
		$model = $this->getModel('companies');
		$company = $model->getPlainCompany($companyId);
		
		$this->setRedirect(JBusinessUtil::getCompanyLink($company));

		return;
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
	 * Claim a listing function
	 */
	public function claimListing() {
		$app = JFactory::getApplication();
		$appSettings = JBusinessUtil::getApplicationSettings();
		$model = $this->getModel('companies');
		
		$post = JFactory::getApplication()->input->post->getArray();
		$companyId = JFactory::getApplication()->input->get('claim_listing_id');
		$company = $model->getPlainCompany($companyId);
		$ipAddress = $_SERVER['REMOTE_ADDR'];
		$post["ipAddress"] = $ipAddress;
		$post["companyId"] = $post["claim_listing_id"];
		
		$filterParam = "";
		$claimListingId = JFactory::getApplication()->input->get("claim_listing_id");
		if (!empty($claimListingId)) {
			$filterParam .="&claim_listing_id=".$claimListingId;
		}
		
		if ($appSettings->captcha) {
			$captchaAnswer = !empty($post['recaptcha_response_field'])?$post['recaptcha_response_field']:$post['g-recaptcha-response'];
			try {
				$namespace="jbusinessdirectory.contact";
				$captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));
				if (!$captcha->checkAnswer($captchaAnswer)) {
					$error = $captcha->getError();
					$app->setUserState('com_jbusinessdirectory.claim.company.data', $post);
					$this->setMessage("Captcha error!", 'warning');
					$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=claimlisting'.$filterParam, false));
					return;
				}
			} catch (Exception $e) {
				$error = $captcha->getError();
				$app->setUserState('com_jbusinessdirectory.claim.company.data', $post);
				$this->setMessage("Captcha error!", 'warning');
				$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=claimlisting'.$filterParam, false));
				return;
			}
		}
		
		if ($model->claimCompany($post)) {
			$this->setMessage(JText::_('LNG_CLAIM_SUCCESSFULLY'));
			EmailService::sendClaimEmail($company, $post);
			
			if (!empty($appSettings->business_usergroup)) {
				$userId =$post['userId'];
				if (!JUserHelper::addUserToGroup($userId, $appSettings->business_usergroup)) {
					JFactory::getApplication()->enqueueMessage(JText::_('LNG_USER_NOT_ASSOCIATED_WITH_GROUP'), 'warning');
				}
			}
			
			if ($appSettings->enable_packages) {
				$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=packages'.$filterParam, false));
			} else {
				$this->setRedirect(JBusinessUtil::getCompanyLink($company));
			}
		} else {
			$this->setMessage(JText::_('LNG_ERROR_CLAIMING_COMPANY'), 'warning');
			$this->setRedirect(JBusinessUtil::getCompanyLink($company));
		}

	}
}
