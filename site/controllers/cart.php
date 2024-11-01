<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');


class JBusinessDirectoryControllerCart extends JControllerLegacy {
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
	public function addToCartAjax() {
		$data = JFactory::getApplication()->input->getArray();
		$model = $this->getModel('Cart');
		$result = $model->addToCartAjax($data);

		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;
	}

	/**
	 * Get's the variables from the ajax call, calls the function in the model and returns the result
	 * as json.
	 */
	public function editCartItemAjax() {
		$offerId = JFactory::getApplication()->input->get('offerId');
		$quantity = JFactory::getApplication()->input->get('quantity');

		$model = $this->getModel('Cart');
		$result = $model->editCartItemAjax($offerId, $quantity);

		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;
	}

	/**
	 * Get's the variables from the ajax call, calls the function in the model and returns the result
	 * as json.
	 */
	public function removeOfferFromCartAjax() {
		$offerId = JFactory::getApplication()->input->get('offerId');

		$response = null;
		$status = 1;
		$message = '';

		if (!empty($offerId)) {
			try {
				$model = $this->getModel('Cart');
				$result = $model->removeCartItemAjax($offerId);
				$response = $result;
			} catch (Exception $e) {
				$status = 0;
				$message = $e->getMessage();
			}
		} else {
			$status = 0;
			$message = JText::_("LNG_OFFER_ID_NOT_SPECIFIED");
		}

		JBusinessUtil::sendJsonResponse($response, $status, $message);
	}

	/**
	 * Get's the variables from the ajax call, calls the function in the model and returns the result
	 * as json.
	 */
	public function removeCartItemAjax() {
		$offerId = JFactory::getApplication()->input->get('offerId');

		$model = $this->getModel('Cart');
		$result = $model->removeCartItemAjax($offerId);

		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;
	}

	/**
	 * Get's the variables from the ajax call, calls the function in the model and returns the result
	 * as json.
	 */
	public function selectShippingMethodAjax() {
		$companyId = JFactory::getApplication()->input->get('companyId');
		$methodId = JFactory::getApplication()->input->get('methodId');

		$model = $this->getModel('Cart');
		$result = $model->selectShippingMethod($companyId, $methodId);

		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;
	}

	
	
	/**
	 * Method to reset the session in order to empty the cart
	 */
	public function emptyCart() {
		OfferSellingService::resetSession();

		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=cart', false));
	}
	
	/**
	 * Method to reset the session in order to empty the cart
	 */
	public function emptyCartAjax() {
		
		OfferSellingService::resetSession();
		/* Send as JSON */
		header("Content-Type: application/json", true);
		
		echo json_encode(true);
		
		exit;
	}
}
