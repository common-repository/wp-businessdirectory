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

class JBusinessDirectoryControllerManageBookmarks extends JControllerLegacy {
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
	public function getModel($name = 'ManageBookmarks', $prefix = 'JBusinessDirectoryModel', $config = array('ignore_request' => true)) {
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
			JFactory::getApplication()->enqueueMessage(JText::_('COM_JBUSINESSDIRECTORY_NO_ITEM_SELECTED'), 'warning');
		} else {
			// Get the model.
			$model = $this->getModel("ManageBookmark");

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
				$this->setMessage(JText::plural('COM_JBUSINESS_DIRECTORY_N_ITEMS_DELETED', count($cid)));
			}
		}

		$this->setRedirect('index.php?option=com_jbusinessdirectory&view=managebookmarks');
	}

    /**
     * Exports bookmarks in a csv file
     *
     * @since 4.9.0
     */
    public function exportBookmarks() {     
		JSession::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));   
        $model = $this->getModel('ManageBookmarks');
        $model->exportBookmarksCSV();
        exit;
    }

	/**
	 * Exports bookmarks in a csv file
	 *
	 * @since 4.9.0
	 */
	public function exportListingsBookmarks() {		
		JSession::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));
		$model = $this->getModel('ManageBookmarks');
		$model->exportListingsBookmarksCSV();
		exit;
	}

	/**
	 * Generates a PDF with the listing bookmarks
	 *
	 * @since 4.9.0
	 */
	public function generateBookmarkListringsPDF() {
		$model = $this->getModel('ManageBookmarks');
		$model->generateBookmarkListringsPDF();
		exit;
	}

    /**
     * Generates a PDF with the bookmarks
     *
     * @since 4.9.0
     */
    public function generateBookmarkPDF() {
        $model = $this->getModel('ManageBookmarks');
        $model->generateBookmarkPDF();
        exit;
    }

	public function reOrderListAjax() {
		$newOrder = JFactory::getApplication()->input->get('newOrder');
		$model = $this->getModel('ManageBookmarks');
		$result = $model->reOrderList($newOrder);

		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;
	}
}
