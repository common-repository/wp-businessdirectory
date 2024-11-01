/**
 * JBD Review javascript class
 */
class JBDReviews {

    /**
     * Validate form and Save Review based on the form ID passed to the function
     * @param formId
     */
    saveReview(formId) {
        var form_id = '#' + formId;
        jQuery(form_id).validationEngine({
            validateNonVisibleFields: true,
            updatePromptsPosition:true,
            ignore: ""
        });
        var isError = jQuery(form_id).validationEngine('validate');
        if (!isError) {
            return;
        }

        document.getElementById(formId).submit();
    }

    /**
     * Cancel add review and hide the new review section
     */
    cancelSubmitReview() {
        jQuery("#add-review").slideUp(500);
    }

    /**
     * If user is required to login and he is not logged in then show login Notice otherwise open the review section
     * and slide down to it
     *
     * @param requiresLogin boolean true or false if it is needed to be logged in to let a review
     * @param companyView boolean true or false if it is on company view
     */
    addNewReview(requiresLogin, companyView) {
        companyView = typeof companyView !== 'undefined' ? companyView : true;
        if (requiresLogin) {
            jbdUtils.showLoginNotice();
        } else {
            if (companyView == true) {
                jbdListings.showDetails('company-reviews');
            }
            window.location.hash = '#reviews';
            jQuery("#add-review").slideDown(500);
            jQuery('html, body').animate({
                scrollTop: (jQuery('#add-review').offset().top + jQuery('#add-review').height()/2)
            }, 1000);
        }
    }

    /**
     * If user is required to login and he is not logged in then show login Notice otherwise open the review section
     * and slide down to it
     * This function is done for tabs form view
     *
     * @param requiresLogin boolean true or false if it is needed to be logged in to let a review
     */
    addNewReviewOnTabs(requiresLogin) {
        if (requiresLogin) {
            jbdUtils.showLoginNotice();
        } else {
            jbdListings.showDirTab("#tabs-3");
            jQuery("#add-review").slideDown(500);
            jbdListings.showTabContent("company-reviews");
            window.location.hash = '#add-review';
        }
    }

    /**
     * Report review abuse
     *
     * @param requiresLogin boolean if true check if it is needed that user should be logged in to let a review abuse
     * @param reviewId int review ID
     */
    reportReviewAbuse(requiresLogin, reviewId) {
        if (requiresLogin) {
            jbdUtils.showLoginNotice();
        } else {
            var form = document.getElementById('reportAbuse');
            form.elements["reviewId"].value = reviewId;
            jQuery('#report-abuse').jbdModal();
        }
    }

    /**
     * Respond to a review
     *
     * @param requiresLogin boolean if true check if it is needed that user should be logged in to let a review response
     * @param reviewId int review ID
     */
    respondToReview(requiresLogin, reviewId) {
        if (requiresLogin) {
            jbdUtils.showLoginNotice();
        } else {
            var form = document.reviewResponseFrm;
            form.elements["reviewId"].value = reviewId;
            jQuery('#new-review-response').jbdModal();
        }
    }

    /**
     * Validates review abuse and submit the form
     */
    saveReviewAbuse() {
        if (!this.validateReportAbuseForm()) {
            return;
        }

        var form = document.reportAbuse;
        form.submit();
    }

    /**
     * Validates review response and submit the form
     */
    saveReviewResponse() {
        if (!this.validateReviewResponseForm()) {
            return;
        }
        var form = document.reviewResponseFrm;
        form.submit();
    }

    /**
     * Close the Modal
     */
    closeDialog() {
        jQuery.jbdModal.close();
    }

    /**
     * When Like is clicked, increase the listing review like count statistic
     *
     * @param reviewId int review ID
     */
    increaseReviewLikeCount(reviewId) {
        var postParameters = '';
        postParameters += "&reviewId=" + reviewId;
        var postData = '&task=companies.increaseReviewLikeCountAjax&view=companies' + postParameters;
        jQuery.post(jbdUtils.getProperty('baseUrl'), postData, this.processIncreaseLikeResult);
    }

    /**
     * When Like is clicked, increase the offer review like count statistic
     *
     * @param reviewId int review ID
     */
    increaseOfferReviewLikeCount(reviewId) {
        var postParameters = '';
        postParameters += "&reviewId=" + reviewId;
        var postData = '&task=offer.increaseReviewLikeCountAjax&view=offers' + postParameters;
        jQuery.post(jbdUtils.getProperty('baseUrl'), postData, this.processIncreaseLikeResult);
    }

    /**
     * Handle Review Like Count Response of the function increaseReviewLikeCount and increaseOfferReviewLikeCount
     *
     * @param response object response
     */
    processIncreaseLikeResult(response) {
        var xml = response;
        //alert(xml);
        //jQuery('#frmFacilitiesFormSubmitWait').hide();
        jQuery(xml).find('answer').each(function () {
            if (jQuery(this).attr('result') == true) {
                jQuery("#like" + jQuery(this).attr('reviewId')).text(parseInt(jQuery("#like" + jQuery(this).attr('reviewId')).text()) + 1);
                jbdReviews.saveCookieLikeId(jQuery(this).attr('reviewId'));
                jQuery("#like" + jQuery(this).attr('reviewId')).parent().parent().children().attr('onclick', '');
                jQuery("#like" + jQuery(this).attr('reviewId')).parent().parent().addClass('reduceOpacity');

            } else {
                //alert('notsaved');
            }
        });
    }


     /**
     * Save Like Cookie
     *
     * @param reviewId int review ID
     */
    saveCookieLikeId(reviewId) {
        var ids = jbdUtils.getCookie("likeIds");
        if (typeof ids === 'undefined') {
            ids = '';
        }
        ids += ',' + reviewId;
        jbdUtils.setCookie("likeIds", ids, 60);
        //alert(ids);
    }


    /**
     * When Love is clicked, increase the listing review love count statistic
     *
     * @param reviewId int review ID
     */
    increaseReviewLoveCount(reviewId) {
        var postParameters = '';
        postParameters += "&reviewId=" + reviewId;
        var postData = '&task=companies.increaseReviewLoveCountAjax&view=companies' + postParameters;
        jQuery.post(jbdUtils.getProperty('baseUrl'), postData, this.processIncreaseLoveResult);
    }
    
    /**
     * Handle Review Love Count Response of the function increaseReviewLoveCount and increaseOfferReviewLoveCount
     *
     * @param response object response
     */
    processIncreaseLoveResult(response) {
        var xml = response;
        //alert(xml);
        //jQuery('#frmFacilitiesFormSubmitWait').hide();
        jQuery(xml).find('answer').each(function () {
            if (jQuery(this).attr('result') == true) {
                jQuery("#love" + jQuery(this).attr('reviewId')).text(parseInt(jQuery("#love" + jQuery(this).attr('reviewId')).text()) + 1);
                jbdReviews.saveCookieLoveId(jQuery(this).attr('reviewId'));
                jQuery("#love" + jQuery(this).attr('reviewId')).parent().parent().children().attr('onclick', '');
                jQuery("#love" + jQuery(this).attr('reviewId')).parent().parent().addClass('reduceOpacity');

            } else {
                //alert('notsaved');
            }
        });
    }



    /**
     * Save Like Cookie
     *
     * @param reviewId int review ID
     */
    saveCookieLoveId(reviewId) {
        var ids = jbdUtils.getCookie("loveIds");
        if (typeof ids === 'undefined') {
            ids = '';
        }
        ids += ',' + reviewId;
        jbdUtils.setCookie("loveIds", ids, 60);
        //alert(ids);
    }

    /**
     * Increase Listing Review Dislike Count
     *
     * @param reviewId int review ID
     */
    increaseReviewDislikeCount(reviewId) {
        var postParameters = '';
        postParameters += "&reviewId=" + reviewId;
        var postData = '&task=companies.increaseReviewDislikeCountAjax&view=companies' + postParameters;
        jQuery.post(jbdUtils.getProperty('baseUrl'), postData, this.processIncreaseDislikeResult);
    }

    /**
     * Increase Offer Review Dislike Count
     *
     * @param reviewId int review ID
     */
    increaseOfferReviewDislikeCount(reviewId) {
        var postParameters = '';
        postParameters += "&reviewId=" + reviewId;
        var postData = '&task=offer.increaseReviewDislikeCountAjax&view=offers' + postParameters;
        jQuery.post(jbdUtils.getProperty('baseUrl'), postData, this.processIncreaseDislikeResult);
    }

    /**
     * Process increaseOfferReviewDislikeCount response
     *
     * @param response object response
     */
    processIncreaseDislikeResult(response) {
        var xml = response;
        //alert(xml);
        //jQuery('#frmFacilitiesFormSubmitWait').hide();
        jQuery(xml).find('answer').each(function () {
            if (jQuery(this).attr('result') == true) {
                jQuery("#dislike" + jQuery(this).attr('reviewId')).text(parseInt(jQuery("#dislike" + jQuery(this).attr('reviewId')).text()) + 1);
                jbdReviews.saveCookieDislikeId(jQuery(this).attr('reviewId'));
                jQuery("#dislike" + jQuery(this).attr('reviewId')).parent().parent().children().attr('onclick', '');
                jQuery("#dislike" + jQuery(this).attr('reviewId')).parent().parent().addClass('reduceOpacity');
            } else {
                //alert('notsaved');
            }
        });
    }

    /**
     * Add the dislike cookie
     * @param reviewId int review ID
     */
    saveCookieDislikeId(reviewId) {
        var ids = jbdUtils.getCookie("dislikeIds");
        if (typeof ids === 'undefined') {
            ids = '';
        }

        ids += ',' + reviewId;
        jbdUtils.setCookie("dislikeIds", ids, 60);
    }

    /**
     * Check if the like has been added from the current computer
     */
    checkLikeStatus() {
        var ids = jbdUtils.getCookie("likeIds");
        if (typeof ids === 'undefined') {
            ids = '';
        }

        ids = ids.split(',');

        for (var i = 0; i < ids.length; i++) {
            jQuery("#like" + ids[i]).parent().parent().children('a:first-child').attr('onclick', '');
            jQuery("#like" + ids[i]).parent().parent().addClass('reduceOpacity');
        }
    }

    /**
     * Check if the like has been added from the current computer
     */
    checkLoveStatus() {
        var ids = jbdUtils.getCookie("loveIds");
        if (typeof ids === 'undefined') {
            ids = '';
        }

        ids = ids.split(',');

        for (var i = 0; i < ids.length; i++) {
            jQuery("#love" + ids[i]).parent().parent().children('a:first-child').attr('onclick', '');
            jQuery("#love" + ids[i]).parent().parent().addClass('reduceOpacity');
        }
    }

    /**
     * Check if the dislike has been added from the current computer
     */
    checkDislikeStatus() {
        var ids = jbdUtils.getCookie("dislikeIds");
        if (typeof ids === 'undefined') {
            ids = '';
        }

        ids = ids.split(',');
        for (var i = 0; i < ids.length; i++) {
            jQuery("#dislike" + ids[i]).parent().parent().children('a:first-child').attr('onclick', '');
            jQuery("#dislike" + ids[i]).parent().parent().addClass('reduceOpacity');
        }
    }

    /**
     * Validates Report Abuse form before submitting it
     *
     * @returns {boolean}
     */
    validateReportAbuseForm() {
        var form = document.reportAbuse;
        var isError = false;

        jQuery(".error_msg").each(function () {
            jQuery(this).hide();
        });

        if (!validateField(form.elements['email'], 'email', false, null)) {
            jQuery("#reportAbuse #frmEmail_error_msg").show();
            if (!isError) {
                jQuery("#reportAbuse #email").focus();
            }
            isError = true;
        }

        if (!validateField(form.elements['description'], 'string', false, null)) {
            jQuery("#reportAbuse #frmDescription_error_msg").show();
            if (!isError) {
                jQuery("#reportAbuse #frmDescription_error_msg").focus();
            }
            isError = true;
        }

        return !isError;
    }

    /**
     * Validate Review Response Form before submitting it
     *
     * @returns {boolean}
     */
    validateReviewResponseForm() {
        var form = document.reviewResponseFrm;
        var isError = false;

        jQuery(".error_msg").each(function () {
            jQuery(this).hide();
        });

        if (!validateField(form.elements['firstName'], 'string', false, null)) {
            jQuery("#reviewResponseFrm #frmFirstName_error_msg").show();
            if (!isError) {
                jQuery("#firstName").focus();
            }
            isError = true;
        }

        if (!validateField(form.elements['lastName'], 'string', false, null)) {
            jQuery(" #reviewResponseFrm #frmLastName_error_msg").show();
            if (!isError) {
                jQuery("#lastName").focus();
            }
            isError = true;
        }

        if (!validateField(form.elements['email'], 'email', false, null)) {
            jQuery("#reviewResponseFrm #frmEmail_error_msg").show();
            if (!isError) {
                jQuery("#email").focus();
            }
            isError = true;
        }

        if (!validateField(form.elements['response'], 'string', false, null)) {
            jQuery("#reviewResponseFrm #frmDescription_error_msg").show();
            if (!isError) {
                jQuery("#reviewResponseFrm #frmDescription_error_msg").focus();
            }
            isError = true;
        }

        return !isError;
    }

    /**
     * Delete Review for the item offer or listing based on the controller passed
     *
     * @param controller string controller name
     * @param id int Review ID
     */
    deleteReview(controller,id) {
        if (confirm(JBD.JText._('LNG_ARE_YOU_SURE_YOU_WANT_TO_DELETE'))) {
            jQuery("#id").val(id);
            jQuery("#task").val(controller+".delete");
            jQuery("#adminForm").submit();
        }
    }

    loadMoreReviews() {
        
        let moreReviewsUrl = jbdUtils.getAjaxUrl('getMoreReviewsAjax', 'companies');
        let start = jQuery('#start').val();
        let companyId = jQuery('#company').val();

        start = parseInt(start)

        jQuery.ajax({
            type: "GET",
            url: moreReviewsUrl,
            data: {start: start, companyId:companyId},
            dataType: 'json',
            cache:false,
            success: function (data) {
                jQuery("#reviews").append(data.data.reviews);
                jQuery('#start').val(start + data.data.reviewsCount)
                jbdListings.magnifyImages('gallery');
                if(!data.data.show_more){
                    jQuery("#load-more-btn").hide();
                }

                jbdReviews.checkLikeStatus();
                jbdReviews.checkDislikeStatus();
                jbdReviews.checkLoveStatus();
                jbdListings.renderReviewRating();
                jQuery('.review-questions').each(function(){
                    jbdListings.showReviewQuestions(jQuery(this).attr('id'));
                });
            }
        });
    }
}

let jbdReviews = new JBDReviews();

window.addEventListener('load', function () {
    jbdReviews.checkLikeStatus();
    jbdReviews.checkDislikeStatus();
    jbdReviews.checkLoveStatus();
});
	