/**
 *  Utility class for batch processing
 */
class JBDBatch {

    /**
     * Constructor
     */
    constructor() {
        this.controller = "";
        this.incremental = 0;
        this.resume = 0;
        this.batchSize = 200;
        this.offset = 0;
        this.progress = 0;
        this.reload = false;
        this.totalItems = 0;

    }

    /**
     * Initialize the index process
     */
    initialize(controller, task, incremental) {
        this.controller = controller;
        this.incremental = incremental;
        this.offset = 0;
        this.progress = 0;
        this.runTask(task);
    }

    /**
     * Call the Indexer
     * 
     * @param {
     * } task 
     */
    runTask(task) {
        let baseUrl = jbdUtils.getAjaxUrl(task, this.controller);
        baseUrl = baseUrl + '&' + jQuery('#jbd-indexer-token').attr('name') + '=1';
        let self = this;

        jQuery.ajax({
            type: "GET",
            url: baseUrl,
            data: { incremental: this.incremental, offset: self.offset },
            success: self.processResponse,
            error: self.processFailure,
            cache: false
        });
    }

    /**
     * Process indexere reponse
     * 
     * @param {*} data 
     * @param {*} resp 
     */
    processResponse(data, resp) {
        try {
            if (data === null) {
                throw resp;
            }
            if (data.error) {
                throw data;
            }
            if (data.start) {
                jbdBatch.totalItems = data.totalItems;
            }

            jbdBatch.offset += data.batchOffset;
            jbdBatch.updateProgress(data.header, data.message);
            if (jbdBatch.offset < jbdBatch.totalItems) {
                jbdBatch.runTask('processBatch');
            } else if (!jbdBatch.reload) {
                jbdBatch.reload = true;
                setTimeout(function() { 
                    //location.reload(); 
                }, 3000);
            }
        } catch (error) {
            jQuery('#progress').remove();

            if (data.error) {
                jQuery('#jbd-progress-title').text(data.header).addClass('finder-error');
                jQuery('#jbd-progress-message').html(data.message).addClass('finder-error');
            }

            return false;
        }

        return true;
    };

    /**
     * Handle the failure cases
     * 
     * @param xhr 
     */
    processFailure(xhr) {
        let data = (typeof xhr == 'object' && xhr.responseText) ? xhr.responseText : null;
        data = data ? jQuery.parseJSON(data) : null;
        jQuery('#progress').remove();
        if (data) {
            data = data.responseText != null ? Json.evaluate(data.responseText, true) : data;
        }
        var header = data ? data.header : JBD.JText._('LNG_AN_ERROR_HAS_OCCURED');
        var message = data ? data.message : JBD.JText._('LNG_MESSAGE_RETRIEVED') + ' <br />' + data;
        jQuery('#jbd-progress-title').text(header).addClass('finder-error');
        jQuery('#jbd-progress-message').html(message).addClass('finder-error');
    };


    /**
     * Update the progress of the index process
     * 
     * @param {*} header 
     * @param {*} message 
     */
    updateProgress(header, message) {

        let progress = (jbdBatch.offset / jbdBatch.totalItems) * 100;

        jQuery('#jbd-progress-title').text(header);
        jQuery('#jbd-progress-message').html(message);
        if (progress < 100) {
            jQuery('#progress-bar').css('width', progress + '%').attr('aria-valuenow', progress);
        } else {
            jQuery('#progress-bar').removeClass('bar-success').addClass('bar-warning').attr('aria-valuemin', 100).attr('aria-valuemax', 200);
            jQuery('#progress-bar').css('width', progress + '%').attr('aria-valuenow', progress);
        }
        if (message == '') {
            jQuery('#progress').remove();
        }
    };
};

let jbdBatch = new JBDBatch();