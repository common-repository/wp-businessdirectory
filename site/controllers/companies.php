<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');



class JBusinessDirectoryControllerCompanies extends JControllerLegacy {
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	 
	public function __construct() {
		parent::__construct();
	}

	public function displayCompany() {
		parent::display();
	}
	
	public function showCompany() {
		$model = $this->getModel('companies');
		$model->increaseViewCount();
		JFactory::getApplication()->input->set("view", "companies");
		parent::display();
	}
	
	public function saveReview() {
		$app = JFactory::getApplication();
		$model = $this->getModel('companies');
		$user = JBusinessUtil::getUser();
		$data = JFactory::getApplication()->input->post->getArray();
		$companyId = JFactory::getApplication()->input->get('itemId');
		$company = $model->getPlainCompany($companyId);
		$userReviews = $model->checkUserReviews($user->ID, $companyId);
		$ipAddress = $_SERVER['REMOTE_ADDR'];
		$data["ipAddress"] = $ipAddress;
		
		$appSettings = JBusinessUtil::getApplicationSettings();
		if ($appSettings->captcha) {
			$namespace="jbusinessdirectory.contact";
			try {
				$captchaAnswer = !empty($post['recaptcha_response_field'])?$post['recaptcha_response_field']:$post['g-recaptcha-response'];
				$captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));
				if (!$captcha->checkAnswer($captchaAnswer)) {
					$error = $captcha->getError();
					$app->setUserState('com_jbusinessdirectory.add.review.data', $post);
					$this->setMessage("Captcha error!", 'warning');
					$this->setRedirect(JBusinessUtil::getCompanyLink($company));
					return;
				}
			} catch (Exception $e) {
				$error = $captcha->getError();
				$this->setMessage("Captcha error!", 'warning');
				$this->setRedirect(JBusinessUtil::getCompanyLink($company));
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
			$this->setRedirect(JBusinessUtil::getCompanyLink($company));
			return;
		}

		if ($userReviews && $user->ID !=0) {
			$this->setMessage(JText::_('LNG_NO_MORE_THAN_ONE_REVIEW'), 'warning');
			$this->setRedirect(JBusinessUtil::getCompanyLink($company));
			return;
		}

		if ($appSettings->enable_reviews_users && $user->ID ==0) {
			$this->setMessage(JText::_('LNG_ERROR_SAVING_REVIEW'), 'error');
			$this->setRedirect(JBusinessUtil::getCompanyLink($company));
			return;
		}
				
		if ($model->saveReview($data, $company)) {
			$this->setMessage(JText::_('LNG_REVIEW_SAVED'));
			$this->setRedirect(JBusinessUtil::getCompanyLink($company));
		} else {
			$this->setMessage(JText::_('LNG_ERROR_SAVING_REVIEW'), 'error');
			$this->setRedirect(JBusinessUtil::getCompanyLink($company));
		}
	}

	public function updateRating() {
		$appSettings = JBusinessUtil::getApplicationSettings();
		$user = JBusinessUtil::getUser();
		if ($appSettings->enable_reviews_users && $user->ID ==0) {
			exit;
		}
		
		$model = $this->getModel('companies');
		$data = JFactory::getApplication()->input->post->getArray();
		$ipAddress = $_SERVER['REMOTE_ADDR'];
		$data["ipAddress"] = $ipAddress;
		$ratingId = $model->saveRating($data);
		$nrRatings = $model->getRatingsCount($data['companyId']);
		$company = $model->getCompany($data['companyId']);
		
		echo '<?xml version="1.0" encoding="utf-8" ?>';
		echo '<company_statement>';
		echo '<answer  nrRatings="'.($nrRatings).'" id="'.$data["companyId"].'" ratingId="'.$ratingId.'" averageRating="'.$company->averageRating.'"/>';
		echo '</company_statement>';
		echo '</xml>';
		exit;
	}
	
	public function reportAbuse() {
		$app = JFactory::getApplication();
		$appSettings = JBusinessUtil::getApplicationSettings();
		$model = $this->getModel('companies');
		$data = JFactory::getApplication()->input->post->getArray();
		
		$companyId = JFactory::getApplication()->input->get('companyId');
		$company = $model->getPlainCompany($companyId);

		if ($appSettings->captcha) {
			$captchaAnswer = (!isset($data['recaptcha_response_field']) && !empty($data['recaptcha_response_field']))?$data['recaptcha_response_field']:$data['g-recaptcha-response'];
			$namespace="jbusinessdirectory.contact";
			try {
				$captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));
				if (!$captcha->checkAnswer($captchaAnswer)) {
					$error = $captcha->getError();
					$this->setMessage("Captcha error!", 'warning');
					$message="Captcha error";
					$this->setRedirect(JBusinessUtil::getCompanyLink($company));
					return;
				}
			} catch (Exception $e) {
				$error = $captcha->getError();
				$this->setMessage("Captcha error!", 'warning');
				$this->setRedirect(JBusinessUtil::getCompanyLink($company));
				return;
			}
		}
		
		$model = $this->getModel('companies');
	
		$result = $model->reportAbuse($data);
	
		if ($result) {
			$this->setMessage(JText::_('COM_JBUSINESS_DIRECTORY_REVIEW_ABUSE_SUCCESS'));
			$this->setRedirect(JBusinessUtil::getCompanyLink($company));
		} else {
			$this->setMessage(JText::_('COM_JBUSINESS_DIRECTORY_REVIEW_ABUSE_FAILED'), 'warning');
			$this->setRedirect(JBusinessUtil::getCompanyLink($company));
		}
	}
	
	public function saveReviewResponse() {
		$app = JFactory::getApplication();
		$appSettings = JBusinessUtil::getApplicationSettings();
		$model = $this->getModel('companies');
		$data = JFactory::getApplication()->input->post->getArray();
		$companyId = JFactory::getApplication()->input->get('companyId');
		$company = $model->getPlainCompany($companyId);
		$ipAddress = $_SERVER['REMOTE_ADDR'];
		$data["ipAddress"] = $ipAddress;
		//exit;
		
		if ($appSettings->captcha) {
			$captchaAnswer = (!isset($data['recaptcha_response_field']) && !empty($data['recaptcha_response_field']))?$data['recaptcha_response_field']:$data['g-recaptcha-response'];
			try {
				$namespace="jbusinessdirectory.contact";
				$captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));
				if (!$captcha->checkAnswer($captchaAnswer)) {
					$error = $captcha->getError();
					$app->setUserState('com_jbusinessdirectory.review.response.data', $post);
					$this->setMessage("Captcha error!", 'warning');
					$this->setRedirect(JBusinessUtil::getCompanyLink($company));
					return;
				}
			} catch (Exception $e) {
				$error = $captcha->getError();
				$this->setMessage("Captcha error!", 'warning');
				$this->setRedirect(JBusinessUtil::getCompanyLink($company));
				return;
			}
		}

		if ($model->saveReviewResponse($data)) {
			$this->setMessage(JText::_('LNG_REVIEW_RESPONSE_SAVED'));
		}else{
			$this->setMessage(JText::_('LNG_ERROR_SAVING_REVIEW'), 'warning');
		}

		$redirect = JFactory::getApplication()->input->get('redirect');
		if(!empty($redirect)){
			$redirect = base64_decode($redirect);
			$this->setRedirect($redirect);
		}else{
			$this->setRedirect(JBusinessUtil::getCompanyLink($company));
		}
	}
	
	public function increaseReviewLikeCountAjax() {
		$model = $this->getModel('companies');
		$data = JFactory::getApplication()->input->post->getArray();
		;
		$result = $model->increaseReviewLikeCount($data["reviewId"]);
		
		echo '<?xml version="1.0" encoding="utf-8" ?>';
		echo '<company_statement>';
		echo '<answer result="'.$result.'" reviewId="'.$data["reviewId"].'"/>';
		echo '</company_statement>';
		echo '</xml>';
		exit;
	}
	
	public function increaseReviewDislikeCountAjax() {
		$model = $this->getModel('companies');
		$data = JFactory::getApplication()->input->post->getArray();
		;
		$result = $model->increaseReviewDislikeCount($data["reviewId"]);
		
		echo '<?xml version="1.0" encoding="utf-8" ?>';
		echo '<company_statement>';
		echo '<answer result="'.$result.'" reviewId="'.$data["reviewId"].'"/>';
		echo '</company_statement>';
		echo '</xml>';
		exit;
	}
	
	public function increaseReviewLoveCountAjax() {
		$model = $this->getModel('companies');
		$data = JFactory::getApplication()->input->post->getArray();
		;
		$result = $model->increaseReviewLoveCount($data["reviewId"]);
		
		echo '<?xml version="1.0" encoding="utf-8" ?>';
		echo '<company_statement>';
		echo '<answer result="'.$result.'" reviewId="'.$data["reviewId"].'"/>';
		echo '</company_statement>';
		echo '</xml>';
		exit;
	}

	public function updateCompanyOwner() {
		$model = $this->getModel('companies');
		$data = JFactory::getApplication()->input->post->getArray();
		;
		$result = $model->updateCompanyOwner($data["companyId"], $data["userId"]);
		
		
		echo '<?xml version="1.0" encoding="utf-8"?>';
		echo '<company_statement>';
		echo '<answer result="'.$result.'"/>';
		echo '</company_statement>';
		echo '</xml>';
		exit;
	}
	
	public function checkCompanyName() {
		$data = JFactory::getApplication()->input->post->getArray();
		$model = $this->getModel('companies');
		$company = $model->getCompanyByName(trim($data["companyName"]));
	
		
		$claim = isset($company->userId)?0:1;
		
		$exists = isset($company)?1:0;
	
		echo '<?xml version="1.0" encoding="utf-8" ?>';
		echo '<company_statement>';
		echo '<answer exists="'.$exists.'" claim="'.$claim.'" name="'.trim($data["companyName"]).'"/>';
		echo '</company_statement>';
		echo '</xml>';
		exit;
	}
	
	public function contactCompany() {
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$jinput = JFactory::getApplication()->input;

		$appSettings = JBusinessUtil::getApplicationSettings();
		$model = $this->getModel('companies');
		
		$data = $jinput->post->getArray();
		$companyId = $jinput->get('companyId');
		$contactId = $jinput->getInt('contact_id');
		$company = $model->getPlainCompany($companyId);

		if ($appSettings->captcha) {
			$captchaAnswer = !empty($data['recaptcha_response_field'])?$data['recaptcha_response_field']:$data['g-recaptcha-response'];
			$namespace="jbusinessdirectory.contact";
			try {
				$captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));
				if (!$captcha->checkAnswer($captchaAnswer)) {
					//$error = $captcha->getError();
					$this->setMessage("Captcha error!", 'warning');
					$this->setRedirect(JBusinessUtil::getCompanyLink($company));
					return;
				}
			} catch (Exception $e) {
				//$error = $captcha->getError();
				$this->setMessage("Captcha error!", 'warning');
				$this->setRedirect(JBusinessUtil::getCompanyLink($company));
				return;
			}
		}

		$data['description'] = $jinput->get('description', '', 'RAW');
		$data['contact_id'] = $contactId;
		
		$result = $model->contactCompany($data);
		
		if ($result) {
			$this->setMessage(JText::_('LNG_CONTACT_REQUEST_SENT_SUCCESSFULLY'));
		} else {
			$this->setMessage(JText::_('LNG_CONTACT_REQUEST_ERROR'));
		}

		$model->saveCompanyMessages();
		if (!empty($appSettings->redirect_contact_url)) {
			$this->setRedirect(htmlspecialchars($appSettings->redirect_contact_url, ENT_QUOTES));
		} else {
			$this->setRedirect(JBusinessUtil::getCompanyLink($company));
		}
	}
	
	
	public function contactCompanyAjax() {
		// Check for request forgeries.
		$appSettings = JBusinessUtil::getApplicationSettings();
		$data = JFactory::getApplication()->input->post->getArray();
		$captchaAnswer = !empty($data['recaptcha_response_field'])?$data['recaptcha_response_field']:$data['g-recaptcha-response'];
		
		$errorFlag = false;
		$message="";
		if ($appSettings->captcha) {
			try {
				$namespace="jbusinessdirectory.contact";
				$captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));
				if (!$captcha->checkAnswer($captchaAnswer)) {
					$error = $captcha->getError();
					$this->setMessage("Captcha error!", 'warning');
					$message="Captcha error";
					$errorFlag = true;
				}
			} catch (Exception $e) {
				$error = $captcha->getError();
				$this->setMessage("Captcha error!", 'warning');
				$message="Captcha error";
				$errorFlag = true;
			}
		}

		$model = $this->getModel('companies');

		if (!$errorFlag) {
			$result = $model->contactCompany($data);
		
			if ($result) {
				$this->setMessage(JText::_('COM_JBUSINESS_DIRECTORY_COMPANY_CONTACTED'));
			} else {
				$this->setMessage(JText::_('COM_JBUSINESS_DIRECTORY_COMPANY_NOT_CONTACTED'));
				$message=JText::_('COM_JBUSINESS_DIRECTORY_COMPANY_NOT_CONTACTED');
				$errorFlag = true;
			}
		}

		$model->saveCompanyMessages();
	
		echo '<?xml version="1.0" encoding="utf-8" ?>';
		echo '<category_statement>';
		echo '<answer error="'.(!$errorFlag ? "0" : "1").'" errorMessage="'.$message.'" redirect_url="'.(!empty($appSettings->redirect_contact_url) ? htmlspecialchars($appSettings->redirect_contact_url, ENT_QUOTES) : "").'" ';
		echo '</category_statement>';
		echo '</xml>';
		exit;
	}
	
	public function requestQuoteCompanyAjax() {
		// Check for request forgeries.
		$appSettings = JBusinessUtil::getApplicationSettings();
		$data = JFactory::getApplication()->input->post->getArray();
		$captchaAnswer = !empty($data['recaptcha_response_field'])?$data['recaptcha_response_field']:$data['g-recaptcha-response'];
	
		$errorFlag = false;
		$message="";
		if ($appSettings->captcha) {
			try {
				$namespace="jbusinessdirectory.contact.quote";
				$captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));
				if (!$captcha->checkAnswer($captchaAnswer)) {
					$error = $captcha->getError();
					$this->setMessage("Captcha error!", 'warning');
					$message="Captcha error";
					$errorFlag = true;
				}
			} catch (Exception $e) {
				$error = $captcha->getError();
				$this->setMessage("Captcha error!", 'warning');
				$message="Captcha error";
				$errorFlag = true;
			}
		}
	
		if (!$errorFlag) {
			$model = $this->getModel('companies');
		
			$result = $model->requestQuoteCompany($data);
		
			if ($result) {
				$this->setMessage(JText::_('COM_JBUSINESS_DIRECTORY_COMPANY_CONTACTED'));
				$message=JText::_('COM_JBUSINESS_DIRECTORY_COMPANY_CONTACTED');
			} else {
				$this->setMessage(JText::_('COM_JBUSINESS_DIRECTORY_COMPANY_NOT_CONTACTED'));
				$message=JText::_('COM_JBUSINESS_DIRECTORY_COMPANY_NOT_CONTACTED');
				$errorFlag = true;
			}
		}
	
	
		echo '<?xml version="1.0" encoding="utf-8" ?>';
		echo '<category_statement>';
		echo '<answer error="'.(!$errorFlag ? "0" : "1").'" errorMessage="'.$message.'"/>';
		echo '</category_statement>';
		echo '</xml>';
		exit;
	}
	
	public function checkBusinessAboutToExpire() {
		$model = $this->getModel('companies');
		$model->checkBusinessAboutToExpire();
	}

	/**
	 * Process expired orders
	 *
	 * @return void
	 */
	public function processExpiredOrders() {
		$model = $this->getModel('companies');
		$model->processExpiredOrders();
	}
		
	public function getReviewQuestionAnswersAjax() {
		$reviewId = JFactory::getApplication()->input->get('reviewId');
		$model = $this->getModel('Companies');

		$result = $model->getReviewQuestionAnswersAjax($reviewId);

		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;
	}

	public function saveAnswerAjax() {
		$answerId = JFactory::getApplication()->input->get('answerId');
		$answer = JFactory::getApplication()->input->getString('answer');

		$data = array();
		$data["id"] = $answerId;
		$data["answer"] = $answer;

		$model = $this->getModel('Companies');

		$result = $model->saveAnswerAjax($data);

		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;
	}

	public function reportListing() {
		$app = JFactory::getApplication();
		$appSettings = JBusinessUtil::getApplicationSettings();
		$model = $this->getModel('companies');
		$data = JFactory::getApplication()->input->post->getArray();
		
		$companyId = JFactory::getApplication()->input->get('companyId');
		$company = $model->getPlainCompany($companyId);
		
		if ($appSettings->captcha) {
			try {
				$captchaAnswer = ! empty($post['recaptcha_response_field']) ? $post['recaptcha_response_field'] : $post['g-recaptcha-response'];
				$namespace = "jbusinessdirectory.contact";
				$captcha = JCaptcha::getInstance("recaptcha", array(
					'namespace' => $namespace
				));
			
				if (! $captcha->checkAnswer($captchaAnswer)) {
					$error = $captcha->getError();
					$this->setMessage("Captcha error!", 'warning');
					$message = "Captcha error";
					$this->setRedirect(JBusinessUtil::getCompanyLink($company));
					return;
				}
			} catch (Exception $e) {
				$error = $captcha->getError();
				$this->setMessage("Captcha error!", 'warning');
				$message = "Captcha error";
				$this->setRedirect(JBusinessUtil::getCompanyLink($company));
				return;
			}
		}
		
		$result = $model->reportListing();
		if ($result) {
			$this->setMessage(JText::_('COM_JBUSINESS_DIRECTORY_OFFER_REVIEW_ABUSE_SUCCESS'));
		} else {
			$this->setMessage(JText::_('COM_JBUSINESS_ERROR'));
		}
		
		$this->setRedirect(JBusinessUtil::getCompanyLink($company));
	}
	/**
	 * Gets the appointment data from the front end, checks the captcha,
	 * redirects in case of an error in the captcha. Calls a function to save the
	 * appointment data. On success, it sends an email containing the information,
	 * and redirects to the company page and displays a success message.
	 *
	 * On failure, it redirects back to the company page and displays an error.
	 */
	public function leaveAppointment() {
		$app = JFactory::getApplication();
		$appSettings = JBusinessUtil::getApplicationSettings();
		$model = $this->getModel('companies');
		$data = JFactory::getApplication()->input->post->getArray();
		$companyId = JFactory::getApplication()->input->get('company_id');
		$company = $model->getPlainCompany($companyId);
		$companyEmail = $company->email;
		$companyName = $company->name;

		if ($appSettings->captcha) {
			try {
				$captchaAnswer = !empty($post['recaptcha_response_field'])?$post['recaptcha_response_field']:$post['g-recaptcha-response'];
				$namespace="jbusinessdirectory.contact";
				$captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));
				if (!$captcha->checkAnswer($captchaAnswer)) {
					$error = $captcha->getError();
					$this->setMessage("Captcha error!", 'warning');
					$this->setRedirect(JBusinessUtil::getCompanyLink($company));
					return;
				}
			} catch (Exception $e) {
				$error = $captcha->getError();
				$this->setMessage("Captcha error!", 'warning');
				$this->setRedirect(JBusinessUtil::getCompanyLink($company));
				return;
			}
		}

		$eventModel = $this->getModel('event');
		$event = $eventModel->getPlainEvent($data['event_id']);
		if ($eventModel->leaveAppointment($data)) {
			$this->setMessage(JText::_('LNG_APPOINTMENT_RESERVED'));
			EmailService::sendAppointmentEmail($event, $data, $company, $companyName);
			$this->setRedirect(JBusinessUtil::getCompanyLink($company));
		} else {
			$this->setMessage(JText::_('LNG_ERROR_RESERVING_APPOINTMENT'), 'warning');
			$this->setRedirect(JBusinessUtil::getCompanyLink($company));
		}
	}

	public function increaseShareCountAjax() {
		$model = $this->getModel('companies');
		$company = $model->increaseShareCount(JFactory::getApplication()->input->get('itemId'), JFactory::getApplication()->input->get('itemType'));

		JFactory::getApplication()->close();
	}

	public function getProjectDetailsAjax() {
		$projectId = JFactory::getApplication()->input->get('projectId');
		$model = $this->getModel('Companies');

		$result = $model->getProjectDetails($projectId);

		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;
	}

	public function generateQrCode() {
		$listingId = JFactory::getApplication()->input->get('itemId');
		$model = $this->getModel('Companies');

		$model->generateQrCode($listingId);
		exit;
	}

	public function generateVCard() {
		$listingId = JFactory::getApplication()->input->get('itemId');
		$model = $this->getModel('Companies');

		$model->generateVCard($listingId);
		exit;
	}

	public function acceptMapGDPRAjax() {
		setcookie('jbd_map_gdpr', true, time() + (86400 * 30), "/");

		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode(true);
		exit;
	}

	public function requestQuoteCompanyProductAjax() {
		// Check for request forgeries.
		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$post = JFactory::getApplication()->input->post->getArray();
		$captchaAnswer = !empty($post['recaptcha_response_field'])?$post['recaptcha_response_field']:$post['g-recaptcha-response'];

		$errorFlag = false;
		$message="";
		if ($appSettings->captcha) {
			$namespace="jbusinessdirectory.contact";
			$captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));
			if (!$captcha->checkAnswer($captchaAnswer)) {
				$error = $captcha->getError();
				$this->setMessage("Captcha error!", 'warning');
				$message="Captcha error";
				$errorFlag = true;
			}
		}

		if (!$errorFlag) {
			$model = $this->getModel('companies');

			$result = $model->requestQuoteCompanyProduct($post);

			if ($result) {
				$this->setMessage(JText::_('COM_JBUSINESS_DIRECTORY_COMPANY_CONTACTED'));
				$message=JText::_('COM_JBUSINESS_DIRECTORY_COMPANY_CONTACTED');
			} else {
				$this->setMessage(JText::_('COM_JBUSINESS_DIRECTORY_COMPANY_NOT_CONTACTED'));
				$message=JText::_('COM_JBUSINESS_DIRECTORY_COMPANY_NOT_CONTACTED');
				$errorFlag = true;
			}
		}


		echo '<?xml version="1.0" encoding="utf-8" ?>';
		echo '<category_statement>';
		echo '<answer error="'.(!$errorFlag ? "0" : "1").'" errorMessage="'.$message.'"/>';
		echo '</category_statement>';
		echo '</xml>';
		exit;
	}

	/**
	 * Process the link request from the user.
	 * Get the main company Id and the company Ids and link them
	 *
	 * @throws Exception
	 */
	public function joinCompany() {
		$companyIds = JFactory::getApplication()->input->getString('companyIds');
		$multipleListings = JFactory::getApplication()->input->getString('multipleListings');
		$mainCompanyId = JFactory::getApplication()->input->getString('mainCompanyId');

		if (empty($companyIds)) {
			if (!$multipleListings) {
				$user = JBusinessUtil::getUser();
				$companyIds = array();
				$companies = JBusinessUtil::getCompaniesOptions($mainCompanyId, $user->ID);
				foreach ($companies as $company) {
					$companyIds[] = $company->id;
				}
			} else {
				$companyIds = array();
			}
		}

		if (!is_array($companyIds)) {
			$companyIds = explode(',', $companyIds);
		}

		$model = $this->getModel('Companies');
		if ($model->joinCompany($mainCompanyId, $companyIds)) {
			$response['message'] = JText::_('COM_JBUSINESS_DIRECTORY_COMPANY_REGISTERED_SUCCESSFULLY');
			$response['error'] = 0;
		} else {
			$response['message'] = JText::_('COM_JBUSINESS_DIRECTORY_COMPANY_NOT_JOINED');
			$response['error'] = 1;
		}

		header('Content-Type: application/json', true);
		echo json_encode($response);
		exit;
	}


	public function getMoreReviewsAjax(){
		
		$view = $this->getView("Companies","Html");

		$input = JFactory::getApplication()->input;
		$start = $input->getInt("start");
		$companyId = $input->getInt("companyId");

		$model = $this->getModel('Companies');
		$company = $model->getCompany();
		$view->setModel($model, true);
		$reviews = $model->getReviews(null, $start, REVIEWS_LIMIT, $companyId);

		$response = new stdClass();
		ob_start();		
		$view->displayReviews($reviews, 'reviews');
		
		$response->reviews =  ob_get_contents();
		ob_end_clean();
		$response->show_more = count($reviews) == REVIEWS_LIMIT;

		$displayLimit = REVIEWS_LIMIT - 1;
		$response->reviewsCount = count($reviews) > $displayLimit ? $displayLimit : count($reviews);
		JBusinessUtil::sendJsonResponse($response, RESPONSE_STATUS_SUCCESS, '');
	}
}
