<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
JTable::addIncludePath(WP_BUSINESSDIRECTORY_PATH . 'admin/tables');

class TaxService
{
	/**
	 * Calculates the VAT based on the amount and country. If VAT for specific country is not
	 * defined on the application settings, default VAT amount is used.
	 *
	 * @param $amount float
	 * @param $country int country ID
	 *
	 * @return float|int
	 *
	 * @since 5.5.0
	 */
	public static function getVatAmount($amount, $countryId = null) {
		return self::getVat($amount, $countryId)->vat_amount;
	}

	/**
	 * Calculates the VAT based on the amount and country. If VAT for specific country is not
	 * defined on the application settings, default VAT amount is used.
	 *
	 * @param $amount  float
	 * @param $country int country ID
	 *
	 * @return stdClass $vatObject contains {vat, vat_amount} fields
	 *
	 * @since 5.5.0
	 */
	public static function getVat($amount, $countryId = null) {
		$appSettings = JBusinessUtil::getApplicationSettings();
		$vatObject = new stdClass();

		$vatAmount = 0;
		if (!empty($appSettings->vat_configuration) && !empty($countryId)) {
			foreach ($appSettings->vat_configuration as $config) {
				if ($config->country_id == $countryId) {
					$vatObject->vat = $config->value;
					$vatAmount = floatval($amount * $config->value / 100);
				}
			}
		}

		if (!empty($appSettings->vat) && empty($vatAmount)) {
			$vatObject->vat = $appSettings->vat;
			$vatAmount = floatval($amount * $appSettings->vat / 100);
		}

		if(empty($vatObject->vat)){
			$vatObject->vat = 0;
		}

		$vatObject->vat_amount = $vatAmount;

		return $vatObject;
	}

	/**
	 * Calculates respective taxes (based on app type) for a certain amount.
	 * Returns an object that contains the total tax amount and the list of taxes that were applied.
	 *
	 * @param      $amount float
	 * @param int  $type
	 * @param null $country int ID of the country
	 *
	 * @return stdClass
	 *
	 * @since 5.5.0
	 */
	public static function calculateTaxes($amount, $type = JBD_PACKAGES, $countryId = null) {
	
		$taxes = [];

		if ($countryId != null) {
			$countryTaxes = self::getTaxes($type, $countryId);
			$defaultTaxes = self::getTaxes($type);

			foreach ($defaultTaxes as $tax) {
				$taxes[$tax->id] = $tax;
				foreach ($countryTaxes as $countryTax) {
					if ($tax->id == $countryTax->id) {
						$taxes[$tax->id] = $countryTax;
					}
				}
			}
		} else {
			$taxes = self::getTaxes($type);
		}
		$result = self::calculateOrderTaxes($amount, $taxes);

		return $result;
	}


	/**
	 * Calculate the taxes amount based on provided taxes
	 *
	 * @param [type] $amount
	 * @param [type] $taxes
	 * @return void
	 */
	public static function calculateOrderTaxes($amount, $taxes) {
		
		$taxAmount = 0;
		if (!empty($taxes)) {

			$amountField = "country_amount";

			foreach ($taxes as &$tax) {
				if (!isset($tax->country_amount)) {
					$amountField = "tax_amount";
				}

				$tax->tax_amount = $tax->$amountField;

				if ($tax->tax_type == 1) {
					$tax->tax_calc_amount = $amount > 0 ? $tax->$amountField : 0;
				} else {
					$tax->tax_calc_amount = $tax->$amountField * $amount / 100;
				}

				$taxAmount += $tax->tax_calc_amount;
			}
		}

		$taxObject = new stdClass();
		$taxObject->taxes = $taxes;
		$taxObject->tax_amount = floatval($taxAmount);

		return $taxObject;
	}


	/**
	 * Retrieves all taxes for a certain type
	 *
	 * @param int  $type
	 * @param null $country int ID of the country
	 *
	 * @return array
	 *
	 * @since 5.5.0
	 */
	public static function getTaxes($type = JBD_PACKAGES, $countryId = null) {
		$taxesTable = JTable::getInstance("Taxes", "Table", array());
		$taxes = $taxesTable->getTaxes($type, $countryId);

		return $taxes;
	}

	/**
	 * Retrieve all order taxes for a specific order.
	 *
	 * @param int $orderId ID of the order
	 * @param int $type of the order
	 *
	 * @return mixed
	 */
	public static function getOrderTaxes($orderId, $type = JBD_PACKAGES) {
		$orderTable = JTable::getInstance("Order", "JTable", array());

		return $orderTable->getOrderTaxes($orderId, $type);
	}

	/**
	 * Calculates and updates order taxes for a specific existing order based on given country.
	 * Will first delete the current associated order taxes, and create new ones with the updated tax values.
	 *
	 * @param int $orderId ID of the order
	 * @param int $countryId ID of the country
	 * @param int $type type of the order
	 *
	 * @return bool
	 * @throws Exception
	 *
	 * @since 5.5.2
	 */
	public static function updateOrderTaxes($orderId, $countryId, $type = JBD_PACKAGES) {
		$orderTable = JTable::getInstance("Order", "JTable", array());

		$order = null;
		$amountField = "initial_amount";
		if ($type == JBD_PACKAGES) {
			$order = $orderTable->getOrder($orderId);
		} else if ($type == JBD_APP_CAMPAIGNS) {
			$campaignTable = JTable::getInstance("Campaign", "JTable", array());
			$amountField = "initial_budget";
			$order = $campaignTable->getCampaign($orderId);
		}

		try {
			$orderTable->deleteOrderTaxes($orderId, $type);
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}

		$taxObject = self::calculateTaxes($order->$amountField, $type, $countryId);
		if (empty($taxObject->taxes)) {
			$taxObject = self::calculateTaxes($order->$amountField, $type);
		}
		$orderTable->createOrderTax($orderId, $type, $taxObject->taxes);

		return true;
	}
}