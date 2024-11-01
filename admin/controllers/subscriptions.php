<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\Utilities\ArrayHelper;

/**
 * Subscriptions Controller
 */
class JBusinessDirectoryControllerSubscriptions extends JControllerLegacy {
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
	public function getModel($name = 'Subscriptions', $prefix = 'JBusinessDirectoryModel', $config = array('ignore_request' => true)) {
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	public function back() {
		$this->setRedirect('index.php?option=com_jbusinessdirectory');
	}

	/**
	 * Cancel subscription
	 *
	 * @return void
	 */
	public function cancelSubscription() {
		$app = JFactory::getApplication();

		$subscriptionId = $app->input->getInt('subscriptionId');
		try {
			SubscriptionService::cancelSubscription($subscriptionId);

			$this->setMessage(JText::_('LNG_SUBSCRIPTION_CANCELED'));
		} catch (Exception $e) {
			$this->setMessage($e->getMessage());
		}

		$this->setRedirect('index.php?option=com_jbusinessdirectory&view=subscriptions');
	}

	
}
