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

class JBusinessDirectoryControllerManageCompanyArticles extends JControllerLegacy {
	/**
	 * Display the view
	 *
	 * @param   boolean			If true, the view output will be cached
	 * @param   array  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController		This object to support chaining.
	 * @since   1.6
	 */
	public function display($cachable = false, $urlparams = false) {
	}

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'ManageArticles', $prefix = 'JBusinessDirectoryModel', $config = array('ignore_request' => true)) {
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	public function back() {
		$this->setRedirect('index.php?option=com_jbusinessdirectory');
	}
	
	public function addNewArticle() {
		$return = base64_encode(('index.php?option=com_jbusinessdirectory&view=managecompanyarticles'));
		$categoryParam = "";
		// Get plugin 'my_plugin' of plugin type 'my_plugin_type'
		$plugin = JPluginHelper::getPlugin('content', 'business');
		
		// Check if plugin is enabled
		if ($plugin) {
			// Get plugin params
			$pluginParams = new JRegistry($plugin->params);
			
			$category_id = $pluginParams->get('category_id');
			if (!empty($category_id)) {
				$categoryParam ="&catid=$category_id";
			}
		}
		
		$businessId = JFactory::getApplication()->input->getInt("business_id");
		$session = JFactory::getSession();
		$session->set("business_id", $businessId);

		$this->setRedirect("index.php?option=com_content&task=article.add&id=0&return=$return$categoryParam");
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
			JFactory::getApplication()->enqueueMessage(JText::_('COM_JBUSINESSDIRECTORY_NO_ARTICLE_SELECTED'), 'warning');
		} else {
			// Get the model.
			$model = $this->getModel("ManageCompanyArticles");

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
				$this->setMessage(JText::plural('COM_JBUSINESS_DIRECTORY_N_COMPANY_ARTICLES_DELETED', count($cid)));
			}
		}

		$this->setRedirect('index.php?option=com_jbusinessdirectory&view=managecompanyarticles');
	}
}
