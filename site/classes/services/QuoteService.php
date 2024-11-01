<?php
/**
 * @package    JBusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
JTable::addIncludePath(WP_BUSINESSDIRECTORY_PATH . 'admin/tables');

require_once BD_CLASSES_PATH.'/traits/QuoteMessagesHelper.php';

class QuoteService {
	use QuoteMessagesHelper;

	const VALIDATE_DEFAULT = 1;
	const VALIDATE_LOCATION = 2;
	const VALIDATE_INPUT = 3;
	const VALIDATE_USER = 4;
	const VALIDATE_SKIP = 0;

	/**
	 * Retrieves all questions belonging to a category along with the question options.
	 *
	 * @param $categoryId int ID of the category
	 *
	 * @return array of questions
	 *
	 * @throws Exception
	 *
	 * @since 5.3.0
	 */
	public static function getQuestionsByCategory($categoryId) {
		$quotesTable = JTable::getInstance('RequestQuoteQuestions', 'Table', array());
		try {
			$questions = $quotesTable->getQuestionsByCategory($categoryId);
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}

		if (count($questions) == 0) {
			//throw new Exception(JText::_('LNG_NO_QUESTIONS_AVAILABLE'));
		}

		return $questions;
	}

	/**
	 * Processes question object
	 *
	 * @param $question object, must contain options field
	 *
	 * @return null|object
	 *
	 * @since 5.3.0
	 */
	public static function processQuestion($question) {
		if (empty($question->options)) {
			return null;
		}

		$question->options = explode('#', $question->options);

		return $question;
	}

	/**
	 * Retrieves the link/button that will initiate the quote request process.
	 *
	 * @param       $categoryId int ID of the category
	 * @param array $options    array of options, can set button text, class or inline style (style="color:#fff;").
	 *
	 * @return string
	 *
	 * @since 5.3.0
	 */
	public static function getQuoteRequestLink($categoryId, $options = array()) {
		$html = '';

		$appSettings = JBusinessUtil::getApplicationSettings();
		if (!$appSettings->enable_request_quote_app) {
			return $html;
		}

		$text  = JText::_('LNG_REQUEST_QUOTE');
		$style = '';
		$class = 'btn btn-primary btn-request-quote';
		if (isset($options["text"])) {
			$text = $options["text"];
		}
		if (isset($options["style"])) {
			$style = $options["style"];
		}
		if (isset($options["class"])) {
			$class = $options["class"];
		}

		$html .= "<a href='javascript:void(0);' onclick='jbdQuoteRequest.init($categoryId)' class='$class' $style>";
		$html .= $text;
		$html .= "</a>";

		$html .= self::initializeQuoteRequets();
		
		return $html;
	}
	
	public static function initializeQuoteRequets() {
		$html = '';
		$html .= self::getModalHtml();
		
		JBusinessUtil::loadMapScripts();
		
		//add translations that will be used in js
		JText::script('LNG_SENDING_REQUESTS');
		JText::script('LNG_PLEASE_SELECT_ONE_OPTION');
		JText::script('LNG_PLEASE_FILL_THE_DETAILS');
		JText::script('LNG_CONTACTING_LISTINGS');
		JText::script('LNG_SEARCHING_LISTINGS');
		JText::script('LNG_RETRIEVING_QUESTIONS');
		JText::script('LNG_ALREADY_HAVE_ACCOUNT');
		JText::script('LNG_LOG_IN');
		JText::script('LNG_ENTER_USER_DETAILS');
		JText::script('LNG_YOU_HAVE_TO_BE_LOGGED_IN');
		JText::script('LNG_NOT_MEMBER');
		JText::script('LNG_REGISTER_HERE');
		
		return $html;
	}
	

	/**
	 * Retrieves the HTML for the quote request modal. Hidden by default.
	 *
	 * @return string HTML
	 *
	 * @since 5.3.0
	 */
	public static function getModalHtml() {
		$html = '';
		$html .= "<div class='jbd-container quote-request-modal' id='quote-request-modal' style='display:none'>";
		$html .=    "<div class='jmodal-sm'>";
		$html .=    	"<div class='jmodal-header'><a href='#close-modal' rel='modal:close' class='close-btn'><i class='la la-close'></i></a></div>";
		$html .=        "<div class='jmodal-body'>";
		$html .=            "<div class='loading-quote-requests' id='loading-quote-requests'>";
		$html .=                "<img class='spinner' />";
		$html .=                "<p class='muted msg'>" . JText::_('LNG_RETRIEVING_QUESTIONS') . "</p>";
		$html .=            "</div>";
		$html .=            self::getThankYouStep();
		$html .=            "<div class='modal-container'>";
		$html .=            "</div>";
		$html .=        "</div>";
		$html .=    "</div>";
		$html .= "</div>";

		return $html;
	}

	/**
	 * Renders the question and options into HTML.
	 *
	 * @param      $question object containing question and options
	 *
	 * @return string HTML
	 *
	 * @since 5.3.0
	 */
	public static function renderQuestion($question) {
		$html = '';

		$oType = ''; // option type
		switch ($question->type) {
			case QUESTION_TYPE_RADIO:
				$oType = 'radio';
				break;
			case QUESTION_TYPE_CHECKBOX:
				$oType = 'checkbox';
				break;
			case QUESTION_TYPE_INPUT:
				$oType = 'text';
				break;

			default:
				$oType = 'radio';
		}

		$html .= "<div class='question-container'>";
		$html .=    '<div class="question-title">';
		$html .=        '<p class="lead">' . $question->name . '</p>';
		$html .=    '</div>';
		$html .= '<div class="options-container">';

		if ($question->type != QUESTION_TYPE_INPUT) {
			foreach ($question->options as $key => $option) {
				$optionId = 'option_' . $question->id . '_' . $key;

				$html .= "<div class='option-row d-flex justify-content-left' id='option-$key'>";
				$html .= "<div class='j$oType question-container'>";
				$html .= "<input type='$oType' name='_answer-$question->id' id='$optionId' value='$option' />";
				$html .= "<input type='hidden' name='_question-$question->id' value='$question->name' />";
				$html .= "<label for='$optionId'></label>";
				$html .= "</div>";
				$html .= "<p class='option-text'>" . $option . "</p>";
				$html .= "</div>";
			}
		} else {
			$html .= "<div class='option-row d-flex justify-content-left' id='option-1'>";
            $html .=    "<textarea style='width: 100%;border:none;' name='_answer-$question->id' id='1' rows='4'></textarea>";
            $html .=    "<input type='hidden' name='_question-$question->id' value='$question->name' />";
            $html .= "</div>";
		}

		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}

	/**
	 * Creates the geo-location input step with autocomplete functionality
	 *
	 * @return string
	 *
	 * @since 5.3.0
	 */
	public static function getLocationStep() {
		$html = '';

		$html .= '  
  				  <div class="row">
  				  	<div class="col-6">
  				  	  <img src="'.BD_PICTURES_PATH.'/flat_world.png" />
					</div>
					
					<div class="col-6 mt-5">
						<p class="lead">'.JText::_('LNG_ENTER_LOCATION').'</p>
					  	<div class="has-jicon-left mt-5" id="quote-zipcode-field-container">
	                        <input class="search-field zipcode-quote" placeholder="' . JText::_("LNG_LOCATION") . '" type="text" name="zipcode" id="quote-zipcode" value="" style="height: 38px;" />
	                        <i class="la la-map-marker"></i>
	                        
	                        <input type="hidden" id="quote-latitude" name="quote-latitude" />
	                        <input type="hidden" id="quote-longitude" name="quote-longitude" />
    					</div>
					</div>
				</div>';

		return $html;
	}

	/**
	 * Creates the user email step
	 *
	 * @return string
	 *
	 * @since 5.3.0
	 */
	public static function getEmailStep() {
		$html = '';

		$html .= '  
  				  <div class="row">
  				      <div class="col-md-6 mt-5">
					  	  <p id="description">'.JText::_('LNG_ENTER_USER_DETAILS').'</p>
					  </div>
					  <div class="col-md-6">
                        <img src="'.BD_PICTURES_PATH.'/flat_email.png" />
                      </div>
				  </div>
                   
                  <div>  
                    <div>
                        <div class="mt-5" id="nameDiv">
                            <input class="search-field validate[required]" placeholder="' . JText::_("LNG_NAME") . '" type="text" name="name" id="name" value="" />
                        </div>
                    </div>
                    <div>
                        <div class="has-jicon-left mt-5">
                            <input class="search-field validate[required,custom[email]]" placeholder="' . JText::_("LNG_EMAIL") . '" type="email" name="email" id="email" value="" />
                            <i class="la la-envelope"></i>
                        </div>
                    </div>
                    <div>
                        <div class="mt-5">
                            <input class="search-field validate[required]" placeholder="' . JText::_("LNG_PASSWORD") . '" type="password" name="password" id="password" value="" />
                        </div>
                    </div>
					<div class="row">
					<div class="col-12" id="loginDiv" style="margin-top: 20px">'
					   . JText::_('LNG_ALREADY_HAVE_ACCOUNT') . " " .'<a onclick="javascript:jbdQuoteRequest.toggleLogIn()" href="#">'
					   . JText::_('LNG_LOG_IN') .  '</a>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<?php echo JBusinessUtil::renderTermsAndConditions("contact"); ?>
					</div>
				</div>
				</div>
				<input type="hidden" id="login" name="login" value="0"> ';

		return $html;
	}

	/**
	 * Creates the leave a note step
	 *
	 * @return string
	 *
	 * @since 5.3.0
	 */
	public static function getNoteStep() {
		$user = JBusinessUtil::getUser();
		$companies = JBusinessUtil::getCompaniesByUserId($user->ID);
		$html = '';

		$html .= '  
  				<div class="row">
  				  	<div class="col-6">
						<img src="'.BD_PICTURES_PATH.'/flat_note.png" />
					</div>
					<div class="col-6">
						<p class="pb-0 mt-2 mb-0 font-weight-bold">'.JText::_('LNG_REQUEST_TITLE').'</p>
						<input type="text" class="form-control" name="title" rows="10"></input type="text">';

						if(!empty($companies) && $user->ID !=0) {
							$html .= '
									<p class="pb-0 mt-2 mb-0 font-weight-bold">'.JText::_('LNG_SELECT_COMPANY').'</p>
									<select name="company_id">
									    <option value="0">'.JText::_("LNG_NO_BUSINESS").'</option>';
										foreach($companies as $company) {
											$html .= '<option value="'.$company->id.'">'.$company->name.'</option>';
										}
							$html .= '</select>';
						}

			$html .= '<p class="pb-0 mt-2 mb-0 font-weight-bold">'.JText::_('LNG_LEAVE_A_NOTE').'</p>
							<textarea class="form-control" name="additional_information" rows="10"></textarea>
						</div>
					</div>';

		return $html;
	}

	/**
	 * Creates the last thank you step
	 *
	 * @return string
	 *
	 * @since 5.3.0
	 */
	public static function getThankYouStep() {
		$html = '';

		$html .= '
				<div id="thankyou-step" class="text-center" style="display:none;">
					<i class="la la-check-circle text-success" style="font-size:120px;"></i>
					<p class="lead">' . JText::_('LNG_THANK_YOU') . '</p>
					<p class="muted">' . JText::_('LNG_REQUEST_QUOTE_THANKYOU_MESSAGE') . '</p>
					<a href="javascript:jbdQuoteRequest.closeModal()" class="btn btn-danger mt-5">' . JText::_('LNG_CLOSE') . '</a>
				</div>
		';

		return $html;
	}

	/**
	 * Gets the action buttons HTML for each step
	 *
	 * @param      $step int step count*
	 * @param int  $validate
	 * @param bool $last true if last step
	 *
	 * @return string
	 *
	 * @since 5.3.0
	 */
	public static function getActionButtons($step, $validate = self::VALIDATE_DEFAULT, $last = false) {
		$html = '';

		$html .= "<div class='action-container d-flex justify-content-between mt-5'>";

		if ($last) {
			$html .= "<a class='btn btn-info' href='javascript:jbdQuoteRequest.openStep(" . ($step - 1) . ", " . self::VALIDATE_SKIP . ")'>" . JText::_('LNG_BACK') . "</a>";
			$html .= "<a class='btn btn-success' href='javascript:jbdQuoteRequest.submitRequest()'>" . JText::_('LNG_SUBMIT') . "</a>";
		} else {
			if ($step == 0) {
				$html .= "<div></div>";
				$html .= "<a class='btn btn-success' href='javascript:jbdQuoteRequest.openStep(" . ($step + 1) . ", " . $validate . ")'>" . JText::_('LNG_CONTINUE') . "</a>";
			} else {
				$html .= "<a class='btn btn-info' href='javascript:jbdQuoteRequest.openStep(" . ($step - 1) . ", " . self::VALIDATE_SKIP . ")'>" . JText::_('LNG_BACK') . "</a>";
				$html .= "<a class='btn btn-success' href='javascript:jbdQuoteRequest.openStep(" . ($step + 1) . ", " . $validate . ")'>" . JText::_('LNG_NEXT') . "</a>";
			}
		}

		$html .= "</div>";

		return $html;
	}

	/**
	 * Renders the whole quote request process HTML with all the questions and additional steps.
	 *
	 * @param null  $categoryId int ID of the category
	 *
	 * @param array $options
	 *
	 * @return string HTML
	 * @throws Exception
	 *
	 * @since 5.3.0
	 */
	public static function renderQuoteRequest($categoryId = null, $options = array()) {
		if (empty($categoryId)) {
			throw new Exception(JText::_('LNG_INVALID_CATEGORY'));
		}

		$html = '';
		try {
			$questions = self::getQuestionsByCategory($categoryId);
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}

		$includeLocation = isset($options["includeLocation"]) ? $options["includeLocation"] : true;

		$html  .= '<div class="quotes-container">';
		$html  .= '<form id="quotes-form">';
		$html  .= '<input type="hidden" name="category_id" value="' . $categoryId . '">';
		$count = 0;

		// geolocation step
		if ($includeLocation) {
			$html .= "<div class='step-container step-active' id='step-$count'>";
			$html .=    self::getLocationStep();
			$html .=    self::getActionButtons($count, self::VALIDATE_LOCATION);
			$html .= "</div>";

			$count++;
		}

		// request quote questions
		foreach ($questions as $question) {
			if ($question->type != QUESTION_TYPE_INPUT) {
				$question = self::processQuestion($question);
				if (empty($question)) {
					continue;
				}
			}

			$display = $count == 0 ? '' : ' style="display:none;"';
			$active  = $count == 0 ? 'step-active' : '';

			$html .= "<div class='step-container $active' id='step-$count' $display >";

			if (!empty($question->image)) {
				$html .= '<div class="question-image" style="background-image: url(' . BD_PICTURES_PATH . $question->image . ') ">';
				$html .= '</div>';
			}

			$html .= "<div class='step-wrapper'>";
			$html .=    self::renderQuestion($question);
			if ($question->type == QUESTION_TYPE_INPUT) {
				$html .= self::getActionButtons($count, self::VALIDATE_INPUT);
			} else {
				$html .= self::getActionButtons($count);
			}
			$html .= "</div>";
			$html .= "</div>";
			$count++;
		}

		// user email step
		$user = JBusinessUtil::getUser();
		if ($user->ID == 0) {
			$html .= "<div class='step-container' id='step-$count' style='display:none;'>";
			$html .=    self::getEmailStep();
			$html .=    self::getActionButtons($count, self::VALIDATE_USER);
			$html .= "</div>";
			$count++;
		}

		// note step
		$html .= "<div class='step-container' id='step-$count' style='display:none;'>";
		$html .=    self::getNoteStep();
		$html .=    self::getActionButtons($count, self::VALIDATE_SKIP, true);
		$html .= "</div>";

		$html .= '</form>';
		$html .= '</div>';

		return $html;
	}


	public static function generateQuoteForEmail($quote) {
		$questionAnsers = json_decode($quote->summary);

		$text = "<table cellspacing='0' cellpadding='0'>
                    <tbody align='left' style='line-height: 1.6; font-size: 12px'>";
						if (!empty($quote->title)) {				
						$text .= "<tr>
                            <td width='auto'>
								<p style='margin:unset'>".JText::_('LNG_QUOTE_REQUEST_TITLE').": <span style='color:#ACACAC'>".$quote->title."</span></p>
                            </td>
                        </tr>";
						}
                        $text .= "<tr>
                            <td>
                                <p style='margin:unset'>".JText::_('LNG_CREATED_ON').": <span style='color:#ACACAC'>".JBusinessUtil::getDateGeneralShortFormat($quote->creationDate)."</span></p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p style='margin:unset'>".JText::_('LNG_AREA_SEARCHED').": <a target='_blank' href='". JBusinessUtil::getDirectionURL(array('latitude'=>$quote->latitude,'longitude'=>$quote->longitude), null, null, true)."'>$quote->location</a></p>
                            </td>
                        </tr>
                        <tr>
                            <td>
								<p style='margin:unset'>".JText::_('LNG_CATEGORY').": <span style='color:#ACACAC'>". $quote->category->name ."</span></p>
                            </td>
                        </tr>
                        <tr>
                            <td>
								<p style='margin:unset'>".JText::_('LNG_NR_LISTING_CONTACTED').": <span style='color:#ACACAC'>".(int)$quote->listings_contacted."</span></p>
                            </td>
                        </tr>";
						if (!empty($quote->additional_information)) {				
                        $text .= "<tr>
							<td>
								<p style='margin:unset'>".JText::_('LNG_ADDITIONAL_INFORMATION').": <span style='color:#ACACAC'>".$quote->additional_information."</span></p>
							</td>
						</tr>";
							}
                        $text .= "<tr>
                            <td>
                                <p style='margin-top: 20px; margin-bottom: 5px'>". JText::_('LNG_QUESTIONS_SUMMARY')."</p>";
		
								foreach ($questionAnsers as $option) {
			$answers = explode('#', $option->answer);
			unset($answers[0]);
			$text .="<p>" . $option->question . "</p>";
			$count = 1;
			foreach ($answers as $answer) {
				$text .="<p>". $count++ . " . " . $answer . "</p>";
			}
		}
		$text.=   			"</td>
						</tr>
					</tbody>
    			</table>";

		return $text;
	}
}
