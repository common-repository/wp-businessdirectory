<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');


class JBusinessDirectoryControllerActivityItinerary extends JControllerLegacy {
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Get's the variables from the ajax call, calls the function in the model and returns the result
	 * as json.
	 */
	public function addToTripAjax() {
		$jinput = JFactory::getApplication()->input;

		$day = $jinput->get('day');
		$id = $jinput->get('id');
		$type = $jinput->get('type');

		$data = array();
		$data["day"] = $day;
		$data["id"] = $id;
		$data["type"] = $type;

		$model = $this->getModel('ActivityItinerary');
		$result = $model->addToTripAjax($data);

		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;
	}

	/**
	 * Get's the variables from the ajax call, calls the function in the model and returns the result
	 * as json.
	 */
	public function removeFromTripAjax() {
		$jinput = JFactory::getApplication()->input;

		$day = $jinput->get('day');
		$id = $jinput->get('id');
		$type = $jinput->get('type');

		$data = array();
		$data["day"] = $day;
		$data["id"] = $id;
		$data["type"] = $type;

		$model = $this->getModel('ActivityItinerary');
		$result = $model->removeFromTripAjax($data);

		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;
	}

	/**
	 * Resets the itinerary data saved in the session, and redirects to the default activity itinerary layout
	 */
	public function reset() {
		$_SESSION['itineraryData'] = null;

		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=activityitinerary&layout=default', false));
	}
}
