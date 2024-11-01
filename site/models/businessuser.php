<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\MVC\Model\FormModel;

JTable::addIncludePath(DS.'components'.'com_jbusinessdirectory'.DS.'tables');

class JBusinessDirectoryModelBusinessUser extends FormModel {
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * Populate state
	 * @param unknown_type $ordering
	 * @param unknown_type $direction
	 */
	protected function populateState($ordering = null, $direction = null) {
		$app = JFactory::getApplication('administrator');
	}
	
	
	public function loginUser() {
		$app    = JFactory::getApplication();
		$input  = $app->input;
		$method = $input->getMethod();
		
		// Populate the data array:
		$data = array();
		
		$data['return']    = base64_decode($app->input->post->get('return', '', 'BASE64'));
		$data['display_name']  = $input->$method->get('display_name', '', 'USERNAME');
		$data['password']  = $input->$method->get('password', '', 'RAW');
		$data['secretkey'] = $input->$method->get('secretkey', '', 'RAW');
		
		// Check for a simple menu item id
		if (is_numeric($data['return'])) {
			if (JLanguageMultilang::isEnabled()) {
				$db = JFactory::getDbo();
				$query = $db->getQuery(true)
				->select('language')
				->from($db->quoteName('#__menu'))
				->where('client_id = 0')
				->where('id =' . $data['return']);
				
				$db->setQuery($query);
				
				try {
					$language = $db->loadResult();
				} catch (RuntimeException $e) {
					return;
				}
				
				if ($language !== '*') {
					$lang = '&lang=' . $language;
				} else {
					$lang = '';
				}
			} else {
				$lang = '';
			}
			
			$data['return'] = 'index.php?Itemid=' . $data['return'] . $lang;
		} else {
			// Don't redirect to an external URL.
			if (!JUri::isInternal($data['return'])) {
				$data['return'] = '';
			}
		}
		
		// Set the return URL if empty.
		if (empty($data['return'])) {
			//$data['return'] = 'index.php?option=com_users&view=profile';
		}
		
		// Set the return URL in the user state to allow modification by plugins
		$app->setUserState('users.login.form.return', $data['return']);
		
		// Get the log in options.
		$options = array();
		$options['remember'] = $this->input->getBool('remember', false);
		$options['return']   = $data['return'];
		
		// Get the log in credentials.
		$credentials = array();
		$credentials['display_name']  = $data['display_name'];
		$credentials['password']  = $data['password'];
		$credentials['secretkey'] = $data['secretkey'];
		
		// Perform the log in.
		if (true !== $app->login($credentials, $options)) {
			// Login failed !
			// Clear user name, password and secret key before sending the login form back to the user.
			$data['remember'] = (int) $options['remember'];
			$data['display_name'] = '';
			$data['password'] = '';
			$data['secretkey'] = '';
			$app->setUserState('users.login.form.data', $data);
			return false;
		}
		
		// Success
		if ($options['remember'] == true) {
			$app->setUserState('rememberLogin', true);
		}
		
		$app->setUserState('users.login.form.data', array());
		return true;
	}

	/**
	 * Verify the email address
	 *
	 * @return void
	 */
	public function verifyEmail(){
		$app    = JFactory::getApplication();
		$input  = $app->input;
		$activationCode = $input->getString("activation_token");
		
		$userProfileTable = JTable::getInstance("UserProfile", "JTable");
		$userProfile = $userProfileTable->getUserByActivationCode($activationCode);
		
		if(empty($userProfile)){
			return false;
		}

		$userProfile->verified = 1;

		if (!$userProfileTable->bind($userProfile)) {
			return false;
		}

		if (!$userProfileTable->store()) {
			return false;
		}

		return true;
	}

	/**
	 * Resend the verification email
	 *
	 * @return void
	 */
	public function resendVerificationEmail(){
		
		$user = JBusinessUtil::getUser();
		$result = UserService::sendActivationEmail($user->ID);
		
		return $result;
	}

	public function getUserByUsername($display_name){

		$userProfileTable = JTable::getInstance("UserProfile", "JTable");
		$result = $userProfileTable->getUserByUsername($display_name);

		return $result;
	}

	public function getUserByEmail($email){

		$userProfileTable = JTable::getInstance("UserProfile", "JTable");
		$result = $userProfileTable->getUserByEmail($email);

		return $result;
	}

	public function getForm($data = [], $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_users.registration', 'registration', ['control' => 'jform', 'load_data' => $loadData]);

        if (empty($form)) {
            return false;
        }

        return $form;
    }

}
