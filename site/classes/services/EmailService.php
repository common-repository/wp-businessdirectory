<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class EmailService {
	public static function sendPaymentEmail($company, $paymentDetails) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();
		$billingInformation = self::getBillingInformation($company);

		$templ = self::getEmailTemplate("Order Email");
		if (empty($templ)) {
			return false;
		}

		$content = self::prepareEmail($paymentDetails, $company, $templ->email_content, $applicationSettings->company_name, $billingInformation, $applicationSettings->vat);
		$content = self::updateCompanyDetails($content);
		$content = str_replace(EMAIL_CUSTOMER_NAME, $paymentDetails->billingDetails->first_name . ' ' . $paymentDetails->billingDetails->last_name, $content);

		$subject = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $templ->email_subject);
		$toEmail = $paymentDetails->billingDetails->email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, null, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	public static function sendPaymentDetailsEmail($company, $paymentDetails) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();
		$billingInformation = self::getBillingInformation($company);

		$templ = self::getEmailTemplate("Payment Details Email");
		if (empty($templ)) {
			return false;
		}

		$content = self::prepareEmail($paymentDetails, $company, $templ->email_content, $applicationSettings->company_name, $billingInformation, $applicationSettings->vat);
		$content = str_replace(EMAIL_PAYMENT_DETAILS, $paymentDetails->details->details, $content);

		$content = self::updateCompanyDetails($content);
		$content = str_replace(EMAIL_CUSTOMER_NAME, $paymentDetails->billingDetails->first_name . ' ' . $paymentDetails->billingDetails->last_name, $content);

		$subject = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $templ->email_subject);
		$toEmail = $paymentDetails->billingDetails->email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$replyTo = null;

		$result = self::sendEmail($fromName, $replyTo, $toEmail, null, null, $subject, $content, $isHtml, $templ->send_to_admin);

		return $result;
	}

	public static function sendNewCompanyNotificationEmailToAdmin($company) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$templ = self::getEmailTemplate("New Company Notification Email");
		if (empty($templ)) {
			return false;
		}

		$content = self::prepareNotificationEmail($company, $templ->email_content);
		$content = self::updateCompanyDetails($content);

		$subject = $templ->email_subject;
		$toEmail = $applicationSettings->company_email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, null, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	/**
	 * Send an email notification to item owner if its quantity is lower or equal than the setting that he has set
	 *
	 * @param $offerId int id of the item
	 * @param $data array contains the item data like orderDetails and notifyQty
	 *
	 * @return bool|int|JException
	 *
	 * @since version
	 */
	public static function sendLowQuantityNotificationEmail($offerId, $data){
		$templ = self::getEmailTemplate("Offer Low Quantity Notification");
		if (empty($templ)) {
			return false;
		}
		$applicationSettings = JBusinessUtil::getApplicationSettings();
		JTable::addIncludePath(JPATH_ROOT.'/administrator/components/com_jbusinessdirectory/tables');
		$offerTable = JTable::getInstance("Offer", "JTable");
		$offer = $offerTable->getOffer($offerId);

		$orderDetails = '';
		if (isset($data['orderDetails'])){
			$attributeDetails = explode('##',$data['orderDetails']);
			$attributeDetails = array_values(array_filter($attributeDetails));
			if (!empty($attributeDetails)) {
				$orderDetails .= '<div>'.JText::_("LNG_STOCK_DETAILS").":</div>";
				foreach ($attributeDetails as $attributeDetail) {
					$orderDetails .= '<div><strong>' . explode(' => ', $attributeDetail)[0] . ':</strong> ' . explode(' => ', $attributeDetail)[1] . '</div>';
				}
			}
		}

		$content = self::updateCompanyDetails($templ->email_content);
		$content = self::updateUserName($offer->user_id, $content);
		$content = str_replace(EMAIL_OFFER_STOCK_DETAILS, $orderDetails, $content);
		$offerLink = '<a target="_blank" title="' . $offer->subject . '" href="' . JBusinessUtil::getOfferLink($offer->id, $offer->alias) . '" >' . $offer->subject . '</a>';
		$content = str_replace(EMAIL_OFFER_NAME, $offerLink, $content);
		$content = str_replace(EMAIL_OFFER_NOTIFICATION_QUANTITY, $data['notifyQty'], $content);
		$content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $content);

		$subject = str_replace(EMAIL_OFFER_NAME, $offer->subject, $templ->email_subject);
		$toEmail = $offer->email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, null, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	public static function sendNewCompanyNotificationEmailToOwner($company) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$templ = self::getEmailTemplate("Listing Creation Notification");
		if (empty($templ)) {
			return false;
		}

		$content = self::prepareNotificationEmail($company, $templ->email_content);
		$content = self::updateCompanyDetails($content);

		$user = JBusinessUtil::getUser($company->userId);
		
		$subject = $templ->email_subject;
		$toEmail = $user->email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, null, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	public static function sendNewCompanyNotificationEmailForClaimToOwner($company) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$templ = self::getEmailTemplate("Listing Creation Notification to Owner");
		if (empty($templ)) {
			return false;
		}

		$content = self::prepareNotificationEmail($company, $templ->email_content);
		$content = self::updateCompanyDetails($content);

		$subject = str_replace(EMAIL_BUSINESS_NAME, $company->name, $templ->email_subject);
		$toEmail = $company->email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, null, $subject, $content, $isHtml, $templ->send_to_admin);
	}


	/**
	 * Send a notification email to a company that has not activated the reucurring payments
	 *
	 * @param  $company
	 * @return boolean
	 */
	public static function sendPayamentEmailNotificationEmail($company) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$templ = self::getEmailTemplate("Payment Notification");
		if (empty($templ)) {
			return false;
		}

		$ordersLink = JBusinessUtil::getWebsiteUrl(true) . "index.php?option=com_jbusinessdirectory&view=billingoverview";
		$ordersLink = '<a href="' . $ordersLink . '" >' . JText::_('LNG_HERE') . '</a>';
		$templ->email_content = str_replace(EMAIL_ORDERS_LINK, $ordersLink, $templ->email_content);
		$content = self::prepareNotificationEmail($company, $templ->email_content);
		$content = self::updateCompanyDetails($content);

		$subject = $templ->email_subject;
		$toEmail = $company->email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, null, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	/**
	 * Send statistics of each company
	 * @param $company
	 * @return bool|int|JException
	 */
	public static function sendStatisticsNotificationEmail($company) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();
		$eventDetails = "";
		$offerDetails = "";
		$reviewDetails = "";

		$templ = self::getEmailTemplate("Business Statistics Email");
		if (empty($templ)) {
			return false;
		}

		if (empty($company->events)) {
			$eventDetails .= JText::_("LNG_NO_EVENTS_THIS_MONTH");
		} else {
			foreach ($company->events as $event) {
				if (!empty($event->picture_path)) {
					$eventDetails .= '<img height="111" style="width:165px" src="' . (BD_PICTURES_PATH . $event->picture_path) . '"/>';
				} else {
					$eventDetails .= '<img height="111" style="width:165px" src="' . (BD_PICTURES_PATH . '/no_image.jpg') . '"/>';
				}
				$eventDetails .= '<br/>';
				$eventDetails .= '<a title="' . $event->name . '" href="' . JBusinessUtil::getEventLink($event->id, $event->alias) . '" >' . $event->name . '</a>';
				$eventDetails .= '<br/>';
				$eventDetails .= JText::_("LNG_TYPE") . ": " . $event->eventType;
				$eventDetails .= '<br/>';
				$eventDetails .= JText::_("LNG_VISITED") . ": " . $event->view_count . " " . JText::_("LNG_TIMES");
				$eventDetails .= '<hr />';
				$eventDetails .= '<br/><br/>';
			}
		}

		if (empty($company->offers)) {
			$offerDetails .= JText::_("LNG_NO_OFFERS_THIS_MONTH");
		} else {
			foreach ($company->offers as $offer) {
				if (!empty($offer->picture_path)) {
					$offerDetails .= '<img height="111" style="width:165px" src="' . (BD_PICTURES_PATH . $offer->picture_path) . '"/>';
				} else {
					$offerDetails .= '<img height="111" style="width:165px" src="' . (BD_PICTURES_PATH . '/no_image.jpg') . '"/>';
				}
				$offerDetails .= '<br/>';
				$offerDetails .= '<a title="' . $offer->subject . '" href="' . JBusinessUtil::getOfferLink($offer->id, $offer->alias) . '" >' . $offer->subject . '</a>';
				$offerDetails .= '<br/>';
				$offerDetails .= JText::_("LNG_VISITED") . ": " . $offer->viewCount . " " . JText::_("LNG_TIMES");
				$offerDetails .= '<hr />';
				$offerDetails .= '<br/><br/>';
			}
		}

		if (empty($company->reviews)) {
			$reviewDetails .= JText::_("LNG_NO_REVIEW_THIS_MONTH");
		} else {
			foreach ($company->reviews as $review) {
				$reviewDetails .= $review->subject . '<br/>';
				$reviewDetails .= $review->description . '<br/>';
				$reviewDetails .= JText::_("LNG_LIKES") . " : " . $review->likeCount . '<br/>';
				$reviewDetails .= JText::_("LNG_DISLIKES") . " : " . $review->dislikeCount . '<br/>';
				$reviewDetails .= JText::_("LNG_RATING") . " : " . $review->rating . '<br/>';
				$reviewDetails .= '<hr />';
				$reviewDetails .= '<br/>';
			}
		}

		$reviewNumber = count($company->reviews);

		$content = self::prepareNotificationEmail($company, $templ->email_content);

		$content = str_replace(BUSINESS_VIEW_COUNT, $company->viewCount, $content);
		$content = str_replace(MONTHLY_VIEW_COUNT, $company->monthlyView, $content);
		$content = str_replace(MONTHLY_ARTICLE_VIEW_COUNT, $company->articlesViews, $content);
		$content = str_replace(BUSINESS_RATING, $company->review_score, $content);
		$content = str_replace(BUSINESS_REVIEW_NUMBER, $reviewNumber, $content);
		$content = str_replace(EVENTS_DETAILS, $eventDetails, $content);
		$content = str_replace(OFFER_DETAILS, $offerDetails, $content);
		$content = str_replace(BUSINESS_REVIEW, $reviewDetails, $content);

		$content = self::updateCompanyDetails($content);

		$subject = $templ->email_subject;
		$toEmail = $company->email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, null, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	/**
	 * For each business wich have a free plan will be send an notification for upgrade
	 * @param $company
	 * @return bool|int|JException
	 */
	public static function sendUpgradeNotificationEmail($company) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$templ = self::getEmailTemplate("Business Upgrade Notification");
		if (empty($templ)) {
			return false;
		}

		$link = JBusinessUtil::getWebsiteUrl(true) . 'index.php?option=com_jbusinessdirectory&view=managecompany&layout=edit&id=' . $company->id;
		$content = self::prepareNotificationEmail($company, $templ->email_content);
		$content = str_replace(BUSINESS_PATH_CONTROL_PANEL, $link, $content);
		$content = self::updateCompanyDetails($content);

		$subject = $templ->email_subject;
		$toEmail = $company->email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, null, $subject, $content, $isHtml, $templ->send_to_admin);
	}


	public static function sendNewOfferNotification($offer, $itemType) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		if ($itemType == OFFER_TYPE_PRODUCT) {
			$templ = self::getEmailTemplate("Product Creation Notification");
		} else {
			$templ = self::getEmailTemplate("Offer Creation Notification");
		}
		if (empty($templ)) {
			return false;
		}

		$content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $templ->email_content);
		$offerLink = '<a title="' . $offer->subject . '" href="' . JBusinessUtil::getOfferLink($offer->id, $offer->alias) . '" >' . $offer->subject . '</a>';
		$content = str_replace(EMAIL_OFFER_NAME, $offerLink, $content);
		$content = self::updateCompanyDetails($content);

		$subject = $templ->email_subject;
		$toEmail = $applicationSettings->company_email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, null, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	public static function sendApproveOfferNotification($offer, $company) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$templ = self::getEmailTemplate("Offer/Product Approval Notification");
		if (empty($templ)) {
			return false;
		}

		$content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $templ->email_content);
		$offerLink = '<a title="' . $offer->subject . '" href="' . JBusinessUtil::getOfferLink($offer->id, $offer->alias) . '" >' . $offer->subject . '</a>';
		$content = str_replace(EMAIL_OFFER_NAME, $offerLink, $content);
		$content = self::updateCompanyDetails($content);
		$content = self::updateUserName($company->userId, $content);

		if ($offer->item_type == OFFER_TYPE_OFFER) {
			$subject = str_replace(EMAIL_ITEM_TYPE, JText::_("LNG_OFFER"), $templ->email_subject);
			$content = str_replace(EMAIL_ITEM_TYPE, JText::_("LNG_OFFER"), $content);
		} else {
			$subject = str_replace(EMAIL_ITEM_TYPE, JText::_("LNG_PRODUCT"), $templ->email_subject);
			$content = str_replace(EMAIL_ITEM_TYPE, JText::_("LNG_PRODUCT"), $content);
		}

		$toEmail = $company->email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, null, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	public static function sendOfferOrderNotification($orderDetails) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$templ = self::getEmailTemplate("Offer Order Notification");
		if (empty($templ)) {
			return false;
		}

		$content = str_replace(EMAIL_OFFER_ORDER_DATE, JBusinessUtil::getDateGeneralFormatWithTime($orderDetails->created), $templ->email_content);
		$content = str_replace(EMAIL_OFFER_ORDER_DETAILS, $orderDetails->itemsSummary, $content);
		$content = str_replace(EMAIL_OFFER_ORDER_BUYER_DETAILS, $orderDetails->buyerDetailsSummary, $content);
		$content = str_replace(EMAIL_OFFER_ORDER_ID, $orderDetails->id, $content);
		$content = str_replace(EMAIL_CUSTOMER_NAME, $orderDetails->billingDetails->first_name . " " . $orderDetails->billingDetails->last_name, $content);
		$content = self::updateCompanyDetails($content);

		$subject = str_replace(EMAIL_OFFER_ORDER_ID, $orderDetails->id, $templ->email_subject);
		$toEmail = $orderDetails->email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	public static function sendOfferOrderWaitingNotification($orderDetails) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$templ = self::getEmailTemplate("Offer Order Waiting Notification");
		if (empty($templ)) {
			return false;
		}

		$content = str_replace(EMAIL_OFFER_ORDER_DATE, JBusinessUtil::getDateGeneralFormatWithTime($orderDetails->created), $templ->email_content);
		$content = str_replace(EMAIL_OFFER_ORDER_DETAILS, $orderDetails->itemsSummary, $content);
		$content = str_replace(EMAIL_OFFER_ORDER_BUYER_DETAILS, $orderDetails->buyerDetailsSummary, $content);
		$content = str_replace(EMAIL_OFFER_ORDER_ID, $orderDetails->id, $content);
		$content = str_replace(EMAIL_CUSTOMER_NAME, $orderDetails->first_name . " " . $orderDetails->last_name, $content);

		$content = str_replace(EMAIL_PAYMENT_DETAILS, $orderDetails->paymentDetails->details, $content);
		$content = self::updateCompanyDetails($content);

		$subject = str_replace(EMAIL_OFFER_ORDER_ID, $orderDetails->id, $templ->email_subject);
		$toEmail = $orderDetails->email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	public static function sendOfferShippingNotification($orderDetails) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$templ = self::getEmailTemplate("Offer Shipping Notification");
		if (empty($templ)) {
			return false;
		}

		$content = str_replace(EMAIL_OFFER_ORDER_DATE, JBusinessUtil::getDateGeneralFormatWithTime($orderDetails->created), $templ->email_content);
		$content = str_replace(EMAIL_OFFER_ORDER_DETAILS, $orderDetails->reservedItems, $content);
		$content = str_replace(EMAIL_OFFER_ORDER_BUYER_DETAILS, $orderDetails->buyerDetails, $content);
		$content = str_replace(EMAIL_OFFER_ORDER_TRACKING_LINK, $orderDetails->tracking_link, $content);
		$content = str_replace(EMAIL_CUSTOMER_NAME, $orderDetails->allData->first_name . " " . $orderDetails->allData->last_name, $content);

		$content = self::updateCompanyDetails($content);

		$subject = $templ->email_subject;
		$toEmail = $orderDetails->email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	public static function sendServiceBookingNotification($orderDetails) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$templ = self::getEmailTemplate("Service Booking Notification");
		if (empty($templ)) {
			return false;
		}

		$content = str_replace(EMAIL_SERVICE_BOOKING_DATE, JBusinessUtil::getDateGeneralFormatWithTime($orderDetails->created), $templ->email_content);
		$content = str_replace(EMAIL_SERVICE_BOOKING_DETAILS, $orderDetails->serviceDetails, $content);
		$content = str_replace(EMAIL_SERVICE_BUYER_DETAILS, $orderDetails->buyerDetails, $content);
		$content = str_replace(EMAIL_SERVICE_BOOKING_NAME, $orderDetails->serviceName, $content);
		$content = self::updateCompanyDetails($content);
		$content = str_replace(EMAIL_CUSTOMER_NAME, $orderDetails->first_name . " " . $orderDetails->last_name, $content);
		$content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $content);

		$subject = str_replace(EMAIL_SERVICE_BOOKING_ID, $orderDetails->id, $templ->email_subject);
		$toEmail = $orderDetails->email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	public static function sendServiceBookingWaitingNotification($orderDetails) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$templ = self::getEmailTemplate("Service Booking Waiting Notification");
		if (empty($templ)) {
			return false;
		}

		$content = str_replace(EMAIL_SERVICE_BOOKING_DATE, JBusinessUtil::getDateGeneralFormatWithTime($orderDetails->created), $templ->email_content);
		$content = str_replace(EMAIL_SERVICE_BOOKING_DETAILS, $orderDetails->serviceDetails, $content);
		$content = str_replace(EMAIL_SERVICE_BUYER_DETAILS, $orderDetails->buyerDetails, $content);
		$content = str_replace(EMAIL_SERVICE_BOOKING_NAME, $orderDetails->serviceName, $content);
		$content = self::updateCompanyDetails($content);
		$content = str_replace(EMAIL_CUSTOMER_NAME, $orderDetails->first_name . " " . $orderDetails->last_name, $content);

		$content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $content);
		$content = str_replace(EMAIL_PAYMENT_DETAILS, $orderDetails->paymentDetails->details, $content);

		$subject = str_replace(EMAIL_SERVICE_BOOKING_ID, $orderDetails->id, $templ->email_subject);
		$toEmail = $orderDetails->email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	public static function sendCompanyAssociationNotification($event, $companyNames) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$templ = self::getEmailTemplate("Company Association Notification");
		if (empty($templ)) {
			return false;
		}

		$eventLink = '<a title="' . $event->name . '" href="' . JBusinessUtil::getEventLink($event->id, $event->alias) . '" >' . $event->name . '</a>';
		$content = str_replace(EMAIL_EVENT_NAME, $eventLink, $templ->email_content);
		$content = str_replace(EMAIL_COMPANY_NAMES, $companyNames, $content);
		$content = self::updateCompanyDetails($content);
		$content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $content);
		$content = self::updateUserName($event->user_id, $content);

		$subject = str_replace(EMAIL_EVENT_NAME, $event->name, $templ->email_subject);
		$toEmail = $event->contact_email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	public static function sendCompanyJoiningNotification($company) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$templ = self::getEmailTemplate('Company Joining Notification');
		if (empty($templ)) {
			return false;
		}

		$content = self::prepareNotificationEmail($company, $templ->email_content);
		$content = self::updateCompanyDetails($content);
		$content = self::updateUserName($company->userId, $content);
		$link = JBusinessUtil::getWebsiteUrl(true) . 'index.php?option=com_jbusinessdirectory&view=managelistingregistrations';
		$linkText = '<a href="' . $link . '" target="_blank">' . JText::_('LNG_LISTING_JOIN_SECTION') . '</a>';
		$content = str_replace(BUSINESS_JOIN_PATH_CONTROL_PANEL, $linkText, $content);

		$subject = str_replace(EMAIL_BUSINESS_NAME, $company->name, $templ->email_subject);
		$toEmail = $company->email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	public static function sendNewEventNotification($event) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$templ = self::getEmailTemplate("Event Creation Notification");
		if (empty($templ)) {
			return false;
		}

		$content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $templ->email_content);
		$eventLink = '<a title="' . $event->name . '" href="' . JBusinessUtil::getEventLink($event->id, $event->alias) . '" >' . $event->name . '</a>';
		$content = str_replace(EMAIL_EVENT_NAME, $eventLink, $content);
		$content = self::updateCompanyDetails($content);

		$subject = $templ->email_subject;
		$toEmail = $applicationSettings->company_email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, null, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	public static function sendApproveEventNotification($event, $company) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();
		
		$email = "";
		if(!empty($event->email)){
			$email = $event->email;
		}else if(isset($company->email)){
			$email = $company->email;
		}
	
		$templ = self::getEmailTemplate("Event Approval Notification");
		if (empty($templ)) {
			return false;
		}
		$content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $templ->email_content);
		$eventLink = '<a title="' . $event->name . '" href="' . JBusinessUtil::getEventLink($event->id, $event->alias) . '" >' . $event->name . '</a>';
		$content = str_replace(EMAIL_EVENT_NAME, $eventLink, $content);
		$content = self::updateCompanyDetails($content);
		$content = self::updateUserName($company->userId, $content);

		$subject = $templ->email_subject;
		$toEmail = $email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	public static function sendEventPaymentDetails($bookingDetails) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$templ = self::getEmailTemplate("Event Payment Details");
		if (empty($templ)) {
			return false;
		}

		$content = str_replace(EMAIL_EVENT_NAME, $bookingDetails->event->name, $templ->email_content);

		$content = str_replace(EMAIL_CUSTOMER_NAME, $bookingDetails->first_name . " " . $bookingDetails->last_name, $content);
		
		$eventAddress = JBusinessUtil::getAddressText($bookingDetails->event);
		$content = str_replace(EMAIL_EVENT_ADDRESS, $eventAddress, $content);
		$content = str_replace(EMAIL_PAYMENT_DETAILS, $bookingDetails->details->details, $content);
		$content = str_replace(EMAIL_EVENT_START_DATE, JBusinessUtil::getDateGeneralFormat($bookingDetails->event->start_date), $content);
		$content = str_replace(EMAIL_BOOKING_DATE, JBusinessUtil::getDateGeneralFormatWithTime($bookingDetails->created), $content);
		$content = str_replace(EMAIL_BOOKING_DETAILS, $bookingDetails->ticketDetailsSummary, $content);
		$content = str_replace(EMAIL_BOOKING_GUEST_DETAILS, $bookingDetails->guestDetailsSummary, $content);
		$content = str_replace(EMAIL_EVENT_PHONE, $bookingDetails->event->contact_phone, $content);
		$content = str_replace(EMAIL_EVENT_EMAIL, $bookingDetails->event->contact_email, $content);
		$content = str_replace(EMAIL_BOOKING_ID, $bookingDetails->id, $content);

		$siteAddress = JBusinessUtil::getWebsiteUrl(true);
		$content = str_replace(EMAIL_SITE_ADDRESS, $siteAddress, $content);

		$content = self::updateCompanyDetails($content);
		$subject = str_replace(EMAIL_EVENT_NAME, $bookingDetails->event->name, $templ->email_subject);
		$toEmail = $bookingDetails->email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		$replyTo = null;

		$result = self::sendEmail($fromName, $replyTo, $toEmail, null, null, $subject, $content, $isHtml, $templ->send_to_admin);
		return $result;
	}

	/**
	 * Prepare & send event reservation email
	 * @param $bookingDetails
	 */
	public static function sendEventReservationNotification($bookingDetails) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$templ = self::getEmailTemplate("Event Reservation Notification");
		if (empty($templ)) {
			return false;
		}

		$eventEmail = !empty($bookingDetails->event->contact_email) ? $bookingDetails->event->contact_email : $bookingDetails->event->companyEmail;
		$eventPhone = !empty($bookingDetails->event->contact_phone) ? $bookingDetails->event->contact_phone : $bookingDetails->event->companyPhone;

		$content = str_replace(EMAIL_EVENT_NAME, $bookingDetails->event->name, $templ->email_content);
		$eventLink = '<a title="' . $bookingDetails->event->name . '" href="' . JBusinessUtil::getEventLink($bookingDetails->event->id, $bookingDetails->event->alias) . '" >' . JText::_('LNG_HERE') . '</a>';
		$content = str_replace(EMAIL_EVENT_LINK, $eventLink, $content);

		$eventAddress = JBusinessUtil::getAddressText($bookingDetails->event);
		$content = str_replace(EMAIL_EVENT_ADDRESS, $eventAddress, $content);

		$content = str_replace(EMAIL_EVENT_START_DATE, JBusinessUtil::getDateGeneralFormat($bookingDetails->event->start_date), $content);
		$content = str_replace(EMAIL_BOOKING_DATE, JBusinessUtil::getDateGeneralFormatWithTime($bookingDetails->created), $content);
		$content = str_replace(EMAIL_BOOKING_DETAILS, $bookingDetails->ticketDetailsSummary, $content);
		$content = str_replace(EMAIL_BOOKING_GUEST_DETAILS, $bookingDetails->guestDetailsSummary, $content);
		$content = str_replace(EMAIL_EVENT_PHONE, $eventPhone, $content);
		$content = str_replace(EMAIL_EVENT_EMAIL, $eventEmail, $content);
		$content = str_replace(EMAIL_CUSTOMER_NAME, $bookingDetails->guestDetails->first_name . " " . $bookingDetails->guestDetails->last_name, $content);

		$content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $content);

		$logoContent = '<img height="111" src="' . (BD_PICTURES_PATH . '/no_image.jpg') . '"/>';
		if (!empty($bookingDetails->event->pictures)) {
			$bookingDetails->event->pictures[0]->picture_path = str_replace(" ", "%20", $bookingDetails->event->pictures[0]->picture_path);
			$logoContent = '<img height="111" src="' . (BD_PICTURES_PATH . $bookingDetails->event->pictures[0]->picture_path) . '"/>';
		}

		$logoContent = '<a href="' . JBusinessUtil::getEventLink($bookingDetails->event->id, $bookingDetails->event->alias) . '">' . $logoContent . '</a>';
		$content = str_replace(EMAIL_EVENT_PICTURE, $logoContent, $content);
		$content = self::updateCompanyDetails($content);

		$subject = str_replace(EMAIL_EVENT_NAME, $bookingDetails->event->name, $templ->email_subject);
		$toEmail = $bookingDetails->email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	/**
	 * Prepare & send event reservation waiting email
	 * @param $bookingDetails
	 */
	public static function sendEventReservationWaitingNotification($bookingDetails) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$templ = self::getEmailTemplate("Event Reservation Waiting Notification");
		if (empty($templ)) {
			return false;
		}

		$eventEmail = !empty($bookingDetails->event->contact_email) ? $bookingDetails->event->contact_email : $bookingDetails->event->companyEmail;
		$eventPhone = !empty($bookingDetails->event->contact_phone) ? $bookingDetails->event->contact_phone : $bookingDetails->event->companyPhone;

		$content = str_replace(EMAIL_EVENT_NAME, $bookingDetails->event->name, $templ->email_content);
		$eventLink = '<a title="' . $bookingDetails->event->name . '" href="' . JBusinessUtil::getEventLink($bookingDetails->event->id, $bookingDetails->event->alias) . '" >' . JText::_('LNG_HERE') . '</a>';
		$content = str_replace(EMAIL_EVENT_LINK, $eventLink, $content);

		$eventAddress = JBusinessUtil::getAddressText($bookingDetails->event);
		$content = str_replace(EMAIL_EVENT_ADDRESS, $eventAddress, $content);

		$content = str_replace(EMAIL_EVENT_START_DATE, JBusinessUtil::getDateGeneralFormat($bookingDetails->event->start_date), $content);
		$content = str_replace(EMAIL_BOOKING_DATE, JBusinessUtil::getDateGeneralFormatWithTime($bookingDetails->created), $content);
		$content = str_replace(EMAIL_BOOKING_DETAILS, $bookingDetails->ticketDetailsSummary, $content);
		$content = str_replace(EMAIL_BOOKING_GUEST_DETAILS, $bookingDetails->guestDetailsSummary, $content);
		$content = str_replace(EMAIL_EVENT_PHONE, $eventPhone, $content);
		$content = str_replace(EMAIL_EVENT_EMAIL, $eventEmail, $content);

		$content = str_replace(EMAIL_PAYMENT_DETAILS, $bookingDetails->paymentDetails->details, $content);
		$content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $content);
		$content = str_replace(EMAIL_CUSTOMER_NAME, $bookingDetails->first_name . ' ' . $bookingDetails->last_name, $content);

		$logoContent = '<img height="111" src="' . (BD_PICTURES_PATH . '/no_image.jpg') . '"/>';
		if (!empty($bookingDetails->event->pictures)) {
			$bookingDetails->event->pictures[0]->picture_path = str_replace(" ", "%20", $bookingDetails->event->pictures[0]->picture_path);
			$logoContent = '<img height="111" src="' . (BD_PICTURES_PATH . $bookingDetails->event->pictures[0]->picture_path) . '"/>';
		}

		$logoContent = '<a href="' . JBusinessUtil::getEventLink($bookingDetails->event->id, $bookingDetails->event->alias) . '">' . $logoContent . '</a>';
		$content = str_replace(EMAIL_EVENT_PICTURE, $logoContent, $content);

		$content = self::updateCompanyDetails($content);
	
		$subject = str_replace(EMAIL_EVENT_NAME, $bookingDetails->event->name, $templ->email_subject);
		$toEmail = $bookingDetails->email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	public static function prepareNotificationEmail($company, $emailTemplate) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();
		$emailContent = $emailTemplate;
		$emailContent = self::updateUserName($company->userId, $emailContent);

		$emailContent = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $emailContent);
		$companyLink = '<a href="' . JBusinessUtil::getCompanyLink($company) . '">' . $company->name . '</a>';
		$emailContent = str_replace(EMAIL_BUSINESS_NAME, $companyLink, $emailContent);
		$emailContent = str_replace(EMAIL_BUSINESS_ADDRESS, JBusinessUtil::getAddressText($company), $emailContent);
		$emailContent = str_replace(EMAIL_BUSINESS_WEBSITE, $company->website, $emailContent);

		$emailContent = self::updateCompanyDetails($emailContent);

		$logoContent = '<img height="111" src="' . (BD_PICTURES_PATH . '/no_image.jpg') . '"/>';
		if (!empty($company->logoLocation)) {
			$company->logoLocation = str_replace(" ", "%20", $company->logoLocation);
			$logoContent = '<img height="111" src="' . (BD_PICTURES_PATH . $company->logoLocation) . '"/>';
		}

		$logoContent = '<a href="' . JBusinessUtil::getCompanyLink($company) . '">' . $logoContent . '</a>';

		$business_admin_area = JBusinessUtil::getWebsiteUrl(true) . 'administrator/index.php?option=com_jbusinessdirectory&view=company&layout=edit&id=' . $company->id;

		$emailContent = str_replace(EMAIL_BUSINESS_ADMINISTRATOR_URL, $business_admin_area, $emailContent);
		$emailContent = str_replace(EMAIL_BUSINESS_LOGO, $logoContent, $emailContent);
		
		if (isset($company->selectedCategories) && isset($company->selectedCategories[0])) {
			$emailContent = str_replace(EMAIL_BUSINESS_CATEGORY, $company->selectedCategories[0]->name, $emailContent);
		} else {
			$emailContent = str_replace(EMAIL_BUSINESS_CATEGORY, "", $emailContent);
		}
		
		if (!empty($company->contacts)) {
			$emailContent = str_replace(EMAIL_BUSINESS_CONTACT_PERSON, $company->contacts[0]->contact_name, $emailContent);
		} else {
			$emailContent = str_replace(EMAIL_BUSINESS_CONTACT_PERSON, "", $emailContent);
		}

		return $emailContent;
	}

	public static function sendApprovalEmail($company) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$templ = self::getEmailTemplate("Approve Email");
		if (empty($templ)) {
			return false;
		}

		$content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $templ->email_content);
		$companyLink = '<a href="' . JBusinessUtil::getCompanyLink($company) . '">' . $company->name . '</a>';
		$content = str_replace(EMAIL_BUSINESS_NAME, $companyLink, $content);
		$content = self::updateCompanyDetails($content);
		$content = self::updateUserName($company->userId, $content);

		$subject = $templ->email_subject;
		$toEmail = $company->email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	public static function getBillingInformation($company) {
		$user = JBusinessUtil::getUser($company->userId);
		$inf = $user->display_name . "<br/>";
		$inf = $inf . $company->name . "<br/>";
		$inf = $inf . JBusinessUtil::getAddressText($company);

		return $inf;
	}

	public static function getEmailTemplate($template) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$db = JFactory::getDBO();
		$query = ' SELECT * FROM #__jbusinessdirectory_emails WHERE email_type = "' . $template . '" AND status=1 ';
		$db->setQuery($query);
		$templ = $db->loadObject();

		if ($applicationSettings->enable_multilingual) {
			$lang = JBusinessUtil::getLanguageTag();
			$translation = JBusinessDirectoryTranslations::getObjectTranslation(EMAIL_TRANSLATION, $templ->email_id, $lang);

			if (!empty($translation)) {
				if (!empty($translation->name)) {
					$templ->email_subject = $translation->name;
				}
				if (!empty($translation->content)) {
					$templ->email_content = $translation->content;
				}
			}
		}

		return $templ;
	}

	public static function prepareEmail($data, $company, $templEmail, $siteName = null, $billingInformation = null, $vat = null) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();
		if (!empty($data->billingDetails)) {
			$templEmail = str_replace(EMAIL_USER_NAME, " " . $data->billingDetails->first_name . ' ' . $data->billingDetails->last_name, $templEmail);
		} else if(!empty($company)){
			$templEmail = self::updateUserName($company->userId, $templEmail);
		}

		$siteAddress = JBusinessUtil::getWebsiteUrl(true);
		$templEmail = str_replace(EMAIL_SITE_ADDRESS, $siteAddress, $templEmail);
		$templEmail = str_replace(EMAIL_COMPANY_NAME, $siteName, $templEmail);
		$templEmail = str_replace(EMAIL_ORDER_ID, $data->order_id, $templEmail);

		$paymentMethod = $data->details->processor_type;
		$templEmail = str_replace(EMAIL_PAYMENT_METHOD, $paymentMethod, $templEmail);

		$orderId = !empty($applicationSettings->invoice_prefix)?$applicationSettings->invoice_prefix.$data->id:$data->id;
		$templEmail = str_replace(EMAIL_INVOICE_NUMBER, $orderId, $templEmail);

		if (!empty($data->paid_at)) {
			$templEmail = str_replace(EMAIL_ORDER_DATE, JBusinessUtil::getDateGeneralFormat($data->paid_at), $templEmail);
		} else {
			$templEmail = str_replace(EMAIL_ORDER_DATE, JBusinessUtil::getDateGeneralFormat($data->details->payment_date), $templEmail);
		}

		$templEmail = str_replace(EMAIL_SERVICE_NAME, $data->service, $templEmail);
		$templEmail = str_replace(EMAIL_UNIT_PRICE, JBusinessUtil::getPriceFormat($data->package->price), $templEmail);

		$totalAmount = $data->amount_paid;
		if (empty($data->amount_paid)) {
			$totalAmount = $data->amount;
		}

		$templEmail = str_replace(EMAIL_TOTAL_PRICE, JBusinessUtil::getPriceFormat($totalAmount), $templEmail);
		$templEmail = str_replace(EMAIL_TAX_AMOUNT, JBusinessUtil::getPriceFormat((float)$data->package->price * (float)$vat / 100), $templEmail);
		$templEmail = str_replace(EMAIL_SUBTOTAL_PRICE, JBusinessUtil::getPriceFormat($data->package->price), $templEmail);

		$templEmail = str_replace(EMAIL_BILLING_INFORMATION, $billingInformation, $templEmail);

		if (isset($data->orderDetails)) {
			$templEmail = str_replace(EMAIL_TAX_DETAIL, $data->orderDetails, $templEmail);
		}
        if(!empty($company)) {
            $companyLink = JBusinessUtil::getCompanyLink($company);
            $companyLink = '<a href="' . $companyLink . '">' . $company->name . '</a>';
            $templEmail = str_replace(EMAIL_BUSINESS_NAME, $companyLink, $templEmail);
        }

		return "<div style='width: 600px;'>" . $templEmail . '</div>';
	}

	public static function prepareEmailFromArray($data, $company, $templEmail) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();
		$templEmail = self::updateUserName($company->userId, $templEmail);

		$fistName = isset($data["firstName"]) ? $data["firstName"] : "";
		$lastName = isset($data["lastName"]) ? $data["lastName"] : "";
		$description = isset($data["description"]) ? $data["description"] : "";
		$email = isset($data["email"]) ? $data["email"] : "";
		$abuseTxt = isset($data["description"]) ? $data["description"] : "";
		$expDays = isset($data["nrDays"]) ? $data["nrDays"] : "";
		$reviewName = isset($data["reviewName"]) ? $data["reviewName"] : "";
		$category = isset($data["category"]) ? $data["category"] : "";
		$category = ltrim($category, "- ");
		$phone = isset($data["phone"]) ? $data["phone"] : "";
		

		$templEmail = str_replace(EMAIL_CATEGORY, $category, $templEmail);
		$templEmail = str_replace(EMAIL_FIRST_NAME, $fistName, $templEmail);
		$templEmail = str_replace(EMAIL_LAST_NAME, $lastName, $templEmail);

		$companyLink = JBusinessUtil::getCompanyLink($company);
		$companyLink = '<a href="' . $companyLink . '">' . $company->name . '</a>';
		$templEmail = str_replace(EMAIL_BUSINESS_NAME, $companyLink, $templEmail);

		$templEmail = str_replace(EMAIL_REVIEW_LINK, $companyLink, $templEmail);

		$templEmail = str_replace(EMAIL_CONTACT_EMAIL, $email, $templEmail);
		$templEmail = str_replace(EMAIL_PHONE, $phone, $templEmail);
		$templEmail = str_replace(EMAIL_CONTACT_CONTENT, $description, $templEmail);
		$templEmail = str_replace(EMAIL_ABUSE_DESCRIPTION, $description, $templEmail);
		$templEmail = str_replace(EMAIL_EXPIRATION_DAYS, $expDays, $templEmail);
		$templEmail = str_replace(EMAIL_REVIEW_NAME, $reviewName, $templEmail);

		$templEmail = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $templEmail);
		$templEmail = str_replace(EMAIL_CLAIMED_COMPANY_NAME, $companyLink, $templEmail);

		return $templEmail;
	}

	public static function sendEmail($fromName, $replyTo, $toEmail, $cc, $bcc, $subject, $content, $isHtml, $sendToAdmin = false, $from = null) {

		$applicationSettings = JBusinessUtil::getApplicationSettings();
		if(!isset($from)){
			$from = $applicationSettings->company_email;
		}

		if(!isset($bcc)) {
			$bcc = $applicationSettings->cc_email;
		}
		
		jimport('joomla.mail.mail');

		if (empty($toEmail)) {
			return false;
		}
		$result = false;
		$content = EmailService::parseCSS($content);

		try {
			$mail = JFactory::getMailer();
			$mail->setSender(array($from, $fromName));
			if (isset($replyTo)) {
				$mail->addReplyTo($replyTo);
			}
			$mail->addRecipient($toEmail);

			if (isset($cc)) {

				if(!is_array($cc)){
					$ccemails = explode(',', (string) $cc);
				}else{
					$ccemails = $cc;
				}
				
				foreach ($ccemails as $ccemail) {
					$ccemail = trim($ccemail);
					
					// Check if the email is not empty and is a valid email address.
					if (!empty($ccemail) && filter_var($ccemail, FILTER_VALIDATE_EMAIL)) {
						$mail->addCC($ccemail);
					}
				}

				$mail->addCC($cc);
			}

			if (isset($bcc)) {

				if(!is_array($bcc)){
					$bccemails = explode(',', (string) $bcc);
				}else{
					$bccemails = $bcc;
				}
				
				foreach ($bccemails as $bccemail) {
					$bccemail = trim($bccemail);
					
					// Check if the email is not empty and is a valid email address.
					if (!empty($bccemail) && filter_var($bccemail, FILTER_VALIDATE_EMAIL)) {
						$mail->addBCC($bccemail);
					}
				}
			}
			
			if ($sendToAdmin) {
				$headers[] = "bcc: $applicationSettings->company_email";
			}

			$result = wp_mail($toEmail, $subject, $content, $headers);
			$log = Logger::getInstance();
        	$log->LogDebug("E-mail with subject ".$subject." sent from ".$from." to ".$toEmail." ".serialize($bcc)." result:".$result);

			
		} catch (Exception $ex) {
			$log = Logger::getInstance();
			$log->LogDebug("E-mail with subject " . $subject . " sent from " . $from . " to " . $toEmail . " failed");
			return 0;
		}

		return $result;
	}

	public static function updateCompanyDetails($emailContent) {
		$logo = self::getCompanyLogoCode();
		$socialNetworks = self::getCompanySocialNetworkCode();
		$emailContent = str_replace(EMAIL_COMPANY_LOGO, $logo, $emailContent);
		$emailContent = str_replace(EMAIL_COMPANY_SOCIAL_NETWORKS, $socialNetworks, $emailContent);
		$link = '<a style="color:#555;text-decoration:none" target="_blank" href="' . JBusinessUtil::getWebsiteURL(true) . '">' . JBusinessUtil::getWebsiteURL(true) . '</a>';
		$emailContent = str_replace(EMAIL_DIRECTORY_WEBSITE, $link, $emailContent);

		return $emailContent;
	}

	public static function updateUserName($userId, $emailContent) {
		if (!empty($userId)) {
			$user = JBusinessUtil::getUser($userId);
		}

		if (!empty($user) && !empty($user->display_name)) {
			$emailContent = str_replace(EMAIL_USER_NAME, " ".$user->display_name, $emailContent);
		} else {
			$emailContent = str_replace(EMAIL_USER_NAME, "", $emailContent);
		}

		return $emailContent;
	}

	public static function getCompanyLogoCode() {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$code = "";
		if (!empty($applicationSettings->logo)) {
			$applicationSettings->logo = str_replace(" ", "%20", $applicationSettings->logo);
			$logoLocaiton = BD_PICTURES_PATH . $applicationSettings->logo;
			$link = JBusinessUtil::getWebsiteUrl(true);
			$code = '<a target="_blank" title"' . $applicationSettings->company_name . '" href="' . $link . '"><img height="55" alt="' . $applicationSettings->company_name . '" src="' . $logoLocaiton . '" ></a>';
		}

		return $code;
	}

	public static function getCompanySocialNetworkCode() {
		$applicationSettings = JBusinessUtil::getApplicationSettings();
		$code = "";
		if (!empty($applicationSettings->twitter)) {
			$code .= '<a href="' . $applicationSettings->twitter . '" target="_blank"><img title="Twitter" src="' . BD_PICTURES_PATH . '/twitter.png' . '" alt="Twitter" height="32" border="0" width="32"></a>';
		}

		if (!empty($applicationSettings->facebook)) {
			$code .= '<a href="' . $applicationSettings->facebook . '" target="_blank"><img title="Facebook" src="' . BD_PICTURES_PATH . '/facebook.png' . '" alt="Facebook" height="32" border="0" width="32"></a>';
		}

		if (!empty($applicationSettings->linkedin)) {
			$code .= '<a href="' . $applicationSettings->linkedin . '" target="_blank"><img title="LinkedIN" src="' . BD_PICTURES_PATH . '/linkedin.png' . '" alt="LinkedIN" height="32" border="0" width="32"></a>';
		}

		if (!empty($applicationSettings->youtube)) {
			$code .= '<a href="' . $applicationSettings->youtube . '" target="_blank"><img title="Youtube" src="' . BD_PICTURES_PATH . '/youtube.png' . '" alt="Youtube" height="32" border="0" width="32"></a>';
		}

		return $code;
	}

	/**
	 * Send
	 * @param unknown_type $company
	 * @param unknown_type $data
	 */
	public static function sendContactCompanyEmail($company, $data) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();
		$templ = self::getEmailTemplate("Contact Email");
		$bcc = array();

		if (empty($templ)) {
			return false;
		}

		$content = self::prepareEmailFromArray($data, $company, $templ->email_content);
		$content = self::updateCompanyDetails($content);

		$subject = str_replace(EMAIL_BUSINESS_NAME, $company->name, $templ->email_subject);
		$toEmail = $company->email;
		$sender = $data["firstName"] . " " . $data["lastName"];
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		if (!empty($data["copy-me"])) {
			$bcc = array($data["email"]);
		}

		return self::sendEmail($fromName, $data["email"], $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	/**
	 * Send
	 * @param unknown_type $company
	 * @param unknown_type $data
	 */
	public static function sendRequestMoreInfoEmail($company, $data) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();
		$templ = self::getEmailTemplate("Request Info Email");
		$bcc = array();

		if (empty($templ)) {
			return false;
		}

		$content = self::prepareEmailFromArray($data, $company, $templ->email_content);
		$content = self::updateCompanyDetails($content);

		$subject = str_replace(EMAIL_BUSINESS_NAME, $company->name, $templ->email_subject);
		$subject = str_replace(EMAIL_BUSINESS_NAME, $company->name, $templ->email_subject);
		$toEmail = $applicationSettings->company_email;
		$sender = $data["firstName"] . " " . $data["lastName"];
		$fromName = $sender;
		$isHtml = true;
		
		
		$bcc = array($data["email"]);
		
		// dump($from);
		// dump($fromName);
		// dump($data["email"]);
		// dump($toEmail);
		// dump($subject);
		// echo $content;
		// exit;

		return self::sendEmail($fromName, $data["email"], $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	public static function sendTestEmail() {
		$applicationSettings = JBusinessUtil::getApplicationSettings();
		$user = JBusinessUtil::getUser();
		
		$templ = self::getEmailTemplate("Test Email");
		if (empty($templ)) {
			return false;
		}

		$content = $templ->email_content;
		$content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $content);
		$content = str_replace(EMAIL_USER_NAME, " ".$user->name, $content);
		$content = self::updateCompanyDetails($content);
				
		$subject = $templ->email_subject;
		$toEmail = $applicationSettings->company_email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, null, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	public static function sendOnContactCompanySMS($company, $data) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$content = JText::_('LNG_SMS_AFTER_LISTING_CONTACT_CONTENT') . ': ';
		$content .= JBusinessUtil::getWebsiteUrl(true) . 'index.php?option=com_jbusinessdirectory&view=managecompanymessages';

		$receiver = $company->mobile . '@' . $applicationSettings->sms_domain;
		$subject = $company->mobile;

		$toEmail = $receiver;
		$fromName = $applicationSettings->company_name;
		$isHtml = false;

		return self::sendEmail($fromName, $data["email"], $toEmail, null, null, $subject, $content, $isHtml, false);
	}

	/**
	 * Send claim request email to site administrator
	 *
	 * @param $company object data of the company that has been claimed
	 * @param $data array data of the form filled
	 * @return boolean status of email
	 *
	 * @since 4.9.1
	 */
	public static function sendClaimEmail($company, $data) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$templ = self::getEmailTemplate("Claim Request Email");
		if ($templ == null) {
			return null;
		}

		if (!isset($company->email)) {
			return;
		}

		$content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $templ->email_content);
		$companyLink = '<a href="' . JBusinessUtil::getCompanyLink($company) . '">' . $company->name . '</a>';
		$content = str_replace(EMAIL_BUSINESS_NAME, $companyLink, $content);
		$content = self::updateCompanyDetails($content);
		$content = self::updateUserName($company->userId, $content);

		$subject = str_replace(EMAIL_COMPANY_NAME, $company->name, $templ->email_subject);
		$toEmail = $data["email"];
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		if (!empty($applicationSettings->company_email)) {
			$bcc = array($applicationSettings->company_email);
		}
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	public static function sendExpirationEmail($company, $nrDays) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();
		$templ = self::getEmailTemplate("Expiration Notification Email");
		if ($templ == null) {
			return null;
		}

		if (!isset($company->email)) {
			return;
		}

		$data = array("nrDays" => $company->expiration_days);
		$content = self::prepareEmailFromArray($data, $company, $templ->email_content);
		$content = self::updateCompanyDetails($content);

		$subject = $templ->email_subject;
		$toEmail = $company->email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	public static function sendAbuseEmail($company, $email, $message, $reportCause, $sendOnlyToAdmin = true) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$templ = self::getEmailTemplate("Report Notification");

		if ($templ == null) {
			return null;
		}

		if (!isset($company->email)) {
			return;
		}

		$content = str_replace(EMAIL_CONTACT_EMAIL, $email, $templ->email_content);
		$content = str_replace(EMAIL_ABUSE_DESCRIPTION, $message, $content);
		$companyLink = '<a href="' . JBusinessUtil::getCompanyLink($company) . '">' . $company->name . '</a>';
		$content = str_replace(EMAIL_BUSINESS_NAME, $companyLink, $content);
		$content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $content);
		$content = str_replace(EMAIL_REPORT_CAUSE, $reportCause, $content);

		$content = self::updateCompanyDetails($content);
		$fromName = $email;
		$toEmail = $applicationSettings->company_email;
		$subject = $templ->email_subject;

		$isHtml = true;
		$bcc = array();


		if ($sendOnlyToAdmin) {
			$bcc = null;
			//$toEmail = $from;
		}
		$replyTo = null;

		$result = self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);

		return $result;
	}

	public static function sendEventExpirationEmail($event, $nrDays) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();
		$templ = self::getEmailTemplate("Event Expiration Notification Email");
		if ($templ == null) {
			return null;
		}

		if (!isset($event->contact_email)) {
			return;
		}

		$eventLink = '<a title="' . $event->name . '" href="' . JBusinessUtil::getEventLink($event->id, $event->alias) . '" >' . $event->name . '</a>';
		$content = str_replace(EMAIL_EVENT_NAME, $eventLink, $templ->email_content);
		$content = str_replace(EMAIL_EXPIRATION_DAYS, $nrDays, $content);
		$content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $content);
		$content = self::updateCompanyDetails($content);
		$content = self::updateUserName($event->user_id, $content);

		$subject = $templ->email_subject;
		$toEmail = $event->contact_email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	public static function sendOfferExpirationEmail($offer, $nrDays) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();
		$templ = self::getEmailTemplate("Offer Expiration Notification Email");
		if ($templ == null) {
			return null;
		}

		if (!isset($offer->companyEmail)) {
			return;
		}

		$offerLink = '<a title="' . $offer->subject . '" href="' . JBusinessUtil::getOfferLink($offer->id, $offer->alias) . '" >' . $offer->subject . '</a>';
		$content = str_replace(EMAIL_OFFER_NAME, $offerLink, $templ->email_content);
		$content = str_replace(EMAIL_EXPIRATION_DAYS, $nrDays, $content);
		$content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $content);
		$content = self::updateCompanyDetails($content);
		$content = self::updateUserName($offer->user_id, $content);

		$subject = $templ->email_subject;
		$toEmail = $offer->companyEmail;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	
	/**
	 * Send a notification to site administrator when a new review is posted.
	 * 
	 * @param unknown $company
	 * @param unknown $data
	 * @return void|NULL|boolean|number|unknown
	 */
	public static function sendNewReviewNotification($company, $data) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();
		
		$templ = self::getEmailTemplate("New Review Notification");

		if ($templ == null) {
			return null;
		}
		
		$content = self::prepareEmail($data, $company, $templ->email_content);
		$companyLink = JBusinessUtil::getCompanyLink($company);
		$companyLink = '<a href="' . $companyLink . '">' . $company->name . '</a>';
		$content = self::updateCompanyDetails($content);
		
		$subject = sprintf($templ->email_subject, $applicationSettings->company_name);
		$toEmail = $applicationSettings->company_email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}
	
	
	public static function sendReviewEmail($company, $data) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$templ = self::getEmailTemplate("Review Email");
		if ($templ == null) {
			return null;
		}

		if (!isset($company->email)) {
			return;
		}

		$content = self::prepareEmail($data, $company, $templ->email_content);
		$companyLink = JBusinessUtil::getCompanyLink($company);
		$companyLink = '<a href="' . $companyLink . '">' . $company->name . '</a>';
		$content = str_replace(EMAIL_REVIEW_LINK, $companyLink, $content);
		$content = self::updateCompanyDetails($content);

		$subject = sprintf($templ->email_subject, $applicationSettings->company_name);
		$toEmail = $company->email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	public static function sendReviewResponseEmail($company, $data) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$templ = self::getEmailTemplate("Review Response Email");
		if ($templ == null) {
			return null;
		}

		if (!isset($company->email)) {
			return;
		}

		$content = self::prepareEmailFromArray($data, $company, $templ->email_content);
		$content = self::updateCompanyDetails($content);

		$subject = sprintf($templ->email_subject, $applicationSettings->company_name);
		$toEmail = $company->email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	public static function sendReportAbuseEmail($data, $review, $company) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$templ = self::getEmailTemplate("Report Abuse Email");
		if ($templ == null) {
			return null;
		}

		if (isset($review)) {
			$data["reviewName"] = $review->subject;
		}

		$content = self::prepareEmailFromArray($data, $company, $templ->email_content);
		$content = self::updateCompanyDetails($content);

		$subject = $templ->email_subject;
		$toEmail = $applicationSettings->company_email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	public static function sendRequestQuoteEmail($data, $company) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$templ = self::getEmailTemplate("Request Quote Email");
		if ($templ == null) {
			return null;
		}

		$content = self::prepareEmailFromArray($data, $company, $templ->email_content);
		$content = self::updateCompanyDetails($content);

		$subject = sprintf($templ->email_subject, $applicationSettings->company_name);
		$toEmail = $company->email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	/**
	 * Send a quote request email to a business
	 * 
	 * @param unknown $company
	 * @param unknown $quoteHtml
	 * @return NULL|boolean|number|unknown
	 */
	public static function sendQuoteRequestEmail($company, $quoteHtml) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$templ = self::getEmailTemplate("Request Quote");
		if ($templ == null) {
			return null;
		}

		$content = self::prepareEmailFromArray(array(), $company, $templ->email_content);
		$content = self::updateCompanyDetails($content);
		
		$categoryLink = '<a target="_blank" href="' . JBusinessUtil::getCategoryLink($company->categoryInfo->id, $company->categoryInfo->alias) . '">' . $company->categoryInfo->name . '</a>';
		$content = str_replace(EMAIL_CATEGORY_LINK, $categoryLink, $content);
		$content = str_replace(EMAIL_BUSINESS_NAME, JBusinessUtil::getCompanyLink($company), $content);
		$content = str_replace(EMAIL_REQUEST_QUOTE_SUMMARY, $quoteHtml, $content);
		
		$url = JRoute::_('index.php?option=com_jbusinessdirectory&view=managelistingrequestquotes', false, -1);
		$replyLink = '<a href=' . $url . ' target="_blank">' . JText::_('LNG_CLICKING_HERE') . '</a>';
		$content = str_replace(EMAIL_CLICK_HERE_LINK, $replyLink, $content);

		$subject = str_replace(EMAIL_BUSINESS_NAME, $company->name, $templ->email_subject);
		$toEmail = $company->email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}
	
	/**
	 * Send a quote request confirmation email to the user
	 *
	 * @param unknown $company
	 * @param unknown $quoteHtml
	 * @return NULL|boolean|number|unknown
	 */
	public static function sendQuoteRequestConfirmationEmail($quote, $quoteHtml) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();
		
		$templ = self::getEmailTemplate("Quote Request Confirmation");
		if ($templ == null) {
			return null;
		}
		
		$content = self::updateUserName($quote->user_id, $templ->email_content);
		$content = self::updateCompanyDetails($content);
		$categoryLink = '<a target="_blank" href="' . JBusinessUtil::getCategoryLink($quote->categoryInfo->id, $quote->categoryInfo->alias) . '">' . $quote->categoryInfo->name . '</a>';
		$content = str_replace(EMAIL_CATEGORY_LINK, $categoryLink, $content);
		$content = str_replace(EMAIL_REQUEST_QUOTE_SUMMARY, $quoteHtml, $content);
		$content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $content);
		$content = str_replace(EMAIL_BUSINESS_NAME, $quote->company_name, $content);
		
		$url = JRoute::_('index.php?option=com_jbusinessdirectory&view=managerequestquotes', false, -1);
		$replyLink = '<a href=' . $url . ' target="_blank">' . JText::_('LNG_HERE') . '</a>';
		$content = str_replace(EMAIL_CLICK_HERE_LINK, $replyLink, $content);
		
		$user = JBusinessUtil::getUser($quote->user_id);
		
		$subject = $templ->email_subject;
		$toEmail = $user->email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		$replyTo = null;
		
		return self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}
	
	/**
	 * Send a quote request reply notification email to the user
	 *
	 * @param unknown $company
	 * @param unknown $quoteHtml
	 * @return NULL|boolean|number|unknown
	 */
	public static function sendQuoteRequestReplyEmail($quote, $toEndUser) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();
		
		$templ = self::getEmailTemplate("Quote Request Reply Notification ");
		if ($templ == null) {
			return null;
		}
		
		$content = self::updateCompanyDetails($templ->email_content);
		$categoryLink = '<a target="_blank" href="' . JBusinessUtil::getCategoryLink($quote->categoryInfo->id, $quote->categoryInfo->alias) . '">' . $quote->categoryInfo->name . '</a>';
		$content = str_replace(EMAIL_CATEGORY_LINK, $categoryLink, $content);
		$content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $content);
		
		$user = JBusinessUtil::getUser($quote->user_id);
		$url = "";
		
		if($toEndUser){
			$content = str_replace(EMAIL_BUSINESS_NAME, $quote->company_name, $content);
			$toEmail = $user->email;
			$content = self::updateUserName($quote->user_id,$content);
			$url = JRoute::_('index.php?option=com_jbusinessdirectory&view=managerequestquotes', false, -1);
		}else{
			$content = str_replace(EMAIL_BUSINESS_NAME, $user->name, $content);
			$toEmail = $quote->company_email;
			$content = self::updateUserName($quote->company_user_id, $content);
			$url = JRoute::_('index.php?option=com_jbusinessdirectory&view=managelistingrequestquotes', false, -1);
		}
		
		$content = str_replace(EMAIL_CLICK_HERE_LINK, $url, $content);
		
		$subject = $templ->email_subject;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		$replyTo = null;
		
		return self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}
	

	public static function sendClaimResponseEmail($company, $claimDetails, $template) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$templ = self::getEmailTemplate($template);
		if ($templ == null) {
			return null;
		}

		$data = array();
		$content = self::prepareEmailFromArray($data, $company, $templ->email_content);
		$content = self::updateCompanyDetails($content);
		$content = str_replace(EMAIL_CUSTOMER_NAME, $claimDetails->firstName . ' ' . $claimDetails->lastName, $content);

		$subject = $templ->email_subject;
		$toEmail = isset($claimDetails->email) ? $claimDetails->email : null;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	public static function sendUpdateCompanyNotificationEmailToAdmin($company) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$templ = self::getEmailTemplate("Business Update Notification");
		if (empty($templ)) {
			return false;
		}

		$content = self::prepareNotificationEmail($company, $templ->email_content);
		$content = self::updateCompanyDetails($content);

		$subject = $templ->email_subject;
		$toEmail = $applicationSettings->company_email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, null, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	/**
	 * Send email for every Response added on an offer review
	 * @param $offer
	 * @param $company
	 * @param $data
	 * @return bool|int|JException|null|void
	 */
	public static function sendOfferReviewResponseEmail($offer, $company, $data) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$templ = self::getEmailTemplate("Offer Review Response Email");
		if ($templ == null) {
			return null;
		}

		if (!isset($company->email)) {
			return false;
		}

		$offerLink = '<a title="' . $offer->subject . '" href="' . JBusinessUtil::getOfferLink($offer->id, $offer->alias) . '" >' . $offer->subject . '</a>';
		$templ->email_content = str_replace(EMAIL_REVIEW_LINK, JBusinessUtil::getOfferLink($offer->id, $offer->alias), $templ->email_content);
		$content = self::prepareEmailFromArray($data, $company, $templ->email_content);
		$content = str_replace(EMAIL_OFFER_NAME, $offerLink, $content);
		$content = self::updateCompanyDetails($content);

		$subject = sprintf($templ->email_subject, $applicationSettings->company_name);
		$toEmail = $company->email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	/**
	 * Send email notification for reviews added on any offer
	 * @param $offer
	 * @param $company
	 * @param $data
	 * @return bool|int|JException|null|void
	 */
	public static function sendOfferReviewEmail($offer, $company, $data) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$templ = self::getEmailTemplate("Offer Review Email");
		if ($templ == null) {
			return null;
		}

		if (!isset($company->email)) {
			return;
		}

		$content = self::prepareEmail($data, $company, $templ->email_content);

		$offerLink = '<a title="' . $offer->subject . '" href="' . JBusinessUtil::getOfferLink($offer->id, $offer->alias) . '" >' . $offer->subject . '</a>';
		$content = str_replace(EMAIL_OFFER_NAME, $offerLink, $content);
		$content = str_replace(EMAIL_REVIEW_LINK, $offerLink, $content);

		$content = self::updateCompanyDetails($content);

		$subject = sprintf($templ->email_subject, $applicationSettings->company_name);
		$toEmail = $company->email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	/**
	 * Send email when an abuse is reported on an offer review
	 * @param $data
	 * @param $review
	 * @param $company
	 * @param $offer
	 * @return bool|int|JException|null
	 */
	public static function sendOfferReportAbuseEmail($data, $review, $company, $offer) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$templ = self::getEmailTemplate("Report Abuse Offer Review");
		if ($templ == null) {
			return null;
		}

		if (isset($review)) {
			$data["reviewName"] = $review->subject;
		}

		$content = self::prepareEmailFromArray($data, $company, $templ->email_content);
		$content = str_replace(EMAIL_OFFER_NAME, $offer->subject, $content);

		$offerLink = '<a title="' . $offer->subject . '" href="' . JBusinessUtil::getOfferLink($offer->id, $offer->alias) . '" >' . $offer->subject . '</a>';
		$content = str_replace(EMAIL_REVIEW_LINK_OFFER, $offerLink, $content);
		$content = self::updateCompanyDetails($content);

		$subject = $templ->email_subject;
		$toEmail = $applicationSettings->company_email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	/**
	 * Send contact email to event
	 * @param $company
	 * @param $data
	 * @return bool|int|JException
	 */
	public static function sendContactEventCompanyEmail($company, $data) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$templ = self::getEmailTemplate("Event Contact Email");

		if (empty($templ)) {
			return false;
		}

		$content = self::prepareEmailFromArray($data, $company, $templ->email_content);
		$eventLink = '<a target="_blank" title="' . $data["event_name"] . '" href="' . JBusinessUtil::getEventLink($data["event_id"], $data["event_alias"]) . '" >' . $data["event_name"] . '</a>';
		$content = str_replace(EMAIL_EVENT_NAME, $eventLink, $content);
		$content = self::updateCompanyDetails($content);

		$subject = str_replace(EMAIL_EVENT_NAME, $data["event_name"], $templ->email_subject);
		$toEmail = $company->email;
		$sender = $data["firstName"] . " " . $data["lastName"];
		$fromName = $sender;
		$isHtml = true;
		if (!empty($data["copy-me"])) {
			$bcc = array($data["email"]);
		}

		return self::sendEmail($fromName, $data["email"], $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	/**
	 * Send contact email for offer
	 * @param $company
	 * @param $data
	 * @return bool|int|JException
	 */
	public static function sendOfferContactCompanyEmail($company, $offer, $data) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$templ = self::getEmailTemplate("Offer Contact Email");

		if (empty($templ)) {
			return false;
		}

		$content = self::prepareEmailFromArray($data, $company, $templ->email_content);
		$offerLink = '<a target="_blank" title="' . $data["offer_name"] . '" href="' . JBusinessUtil::getOfferLink($data["offer_id"], $data["offer_alias"]) . '" >' . $data["offer_name"] . '</a>';
		$content = str_replace(EMAIL_OFFER_NAME, $offerLink, $content);
		$content = self::updateCompanyDetails($content);

		$subject = str_replace(EMAIL_OFFER_NAME, $data["offer_name"], $templ->email_subject);
		if ($data['item_type'] == OFFER_TYPE_OFFER) {
			$subject = str_replace(EMAIL_ITEM_TYPE, JText::_("LNG_OFFER"), $subject);
		} else {
			$subject = str_replace(EMAIL_ITEM_TYPE, JText::_("LNG_PRODUCT"), $subject);
		}


		$email = !empty($offer->contact_email)?$offer->contact_email:$company->email;

		$toEmail = $email;
		$sender = $data["firstName"] . " " . $data["lastName"];
		$fromName = $sender;
		$isHtml = true;
		if (!empty($data["copy-me"])) {
			$bcc = array($data["email"]);
		}

		return self::sendEmail($fromName, $data["email"], $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	public static function sendAppointmentEmail($event, $data, $company, $companyName) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$templ = self::getEmailTemplate("Event Appointment Email");
		if (empty($templ)) {
			return false;
		}

		$content = str_replace(EMAIL_EVENT_NAME, $event->name, $templ->email_content);
		$content = str_replace(EMAIL_APPOINTMENT_DATE, JBusinessUtil::getDateGeneralFormat($data['date']), $content);
		$content = str_replace(EMAIL_APPOINTMENT_TIME, JBusinessUtil::getTimeText($data['time']), $content);
		$content = str_replace(EMAIL_FIRST_NAME, $data['first_name'], $content);
		$content = str_replace(EMAIL_LAST_NAME, $data['last_name'], $content);
		$content = str_replace(EMAIL_EMAIL, $data['email'], $content);
		$content = str_replace(EMAIL_PHONE, $data['phone'], $content);
		$content = str_replace(EMAIL_BUSINESS_NAME, $companyName, $content);
		$content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $content);

		$content = self::updateCompanyDetails($content);
		$content = self::updateUserName($company->userId, $content);

		$subject = str_replace(EMAIL_EVENT_NAME, $event->name, $templ->email_subject);
		$subject = str_replace(EMAIL_APPOINTMENT_DATE, JBusinessUtil::getDateGeneralFormat($data['date']), $subject);
		$toEmail = $data["email"];
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array($company->email);
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	public static function sendAppointmentStatusEmail($appointment, $status) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$templ = self::getEmailTemplate("Event Appointment Status Notification");
		if (empty($templ)) {
			return false;
		}

		$statusText = '';
		if ($status == EVENT_APPOINTMENT_CONFIRMED) {
			$statusText = JText::_('LNG_CONFIRMED');
		} else {
			$statusText = JText::_('LNG_CANCELED');
		}

		$content = str_replace(EMAIL_EVENT_NAME, $appointment->eventName, $templ->email_content);
		$content = str_replace(EMAIL_APPOINTMENT_DATE, JBusinessUtil::getDateGeneralFormat($appointment->date), $content);
		$content = str_replace(EMAIL_APPOINTMENT_TIME, JBusinessUtil::getTimeText($appointment->time), $content);
		$content = str_replace(EMAIL_APPOINTMENT_STATUS, $statusText, $content);
		$content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $content);
		$content = str_replace(EMAIL_CUSTOMER_NAME, $appointment->first_name . ' ' . $appointment->last_name, $content);
		$content = self::updateCompanyDetails($content);

		$subject = $templ->email_subject;
		$toEmail = $appointment->email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	public static function sendDisapprovalEmail($company) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$templ = self::getEmailTemplate("Disapprove Email");
		if (empty($templ)) {
			return false;
		}
		if(!empty($company->disapproval_text)){
			$disapprovalText = $company->disapproval_text;
		} else {
			$disapprovalText = '(' . JText::_("LNG_NO_REASON") . ')';
		}

		$content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $templ->email_content);
		$companyLink = '<a href="' . JBusinessUtil::getCompanyLink($company) . '">' . $company->name . '</a>';
		$content = str_replace(EMAIL_BUSINESS_NAME, $companyLink, $content);
		$content = str_replace(EMAIL_DISAPPROVAL_TEXT, $disapprovalText, $content);
		$content = self::updateCompanyDetails($content);
		$content = self::updateUserName($company->userId, $content);
		$subject = str_replace(EMAIL_BUSINESS_NAME, $company->name, $templ->email_subject);
		$toEmail = $company->email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	/**
	 * Prepare & send the campaign payment email
	 *
	 * @param $bookingDetails
	 *
	 * @since 5.2.2
	 * @return bool|int
	 */
	public static function sendCampaignPaymentNotification($bookingDetails) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$templ = self::getEmailTemplate("Campaign Payment Notification");
		if (empty($templ)) {
			return false;
		}

		$content = str_replace(EMAIL_CAMPAIGN_NAME, $bookingDetails->name, $templ->email_content);
		$content = str_replace(EMAIL_CAMPAIGN_DETAILS, $bookingDetails->campaignSummary, $content);
		$content = str_replace(EMAIL_CAMPAIGN_BUYER_DETAILS, $bookingDetails->guestDetailsSummary, $content);
		$content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company, $content);

		$content = self::updateCompanyDetails($content);
		$content = str_replace(EMAIL_CUSTOMER_NAME, $bookingDetails->guestDetails->first_name . " " . $bookingDetails->guestDetails->last_name, $content);

		$subject = str_replace(EMAIL_CAMPAIGN_ID, $bookingDetails->id, $templ->email_subject);
		$toEmail = $bookingDetails->email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	/**
	 * Prepare & send the campaign payment waiting email
	 *
	 * @param $bookingDetails
	 *
	 * @since 5.2.2
	 * @return bool|int
	 */
	public static function sendCampaignPaymentWaitingNotification($bookingDetails) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$templ = self::getEmailTemplate("Campaign Payment Waiting Notification");
		if (empty($templ)) {
			return false;
		}

		$content = str_replace(EMAIL_CAMPAIGN_NAME, $bookingDetails->name, $templ->email_content);
		$content = str_replace(EMAIL_CAMPAIGN_DETAILS, $bookingDetails->campaignSummary, $content);
		$content = str_replace(EMAIL_CAMPAIGN_BUYER_DETAILS, $bookingDetails->guestDetailsSummary, $content);
		$content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company, $content);

		$content = str_replace(EMAIL_PAYMENT_DETAILS, $bookingDetails->paymentDetails->details, $content);
		$content = self::updateCompanyDetails($content);
		$content = str_replace(EMAIL_CUSTOMER_NAME, $bookingDetails->guestDetails->first_name . " " . $bookingDetails->guestDetails->last_name, $content);

		$subject = str_replace(EMAIL_CAMPAIGN_ID, $bookingDetails->id, $templ->email_subject);
		$toEmail = $bookingDetails->email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	public static function sendListingUserChangeNotification($company, $data) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$templ = self::getEmailTemplate("Listing owner changed");
		if ($templ == null) {
			return null;
		}

		$previousUser = JBusinessUtil::getUser($data['current_user_id']);
		$actualUser = JBusinessUtil::getUser($data['userId']);

		$content = self::prepareEmail($data, $company, $templ->email_content);
		$companyLink = JBusinessUtil::getCompanyLink($company);
		$companyLink = '<a target="_blank" href="' . $companyLink . '">' . $company->name . '</a>';
		$content = str_replace(EMAIL_BUSINESS_NAME, $companyLink, $content);
		$content = str_replace(EMAIL_PREVIOUS_USER, $previousUser->name, $content);
		$content = str_replace(EMAIL_ACTUAL_USER, $actualUser->name, $content);

		$content = self::updateCompanyDetails($content);

		$subject = str_replace(EMAIL_BUSINESS_NAME, $company->name, $templ->email_subject);
		$toEmail = $actualUser->email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array($previousUser->email);
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	public static function sendRequestQuoteProductEmail($data, $company) {
		$applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();

		$templ = self::getEmailTemplate("Request Quote Product Email");
		if ($templ ==null) {
			return null;
		}

		$content = self::prepareEmailFromArray($data, $company, $templ->email_content);
		$content = self::updateCompanyDetails($content);

		$offerLink = '<a title="'.$data["productSubject"].'" href="'.JBusinessUtil::getOfferLink($data["productId"], $data["productAlias"]).'" >'.$data["productSubject"].'</a>';
		$productMsg = JText::_("LNG_CHECK_PRODUCT_HERE")."->".$offerLink;
		if ($data["productId"] != '0') {
			$content = str_replace(EMAIL_PRODUCT_REQUESTED, $productMsg, $content);
		} else {
			$content = str_replace(EMAIL_PRODUCT_REQUESTED, "", $content);
		}
		$content = str_replace(EMAIL_PRODUCT_REQUESTED_PATH, $company->name."->".$data["path"], $content);
		$subject=sprintf($templ->email_subject, $applicationSettings->company_name);
		$toEmail = $company->email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

    /**
     * @param $data
     * @param $company
     * @return bool|int|null
     */
    public static function sendAppointmentURLNotification($data) {
        $applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();

        $templ = self::getEmailTemplate("Appointment URL Notification");
        if ($templ ==null) {
            return null;
        }

		$date = JBusinessUtil::getDateGeneralFormat($data->date)." - ".JBusinessUtil::getTimeText($data->time);

        $content = str_replace(EMAIL_SERVICE_NAME, $data->serviceName, $templ->email_content);
        $content = str_replace(EMAIL_PROVIDER_NAME, $data->providerName, $content);
        $content = str_replace(EMAIL_APPOINTMENT_DATE, $date, $content);

        $url = $offerLink = '<a title="'.$data->serviceName.'" href="'.$data->url.'" >'.$data->url.'</a>';
        $content = str_replace(EMAIL_APPOINTMENT_URL, $url, $content);

		$content = str_replace(EMAIL_USER_NAME, " ".$data->first_name." ".$data->last_name, $content);
		$content = str_replace(EMAIL_PHONE, " ".$data->phone, $content);
        $content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $content);
        $content = self::updateCompanyDetails($content);

		$subject = $templ->email_subject;
		$subject = str_replace(EMAIL_SERVICE_NAME, $data->serviceName, $subject);
        $subject = str_replace(EMAIL_PROVIDER_NAME, $data->providerName, $subject);
        $subject = str_replace(EMAIL_APPOINTMENT_DATE, $date, $subject);

        $toEmail = $data->email;
        $fromName = $applicationSettings->company_name;
        $isHtml = true;
        $bcc = array();
		$replyTo = null;

        return self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
    }


	/**
	 * Send the email activation email
	 *
	 * @param [type] $data
	 * @return void
	 */
	public static function sendUserEmailConfirmationEmail($data){
		$applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();

		$templ = self::getEmailTemplate("Email verification");
		if ($templ ==null) {
            return null;
        }
		
		$user = JBusinessUtil::getUser($data["userId"]);
		$content = str_replace(EMAIL_USER_NAME, " ".$user->name, $templ->email_content);
		$content = self::updateCompanyDetails($content);

		$activationLinkText = '<a href="'.$data['activationLink'].'" >'.JText::_("LNG_HERE").'</a>';
		
		$content = str_replace(EMAIL_ACTIVATION_URL, $data['activationLink'], $content);
		$content = str_replace(EMAIL_ACTIVATION_URL_TEXT, $activationLinkText, $content);
		$content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $content);

		$welcomeText = "";
		if(isset($data["need_review"])){
			$welcomeText = $data["need_review"] == true ? JText::_("LNG_WELCOME_NEED_REVIEW") : JText::_("LNG_WELCOME_PROFILE_ADDED");
		}

		$content = str_replace(EMAIL_WELCOME_TEXT, $welcomeText, $content);
		
		$subject = $templ->email_subject;
		$toEmail = $user->email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}
	

	/**
     * @param $data
     * @return bool|int|null
     */
    public static function sendNotificationMessage($data) {
        $applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();

        $templ = self::getEmailTemplate("Appointment Email Notification");

        if ($templ ==null) {
            return null;
        }

		$date = JBusinessUtil::getDateGeneralFormat($data->date)." - ".JBusinessUtil::getTimeText($data->time);

        $content = str_replace(EMAIL_SERVICE_NAME, $data->serviceName, $templ->email_content);
        $content = str_replace(EMAIL_PROVIDER_NAME, $data->providerName, $content);
        $content = str_replace(EMAIL_APPOINTMENT_DATE, $date, $content);

		$content = str_replace(EMAIL_USER_NAME, " ".$data->first_name." ".$data->last_name, $content);
		$content = str_replace(EMAIL_PHONE, " ".$data->phone, $content);
        $content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $content);
        $content = str_replace(EMAIL_APPOINTMENT_MESSAGE, $data->message, $content);
        $content = self::updateCompanyDetails($content);

		$subject = $templ->email_subject;
		$subject = str_replace(EMAIL_SERVICE_NAME, $data->serviceName, $subject);
        $subject = str_replace(EMAIL_PROVIDER_NAME, $data->providerName, $subject);
        $subject = str_replace(EMAIL_APPOINTMENT_DATE, $date, $subject);

        $toEmail = $data->email;
        $from = !empty($data->providerEmail) ? $data->providerEmail : $data->companyEmail;
        $fromName = $data->providerName;
        $isHtml = true;
        $bcc = array();
		$replyTo = null;

        return self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
    }

	
	public static function sendServiceBookingReminder($data) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();
		$templ = self::getEmailTemplate("Service Booking Reminder");
		if ($templ == null) {
			return null;
		}

		$serviceDate = JBusinessUtil::getDateGeneralFormat($data->bookingDate);

		$content = str_replace(EMAIL_USER_NAME, " ".$data->first_name, $templ->email_content);
		$content = self::updateCompanyDetails($content);
		$content = str_replace(EMAIL_SERVICE_BOOKING_NAME, $data->serviceName, $content);
		$content = str_replace(EMAIL_SERVICE_BOOKING_DATE, $serviceDate, $content);
		$content = str_replace(EMAIL_SERVICE_BOOKING_DETAILS, $data->serviceDetails, $content);
		$content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $content);

		$subject = str_replace(EMAIL_SERVICE_BOOKING_NAME, $data->serviceName, $templ->email_subject);
		$toEmail = $data->email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	public static function sendEventAppointmentReminder($data) {
		$applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();

		$templ = self::getEmailTemplate("Event Appointment Reminder");
		if ($templ == null) {
			return null;
		}

		$appointmentDate = JBusinessUtil::getDateGeneralFormat($data->date);

		$content = str_replace(EMAIL_USER_NAME, " ".$data->first_name, $templ->email_content);
		$content = self::updateCompanyDetails($content);
		$content = str_replace(EMAIL_EVENT_NAME, $data->eventName, $content);
		$content = str_replace(EMAIL_APPOINTMENT_DATE, $appointmentDate, $content);
		$content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $content);

		$subject = str_replace(EMAIL_EVENT_NAME, $data->eventName, $templ->email_subject);
		$toEmail = $data->email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	public static function sendEventBookingReminder($data) {
		$applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();

		$templ = self::getEmailTemplate("Event Booking Reminder");
		if ($templ == null) {
			return null;
		}

		$createdDate = JBusinessUtil::getDateGeneralFormat($data->createdDate);

		$content = str_replace(EMAIL_USER_NAME, " ".$data->first_name, $templ->email_content);
		$content = self::updateCompanyDetails($content);
		$content = str_replace(EMAIL_EVENT_NAME, $data->eventName, $content);
		$content = str_replace(EMAIL_BOOKING_DATE, $createdDate, $content);
		$content = str_replace(EMAIL_BOOKING_DETAILS, $data->bookingDetails, $content);
		$content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $content);

		$logoContent = '<img height="111" src="' . (BD_PICTURES_PATH . '/no_image.jpg') . '"/>';
		if (!empty($data->pictures)) {
			$data->pictures[0]->picture_path = str_replace(" ", "%20", $data->pictures[0]->picture_path);
			$logoContent = '<img height="111" src="' . (BD_PICTURES_PATH . $data->pictures[0]->picture_path) . '"/>';
		}
		$content = str_replace(EMAIL_EVENT_PICTURE, $logoContent, $content);

		$subject = str_replace(EMAIL_EVENT_NAME, $data->eventName, $templ->email_subject);
		$toEmail = $data->email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	/**
	 * Replace the CSS classes with inline style
	 */
	public static function parseCSS($content){
		require_once BD_LIBRARIES_PATH.'/cssparser/cssin.php';
		
		try{
			$cssin = new FM\CSSIN();
			
			$urls = array(BD_ASSETS_FOLDER_PATH."css/email.css");
			$content = $cssin->inlineCSS($content, $urls);
			//dump($content);
			//echo ($content);
		}catch(Exception $e){
			dump($e);
		}

		return $content;
	}

	public static function sendHireEmail($company, $quoteHtml) {
        $applicationSettings = JBusinessUtil::getApplicationSettings();
		$user = JBusinessUtil::getUser();

        $templ = self::getEmailTemplate("Hire Email");
        if (empty($templ)) {
            return false;
        }

        $content = $templ->email_content;
		$content = self::updateCompanyDetails($content);
		$content = self::updateUserName($company->userId, $content);
        $content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $content);
        $content = str_replace(EMAIL_BUSINESS_NAME, $company->name, $content);
		$content = str_replace(EMAIL_QUOTE_USER_NAME, $user->name, $content);
		$content = str_replace(EMAIL_CONTACT_EMAIL, $user->email, $content);
		$content = str_replace(EMAIL_REQUEST_QUOTE_SUMMARY, $quoteHtml, $content);


        $subject = $templ->email_subject;
        $toEmail = $company->email;
        $fromName = $applicationSettings->company_name;
        $isHtml = true;
		$replyTo = null;

        return self::sendEmail($fromName, $replyTo, $toEmail, null, null, $subject, $content, $isHtml, $templ->send_to_admin);
    }

	public static function sendPaymentReminderEmail($orderDetails) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$templ = self::getEmailTemplate("Payment Reminder");
		if (empty($templ)) {
			return false;
		}
		$link = JBusinessUtil::getWebsiteUrl(true) . 'index.php?option=com_jbusinessdirectory&view=payment&orderId='.$orderDetails->id;

		$content = self::prepareEmail($orderDetails, null, $templ->email_content, $applicationSettings->company_name);
		$content = str_replace(EMAIL_CUSTOMER_NAME, $orderDetails->billingDetails->first_name . ' ' . $orderDetails->billingDetails->last_name, $content);
		$content = str_replace(EMAIL_ORDER_ID, $orderDetails->id, $content);
		$content = str_replace(EMAIL_ORDER_PAYMENT_URL, $link, $content);
		$subject = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $templ->email_subject);
		$toEmail = $orderDetails->billingDetails->email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$replyTo = null;
		
		return self::sendEmail($fromName, $replyTo, $toEmail, null, null, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	public static function sendSubscriptionEmail($data) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();
		$templ = self::getEmailTemplate("Subscription Email");

		if (empty($templ)) {
			return false;
		}

		$subject = str_replace(EMAIL_FORM_TYPE, $data["form_type"] == 'events' ? strtolower(JText::_("LNG_EVENTS")) : strtolower(JText::_("LNG_TRIPS")), $templ->email_subject);

		$content = self::prepareEmail($data, null, $templ->email_content, $applicationSettings->company_name);
		$content = self::updateCompanyDetails($content);

		$content = str_replace(EMAIL_CUSTOMER_NAME, $data["first_name"] . ' ' . $data["last_name"], $content);
		$content = str_replace(EMAIL_CONTACT_EMAIL, $data["email"], $content);
		$content = str_replace(EMAIL_PHONE, " ".$data["phone"], $content);
		$content = str_replace(EMAIL_FORM_TYPE, $data["form_type"] == 'events' ? strtolower(JText::_("LNG_EVENTS")) : strtolower(JText::_("LNG_TRIPS")), $content);

		$toEmail = $applicationSettings->company_email;;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, null, $subject, $content, $isHtml, $templ->send_to_admin);
	}


	/**
	 * Send an invitation to become an editor for a listing
	 *
	 * @param [type] $company
	 * @param [type] $email
	 * @param [type] $link
	 * @return void
	 */
	public static function sendEditorInvitation($company, $email, $link) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();
		$templ = self::getEmailTemplate("Listing Editor Invitation");

		if (empty($templ)) {
			return false;
		}

		$subject = str_replace(EMAIL_BUSINESS_NAME, $company->name, $templ->email_subject);

		$content = $templ->email_content;
		$content = str_replace(EMAIL_BUSINESS_NAME, $company->name, $content);
		$content = str_replace(EMAIL_LINK, $link, $content);
		$content = str_replace(EMAIL_USER_NAME, "", $content);
		$content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $content);

		$content = self::updateCompanyDetails($content);

		$toEmail =  $email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, null, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	/**
	 * Send a message to a user
	 *
	 * @param [type] $company
	 * @param [type] $email
	 * @param [type] $link
	 * @return void
	 */
	public static function sendUserMessage($subject, $message, $userId, $to_email) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();
		$templ = self::getEmailTemplate("General message");

		if (empty($templ)) {
			return false;
		}

		$content = $templ->email_content;

		$content = str_replace(EMAIL_MESSAGE, $message, $content);
		$content = self::updateUserName($userId, $content);
		$content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $content);

		$content = self::updateCompanyDetails($content);
		
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $to_email, null, null, $subject, $content, $isHtml, $templ->send_to_admin);
	}
	

	/**
	 * Send an email to the end user if service booking has been confirmed or canceled by admin
	 *
	 * @param $bookingDetails
	 * @return bool|int
	 */
	public static function sendServiceBookingStatusUpdateNotification($bookingDetails) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$templ = self::getEmailTemplate("Service Booking Status Update Notification");
		if (empty($templ)) {
			return false;
		}

		$serviceBookingStatus = '';
		switch($bookingDetails->status) {
			case SERVICE_BOOKING_CONFIRMED:
				$serviceBookingStatus = JText::_('LNG_CONFIRMED');
				break;
			case SERVICE_BOOKING_CANCELED:
				$serviceBookingStatus = JText::_('LNG_CANCELED');
				break;
		}
		
		$content = str_replace(EMAIL_SERVICE_BOOKING_DATE, JBusinessUtil::getDateGeneralFormatWithTime($bookingDetails->created), $templ->email_content);
		$content = str_replace(EMAIL_SERVICE_BOOKING_NAME, $bookingDetails->serviceName, $content);
		$content = str_replace(EMAIL_SERVICE_BOOKING_STATUS, strtolower($serviceBookingStatus), $content);
		$content = self::updateCompanyDetails($content);
		$content = str_replace(EMAIL_CUSTOMER_NAME, $bookingDetails->first_name . " " . $bookingDetails->last_name, $content);
		$content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $content);

		$subject = str_replace(EMAIL_SERVICE_BOOKING_ID, $bookingDetails->id, $templ->email_subject);
		$subject = str_replace(EMAIL_SERVICE_BOOKING_STATUS, strtolower($serviceBookingStatus), $subject);
		$toEmail = $bookingDetails->email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		$replyTo = null;

		return self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	/**
	 * Sends the listing review email
	 *

	 * @return void
	 */
	public static function sendListingReviewResultEmail($user, $company) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();
		
		$templ = self::getEmailTemplate("Listing Review");

		if (empty($templ)) {
			return false;
		}

		$content = $templ->email_content;
		$content = str_replace(EMAIL_USER_NAME, $user->name, $content);
		$content = str_replace(EMAIL_USERNAME, $user->display_name, $content);
		$content = str_replace(EMAIL_PASSWORD, $user->password, $content);
		$content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $content);

		$content = str_replace(EMAIL_PAYMENT_PLAN_INFO, "", $content);
		
		$dashboardLink = JBusinessUtil::processURL(JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanies', false, -1));
		$dashboardBtn = '<a style="white-space: nowrap; background-color: #0978f7; border-radius: 5px; display: inline-block; color: #ffffff; letter-spacing: 0.01em; text-align: center; line-height: 36px; width: 200px;text-decoration: none;" href="'.$dashboardLink.'">Access Dashbard</a>';
		$content = str_replace(EMAIL_DASHBOARD,$dashboardBtn, $content);
		$content = self::updateCompanyDetails($content);
		
		$subject = $templ->email_subject;
		$to_email = $company->email;
		$from = $applicationSettings->company_email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;

		return self::sendEmail($from, $fromName, $from, $to_email, null, null, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	public static function sendOfferAbuseEmail($offer, $email, $message, $reportCause, $sendOnlyToAdmin = true) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$templ = self::getEmailTemplate("Offer Report Notification");

		if ($templ == null) {
			return null;
		}

		$content = str_replace(EMAIL_CONTACT_EMAIL, $email, $templ->email_content);
		$content = str_replace(EMAIL_ABUSE_DESCRIPTION, $message, $content);
		$offerLink = '<a href="' . JBusinessUtil::getOfferLink($offer->id, $offer->alias) . '">' . $offer->name . '</a>';
		$content = str_replace(EMAIL_BUSINESS_NAME, $offerLink, $content);
		$content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $content);
		$content = str_replace(EMAIL_REPORT_CAUSE, $reportCause, $content);

		$content = self::updateCompanyDetails($content);
		$fromName = $email;
		$toEmail = $applicationSettings->company_email;
		$subject = $templ->email_subject;

		$isHtml = true;
		$bcc = array();


		if ($sendOnlyToAdmin) {
			$bcc = null;
			//$toEmail = $from;
		}
		$replyTo = null;

		$result = self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);

		return $result;
	}

	public static function sendEventAbuseEmail($event, $email, $message, $reportCause, $sendOnlyToAdmin = true) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();

		$templ = self::getEmailTemplate("Event Report Notification");

		if ($templ == null) {
			return null;
		}

		$content = str_replace(EMAIL_CONTACT_EMAIL, $email, $templ->email_content);
		$content = str_replace(EMAIL_ABUSE_DESCRIPTION, $message, $content);
		$eventLink = '<a href="' . JBusinessUtil::getEventLink($event->id, $event->alias) . '">' . $event->name . '</a>';
		$content = str_replace(EMAIL_BUSINESS_NAME, $eventLink, $content);
		$content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $content);
		$content = str_replace(EMAIL_REPORT_CAUSE, $reportCause, $content);

		$content = self::updateCompanyDetails($content);
		$fromName = $email;
		$toEmail = $applicationSettings->company_email;
		$subject = $templ->email_subject;

		$isHtml = true;
		$bcc = array();


		if ($sendOnlyToAdmin) {
			$bcc = null;
			//$toEmail = $from;
		}
		$replyTo = null;

		$result = self::sendEmail($fromName, $replyTo, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);

		return $result;
	}
}
