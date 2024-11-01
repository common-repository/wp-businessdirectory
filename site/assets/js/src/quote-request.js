/**
 * JBD Quote Request javascript Class
 */
class JBDQuoteRequest{

    /**
     * Constructor
     *
     * @param categoryId int ID of the category
     */
    constructor(categoryId = null) {
    	this.response_success = 1;
    	this.response_error = 0;

    	this.validate_default = 1;
    	this.validate_location = 2;
    	this.validate_input = 3;
    	this.validate_user = 4;
    	this.validate_skip = 0;

        this.category_id = categoryId;
    }

    /**
     * Opens modal that will hold the quote request. Modal HTML must already be present on the page.
     */
    init(categoryId) {
    	this.category_id = categoryId;
    	this.modal = jQuery('#quote-request-modal');
        this.modal.jbdModal({
            clickClose: false
        });
        this.modal.find('.modal-container').html('');
        this.modal.find('#thankyou-step').hide();
        this.modal.find('#loading-quote-requests').find('.msg').text(JBD.JText._('LNG_RETRIEVING_QUESTIONS'));
        this.modal.find('#loading-quote-requests').show();
        this.retrieveQuestions();
    }

    /**
     * Retrieves the questions HTML and attaches it inside the container.
     * category_id must be set in order to retrieve the right questions.
     */
    retrieveQuestions() {
        let questionsUrl = jbdUtils.getAjaxUrl('getQuestions', 'requestquote');
        let self = this;
        let categoryId = this.category_id;

        jQuery("#quotes-form").validationEngine('attach');
        
        jQuery.ajax({
            type: "GET",
            url: questionsUrl,
            data: {categoryId: categoryId, cache: false},
            dataType: 'json',
            cache:false,
            success: function (data) {
                if (data.status == self.response_error) {
                    console.error(data);
                    alert(data.message);
                    self.closeModal(); // close Modal if error occurs
                } else if (data.status == self.response_success) {
                    self.modal.find('#loading-quote-requests').hide();
                    // attach questions HTML to modal container
                    self.modal.find('.modal-container').html(data.data);
                    self.initAutocomplete();
                }
            }
        });
    }

    /**
     * Shows the next step and hides current one. If validation fails, it does not continue to the next step.
     *
     * @param step int count of the step
     * @param validate int validation type
     * @returns {boolean}
     */
    openStep(step, validate) {
        if (typeof validate === 'undefined') {
            validate = this.validate_default;
        }

        if(jQuery('#login').val() == 1) {
            let stopStep = false;
            let message = '';
            let email = jQuery('#email').val();
            let password = jQuery('#password').val();
            let result = jbdUtils.getAjaxUrl('checkUserLoginCredentials', 'businessuser');

            jQuery.ajax({
                type: "GET",
                url: result,
                data: {
                    email, password
                },
                dataType: 'json',
                cache:false,
                async: false,
                success: function (data) {
                    console.log(data)
                    if (data.status == 0) {
                        stopStep = true;
                        message = data.message;
                    } else if(data.status == 2){
                        stopStep = true;
                        message = data.message;
                    }
                }
            });

            if(stopStep == true) {
                alert(message)
                return;
            }
        }
        
        if (validate != this.validate_skip && !this.validateStep(validate)) {
            alert(JBD.JText._('LNG_PLEASE_FILL_THE_DETAILS'));
            return ;
        }

        let oldStep = jQuery('.step-container.step-active');
        oldStep.hide();
        oldStep.removeClass('step-active');

        // if we are validating the location step, call also the searchListings
        if (validate == this.validate_location) {
            let loadingDiv = jQuery('#loading-quote-requests');
            let loadingMessage = loadingDiv.find('.msg');
            loadingMessage.text(JBD.JText._('LNG_SEARCHING_LISTINGS'));

            loadingDiv.show();

            this.searchListings(function () {
                let newStep = jQuery('#step-' + step);
                newStep.addClass('step-active');
                newStep.fadeIn();
            });
        } else {
            let newStep = jQuery('#step-' + step);
            newStep.addClass('step-active');
            newStep.fadeIn();
        }
    }

    /**
     * Validates a step. The validate param will determine what kind of validation will be made.
     * validate_default means radio/checkbox validation.
     *
     * @param validate
     * @returns {boolean}
     */
    validateStep(validate) {
        if (validate == this.validate_default) {
            let isChecked = false;
            jQuery('.step-active .options-container .option-row').each(function () {
                let checkbox = jQuery(this).find('.question-container').find('input');
                if (checkbox.prop('checked') !== false) {
                    isChecked = true;
                    return true;
                }
            });

            return isChecked;
        } else if (validate == this.validate_location) {
            return jQuery('.step-active').find('.zipcode-quote').val().length != 0;
        } else if (validate == this.validate_user) {
        	//console.debug("validate");
            return jQuery("#quotes-form").validationEngine('validate');
        } else if (validate == this.validate_input) {
            let isFilled = false;
            jQuery('.step-active .options-container .option-row').each(function () {
                let input = jQuery(this).find('input').val();
                if (input.trim() !== '') {
                    isFilled = true;
                    return true;
                }
            });
            return isFilled;
        }
    }

    /**
     * Initializes the autocomplete for the location autosuggestion during the location step.
     */
    initAutocomplete() {
        let element = document.getElementsByClassName('zipcode-quote')[0];
        jQuery(document).ready( function () {
            JBDMapHelper.initAutocomplete(element, function (place) {
                jQuery("#quote-latitude").val(place['latitude']);
                jQuery("#quote-longitude").val(place['longitude']);
            }, false, false);
        });
    }

    /**
     * Submits all the information gathered throughout the steps. If response is successful, it will
     * show the 'Thank you' step and call the contactListings function in the background.
     */
    submitRequest() {
        let data = jQuery('#quotes-form').serializeArray();
        let saveQuote = jbdUtils.getAjaxUrl('saveQuoteRequests', 'requestquote');
        let self = this;

        let loadingMessage = this.modal.find('#loading-quote-requests').find('.msg');
        let loadingDiv = this.modal.find('#loading-quote-requests');

        this.modal.find('.modal-container').html('');
        loadingMessage.text(JBD.JText._('LNG_SENDING_REQUESTS'));
        loadingDiv.show();

        jQuery.ajax({
            type: "POST",
            url: saveQuote,
            data: {data: data},
            dataType: 'json',
            cache:false,
            success: function (data) {
                if (data.status == self.response_error) {
                    self.closeModal();
                    console.error(data);
                    alert(data.message);
                } else if (data.status == self.response_success) {

                    if(data.data.contactListings == true){
                        self.contactListings(data.data.quoteId, data.data.companyIds);
                    }

                    loadingDiv.hide();
                    self.modal.find('#thankyou-step').fadeIn();
                }
            }
        });
    }

    /**
     * Searches if there are any listings present for the given location. If long and lat are not determined, it will
     * get the coordinates based on the location string.
     *
     * If no listings are found, it will fail. If successful, it will save the lat and long (in case they were not determined).
     *
     * @param callback gets called in case of success
     */
    searchListings(callback) {
        let searchListing = jbdUtils.getAjaxUrl('searchListingsAjax', 'requestquote');
        let self = this;

        let lat = jQuery('#quote-latitude').val();
        let long = jQuery('#quote-longitude').val();
        let locationString = jQuery('#quote-zipcode').val();

        jQuery.ajax({
            type: "GET",
            url: searchListing,
            data: {
                categoryId: self.category_id,
                latitude: lat,
                longitude: long,
                location: locationString
            },
            dataType: 'json',
            cache:false,
            success: function (data) {
                if (data.status == self.response_error) {
                    self.closeModal();
                    console.error(data);
                    let html = '<div class="jbd-container" style="display:none"><div class="jmodal-sm"><div class="jmodal-header"><p class="jmodal-header-title">' + data.data.title +'</p>'
        			+'<a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>  </div> <div class="jmodal-body"><p>' + data.message + '</p></div> </div></div>';

                    jQuery(html).appendTo('body').jbdModal();
                } else if (data.status == self.response_success) {
                    let loadingDiv = self.modal.find('#loading-quote-requests');
                    jQuery('#quote-latitude').val(data.data.latitude);
                    jQuery('#quote-longitude').val(data.data.longitude);
                    loadingDiv.hide();
                    callback();
                }
            }
        });
    }

    /**
     * Calls the contact listings endpoint in order to send emails to the listings.
     *
     * @param quoteId int ID of the request quote
     * @param companyIds string list of concatenated company ids
     */
    contactListings(quoteId, companyIds) {
        let contactListings = jbdUtils.getAjaxUrl('contactListingsAjax', 'requestquote');

        console.log(JBD.JText._('LNG_CONTACTING_LISTINGS'));
        jQuery.ajax({
            type: "GET",
            url: contactListings,
            data: {
                quoteId: quoteId,
                companyIds: companyIds
            },
            dataType: 'json',
            cache:false,
            success: function (data) {
                console.log(data);
            }
        });
    }

    quoteApply(quoteId, companyId, price = null, message = null){
        this.contactListings(quoteId, companyId);
        let applyUrl = jbdUtils.getAjaxUrl('replyToQuoteAjax', 'managelistingrequestquotes');
        
        console.log(JBD.JText._('LNG_CONTACTING_LISTINGS'));
        jQuery.ajax({
            type: "GET",
            url: applyUrl,
            data: {
                quote_id: quoteId,
                company_id: companyId,
                price: price,
                message: message
            },
            dataType: 'json',
            cache:false,
            success: function (data) {
                console.log(message);
                if(price!=null){
                    jQuery.jbdModal.close();
                }
                jQuery("#apply-"+quoteId).html('<i class="la la-check-circle la-2x jbd-green mr-2"></i>');
            }
        });
    }

    /**
     * Closes the modal
     */
    closeModal() {
        this.modal.find('#thankyou-step').hide();
        this.modal.find('#loading-quote-requests').hide();
        jQuery.jbdModal.close();
    }

    toggleLogIn(){
            jQuery('#nameDiv').toggle();
            jQuery('#name, #password, #email').val('');
            
            if(jQuery('#login').val() == 1) {
                jQuery('#loginDiv').html(JBD.JText._('LNG_ALREADY_HAVE_ACCOUNT') + " " +'<a href="javascript:jbdQuoteRequest.toggleLogIn()">' + JBD.JText._('LNG_LOG_IN') + '</a>');
                jQuery('#description').html(JBD.JText._('LNG_ENTER_USER_DETAILS'));
                jQuery('#login').val(0);
            } else {
                jQuery('#loginDiv').html(JBD.JText._('LNG_NOT_MEMBER') + " " +'<a href="javascript:jbdQuoteRequest.toggleLogIn()">' +JBD.JText._('LNG_REGISTER_HERE') + '</a>');
                jQuery('#description').html(JBD.JText._('LNG_YOU_HAVE_TO_BE_LOGGED_IN'));
                jQuery('#login').val(1);
            }
    }
}

let jbdQuoteRequest = new JBDQuoteRequest();