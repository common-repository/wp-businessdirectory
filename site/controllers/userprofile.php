<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

require_once BD_CLASSES_PATH . '/oauth/JBDAuthFactory.php';

class JBusinessDirectoryControllerUserProfile extends JControllerLegacy {
	public function __construct() {
		parent::__construct();
	
	}

	public function oauthCallback() {
		$app     = JFactory::getApplication();
		$session = JFactory::getSession();

		$type = $app->input->get('type');
		$redirectUrl = $app->input->get('redirect', null, 'RAW');

		if (empty($redirectUrl) && isset($_SESSION['oauth2redirect'])) {
			$redirectUrl = $_SESSION['oauth2redirect'];
		}

		if (!JBDAuthFactory::isOAuthProviderValid($type)) {
			$this->setError(JText::_('LNG_ERROR'));
			$this->setMessage(JText::_('LNG_INVALID_PROVIDER_TYPE'), 'error');

			$this->oauthRedirect($redirectUrl);
			return;
		}

		$state = $app->input->get('state');
		$auth  = JBDAuthFactory::create($type);

		// if initialization fails, return
		try {
			$auth->initializeProvider();
		} catch (Exception $e) {
			$this->setError(JText::_('LNG_ERROR'));
			$this->setMessage($e->getMessage(), 'error');
			$this->oauthRedirect($redirectUrl);

			return;
		}

		$error = $app->input->get('error', null);
		if (!empty($error)) {
			$this->setError(JText::_('LNG_ERROR'));
			$this->setMessage($app->input->get('error_description', null, 'RAW'), 'error');
			$redirectUrl = !empty($redirectUrl) ? $redirectUrl : JBusinessUtil::getWebsiteURL(true).('index.php?option=com_jbusinessdirectory');
			$this->oauthRedirect($redirectUrl);
			return;
		}

		$code = $app->input->get('code');

		if (empty($code)) {
			$tmpState = bin2hex(random_bytes(16)); // new random string to use as the state parameter
			$authUrl  = $auth->getAuthorizationUrl(array(
				'state' => $tmpState
			));

			$_SESSION['oauth2state'] = $auth->getState();
			$_SESSION['oauth2redirect'] = $redirectUrl;

			$this->setRedirect($authUrl);
			return;
		}
		// Check given state against previously stored one to mitigate CSRF attack
		elseif (empty($state) || ($state !== $_SESSION['oauth2state'])) {
			unset($_SESSION['oauth2state']);
			unset($_SESSION['oauth2redirect']);
			$this->setError(JText::_('LNG_ERROR'));
			$this->setMessage(JText::_('LNG_INVALID_STATE'), 'error');

			$this->oauthRedirect($redirectUrl);
			return;
		}

		$token = $auth->getToken($code);
		$user  = $auth->getUserDetails($token);

		try {
			$result = $auth->loginUser($user->toArray(), $token->getToken(), JBDAuthFactory::getOAuthProviderCode($type));
		} catch (Exception $e) {
			$this->setError(JText::_('LNG_ERROR'));
			$this->setMessage($e->getMessage(), 'error');
		}

		$this->setMessage(JText::_('LNG_LOGIN_SUCCESSFUL'), 'success');
		$this->oauthRedirect($redirectUrl);
	}

	public function oauthRedirect($redirectUrl) {
		$this->setRedirect($redirectUrl);
	}
}
