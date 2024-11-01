<?php
/**
 * @package    JBusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

trait QuoteMessagesAjax {
	public static $responseSuccess = 1;
	public static $responseError = 0;

	/**
	 * Ajax function for sending a single message in the quote messages chat.
	 *
	 * Returns new message object
	 *
	 * @since 5.3.0
	 */
	public function sendMessageAjax() {
		$app = JFactory::getApplication();

		$senderId = $app->input->get('senderId');
		$replyId  = $app->input->get('replyId');
		$text     = $app->input->get('text', '', 'RAW');

        $response          = new stdClass();
		$response->data    = null;
        $response->status  = self::$responseSuccess;
		$response->message = null;

		$msgId = null;
		try {
			$msgId          = QuoteService::createMessage($senderId, $replyId, $text);
			$msg            = QuoteService::getMessage($msgId);
			$response->data = $msg;

			$quoteMessageTable = JTable::getInstance("RequestQuoteMessage");
			$messages = $quoteMessageTable->getLastMessages($replyId);
			
			if(count($messages)==1){
				$quoteTable = JTable::getInstance("RequestQuote");
				$quote = $quoteTable->getQuoteRequest($replyId);
				
				$categoryTable = JTable::getInstance("Category", "JBusinessTable");
				$quote->categoryInfo = $categoryTable->getCategoryById($quote->category_id);
				
				$message = $messages[0];
				$sendToEndUser = false;
				if($message->sender_id == $quote->company_user_id){
					$sendToEndUser = true;
				}
				
				EmailService::sendQuoteRequestReplyEmail($quote, $sendToEndUser);
			}
		} catch (Exception $e) {
            $response->status     = self::$responseError;
			$response->message    = $e->getMessage();
		}

        JBusinessUtil::sendJsonResponse($response->data, $response->status, $response->message);
	}

	/**
	 * Ajax function for retrieving quote request messages based on the quote reply ID.
	 * If firstId is set, it will retrieve all messages previous to the ID.
	 * If lastId is set, it will retrieve all messages after the ID.
	 *
	 * Returns list of messages
	 *
	 * @since 5.3.0
	 */
	public function getMessagesAjax() {
		$app = JFactory::getApplication();

		$replyId = $app->input->get('replyId');
		$firstId = $app->input->get('firstId', null);
		$lastId  = $app->input->get('lastId', null);

        $response          = new stdClass();
        $response->data    = null;
        $response->status  = self::$responseSuccess;
        $response->message = null;

        try {
            $messages         = QuoteService::getMessages($replyId, $lastId, $firstId);
            $response->data   = $messages;
        } catch (Exception $e) {
            $response->status  = self::$responseError;
            $response->message = $e->getMessage();
        }

        JBusinessUtil::sendJsonResponse($response->data, $response->status, $response->message);
	}

	/**
	 * Get's the chat HTML and returns it as JSON.
	 *
	 * @since 5.3.0
	 */
	public function getChatHtmlAjax() {
		$app = JFactory::getApplication();

		$replyId = $app->input->get('replyId');

		$html = QuoteService::getChatHtml($replyId);

        $response          = new stdClass();
        $response->data    = $html;
        $response->status  = self::$responseSuccess;
        $response->message = null;

        JBusinessUtil::sendJsonResponse($response->data, $response->status, $response->message);
	}

}
