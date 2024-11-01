<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
class JBusinessDirectoryControllerOffer extends JControllerLegacy {
	
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Generate coupon
	 */
	public function generateCoupon() {
		$model = $this->getModel('Offer');

		$user = JBusinessUtil::getUser();
		if (!$user->guest) {
			$model->getCoupon();
		} else {
			JFactory::getApplication()->enqueueMessage(JText::_('LNG_YOU_HAVE_TO_BE_LOGGED_IN'), 'warning');
			$this->setRedirect('index.php?option=com_jbusinessdirectory&view=offers');
		}
	}

	public function checkOffersAboutToExpire() {
		$model = $this->getModel('offer');
		$model->checkOffersAboutToExpire();
	}



	/**
	 * Save the review for the offer
	 */
	public function saveReview() {
		$app = JFactory::getApplication();
		$model = $this->getModel('Offer');
		$user = JBusinessUtil::getUser();
		$data = JFactory::getApplication()->input->post->getArray();
		$offerId = JFactory::getApplication()->input->get('itemId');
		$offer = $model->getPlainOffer($offerId);
		$userReviews = $model->checkUserReviews($user->ID, $offerId);

		$ipAddress = $_SERVER['REMOTE_ADDR'];
		$data["ipAddress"] = $ipAddress;
		$appSettings = JBusinessUtil::getApplicationSettings();

		if ($appSettings->captcha) {
			$captchaAnswer = !empty($data['recaptcha_response_field'])?$data['recaptcha_response_field']:$data['g-recaptcha-response'];
			try {
				$namespace="jbusinessdirectory.contact";
				$captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));
				if (!$captcha->checkAnswer($captchaAnswer)) {
					$error = $captcha->getError();
					$app->setUserState('com_jbusinessdirectory.add.review.data', $data);
					$this->setMessage("Captcha error!", 'warning');
					$this->setRedirect(JBusinessUtil::getOfferLink($offerId, $offer->alias));
					return;
				}
			} catch (Exception $e) {
				$error = $captcha->getError();
				$app->setUserState('com_jbusinessdirectory.add.review.data', $data);
				$this->setMessage("Captcha error!", 'warning');
				$this->setRedirect(JBusinessUtil::getOfferLink($offerId, $offer->alias));
				return;
			}
		}

		$pictures = array();
		foreach ($data as $key => $value) {
			if (
					strpos($key, 'picture_info') !== false
					||
					strpos($key, 'picture_path') !== false
					||
					strpos($key, 'picture_enable') !== false
			) {
				foreach ($value as $k => $v) {
					if (!isset($pictures[$k])) {
						$pictures[$k] = array('picture_info'=>'', 'picture_path'=>'','picture_enable'=>1);
					}
					$pictures[$k][$key] = $v;
				}
			}
		}
		$data['pictures'] = $pictures;

		if ($data['itemUserId'] == $user->ID && !empty($user->ID)) {
			$this->setMessage(JText::_('LNG_NO_REVIEW_FROM_ITEM_OWNER'), 'warning');
			$this->setRedirect(JBusinessUtil::getOfferLink($offerId, $offer->alias));
			return;
		}

		if ($userReviews && $user->ID !=0) {
			$this->setMessage(JText::_('LNG_NO_MORE_THAN_ONE_REVIEW'), 'warning');
			$this->setRedirect(JBusinessUtil::getOfferLink($offerId, $offer->alias));
			return;
		}

		if ($model->saveReview($data)) {
			$this->setMessage(JText::_('LNG_REVIEW_SAVED'));
			$this->setRedirect(JBusinessUtil::getOfferLink($offerId, $offer->alias));
		} else {
			$this->setMessage(JText::_('LNG_ERROR_SAVING_REVIEW'), 'error');
			$this->setRedirect(JBusinessUtil::getOfferLink($offerId, $offer->alias));
		}
	}

	public function cancelReview() {
		$this->setMessage(JText::_('LNG_OPERATION_CANCELLED'));
		$model = $this->getModel('Offer');
		$offerId = JFactory::getApplication()->input->get('itemId');
		$offer = $model->getPlainOffer($offerId);
		$this->setRedirect(JBusinessUtil::getOfferLink($offerId, $offer->alias));
	}


	public function reportAbuse() {
		$app = JFactory::getApplication();
		$appSettings = JBusinessUtil::getApplicationSettings();
		$model = $this->getModel('Offer');
		$data = JFactory::getApplication()->input->post->getArray();

		$offerId = JFactory::getApplication()->input->get('companyId');
		$offer = $model->getPlainOffer($offerId);
		$captchaAnswer = !empty($data['recaptcha_response_field'])?$data['recaptcha_response_field']:$data['g-recaptcha-response'];

		if ($appSettings->captcha) {
			try {
				$namespace="jbusinessdirectory.contact";
				$captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));
				if (!$captcha->checkAnswer($captchaAnswer)) {
					$error = $captcha->getError();
					$this->setMessage("Captcha error!", 'warning');
					$message="Captcha error";
					$this->setRedirect(JBusinessUtil::getOfferLink($offerId, $offer->alias));
					return;
				}
			} catch (Exception $e) {
				$error = $captcha->getError();
				$this->setMessage("Captcha error!", 'warning');
				$message="Captcha error";
				$this->setRedirect(JBusinessUtil::getOfferLink($offerId, $offer->alias));
				return;
			}
		}

		$model = $this->getModel('Offer');

		$result = $model->reportAbuse($data);

		if ($result) {
			$this->setMessage(JText::_('COM_JBUSINESS_DIRECTORY_REVIEW_ABUSE_SUCCESS'));
			$this->setRedirect(JBusinessUtil::getOfferLink($offerId, $offer->alias));
		} else {
			$this->setMessage(JText::_('COM_JBUSINESS_DIRECTORY_REVIEW_ABUSE_FAILED'), 'warning');
			$this->setRedirect(JBusinessUtil::getOfferLink($offerId, $offer->alias));
		}
	}

	public function saveReviewResponse() {
		$app = JFactory::getApplication();
		$appSettings = JBusinessUtil::getApplicationSettings();
		$model = $this->getModel('Offer');
		$data = JFactory::getApplication()->input->post->getArray();
		$offerId = JFactory::getApplication()->input->get('companyId');
		$offer = $model->getPlainOffer($offerId);
		$ipAddress = $_SERVER['REMOTE_ADDR'];
		$data["ipAddress"] = $ipAddress;
		$captchaAnswer = !empty($data['recaptcha_response_field'])?$data['recaptcha_response_field']:$data['g-recaptcha-response'];
		//exit;

		if ($appSettings->captcha) {
			try {
				$namespace="jbusinessdirectory.contact";
				$captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));
				if (!$captcha->checkAnswer($captchaAnswer)) {
					$error = $captcha->getError();
					$app->setUserState('com_jbusinessdirectory.review.response.data', $post);
					$this->setMessage("Captcha error!", 'warning');
					$this->setRedirect(JBusinessUtil::getOfferLink($offerId, $offer->alias));
					return;
				}
			} catch (Exception $e) {
				$error = $captcha->getError();
				$app->setUserState('com_jbusinessdirectory.review.response.data', $data);
				$this->setMessage("Captcha error!", 'warning');
				$this->setRedirect(JBusinessUtil::getOfferLink($offerId, $offer->alias));
				return;
			}
		}

		if ($model->saveReviewResponse($data)) {
			$this->setMessage(JText::_('LNG_REVIEW_RESPONSE_SAVED'));
			$this->setRedirect(JBusinessUtil::getOfferLink($offerId, $offer->alias));
		} else {
			$this->setMessage(JText::_('LNG_ERROR_SAVING_REVIEW'), 'warning');
			$this->setRedirect(JBusinessUtil::getOfferLink($offerId, $offer->alias));
		}
	}

	public function increaseReviewLikeCountAjax() {
		$model = $this->getModel('Offer');
		$data = JFactory::getApplication()->input->post->getArray();
		$result = $model->increaseReviewLikeCount($data["reviewId"]);

		echo '<?xml version="1.0" encoding="utf-8" ?>';
		echo '<offer_statement>';
		echo '<answer result="'.$result.'" reviewId="'.$data["reviewId"].'"/>';
		echo '</offer_statement>';
		echo '</xml>';
		exit;
	}

	public function increaseReviewDislikeCountAjax() {
		$model = $this->getModel('Offer');
		$data = JFactory::getApplication()->input->post->getArray();
		$result = $model->increaseReviewDislikeCount($data["reviewId"]);

		echo '<?xml version="1.0" encoding="utf-8" ?>';
		echo '<offer_statement>';
		echo '<answer result="'.$result.'" reviewId="'.$data["reviewId"].'"/>';
		echo '</offer_statement>';
		echo '</xml>';
		exit;
	}

	public function contactCompany() {
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$appSettings = JBusinessUtil::getApplicationSettings();
		$offermodel = $this->getModel('offer');
		$data = JFactory::getApplication()->input->post->getArray();

		$offerId = JFactory::getApplication()->input->get('offer_Id');
		$contactId = JFactory::getApplication()->input->get('contactId');

		$offer = $offermodel->getPlainOffer($offerId);
		
		if ($appSettings->captcha) {
			$captchaAnswer = !empty($data['recaptcha_response_field'])?$data['recaptcha_response_field']:$data['g-recaptcha-response'];
			try {
				$namespace="jbusinessdirectory.contact";
				$captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));
				if (!$captcha->checkAnswer($captchaAnswer)) {
					$error = $captcha->getError();
					$this->setMessage("Captcha error!", 'warning');
					$this->setRedirect(JBusinessUtil::getOfferLink($offerId, $offer->alias));
					return;
				}
			} catch (Exception $e) {
				$error = $captcha->getError();
				$this->setMessage("Captcha error!", 'warning');
				$this->setRedirect(JBusinessUtil::getOfferLink($offerId, $offer->alias));
				return;
			}
		}

		$data['description'] = JFactory::getApplication()->input->get('description', '', 'RAW');
		$data['contact_id'] = $contactId;
		$data['offer_name'] = $offer->subject;
		$data['offer_alias'] = $offer->alias;
		$data['offer_id'] = $offer->id;

		$result = $offermodel->contactOfferCompany($data, $offer);
		if ($result) {
			$this->setMessage(JText::_('COM_JBUSINESS_DIRECTORY_OFFER_CONTACTED'));
		} else {
			$this->setMessage(JText::_('COM_JBUSINESS_DIRECTORY_COMPANY_NOT_CONTACTED'));
		}

		$offermodel->saveOfferMessages();
		if (!empty($appSettings->redirect_contact_url)) {
			$this->setRedirect(htmlspecialchars($appSettings->redirect_contact_url, ENT_QUOTES));
		} else {
			$this->setRedirect(JBusinessUtil::getOfferLink($offerId, $offer->alias));
		}
	}

	public function addBookmark() {
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$data = JFactory::getApplication()->input->post->getArray();

		$model = $this->getModel('offer');

		$result = $model->addBookmark($data);

		if ($result) {
			$this->setMessage(JText::sprintf('COM_JBUSINESS_BOOKMARK_ADDED', '<a href="'.JRoute::_('index.php?option=com_jbusinessdirectory&view=managebookmarks').'">'.JText::_('LNG_HERE').'</a>'));
		} else {
			$this->setMessage(JText::_('JLIB_APPLICATION_ERROR_SAVE_FAILED'));
		}

		if (isset($data['item_link'])) {
			$link = $data['item_link'];
		} else {
			$offer = $model->getPlainOffer($data["item_id"]);
			$link = JBusinessUtil::getOfferLink($offer->id, $offer->alias);
		}

		$this->setRedirect($link);
	}

	public function updateBookmark() {
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$data = JFactory::getApplication()->input->post->getArray();

		$model = $this->getModel('offer');

		$result = $model->updateBookmark($data);

		if ($result) {
			$this->setMessage(JText::_('COM_JBUSINESS_BOOKMARK_UPDATED'));
		} else {
			$this->setMessage(JText::_('JLIB_APPLICATION_ERROR_SAVE_FAILED'));
		}

		if (isset($data['item_link'])) {
			$link = $data['item_link'];
		} else {
			$offer = $model->getPlainOffer($data["item_id"]);
			$link = JBusinessUtil::getOfferLink($offer->id, $offer->alias);
		}

		$this->setRedirect($link);
	}

	public function removeBookmark() {
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$data = JFactory::getApplication()->input->post->getArray();

		$model = $this->getModel('offer');
		$result = $model->removeBookmark($data);

		if ($result) {
			$this->setMessage(JText::_('COM_JBUSINESS_BOOKMARK_REMVED'));
		} else {
			$this->setMessage(JText::_('JLIB_APPLICATION_ERROR_SAVE_FAILED'));
		}

		if (isset($data['item_link'])) {
			$link = $data['item_link'];
		} else {
			$offer = $model->getPlainOffer($data["item_id"]);
			$link = JBusinessUtil::getOfferLink($offer->id, $offer->alias);
		}

		$this->setRedirect($link);
	}

	/**
	 * Method to update the quantity of selling options
	 *
	 * @throws Exception
	 * @since version
	 */
	public function updateQuantityAjax(){
		$data = JFactory::getApplication()->input->getArray();

		$model = $this->getModel('Offer');
		$result = $model->updateQuantityAjax($data);

		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;
	}
}
