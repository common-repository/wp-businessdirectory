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

jimport('joomla.application.component.modeladmin');
/**
 * Company Model for Companies.
 *
 */
class JBusinessDirectoryModelEmailTemplate extends JModelAdmin {
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since   1.6
	 */
	protected $text_prefix = 'COM_JBUSINESSDIRECTORY_EMAIL_TEMPLATE';

	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $_context		= 'com_jbusinessdirectory.emailtemplate';

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object	A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
	 */
	protected function canDelete($record) {
		return true;
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object	A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
	 */
	protected function canEditState($record) {
		return true;
	}

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   type	The table type to instantiate
	 * @param   string	A prefix for the table class name. Optional.
	 * @param   array  Configuration array for model. Optional.
	 * @return  JTable	A database object
	*/
	public function getTable($type = 'EmailTemplate', $prefix = 'JTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since   1.6
	 */
	protected function populateState() {
		$app = JFactory::getApplication('administrator');

		// Load the User state.
		$input = JFactory::getApplication()->input;
		$id = $input->getInt('email_id');
		if ($id==0) {
			$id = $input->getInt('id');
		}
		$this->setState('emailtemplate.id', $id);
	}

	/**
	 * Method to get a menu item.
	 *
	 * @param   integer	The id of the menu item to get.
	 *
	 * @return  mixed  Menu item data object on success, false on failure.
	 */
	public function &getItem($itemId = null) {
		$itemId = (!empty($itemId)) ? $itemId : (int) $this->getState('emailtemplate.id');
		$false	= false;

		// Get a menu item row instance.
		$table = $this->getTable();

		// Attempt to load the row.
		$return = $table->load($itemId);

		// Check for a table object error.
		if ($return === false && $table->getError()) {
			$this->setError($table->getError());
			return $false;
		}

		$properties = $table->getProperties(1);
		$value = ArrayHelper::toObject($properties, 'JObject');

		return $value;
	}

	/**
	 * Method to get the menu item form.
	 *
	 * @param   array  $data		Data for the form.
	 * @param   boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return  JForm	A JForm object on success, false on failure
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true) {
		exit;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 * @since   1.6
	 */
	protected function loadFormData() {
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_jbusinessdirectory.edit.emailtemplate.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  The form data.
	 * @return  boolean  True on success.
	 */
	public function save($data) {
		$id	= (!empty($data['email_id'])) ? $data['email_id'] : (int) $this->getState('emailtemplate.id');
		if (empty($data['email_id'])) {
			$data['email_id']=0;
		}
		$isNew = true;

		$defaultLng = JBusinessUtil::getLanguageTag();
		$input = JFactory::getApplication()->input;
		$description = $input->get('description_'.$defaultLng, '', 'RAW');
		$name = $input->get('name_'.$defaultLng, '', 'RAW');

		if (!empty($description) && empty($data["email_content"])) {
			$data["email_content"] = $description;
		}
		
		if (!empty($name) && empty($data["email_subject"])) {
			$data["email_subject"] = $name;
		}

		// Get a row instance.
		$table = $this->getTable();

		// Load the row if saving an existing item.
		if ($id > 0) {
			$table->load($id);
			$isNew = false;
		}

		// Bind the data.
		if (!$table->bind($data)) {
			$this->setError($table->getError());
			return false;
		}

		// Check the data.
		if (!$table->check()) {
			$this->setError($table->getError());
			return false;
		}

		// Store the data.
		if (!$table->store()) {
			$this->setError($table->getError());
			return false;
		}

		$this->setState('emailtemplate.id', $table->email_id);

		JBusinessDirectoryTranslations::saveTranslations(EMAIL_TRANSLATION, $table->email_id, 'description_');
		
		// Clean the cache
		$this->cleanCache();

		return true;
	}

	public function state() {
		$query = 	' SELECT * FROM #__jbusinessdirectory_emails WHERE email_id = '.$this->_email_id;
	
		$this->_db->setQuery($query);
		$item = $this->_db->loadObject();
	
		$query = 	" UPDATE #__jbusinessdirectory_emails SET is_default = IF(email_id = ".$this->_email_id.", 1, 0)
		WHERE email_type = '".$item->email_type."'
		";
		$this->_db->setQuery($query);
		if (!$this->_db->execute()) {
			return false;
		}
		return true;
	}
	
	/**
	 * Method to delete groups.
	 *
	 * @param   array  An array of item ids.
	 * @return  boolean  Returns true on success, false on failure.
	 */
	public function delete(&$itemIds) {
		// Sanitize the ids.
		$itemIds = (array) $itemIds;
		ArrayHelper::toInteger($itemIds);
	
		// Get a group row instance.
		$table = $this->getTable();
	
		// Iterate the items to delete each one.
		foreach ($itemIds as $itemId) {
			if (!$table->delete($itemId)) {
				$this->setError($table->getError());
				return false;
			}
		}
	
		// Clean the cache
		$this->cleanCache();
	
		return true;
	}

	/**
	 * Changes state of email template entry
	 *
	 * @param $id int ID of the email template
	 * @param $value int value of status
	 *
	 * @return mixed
	 *
	 * @since 5.2.0
	 */
	public function changeState($id, $value) {
		$table = $this->getTable();
		return $table->changeState($id, $value);
	}

	public function getAvailablePlaceholders() {
		$emailId = $this->getState('emailtemplate.id');
		$email = $this->getItem($emailId);
		$types = $this->getEmailTypes();
		$placeholders = array(
			'subject' => array(

			),
			'content' => array(
				EMAIL_USER_NAME => JText::_('LNG_EMAIL_USER_NAME_DESC'),
				EMAIL_COMPANY_LOGO => JText::_('LNG_EMAIL_COMPANY_LOGO_DESC'),
				EMAIL_COMPANY_SOCIAL_NETWORKS => JText::_('LNG_EMAIL_COMPANY_SOCIAL_NETWORKS_DESC'),
				EMAIL_DIRECTORY_WEBSITE => JText::_('LNG_EMAIL_DIRECTORY_WEBSITE_DESC')
			));

		$prepareEmailPlaceHolders = array(
			EMAIL_SITE_ADDRESS => JText::_('LNG_EMAIL_SITE_ADDRESS_DESC'),
			EMAIL_COMPANY_NAME => JText::_('LNG_EMAIL_COMPANY_NAME_DESC'),
			EMAIL_ORDER_ID => JText::_('LNG_EMAIL_ORDER_ID_DESC'),
			EMAIL_PAYMENT_METHOD => JText::_('LNG_EMAIL_PAYMENT_METHOD_DESC'),
			EMAIL_INVOICE_NUMBER => JText::_('LNG_EMAIL_INVOICE_NUMBER_DESC'),
			EMAIL_ORDER_DATE => JText::_('LNG_EMAIL_ORDER_DATE_DESC'),
			EMAIL_SERVICE_NAME => JText::_('LNG_EMAIL_SERVICE_NAME_DESC'),
			EMAIL_UNIT_PRICE => JText::_('LNG_EMAIL_UNIT_PRICE_DESC'),
			EMAIL_TOTAL_PRICE => JText::_('LNG_EMAIL_TOTAL_PRICE_DESC'),
			EMAIL_TAX_AMOUNT => JText::_('LNG_EMAIL_TAX_AMOUNT_DESC'),
			EMAIL_SUBTOTAL_PRICE => JText::_('LNG_EMAIL_SUBTOTAL_PRICE_DESC'),
			EMAIL_BILLING_INFORMATION => JText::_('LNG_EMAIL_BILLING_INFORMATION_DESC'),
			EMAIL_TAX_DETAIL => JText::_('LNG_EMAIL_TAX_DETAIL_DESC'),
			EMAIL_BUSINESS_NAME => JText::_('LNG_EMAIL_BUSINESS_NAME_DESC')
		);

		$prepareEmailFromArrayPlaceHolders = array(
			EMAIL_CATEGORY => JText::_('LNG_EMAIL_CATEGORY_DESC'),
			EMAIL_FIRST_NAME => JText::_('LNG_EMAIL_FIRST_NAME_DESC'),
			EMAIL_LAST_NAME => JText::_('LNG_EMAIL_LAST_NAME_DESC'),
			EMAIL_BUSINESS_NAME => JText::_('LNG_EMAIL_BUSINESS_NAME_DESC'),
			EMAIL_REVIEW_LINK => JText::_('LNG_EMAIL_REVIEW_LINK_DESC'),
			EMAIL_CONTACT_EMAIL => JText::_('LNG_EMAIL_CONTACT_EMAIL_DESC'),
			EMAIL_CONTACT_CONTENT => JText::_('LNG_EMAIL_CONTACT_CONTENT_DESC'),
			EMAIL_ABUSE_DESCRIPTION => JText::_('LNG_EMAIL_ABUSE_DESCRIPTION_DESC'),
			EMAIL_EXPIRATION_DAYS => JText::_('LNG_EMAIL_EXPIRATION_DAYS_DESC'),
			EMAIL_REVIEW_NAME => JText::_('LNG_EMAIL_REVIEW_NAME_DESC'),
			EMAIL_COMPANY_NAME => JText::_('LNG_EMAIL_COMPANY_NAME_DESC'),
			EMAIL_CLAIMED_COMPANY_NAME => JText::_('LNG_EMAIL_CLAIMED_COMPANY_NAME_DESC')

		);

		$prepareNotificationPlaceHolders = array(
			EMAIL_COMPANY_NAME => JText::_('LNG_EMAIL_COMPANY_NAME_DESC'),
			EMAIL_BUSINESS_NAME => JText::_('LNG_EMAIL_BUSINESS_NAME_DESC'),
			EMAIL_BUSINESS_ADDRESS => JText::_('LNG_EMAIL_BUSINESS_ADDRESS_DESC'),
			EMAIL_BUSINESS_WEBSITE => JText::_('LNG_EMAIL_BUSINESS_WEBSITE_DESC'),
			EMAIL_BUSINESS_ADMINISTRATOR_URL => JText::_('LNG_EMAIL_BUSINESS_ADMINISTRATOR_URL_DESC'),
			EMAIL_BUSINESS_LOGO => JText::_('LNG_EMAIL_BUSINESS_LOGO_DESC'),
			EMAIL_BUSINESS_CATEGORY => JText::_('LNG_EMAIL_BUSINESS_CATEGORY_DESC'),
			EMAIL_BUSINESS_CONTACT_PERSON => JText::_('LNG_EMAIL_BUSINESS_CONTACT_PERSON_DESC')
		);

		switch ($email->email_type) {
			case 'Test Email':
				$placeholders['content'] = array_merge(
					$placeholders['content'],
					array(						
						EMAIL_COMPANY_NAME => JText::_('LNG_EMAIL_COMPANY_NAME_DESC')
					)
				);
				break;
			case 'New Company Notification Email':
				$placeholders['content'] = array_merge($placeholders['content'], $prepareNotificationPlaceHolders);
				break;
			case 'Listing Creation Notification':
				$placeholders['content'] = array_merge($placeholders['content'], $prepareNotificationPlaceHolders);
				break;
			case 'Approve Email':
				$placeholders['content'] = array_merge(
					$placeholders['content'],
					array(
						EMAIL_COMPANY_NAME =>  JText::_('LNG_EMAIL_COMPANY_NAME_DESC'),
						EMAIL_BUSINESS_NAME =>  JText::_('LNG_EMAIL_BUSINESS_NAME_DESC')
					)
				);
				break;
			case 'Claim Request Email':
				$placeholders['subject'] = array(
					EMAIL_COMPANY_NAME =>  JText::_('LNG_EMAIL_COMPANY_NAME_DESC')
				);
				$placeholders['content'] = array_merge(
					$placeholders['content'],
					array(
						EMAIL_COMPANY_NAME =>  JText::_('LNG_EMAIL_COMPANY_NAME_DESC'),
						EMAIL_BUSINESS_NAME =>  JText::_('LNG_EMAIL_BUSINESS_NAME_DESC')
					)
				);
				break;
			case 'Claim Response Email':
			case 'Claim Negative Response Email':
				$placeholders['content'] = array_merge($placeholders['content'], $prepareEmailFromArrayPlaceHolders, array(
					EMAIL_CUSTOMER_NAME => JText::_('LNG_EMAIL_CUSTOMER_NAME_DESC')
				));
				break;
			case 'Contact Email':
				$placeholders['subject'] = array(
					EMAIL_BUSINESS_NAME =>  JText::_('LNG_EMAIL_BUSINESS_NAME_DESC')
				);
				$placeholders['content'] = array_merge(
					$placeholders['content'],
					array(						
						EMAIL_PHONE =>  JText::_('LNG_EMAIL_PHONE_DESC')
					)
				);
				$placeholders['content'] = array_merge($placeholders['content'], $prepareEmailFromArrayPlaceHolders);
				break;
			case 'Request Info Email':
				$placeholders['subject'] = array(
					EMAIL_BUSINESS_NAME =>  JText::_('LNG_EMAIL_BUSINESS_NAME_DESC')
				);
				$placeholders['content'] = array_merge(
					$placeholders['content'],
					array(						
						EMAIL_PHONE =>  JText::_('LNG_EMAIL_PHONE_DESC')
					)
				);
				$placeholders['content'] = array_merge($placeholders['content'], $prepareEmailFromArrayPlaceHolders);
				break;
			case 'Request Quote Email':
				$placeholders['content'] = array_merge($placeholders['content'], $prepareEmailFromArrayPlaceHolders, array(						
					EMAIL_PHONE =>  JText::_('LNG_EMAIL_PHONE_DESC')
				));
				break;
			case 'Order Email':
				$placeholders['subject'] = array(
					EMAIL_COMPANY_NAME =>  JText::_('LNG_EMAIL_COMPANY_NAME_DESC')
				);
				$placeholders['content'] = array_merge($placeholders['content'], $prepareEmailPlaceHolders, array(
					EMAIL_CUSTOMER_NAME => JText::_('LNG_EMAIL_CUSTOMER_NAME_DESC')
				));
				break;
			case 'Payment Details Email':
				$placeholders['subject'] = array(
					EMAIL_COMPANY_NAME =>  JText::_('LNG_EMAIL_COMPANY_NAME_DESC')
				);
				$placeholders['content'] = array_merge($placeholders['content'], $prepareEmailPlaceHolders, array(
					EMAIL_PAYMENT_DETAILS => JText::_('LNG_EMAIL_PAYMENT_DETAILS_DESC'),
					EMAIL_CUSTOMER_NAME => JText::_('LNG_EMAIL_CUSTOMER_NAME_DESC')
				));
				break;
			case 'Expiration Notification Email':
				$placeholders['content'] = array_merge($placeholders['content'], $prepareEmailFromArrayPlaceHolders);
				break;
			case 'Review Email':
				$placeholders['content'] = array_merge($placeholders['content'], $prepareEmailPlaceHolders, array(
					EMAIL_REVIEW_LINK => JText::_('LNG_EMAIL_REVIEW_LINK_DESC')
				));
				unset($placeholders['content'][EMAIL_TAX_DETAIL]);
				break;
			case 'Review Response Email':
				$placeholders['content'] = array_merge($placeholders['content'], $prepareEmailFromArrayPlaceHolders);
				break;
			case 'Report Abuse Email':
				$placeholders['content'] = array_merge($placeholders['content'], $prepareEmailFromArrayPlaceHolders);
				break;
			case 'Offer Creation Notification':
				$placeholders['content'] = array_merge($placeholders['content'], array(
					EMAIL_COMPANY_NAME => JText::_('LNG_EMAIL_COMPANY_NAME_DESC'),
					EMAIL_OFFER_NAME => JText::_('LNG_EMAIL_OFFER_NAME_DESC')
				));
				unset($placeholders['content'][EMAIL_USER_NAME]);
				break;
			case 'Offer Approval Notification':
				$placeholders['content'] = array_merge($placeholders['content'], array(
					EMAIL_COMPANY_NAME => JText::_('LNG_EMAIL_COMPANY_NAME_DESC'),
					EMAIL_OFFER_NAME => JText::_('LNG_EMAIL_OFFER_NAME_DESC')
				));
				break;
			case 'Offer Expiration Notification Email':
				$placeholders['content'] = array_merge($placeholders['content'], array(
					EMAIL_OFFER_NAME => JText::_('LNG_EMAIL_OFFER_NAME_DESC'),
					EMAIL_EXPIRATION_DAYS => JText::_('LNG_EMAIL_EXPIRATION_DAYS_DESC'),
					EMAIL_COMPANY_NAME => JText::_('LNG_EMAIL_COMPANY_NAME_DESC')
				));
				break;
			case 'Offer Contact Email':
				$placeholders['subject'] = array(
					EMAIL_OFFER_NAME =>  JText::_('LNG_EMAIL_OFFER_NAME_DESC')
				);
				$placeholders['content'] = array_merge($placeholders['content'], $prepareEmailFromArrayPlaceHolders, array(
					EMAIL_OFFER_NAME =>  JText::_('LNG_EMAIL_OFFER_NAME_DESC')
				));
				break;
			case 'Event Creation Notification':
				$placeholders['content'] = array_merge($placeholders['content'], array(
					EMAIL_COMPANY_NAME => JText::_('LNG_EMAIL_COMPANY_NAME_DESC'),
					EMAIL_EVENT_NAME => JText::_('LNG_EMAIL_EVENT_NAME_DESC')
				));
				unset($placeholders['content'][EMAIL_USER_NAME]);
				break;
			case 'Event Approval Notification':
				$placeholders['content'] = array_merge($placeholders['content'], array(
					EMAIL_COMPANY_NAME => JText::_('LNG_EMAIL_COMPANY_NAME_DESC'),
					EMAIL_EVENT_NAME => JText::_('LNG_EMAIL_EVENT_NAME_DESC')
				));
				break;
			case 'Event Reservation Notification':
				$placeholders['subject'] = array(
					EMAIL_EVENT_NAME => JText::_('LNG_EMAIL_EVENT_NAME_DESC')
				);
				$placeholders['content'] = array_merge($placeholders['content'], array(
					EMAIL_EVENT_NAME => JText::_('LNG_EMAIL_EVENT_NAME_DESC'),
					EMAIL_EVENT_LINK => JText::_('LNG_EMAIL_EVENT_LINK_DESC'),
					EMAIL_EVENT_ADDRESS => JText::_('LNG_EMAIL_EVENT_ADDRESS_DESC'),
					EMAIL_EVENT_START_DATE => JText::_('LNG_EMAIL_EVENT_START_DATE_DESC'),
					EMAIL_BOOKING_DATE => JText::_('LNG_EMAIL_BOOKING_DATE_DESC'),
					EMAIL_BOOKING_DETAILS => JText::_('LNG_EMAIL_BOOKING_DETAILS_DESC'),
					EMAIL_BOOKING_GUEST_DETAILS => JText::_('LNG_EMAIL_BOOKING_GUEST_DETAILS_DESC'),
					EMAIL_EVENT_PHONE => JText::_('LNG_EMAIL_EVENT_PHONE_DESC'),
					EMAIL_EVENT_EMAIL => JText::_('LNG_EMAIL_EVENT_EMAIL_DESC'),
					EMAIL_CUSTOMER_NAME => JText::_('LNG_EMAIL_CUSTOMER_NAME_DESC'),
					EMAIL_COMPANY_NAME => JText::_('LNG_EMAIL_COMPANY_NAME_DESC')
				));
				unset($placeholders['content'][EMAIL_USER_NAME]);
				break;
			case 'Event Payment Details':
				$placeholders['subject'] = array(
					EMAIL_EVENT_NAME => JText::_('LNG_EMAIL_EVENT_NAME_DESC')
				);
				$placeholders['content'] = array_merge($placeholders['content'], array(
					EMAIL_EVENT_NAME => JText::_('LNG_EMAIL_EVENT_NAME_DESC'),
					EMAIL_CUSTOMER_NAME => JText::_('LNG_EMAIL_CUSTOMER_NAME_DESC'),
					EMAIL_EVENT_ADDRESS => JText::_('LNG_EMAIL_EVENT_ADDRESS_DESC'),
					EMAIL_PAYMENT_DETAILS => JText::_('LNG_EMAIL_PAYMENT_DETAILS_DESC'),
					EMAIL_EVENT_START_DATE => JText::_('LNG_EMAIL_EVENT_START_DATE_DESC'),
					EMAIL_BOOKING_DATE => JText::_('LNG_EMAIL_BOOKING_DATE_DESC'),
					EMAIL_BOOKING_DETAILS => JText::_('LNG_EMAIL_BOOKING_DETAILS_DESC'),
					EMAIL_BOOKING_GUEST_DETAILS => JText::_('LNG_EMAIL_BOOKING_GUEST_DETAILS_DESC'),
					EMAIL_EVENT_PHONE => JText::_('LNG_EMAIL_EVENT_PHONE_DESC'),
					EMAIL_EVENT_EMAIL => JText::_('LNG_EMAIL_EVENT_EMAIL_DESC'),
					EMAIL_BOOKING_ID => JText::_('LNG_EMAIL_BOOKING_ID_DESC'),
					EMAIL_SITE_ADDRESS => JText::_('LNG_EMAIL_SITE_ADDRESS_DESC')
				));
				unset($placeholders['content'][EMAIL_USER_NAME]);
				break;
			case 'Event Expiration Notification Email':
				$placeholders['content'] = array_merge($placeholders['content'], array(
					EMAIL_EVENT_NAME => JText::_('LNG_EMAIL_EVENT_NAME_DESC'),
					EMAIL_EXPIRATION_DAYS => JText::_('LNG_EMAIL_EXPIRATION_DAYS_DESC'),
					EMAIL_COMPANY_NAME => JText::_('LNG_EMAIL_COMPANY_NAME_DESC')
				));
				break;
			case 'Event Contact Email':
				$placeholders['subject'] = array(
					EMAIL_EVENT_NAME => JText::_('LNG_EMAIL_EVENT_NAME_DESC')
				);
				$placeholders['content'] = array_merge($placeholders['content'], $prepareEmailFromArrayPlaceHolders, array(
					EMAIL_EVENT_NAME => JText::_('LNG_EMAIL_EVENT_NAME_DESC')
				));
				break;
			case 'Offer Order Notification':
				$placeholders['subject'] = array(
					EMAIL_OFFER_ORDER_ID => JText::_('LNG_EMAIL_OFFER_ORDER_ID_DESC')
				);
				$placeholders['content'] = array_merge($placeholders['content'], array(
					EMAIL_OFFER_ORDER_DATE => JText::_('LNG_EMAIL_OFFER_ORDER_DATE_DESC'),
					EMAIL_OFFER_ORDER_DETAILS => JText::_('LNG_EMAIL_OFFER_ORDER_DETAILS_DESC'),
					EMAIL_OFFER_ORDER_BUYER_DETAILS => JText::_('LNG_EMAIL_OFFER_ORDER_BUYER_DETAILS_DESC'),
					EMAIL_OFFER_ORDER_ID => JText::_('LNG_EMAIL_OFFER_ORDER_ID_DESC'),
					EMAIL_CUSTOMER_NAME => JText::_('LNG_EMAIL_CUSTOMER_NAME_DESC')
				));
				unset($placeholders['content'][EMAIL_USER_NAME]);
				break;
			case 'Event Appointment Email':
				$placeholders['subject'] = array(
					EMAIL_EVENT_NAME => JText::_('LNG_EMAIL_EVENT_NAME_DESC'),
					EMAIL_APPOINTMENT_DATE => JText::_('LNG_EMAIL_APPOINTMENT_DATE_DESC')
				);
				$placeholders['content'] = array_merge($placeholders['content'], array(
					EMAIL_EVENT_NAME => JText::_('LNG_EMAIL_EVENT_NAME_DESC'),
					EMAIL_APPOINTMENT_DATE => JText::_('LNG_EMAIL_APPOINTMENT_DATE_DESC'),
					EMAIL_APPOINTMENT_TIME => JText::_('LNG_EMAIL_APPOINTMENT_TIME_DESC'),
					EMAIL_FIRST_NAME => JText::_('LNG_EMAIL_FIRST_NAME_DESC'),
					EMAIL_LAST_NAME => JText::_('LNG_EMAIL_LAST_NAME_DESC'),
					EMAIL_EMAIL => JText::_('LNG_EMAIL_EMAIL_DESC'),
					EMAIL_PHONE => JText::_('LNG_EMAIL_PHONE_DESC'),
					EMAIL_BUSINESS_NAME => JText::_('LNG_EMAIL_BUSINESS_NAME_DESC'),
					EMAIL_COMPANY_NAME => JText::_('LNG_EMAIL_COMPANY_NAME_DESC')
				));
				break;
			case 'Event Appointment Status Notification':
				$placeholders['content'] = array_merge($placeholders['content'], array(
					EMAIL_EVENT_NAME => JText::_('LNG_EMAIL_EVENT_NAME_DESC'),
					EMAIL_APPOINTMENT_DATE => JText::_('LNG_EMAIL_APPOINTMENT_DATE_DESC'),
					EMAIL_APPOINTMENT_TIME => JText::_('LNG_EMAIL_APPOINTMENT_TIME_DESC'),
					EMAIL_APPOINTMENT_STATUS => JText::_('LNG_EMAIL_APPOINTMENT_STATUS_DESC'),
					EMAIL_COMPANY_NAME => JText::_('LNG_EMAIL_COMPANY_NAME_DESC'),
					EMAIL_CUSTOMER_NAME => JText::_('LNG_EMAIL_CUSTOMER_NAME_DESC')
				));
				break;
			case 'Report Abuse Offer Review':
				$placeholders['content'] = array_merge($placeholders['content'], $prepareEmailFromArrayPlaceHolders, array(
					EMAIL_OFFER_NAME => JText::_('LNG_EMAIL_OFFER_NAME_DESC'),
					EMAIL_REVIEW_LINK_OFFER => JText::_('LNG_EMAIL_REVIEW_LINK_OFFER_DESC')
				));
				break;
			case 'Service Booking Notification':
				$placeholders['subject'] = array(
					EMAIL_SERVICE_BOOKING_ID => JText::_('LNG_EMAIL_SERVICE_BOOKING_ID_DESC')
				);
				$placeholders['content'] = array_merge($placeholders['content'], array(
					EMAIL_SERVICE_BOOKING_DATE => JText::_('LNG_EMAIL_SERVICE_BOOKING_DATE_DESC'),
					EMAIL_SERVICE_BOOKING_DETAILS => JText::_('LNG_EMAIL_SERVICE_BOOKING_DETAILS_DESC'),
					EMAIL_SERVICE_BUYER_DETAILS => JText::_('LNG_EMAIL_SERVICE_BUYER_DETAILS_DESC'),
					EMAIL_SERVICE_BOOKING_NAME => JText::_('LNG_EMAIL_SERVICE_BOOKING_NAME_DESC'),
					EMAIL_CUSTOMER_NAME => JText::_('LNG_EMAIL_CUSTOMER_NAME_DESC'),
					EMAIL_COMPANY_NAME => JText::_('LNG_EMAIL_COMPANY_NAME_DESC')
				));
				unset($placeholders['content'][EMAIL_USER_NAME]);
				break;
			case 'Company Association Notification':
				$placeholders['subject'] = array(
					EMAIL_EVENT_NAME => JText::_('LNG_EMAIL_EVENT_NAME_DESC')
				);
				$placeholders['content'] = array_merge($placeholders['content'], array(
					EMAIL_EVENT_NAME => JText::_('LNG_EMAIL_EVENT_NAME_DESC'),
					EMAIL_COMPANY_NAMES => JText::_('LNG_EMAIL_COMPANY_NAMES_DESC'),
					EMAIL_COMPANY_NAME => JText::_('LNG_EMAIL_COMPANY_NAME_DESC')
				));
				break;
			case 'Report Notification':
				$placeholders['content'] = array_merge($placeholders['content'], array(
					EMAIL_CONTACT_EMAIL => JText::_('LNG_EMAIL_CONTACT_EMAIL_DESC'),
					EMAIL_ABUSE_DESCRIPTION => JText::_('LNG_EMAIL_ABUSE_DESCRIPTION_DESC'),
					EMAIL_BUSINESS_NAME => JText::_('LNG_EMAIL_BUSINESS_NAME_DESC'),
					EMAIL_COMPANY_NAME => JText::_('LNG_EMAIL_COMPANY_NAME_DESC'),
					EMAIL_REPORT_CAUSE => JText::_('LNG_EMAIL_REPORT_CAUSE_DESC')
				));
				unset($placeholders['content'][EMAIL_USER_NAME]);
				break;
			case 'Disapprove Email':
				$placeholders['subject'] = array(
					EMAIL_BUSINESS_NAME => JText::_('LNG_EMAIL_BUSINESS_NAME_DESC')
				);
				$placeholders['content'] = array_merge($placeholders['content'], array(
					EMAIL_COMPANY_NAME => JText::_('LNG_EMAIL_COMPANY_NAME_DESC'),
					EMAIL_BUSINESS_NAME => JText::_('LNG_EMAIL_BUSINESS_NAME_DESC'),
					EMAIL_DISAPPROVAL_TEXT => JText::_('LNG_EMAIL_DISAPPROVAL_TEXT_DESC')
				));
				break;
			case 'Business Statistics Email':
				$placeholders['content'] = array_merge($placeholders['content'], $prepareNotificationPlaceHolders, array(
					BUSINESS_VIEW_COUNT => JText::_('LNG_BUSINESS_VIEW_COUNT_DESC'),
					MONTHLY_VIEW_COUNT => JText::_('LNG_MONTHLY_VIEW_COUNT_DESC'),
					MONTHLY_ARTICLE_VIEW_COUNT => JText::_('LNG_MONTHLY_ARTICLE_VIEW_COUNT_DESC'),
					BUSINESS_RATING => JText::_('LNG_BUSINESS_RATING_DESC'),
					BUSINESS_REVIEW_NUMBER => JText::_('LNG_BUSINESS_REVIEW_NUMBER_DESC'),
					EVENTS_DETAILS => JText::_('LNG_EVENTS_DETAILS_DESC'),
					OFFER_DETAILS => JText::_('LNG_OFFER_DETAILS_DESC'),
					BUSINESS_REVIEW => JText::_('LNG_BUSINESS_REVIEW_DESC')
				));
				break;
			case 'Business Upgrade Notification':
				$placeholders['content'] = array_merge($placeholders['content'], $prepareNotificationPlaceHolders, array(
					BUSINESS_PATH_CONTROL_PANEL => JText::_('LNG_BUSINESS_PATH_CONTROL_PANEL_DESC')
				));
				break;
			case 'Offer Shipping Notification':
				$placeholders['content'] = array_merge($placeholders['content'], array(
					EMAIL_OFFER_ORDER_DATE => JText::_('LNG_EMAIL_OFFER_ORDER_DATE_DESC'),
					EMAIL_OFFER_ORDER_DETAILS => JText::_('LNG_EMAIL_OFFER_ORDER_DETAILS_DESC'),
					EMAIL_OFFER_ORDER_BUYER_DETAILS => JText::_('LNG_EMAIL_OFFER_ORDER_BUYER_DETAILS_DESC'),
					EMAIL_OFFER_ORDER_TRACKING_LINK => JText::_('LNG_EMAIL_OFFER_ORDER_TRACKING_LINK_DESC'),
					EMAIL_CUSTOMER_NAME => JText::_('LNG_EMAIL_CUSTOMER_NAME_DESC')
				));
				break;
			case 'Event Reservation Waiting Notification':
				$placeholders['subject'] = array(
					EMAIL_EVENT_NAME => JText::_('LNG_EMAIL_EVENT_NAME_DESC')
				);
				$placeholders['content'] = array_merge($placeholders['content'], array(
					EMAIL_EVENT_NAME => JText::_('LNG_EMAIL_EVENT_NAME_DESC'),
					EMAIL_EVENT_LINK => JText::_('LNG_EMAIL_EVENT_LINK_DESC'),
					EMAIL_EVENT_ADDRESS => JText::_('LNG_EMAIL_EVENT_ADDRESS_DESC'),
					EMAIL_EVENT_START_DATE => JText::_('LNG_EMAIL_EVENT_START_DATE_DESC'),
					EMAIL_BOOKING_DATE => JText::_('LNG_EMAIL_BOOKING_DATE_DESC'),
					EMAIL_BOOKING_DETAILS => JText::_('LNG_EMAIL_BOOKING_DETAILS_DESC'),
					EMAIL_BOOKING_GUEST_DETAILS => JText::_('LNG_EMAIL_BOOKING_GUEST_DETAILS_DESC'),
					EMAIL_EVENT_PHONE => JText::_('LNG_EMAIL_EVENT_PHONE_DESC'),
					EMAIL_EVENT_EMAIL => JText::_('LNG_EMAIL_EVENT_EMAIL_DESC'),
					EMAIL_PAYMENT_DETAILS => JText::_('LNG_EMAIL_PAYMENT_DETAILS_DESC'),
					EMAIL_COMPANY_NAME => JText::_('LNG_EMAIL_COMPANY_NAME_DESC'),
					EMAIL_CUSTOMER_NAME => JText::_('LNG_EMAIL_CUSTOMER_NAME_DESC'),
					EMAIL_EVENT_PICTURE => JText::_('LNG_EMAIL_EVENT_PICTURE_DESC')
				));
				unset($placeholders['content'][EMAIL_USER_NAME]);
				break;
			case 'Service Booking Waiting Notification':
				$placeholders['subject'] = array(
					EMAIL_SERVICE_BOOKING_ID => JText::_('LNG_EMAIL_SERVICE_BOOKING_ID_DESC')
				);
				$placeholders['content'] = array_merge($placeholders['content'], array(
					EMAIL_SERVICE_BOOKING_DATE => JText::_('LNG_EMAIL_SERVICE_BOOKING_DATE_DESC'),
					EMAIL_SERVICE_BOOKING_DETAILS => JText::_('LNG_EMAIL_SERVICE_BOOKING_DETAILS_DESC'),
					EMAIL_SERVICE_BUYER_DETAILS => JText::_('LNG_EMAIL_SERVICE_BUYER_DETAILS_DESC'),
					EMAIL_SERVICE_BOOKING_NAME => JText::_('LNG_EMAIL_SERVICE_BOOKING_NAME_DESC'),
					EMAIL_CUSTOMER_NAME => JText::_('LNG_EMAIL_CUSTOMER_NAME_DESC'),
					EMAIL_COMPANY_NAME => JText::_('LNG_EMAIL_COMPANY_NAME_DESC'),
					EMAIL_PAYMENT_DETAILS => JText::_('LNG_EMAIL_PAYMENT_DETAILS_DESC')
				));
				unset($placeholders['content'][EMAIL_USER_NAME]);
				break;
			case 'Offer Order Waiting Notification':
				$placeholders['subject'] = array(
					EMAIL_OFFER_ORDER_ID => JText::_('LNG_EMAIL_OFFER_ORDER_ID_DESC')
				);
				$placeholders['content'] = array_merge($placeholders['content'], array(
					EMAIL_OFFER_ORDER_ID => JText::_('LNG_EMAIL_OFFER_ORDER_ID_DESC'),
					EMAIL_OFFER_ORDER_DATE => JText::_('LNG_EMAIL_OFFER_ORDER_DATE_DESC'),
					EMAIL_OFFER_ORDER_DETAILS => JText::_('LNG_EMAIL_OFFER_ORDER_DETAILS_DESC'),
					EMAIL_OFFER_ORDER_BUYER_DETAILS => JText::_('LNG_EMAIL_OFFER_ORDER_BUYER_DETAILS_DESC'),
					EMAIL_CUSTOMER_NAME => JText::_('LNG_EMAIL_CUSTOMER_NAME_DESC'),
					EMAIL_PAYMENT_DETAILS => JText::_('LNG_EMAIL_PAYMENT_DETAILS_DESC')
				));
				unset($placeholders['content'][EMAIL_USER_NAME]);
				break;
			case 'Campaign Payment Notification':
				$placeholders['subject'] = array(
					EMAIL_CAMPAIGN_ID => JText::_('LNG_EMAIL_CAMPAIGN_ID_DESC')
				);
				$placeholders['content'] = array_merge($placeholders['content'], array(
					EMAIL_CAMPAIGN_NAME => JText::_('LNG_EMAIL_CAMPAIGN_NAME_DESC'),
					EMAIL_CAMPAIGN_DETAILS => JText::_('LNG_EMAIL_CAMPAIGN_DETAILS_DESC'),
					EMAIL_OFFER_ORDER_DETAILS => JText::_('LNG_EMAIL_OFFER_ORDER_DETAILS_DESC'),
					EMAIL_COMPANY_NAME => JText::_('LNG_EMAIL_COMPANY_NAME_DESC'),
					EMAIL_CUSTOMER_NAME => JText::_('LNG_EMAIL_CUSTOMER_NAME_DESC')
				));
				unset($placeholders['content'][EMAIL_USER_NAME]);
				break;
			case 'Campaign Payment Waiting Notification':
				$placeholders['subject'] = array(
					EMAIL_CAMPAIGN_ID => JText::_('LNG_EMAIL_CAMPAIGN_ID_DESC')
				);
				$placeholders['content'] = array_merge($placeholders['content'], array(
					EMAIL_CAMPAIGN_NAME => JText::_('LNG_EMAIL_CAMPAIGN_NAME_DESC'),
					EMAIL_CAMPAIGN_DETAILS => JText::_('LNG_EMAIL_CAMPAIGN_DETAILS_DESC'),
					EMAIL_CAMPAIGN_BUYER_DETAILS => JText::_('LNG_EMAIL_CAMPAIGN_BUYER_DETAILS_DESC'),
					EMAIL_COMPANY_NAME => JText::_('LNG_EMAIL_COMPANY_NAME_DESC'),
					EMAIL_PAYMENT_DETAILS => JText::_('LNG_EMAIL_PAYMENT_DETAILS_DESC'),
					EMAIL_CUSTOMER_NAME => JText::_('LNG_EMAIL_CUSTOMER_NAME_DESC')
				));
				unset($placeholders['content'][EMAIL_USER_NAME]);
				break;
			case 'Request Quote':
				$placeholders['subject'] = array(
					EMAIL_BUSINESS_NAME => JText::_('LNG_EMAIL_BUSINESS_NAME_DESC')
				);
				$placeholders['content'] = array_merge($placeholders['content'], $prepareEmailFromArrayPlaceHolders, array(
					EMAIL_CATEGORY_LINK => JText::_('LNG_EMAIL_CATEGORY_LINK_DESC'),
					EMAIL_BUSINESS_NAME => JText::_('LNG_EMAIL_BUSINESS_NAME_DESC'),
					EMAIL_REQUEST_QUOTE_SUMMARY => JText::_('LNG_EMAIL_REQUEST_QUOTE_SUMMARY_DESC'),
					EMAIL_CLICK_HERE_LINK => JText::_('LNG_EMAIL_CLICK_HERE_LINK_DESC')
				));
				break;
			case 'Payment Notification':
				$placeholders['content'] = array_merge($placeholders['content'], $prepareNotificationPlaceHolders, array(
					EMAIL_ORDERS_LINK => JText::_('LNG_EMAIL_ORDERS_LINK_DESC')
				));
				break;
			case 'Business Update Notification':
				$placeholders['content'] = array_merge($placeholders['content'], $prepareNotificationPlaceHolders);
				break;
			case 'Offer Review Response Email':
				$placeholders['content'] = array_merge($placeholders['content'], $prepareEmailFromArrayPlaceHolders, array(
					EMAIL_REVIEW_LINK => JText::_('LNG_EMAIL_REVIEW_LINK_DESC'),
					EMAIL_OFFER_NAME => JText::_('LNG_EMAIL_OFFER_NAME_DESC')
				));
				break;
			case 'Offer Review Email':
				$placeholders['content'] = array_merge($placeholders['content'], $prepareEmailPlaceHolders, array(
					EMAIL_OFFER_NAME => JText::_('LNG_EMAIL_OFFER_NAME_DESC'),
					EMAIL_REVIEW_LINK => JText::_('LNG_EMAIL_REVIEW_LINK_DESC')
				));
				break;
			case 'Listing owner changed':
				$placeholders['content'] = array_merge($placeholders['content'], $prepareEmailPlaceHolders, array(
					EMAIL_BUSINESS_NAME => JText::_('LNG_EMAIL_BUSINESS_NAME_DESC'),
					EMAIL_PREVIOUS_USER => JText::_('LNG_EMAIL_PREVIOUS_USER_DESC'),
					EMAIL_ACTUAL_USER => JText::_('LNG_EMAIL_ACTUAL_USER_DESC')
				));
				break;
			case 'Company Joining Notification':
				$placeholders['subject'] = array(
					EMAIL_BUSINESS_NAME => JText::_('LNG_EMAIL_BUSINESS_NAME_DESC')
				);
				$placeholders['content'] = array_merge($placeholders['content'], $prepareNotificationPlaceHolders);
				break;
			case 'Listing Creation Notification to Owner':
				$placeholders['subject'] = array(
					EMAIL_BUSINESS_NAME => JText::_('LNG_EMAIL_BUSINESS_NAME_DESC')
				);
				$placeholders['content'] = array_merge($placeholders['content'], $prepareEmailPlaceHolders, array(
					EMAIL_BUSINESS_NAME => JText::_('LNG_EMAIL_BUSINESS_NAME_DESC')
				));
				break;
			case 'Offer Low Quantity Notification':
				$placeholders['subject'] = array(
					EMAIL_OFFER_NAME => JText::_('LNG_EMAIL_OFFER_NAME_DESC')
				);
				$placeholders['content'] = array_merge($placeholders['content'], array(
					EMAIL_OFFER_NAME => JText::_('LNG_EMAIL_OFFER_NAME_DESC'),
					EMAIL_OFFER_NOTIFICATION_QUANTITY => JText::_('LNG_EMAIL_OFFER_NOTIFICATION_QUANTITY'),
					EMAIL_OFFER_STOCK_DETAILS => JText::_('LNG_EMAIL_OFFER_STOCK_DETAILS'),
					EMAIL_COMPANY_NAME => JText::_('LNG_EMAIL_COMPANY_NAME_DESC')
				));
				break;
			case 'Appointment URL Notification':
				$placeholders['content'] = array_merge($placeholders['content'], array(
					EMAIL_SERVICE_NAME => JText::_('LNG_EMAIL_SERVICE_NAME'),
					EMAIL_PROVIDER_NAME => JText::_('LNG_EMAIL_PROVIDER_NAME'),
					EMAIL_APPOINTMENT_DATE => JText::_('LNG_EMAIL_APPOINTMENT_DATE'),
					EMAIL_APPOINTMENT_URL => JText::_('LNG_EMAIL_APPOINTMENT_URL')
				));
				break;
			case 'Appointment Email Notification':
				$placeholders['content'] = array_merge($placeholders['content'], array(
					EMAIL_SERVICE_NAME => JText::_('LNG_EMAIL_SERVICE_NAME'),
					EMAIL_PROVIDER_NAME => JText::_('LNG_EMAIL_PROVIDER_NAME'),
					EMAIL_APPOINTMENT_DATE => JText::_('LNG_EMAIL_APPOINTMENT_DATE'),
					EMAIL_APPOINTMENT_MESSAGE => JText::_('LNG_EMAIL_APPOINTMENT_MESSAGE')
				));
				break;
			case 'Hire Email':
				$placeholders['content'] = array_merge(
					$placeholders['content'],
					array(						
						EMAIL_COMPANY_NAME => JText::_('LNG_EMAIL_COMPANY_NAME_DESC'),
						EMAIL_BUSINESS_NAME => JText::_('LNG_EMAIL_BUSINESS_NAME_DESC'),
						EMAIL_QUOTE_USER_NAME => JText::_('LNG_EMAIL_QUOTE_USER_NAME_DESC'),
						EMAIL_CONTACT_EMAIL => JText::_('LNG_EMAIL_CONTACT_EMAIL_DESC'),
						EMAIL_REQUEST_QUOTE_SUMMARY => JText::_('LNG_EMAIL_REQUEST_QUOTE_SUMMARY_DESC')
					));
				break;
			case 'Service Booking Reminder':
				$placeholders['subject'] = array(
					EMAIL_SERVICE_BOOKING_NAME => JText::_('LNG_EMAIL_SERVICE_BOOKING_NAME_DESC')
				);
				$placeholders['content'] = array_merge(
					$placeholders['content'],
					array(			
						EMAIL_SERVICE_BOOKING_DATE => JText::_('LNG_EMAIL_SERVICE_BOOKING_DATE_DESC'),
						EMAIL_SERVICE_BOOKING_DETAILS => JText::_('LNG_EMAIL_SERVICE_BOOKING_DETAILS_DESC'),
						EMAIL_SERVICE_BOOKING_NAME => JText::_('LNG_EMAIL_SERVICE_BOOKING_NAME_DESC'),			
						EMAIL_COMPANY_NAME => JText::_('LNG_EMAIL_COMPANY_NAME_DESC'),
					));
				break;
			case 'Event Appointment Reminder':
				$placeholders['subject'] = array(
					EMAIL_EVENT_NAME => JText::_('LNG_EMAIL_EVENT_NAME_DESC')
				);
				$placeholders['content'] = array_merge(
					$placeholders['content'],
					array(			
						EMAIL_APPOINTMENT_DATE => JText::_('LNG_EMAIL_APPOINTMENT_DATE_DESC'),
						EMAIL_EVENT_NAME => JText::_('LNG_EMAIL_EVENT_NAME_DESC'),			
						EMAIL_COMPANY_NAME => JText::_('LNG_EMAIL_COMPANY_NAME_DESC'),
					));
				break;
			case 'Event Booking Reminder':
				$placeholders['subject'] = array(
					EMAIL_EVENT_NAME => JText::_('LNG_EMAIL_EVENT_NAME_DESC')
				);
				$placeholders['content'] = array_merge(
					$placeholders['content'],
					array(			
						EMAIL_BOOKING_DATE => JText::_('LNG_EMAIL_BOOKING_DATE_DESC'),
						EMAIL_BOOKING_DETAILS => JText::_('LNG_EMAIL_BOOKING_DETAILS_DESC'),
						EMAIL_EVENT_NAME => JText::_('LNG_EMAIL_EVENT_NAME_DESC'),			
						EMAIL_COMPANY_NAME => JText::_('LNG_EMAIL_COMPANY_NAME_DESC'),
						EMAIL_EVENT_PICTURE => JText::_('LNG_EMAIL_EVENT_PICTURE_DESC')
					));
				break;
			case 'Payment Reminder':
				$placeholders['subject'] = array(
					EMAIL_COMPANY_NAME =>  JText::_('LNG_EMAIL_COMPANY_NAME_DESC')
				);
				$placeholders['content'] = array_merge(
					$placeholders['content'],
					array(			
						EMAIL_CUSTOMER_NAME => JText::_('LNG_EMAIL_CUSTOMER_NAME_DESC'),
						EMAIL_SITE_ADDRESS => JText::_('LNG_EMAIL_SITE_ADDRESS_DESC'),
						EMAIL_ORDER_ID => JText::_('LNG_EMAIL_ORDER_ID_DESC'),
						EMAIL_INVOICE_NUMBER => JText::_('LNG_EMAIL_INVOICE_NUMBER_DESC'),
						EMAIL_TOTAL_PRICE => JText::_('LNG_EMAIL_TOTAL_PRICE_DESC'),
						EMAIL_ORDER_PAYMENT_URL => JText::_('LNG_ORDER_PAYMENT_URL_DESC'),
					));
				break;
			case 'Subscription Email':
				$placeholders['subject'] = array(
					EMAIL_COMPANY_NAME =>  JText::_('LNG_EMAIL_COMPANY_NAME_DESC')
				);
				$placeholders['content'] = array_merge(
					$placeholders['content'],
					array(			
						EMAIL_CUSTOMER_NAME => JText::_('LNG_EMAIL_CUSTOMER_NAME_DESC'),
						EMAIL_CONTACT_EMAIL => JText::_('LNG_EMAIL_CONTACT_EMAIL_DESC'),
						EMAIL_PHONE =>  JText::_('LNG_EMAIL_PHONE_DESC'),
						EMAIL_FORM_TYPE =>  JText::_('LNG_EMAIL_EMAIL_FORM_TYPE'),
					));
				break;
			case 'Listing Editor Invitation':
				$placeholders['subject'] = array(
					EMAIL_BUSINESS_NAME => JText::_('LNG_EMAIL_BUSINESS_NAME_DESC')
				);
				$placeholders['content'] = array_merge(
					$placeholders['content'],
					array(			
						EMAIL_BUSINESS_NAME => JText::_('LNG_EMAIL_BUSINESS_NAME_DESC'),
						EMAIL_LINK => JText::_('LNG_EMAIL_LINK'),
					));
				break;
			case 'General Message':
				$placeholders['content'] = array_merge(
					$placeholders['content'],
					array(			
						EMAIL_MESSAGE => JText::_('LNG_MESSAGE'),
					));
				break;
			case 'Service Booking Status Update Notification':
				$placeholders['subject'] = array(
					EMAIL_SERVICE_BOOKING_ID => JText::_('LNG_EMAIL_SERVICE_BOOKING_ID_DESC'),
					EMAIL_SERVICE_BOOKING_STATUS => JText::_('LNG_EMAIL_SERVICE_BOOKING_STATUS')
				);
				$placeholders['content'] = array_merge($placeholders['content'], array(
					EMAIL_SERVICE_BOOKING_DATE => JText::_('LNG_EMAIL_SERVICE_BOOKING_DATE_DESC'),
					EMAIL_SERVICE_BOOKING_NAME => JText::_('LNG_EMAIL_SERVICE_BOOKING_NAME_DESC'),
					EMAIL_SERVICE_BOOKING_STATUS => JText::_('LNG_EMAIL_SERVICE_BOOKING_STATUS'),
					EMAIL_CUSTOMER_NAME => JText::_('LNG_EMAIL_CUSTOMER_NAME_DESC'),
					EMAIL_COMPANY_NAME => JText::_('LNG_EMAIL_COMPANY_NAME_DESC')
				));
				break;
			case 'Offer Report Notification':
			case 'Event Report Notification':
				$placeholders['content'] = array_merge($placeholders['content'], array(
					EMAIL_CONTACT_EMAIL => JText::_('LNG_EMAIL_CONTACT_EMAIL_DESC'),
					EMAIL_ABUSE_DESCRIPTION => JText::_('LNG_EMAIL_ABUSE_DESCRIPTION_DESC'),
					EMAIL_BUSINESS_NAME => JText::_('LNG_EMAIL_BUSINESS_NAME_DESC'),
					EMAIL_COMPANY_NAME => JText::_('LNG_EMAIL_COMPANY_NAME_DESC'),
					EMAIL_REPORT_CAUSE => JText::_('LNG_EMAIL_REPORT_CAUSE_DESC')
				));
				unset($placeholders['content'][EMAIL_USER_NAME]);
				break;
			default:
				return $placeholders;
		}

		ksort($placeholders['subject']);
		ksort($placeholders['content']);

		return $placeholders;
	}

	public function getEmailTypes() {
		return array(
			'New Company Notification Email'            => JText::_('LNG_NEW_COMPANY_NOTIFICATION_EMAIL'),
			'Listing Creation Notification'             => JText::_('LNG_LISTING_CREATION_NOTIFICATION_EMAIL'),
			'Approve Email'                             => JText::_('LNG_APPROVE_EMAIL'),
			'Claim Request Email'                       => JText::_('LNG_CLAIM_REQUEST_EMAIL'),
			'Claim Response Email'                      => JText::_('LNG_CLAIM_RESPONSE_EMAIL'),
			'Claim Negative Response Email'             => JText::_('LNG_CLAIM_NEGATIVE_RESPONSE_EMAIL'),
			'Contact Email'                             => JText::_('LNG_CONTACT_EMAIL'),
			'Request Info Email'                        => JText::_('LNG_REQUEST_INFO_EMAIL'),
			'Request Quote Email'                       => JText::_('LNG_REQUEST_QUOTE'),
			'Order Email'                               => JText::_('LNG_ORDER_EMAIL'),
			'Payment Details Email'                     => JText::_('LNG_PAYMENT_DETAILS_EMAIL'),
			'Expiration Notification Email'             => JText::_('LNG_EXPIRATION_NOTIFICATION_EMAIL'),
			'Review Email'                              => JText::_('LNG_REVIEW_EMAIL'),
			'Review Response Email'                     => JText::_('LNG_REVIEW_RESPONSE_EMAIL'),
			'Report Abuse Email'                        => JText::_('LNG_REPORT_ABUSE_EMAIL'),
			'Offer Creation Notification'               => JText::_('LNG_OFFER_CREATION_NOTIFICATION'),
			'Offer Approval Notification'               => JText::_('LNG_OFFER_APPROVAL_NOTIFICATION'),
			'Offer Expiration Notification Email'       => JText::_('LNG_OFFER_EXPIRATION_NOTIFICATION_EMAIL'),
			'Offer Contact Email'                       => JText::_('LNG_OFFER_CONTACT_EMAIL'),
			'Event Creation Notification'               => JText::_('LNG_EVENT_CREATION_NOTIFICATION'),
			'Event Approval Notification'               => JText::_('LNG_EVENT_APPROVAL_NOTIFICATION'),
			'Event Reservation Notification'            => JText::_('LNG_EVENT_RESERVATION_NOTIFICATION'),
			'Event Payment Details'                     => JText::_('LNG_EVENT_PAYMENT_DETAILS'),
			'Event Expiration Notification Email'       => JText::_('LNG_EVENT_EXPIRATION_NOTIFICATION_EMAIL'),
			'Event Contact Email'                       => JText::_('LNG_EVENT_CONTACT_EMAIL'),
			'Offer Order Notification'                  => JText::_('LNG_OFFER_ORDER_NOTIFICATION'),
			'Event Appointment Email'                   => JText::_('LNG_EVENT_APPOINTMENT_EMAIL'),
			'Event Appointment Status Notification'     => JText::_('LNG_EVENT_APPOINTMENT_STATUS_NOTIFICATION'),
			'Report Abuse Offer Review'                 => JText::_('LNG_REPORT_ABUSE_OFFER_REVIEW'),
			'Service Booking Notification'              => JText::_('LNG_SERVICE_BOOKING_NOTIFICATION'),
			'Company Association Notification'          => JText::_('LNG_COMPANY_ASSOCIATION_NOTIFICATION'),
			'Report Notification'                       => JText::_('LNG_REPORT_NOTIFICATION'),
			'Disapprove Email'                          => JText::_('LNG_DISAPPROVE_EMAIL'),
			'Business Statistics Email'                 => JText::_('LNG_BUSINESS_STATISTICAL'),
			'Business Upgrade Notification'             => JText::_('LNG_BUSINESS_UPGRADE'),
			'Offer Shipping Notification'               => JText::_('LNG_OFFER_SHIPPING_NOTIFICATION'),
			'Event Reservation Waiting Notification'    => JText::_('LNG_EVENT_RESERVATION_WAITING_NOTIFICATION'),
			'Service Booking Waiting Notification'      => JText::_('LNG_SERVICE_BOOKING_WAITING_NOTIFICATION'),
			'Offer Order Waiting Notification'          => JText::_('LNG_OFFER_ORDER_WAITING_NOTIFICATION'),
			'Campaign Payment Notification'             => JText::_('LNG_CAMPAIGN_PAYMENT_NOTIFICATION'),
			'Campaign Payment Waiting Notification'     => JText::_('LNG_CAMPAIGN_PAYMENT_WAITING_NOTIFICATION'),
			'Request Quote'                             => JText::_('LNG_REQUEST_QUOTE'),
			'Payment Notification'                      => JText::_('LNG_PAYMENT_NOTIFICATION'),
			'Business Update Notification'              => JText::_('LNG_BUSINESS_UPDATE_NOTIFICATION'),
			'Offer Review Response Email'               => JText::_('LNG_OFFER_REVIEW_RESPONSE_EMAIL'),
			'Offer Review Email'                        => JText::_('LNG_OFFER_REVIEW_EMAIL'),
			'Listing owner changed'                     => JText::_('LNG_LISTING_OWNER_CHANGE'),
			'Request Quote Product Email'             	=> JText::_('LNG_REQUEST_QUOTE_PRODUCT_EMAIL'),
			'Product Creation Notification'             => JText::_('LNG_PRODUCT_CREATION_NOTIFICATION'),
			'Company Joining Notification'              => JText::_('LNG_COMPANY_JOINING_NOTIFICATION'),
			'Listing Creation Notification to Owner'    => JText::_('LNG_LISTING_CREATION_NOTIFICATION_EMAIL_TO_OWNER'),
			'New Review Notification'    				=> JText::_('LNG_NEW_REVIEW_NOTIFICATION'),
			'Offer Low Quantity Notification'           => JText::_('LNG_OFFER_LOW_QUANTITY_NOTIFICATION'),
			'Quote Request Confirmation'                => JText::_('LNG_REQUEST_QUOTE_CONFIRMATION'),
			'Quote Request Reply Notification'          => JText::_('LNG_REQUEST_QUOTE_REPLY_NOTIFICATION'),
			'Appointment URL Notification'              => JText::_('LNG_APPOINTMENT_URL_NOTIFICATION'),
			'Email verification'        			    => JText::_('LNG_EMAIL_VERIFICATION'),
			'Appointment Email Notification'        	=> JText::_('LNG_APPOINTMENT_EMAIL_NOTIFICATION'),
			'Test Email' 								=> JText::_('LNG_TEST_EMAIL'),
			'Hire Email'                                => JText::_('LNG_HIRE_EMAIL'),
			'Service Booking Reminder'                  => JText::_('LNG_SERVICE_BOOKING_REMINDER'),
			'Event Appointment Reminder'                => JText::_('LNG_EVENT_APPOINTMENT_REMINDER'),
			'Event Booking Reminder'                    => JText::_('LNG_EVENT_BOOKING_REMINDER'),
			'Payment Reminder'		                    => JText::_('LNG_PAYMENT_REMINDER'),
			'Subscription Email' 						=> JText::_('LNG_SUBSCRIPTION_EMAIL'),
			'Listing Editor Invitation' 				=> JText::_('LNG_LISTING_EDITOR_INVITATION'),
			'General Message' 							=> JText::_('LNG_GENERAL_MESSAGE'),
			'Service Booking Status Update Notification'=> JText::_('LNG_SERVICE_BOOKING_STATUS_UPDATE_NOTIFICATION'),
			'Listing Review'                            => JText::_('LNG_LISTING_REVIEW'),
			'Listing Review Free Trial'					=> JText::_('LNG_LISTING_REVIEW_FREE_TRIAL'),
			'Offer Report Notification'                 => JText::_('LNG_OFFER_REPORT_NOTIFICATION'),
			'Event Report Notification'                 => JText::_('LNG_EVENT_REPORT_NOTIFICATION'),
		);
	}

	/**
     * Method to perform batch operations on an item or a set of items.
     *
     * @param array $vars
     * @param array $pks
     * @param array $contexts
     * @return bool
     */
    public function batch($vars, $pks, $contexts)
    {
        // Sanitize ids.
        $pks = array_unique($pks);
        JArrayHelper::toInteger($pks);

        // Remove any values of zero.
        if (array_search(0, $pks, true))
        {
            unset($pks[array_search(0, $pks, true)]);
        }

        if (empty($pks))
        {
            $this->setError(JText::_('JGLOBAL_NO_ITEM_SELECTED'));

            return false;
        }

        $done = false;

        // Set some needed variables.
        $this->user = JFactory::getUser();
        $this->table = $this->getTable();
        $this->tableClassName = get_class($this->table);
        $this->batchSet = true;
        // Parent exists so let's proceed
        while (!empty($pks))
        {
            // Pop the first ID off the stack
            $pk = array_shift($pks);

            $this->table->reset();

            // Check that the row actually exists
            if (!$this->table->load($pk))
            {
                if ($error = $this->table->getError())
                {
                    // Fatal error
                    $this->setError($error);

                    return false;
                }
                else
                {
                    // Not fatal error
                    $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
                    continue;
                }
            }

            // set new approval state
            if ($vars["status_id"]!="")
            {
                $this->table->status = $vars["status_id"];
            }

            // Check the row.
            if (!$this->table->check())
            {
                $this->setError($this->table->getError());

                return false;
            }

            // Store the row.
            if (!$this->table->store())
            {
                $this->setError($this->table->getError());

                return false;
            }

        }

        // Clean the cache
        $this->cleanCache();

        return true;
    }

}