/**
 * JBD Quote Message javascript Class
 */
class JBDQuoteMessages{

    /**
     * Constructor
     */
    constructor() {
        this.response_success = 1;
        this.response_error = 0;

        this.allowSend = false;
        this.messages = [];
        this.lastMsgId = 0;
        this.firstMsgId = 0;

        this.pollingInterval = JBDConstants.QUOTE_MESSAGES_POLLING_INTERVAL;
    }

    /**
     * Initializes chat. Chat HTML must already be present on the page.
     *
     *  @param replyId int ID of the quote reply
     * @param userId int optional ID of the user that is initializing the chat
     * @param options array of options (optional)
     *        [
     *          allowSend => if false, will disable possibility to send message
     *        ]
     */
    init(replyId, userId = null, options = null) {
        this.replyId = replyId;
        this.userId = userId;

        if (options != null) {
            if (typeof options['allowSend'] !== 'undefined') {
                this.allowSend = options['allowSend'];
            }
        }

        this.chatContainer = jQuery('#chat-container-' + this.replyId);
        this.chatFooter = this.chatContainer.find('.chat-footer');
        this.chatInput = this.chatContainer.find('.chat-textbox');
        this.chatSendBtn = this.chatContainer.find('.btn-send');
        this.chatBody = this.chatContainer.find('.chat-body');
        this.chatLoading = this.chatBody.find('.loading-quote-messages');

        let self = this;

        if (this.allowSend) {
            // send message if enter key is pressed
            this.chatInput.on("keydown", function (event) {
                if (event.which == 13) {
                    self.sendMessage();
                }
            });
            // send message if send button is clicked
            this.chatSendBtn.on("click", function (event) {
                self.sendMessage();
            });

        } else {
            this.chatFooter.attr('style', 'display:none !important');
        }

        // if user scrolls on top of chat body, load previous messages
        this.chatBody.on('scroll', function () {
            if (jQuery(this).scrollTop() == 0) {
                self.loadHistory();
            }
        });

        // show loading icon while retrieving messages
        this.chatLoading.show();
        this.chatBody.hide();

        this.getMessages();

        // hide loading icon when messages are retrieved
        this.chatLoading.hide();
        this.chatBody.show();

        // set polling interval for retrieving new messages
        this.interval = setInterval(function () {
            self.pollMessages();
        }, self.pollingInterval);
    }

    /**
     * Retrieves messages for a quote reply ID.
     */
    getMessages() {
        let self = this;
        let getMessages = jbdUtils.getAjaxUrl('getMessagesAjax', 'requestquotemessages');

        jQuery.ajax({
            type: "POST",
            url: getMessages,
            data: {
                replyId: self.replyId
            },
            dataType: 'json',
            cache:false,
            success: function (data) {
                if (data.status == self.response_success) {
                    self.messages = data.data;
                    self.chatInput.val('');
                    self.renderMessages(true);

                    self.chatLoading.hide();
                    self.chatBody.show();
                } else {
                    console.error(data);
                    alert(data.message);
                }
            }
        });
    }

    /**
     * Renders a list of messages on the screen.
     *
     * @param scrollDown boolean if true, it will scroll down to the end of the screen once messages are rendered.
     * @param messages if not defined, messages will be retrieved at this.messages of the instance
     */
    renderMessages(scrollDown, messages) {
        if (typeof messages === 'undefined') {
            messages = this.messages;
        }

        if (typeof scrollDown === 'undefined') {
            scrollDown = false;
        }

        for (let i in messages) {
            let msg = messages[i];

            // is this message already rendered on screen? If so, don't render it again.
            if (typeof msg !== 'undefined' && jQuery('#msg-' + msg.msgId).length === 0 && messages.hasOwnProperty(i)) {
                let html = this.getMessageHtml(msg);

                msg.msgId = parseInt(msg.msgId);
                if (this.lastMsgId != 0) {
                    // if message is a new one, append it at the bottom of the screen
                    if (msg.msgId > this.lastMsgId) {
                        this.chatBody.append(html);
                    }
                    // if message is an old one (from history), append it at the top of the screen
                    else {
                        this.chatBody.find('.chat-top').after(html);
                    }
                }
                // if lastId is 0, it means that this is the first message to be rendered. Append it on the bottom
                else {
                    this.chatBody.append(html);
                }

                // keep track of the last message on screen
                if (this.lastMsgId < msg.msgId) {
                    this.lastMsgId = msg.msgId;
                }

                // keep track of the first message on screen
                if (this.firstMsgId === 0) {
                    this.firstMsgId = msg.msgId;
                } else if (this.firstMsgId > msg.msgId) {
                    this.firstMsgId = msg.msgId;
                }
            }
        }

        // scroll down at the bottom
        if (scrollDown) {
            if (typeof this.chatBody[0] !== 'undefined') {
                this.chatBody.scrollTop(this.chatBody[0].scrollHeight);
            }
        }
    }

    /**
     * Creates the HTML for a single message bubble.
     *
     * @param msg object
     * @returns {string}
     */
    getMessageHtml(msg) {
        let html = '';
        let msgClass = 'msg-rcvd';

        // if sender is not defined, just pick a user at random to be as sender
        if (this.userId == null) {
            this.userId = msg.senderId;
        }

        if (msg.senderId == this.userId) {
            msgClass = 'msg-snt';
        }

        let date = new Date(msg.created);
        html += '<div class="msg" id="msg-' + msg.msgId + '">';
        html += '<div class="' + msgClass + '">';
        html += '<div class="msg-txt">';
        html += msg.text;
        html += '</div>';
        html += '<p class="msg-date">' + date.toLocaleString() + '</p>';
        html += '</div>';
        html += '</div>';

        return html;
    }

    /**
     * Sends a single message.
     *
     * @returns {boolean}
     */
    sendMessage() {
        let msg = {};
        let text = this.chatInput.val();
        if (text.length == 0 || jbdUtils.isBlank(text)) {
            return false;
        }

        let self = this;
        let sendMessage = jbdUtils.getAjaxUrl('sendMessageAjax', 'requestquotemessages');

        jQuery.ajax({
            type: "POST",
            url: sendMessage,
            data: {
                replyId: self.replyId,
                senderId: self.userId,
                text: text
            },
            dataType: 'json',
            cache:false,
            success: function (data) {
                if (data.status == self.response_success) {
                    msg = data.data;
                    self.messages.unshift(msg); // add new message to list of messages
                    self.chatInput.val(''); // reset chat textbox after sending
                    self.renderMessages(true);
                } else {
                    console.error(data);
                    alert(data.message);
                }
            }
        });
    }

    /**
     * Loads history of chat by retrieving all messages prior to this.firstId (ID of the first message on screen).
     */
    loadHistory() {
        let messages = [];
        let getMessages = jbdUtils.getAjaxUrl('getMessagesAjax', 'requestquotemessages');
        let self = this;

        self.chatLoading.show();
        jQuery.ajax({
            type: "POST",
            url: getMessages,
            data: {
                replyId: self.replyId,
                firstId: self.firstMsgId
            },
            dataType: 'json',
            cache:false,
            success: function (data) {
                if (data.status == self.response_success) {
                    messages = data.data;
                    self.renderMessages(false, messages);

                    self.chatLoading.hide();
                } else {
                    console.error(data);
                    alert(data.message);
                }
            }
        });
    }

    /**
     * Function that calls endpoint to see if new messages are available. If so, renders them on screen.
     */
    pollMessages() {
        let self = this;

        let getMessages = jbdUtils.getAjaxUrl('getMessagesAjax', 'requestquotemessages');

        jQuery.ajax({
            type: "POST",
            url: getMessages,
            data: {
                replyId: self.replyId,
                lastId: self.lastMsgId
            },
            dataType: 'json',
            cache:false,
            success: function (data) {
                if (data.status == self.response_success) {
                    if (data.data.length > 0) {
                        self.messages = data.data;
                        self.renderMessages(true);
                    }
                } else {
                    console.error(data);
                    alert(data.message);
                }
            }
        });
    }

    /**
     * Opens the chat modal. Chat modal HTML must be present on the page.
     *
     * If chat is initialized through modal, chat body HTML is retrieved by call to endpoint.
     *
     * @param replyId int ID of the reply quote
     * @param userId int ID of the sender
     * @param options array of options
     *        [
     *          allowSend => if false, will disable possibility to send message
     *        ]
     *
     */
    openModal(replyId, userId = null, options = null) {
        let modal = jQuery('#quote-request-messages-modal');

        modal.jbdModal();

        let self = this;

        //retrieve the HTML for the chat body
        let getChat = jbdUtils.getAjaxUrl('getChatHtmlAjax', 'requestquotemessages');

        jQuery.ajax({
            type: "POST",
            url: getChat,
            data: {
                replyId: replyId
            },
            dataType: 'json',
            cache:false,
            success: function (data) {
                if (data.status == self.response_success) {
                    modal.find('.jmodal-body').html(data.data);
                    self.init(replyId, userId, options);
                } else {
                    console.error(data);
                    alert(data.message);
                }
            }
        });

        // if modal is closed, clear instance
        modal.on(jQuery.jbdModal.BEFORE_CLOSE, function () {
            self.clearInstance();
        });
    }

    /**
     * Resets the current active chat instance.
     */
    clearInstance() {
        this.replyId = null;
        this.userId = null;
        this.allowSend = false;
        this.messages = [];
        this.lastMsgId = 0;
        this.firstMsgId = 0;

        this.chatContainer = null;
        this.chatInput = null;
        this.chatSendBtn = null;
        this.chatLoading = null;
        this.chatBody = null;
        this.chatFooter = null;

        clearInterval(this.interval);

        this.interval = null;

        jQuery('#quote-request-messages-modal').find('.modal-body').html('')
    }
}

let jbdQuoteMessages = new JBDQuoteMessages();