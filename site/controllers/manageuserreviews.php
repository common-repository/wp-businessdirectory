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

JTable::addIncludePath(DS.'components'.'com_jbusinessdirectory'.DS.'tables');
JTable::addIncludePath(DS.'components'.'com_jbusinessdirectory'.DS.'models');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'controllers'.DS.'reviews.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'reviews.php');

class JBusinessDirectoryControllerManageUserReviews extends JBusinessDirectoryControllerReviews {
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */

	public function __construct() {
		parent::__construct();
		$this->log = Logger::getInstance();
	}


	/**
	 * Removes an item
	 */
	public function delete() {
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$cid = JFactory::getApplication()->input->get('id');

		if (empty($cid)) {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_JBUSINESSDIRECTORY_NO_REVIEWS_SELECTED'), 'error');
		} else {
			// Get the model.
			$model = $this->getModel("ManageUserReviews");

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
				$this->setMessage(JText::plural('COM_JBUSINESS_DIRECTORY_N_REVIEWS_DELETED', count($cid)));
			}
		}

		$this->setRedirect('index.php?option=com_jbusinessdirectory&view=manageuserreviews');
	}
}
