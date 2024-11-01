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

trait QuoteMessagesHelper {
	/**
	 * Renders the chat HTML for a certain quote reply. If senderID is set, it will render the chat along with the
	 * possibility to send messages (front end). Otherwise it will disable the send message option. (back-end)
	 *
	 * @param      $replyId  int ID of the quote reply
	 * @param null $senderId int ID of the sender
	 *
	 * @since 5.3.0
	 */
	public static function renderChat($replyId, $senderId = null) {
		$html = '';

		$script = '		    
			window.addEventListener("load",function() {
			    jbdQuoteMessages.init($replyId);
			});
		';

		if (!empty($senderId)) {
			$script = '		    
			window.addEventListener("load",function() {
			        jbdQuoteMessages.init($replyId, $senderId, true);
			});
		';
		}

		$html .= self::getChatHtml($replyId);
		$html .= "<script>$script</script";

		echo $html;
	}

	/**
	 * Generates and returns the chat HTMl for a certain quote reply.
	 *
	 * @param $replyId int ID of the quote reply
	 *
	 * @return string
	 *
	 * @since 5.3.0
	 */
	public static function getChatHtml($replyId) {
		$html = '';

		$html .= "
		
		<div class='chat-container' id='chat-container-$replyId'>
				
		    <div class='chat-body'>
		    
			     <div class='chat-top'>
					<div class='loading-quote-messages' style='display:none;'>
						<img class='spinner' />
					</div>
				</div>
		
		    </div>
		
		    <div class='chat-footer d-flex justify-content-between'>
		        <input class='chat-textbox' type='text' placeholder='" . JText::_('LNG_TYPE_MESSAGE') . "' />
		        <a class='btn-send' href='javascript:void(0)'><i class='la la-paper-plane'></i></a>
		
		    </div>
		</div>
		";

		return $html;
	}

	/**
	 * Creates a single message record.
	 *
	 * @param $senderId int ID of the message sender
	 * @param $replyId  int ID of the quote reply
	 * @param $text     string text of the message
	 *
	 * @return mixed
	 * @throws Exception
	 *
	 * @since 5.3.0
	 */
	public static function createMessage($senderId, $replyId, $text) {
		$table = JTable::getInstance('RequestQuoteMessage', 'JTable', array());

		$data                   = array();
		$data["id"]             = 0;
		$data["quote_reply_id"] = $replyId;
		$data["sender_id"]      = $senderId;
		$data["text"]           = $text;

		$error = null;

		// Bind the data.
		if (!$table->bind($data)) {
			$error = $table->getError();
		}

		// Check the data.
		if (!$table->check()) {
			$error = $table->getError();
		}

		// Store the data.
		if (!$table->store()) {
			$error = $table->getError();
		}

		if (!empty($error)) {
			throw new Exception($error);
		}

		return $table->id;
	}

	/**
	 * Retrieves messages for a quote reply.
	 * If lastID is set, will retrieve all messages after this ID.
	 * If firstID is set, it will retrieve all messages before this ID.
	 *
	 * @param      $replyId int ID of the quote reply
	 * @param null $lastId  int ID of the last message to retrieve from
	 * @param null $firstId int ID of the first message to retrieve from
	 *
	 * @return mixed
	 * @throws Exception
	 *
	 * @since 5.3.0
	 */
	public static function getMessages($replyId, $lastId = null, $firstId = null) {
		try {
			$table    = JTable::getInstance('RequestQuoteMessage', 'JTable', array());
			$messages = $table->getMessages($replyId, $lastId, $firstId);

			if(!empty($messages)){
				$user = JBusinessUtil::getUser();
				$ids = implode(', ', array_map(function ($c) {
					return $c->msgId;
			    }, $messages));
   
				$app = JFactory::getApplication();
			   	if (!$app->isClient('administrator')) {
					$table->changeMessageStatus($ids, $user->ID);
				}
			   
			}

		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}

		return $messages;
	}

	/**
	 * Retrieves single message by message ID
	 *
	 * @param $msgId int ID of the message
	 *
	 * @return mixed
	 * @throws Exception
	 *
	 * @since 5.3.0
	 */
	public static function getMessage($msgId) {
		try {
			$table    = JTable::getInstance('RequestQuoteMessage', 'JTable', array());
			$msg = $table->getMessage($msgId);
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}

		return $msg;
	}

	/**
	 * Retrieves the HTML for the quote request modal. Hidden by default.
	 *
	 * @return string HTML
	 *
	 * @since 5.3.0
	 */
	public static function getMessagesModalHtml() {
		$html = '';

		$html .= "<div class='jbd-container quote-request' id='quote-request-messages-modal' style='display:none'>";
		$html .=    "<div class='jmodal-sm'>";
		$html .=    	'<div class="jmodal-header">
							<p class="jmodal-header-title">'.JText::_('LNG_MESSAGES').'</p>
							<a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
						</div>';
		$html .=        "<div class='jmodal-body'>";
		$html .=        "</div>";
		$html .=    "</div>";
		$html .= "</div>";

		return $html;
	}
}
