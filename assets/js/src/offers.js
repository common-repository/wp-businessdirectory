/**
 * JBD Offers javascript class
 */
class JBDOffers {

    /**-- ADMNIN FUNCTIONS --**/

    /**
     * Add the dates to the form fields to save them on item save
     */
    saveDates() {
        let start_time = jQuery('#publish_start_time').val();
        let end_time = jQuery('#publish_end_time').val();

        if (start_time == '') {
            jQuery('#start_time').attr('value', '');
        }
        if (end_time == '') {
            jQuery('#end_time').attr('value', '');
        }
    }

    /**
     * Update the attributes on category selection. If linking of categories and attributes is enabled then the attributes
     * will be shown based on the category selection for the item
     *
     * @param categoryId int category id
     * @param offerId int offer id
     */
    updateAttributes(categoryId, offerId) {
        let attributesUrl = jbdUtils.getAjaxUrl('getAttributesAjax', 'offer', 'managecompanyoffer');

        jQuery.ajax({
            type: "GET",
            url: attributesUrl,
            data: {categoryId: categoryId, offerId: offerId},
            dataType: 'json',
            cache:false,
            success: function (data) {
                jQuery('#customFieldsContent').html(data);
                jQuery(".chosen-select").chosen({width: "95%", disable_search_threshold: 5, search_contains: true,  placeholder_text_single: JBD.JText._('LNG_SELECT_OPTION'), placeholder_text_multiple: JBD.JText._('LNG_SELECT_OPTION')});
            }
        });
    }

    /**
     * Update the offer selling options on category selection. If linking of categories and attributes is enabled then the options
     * will be shown based on the category selection for the item
     *
     * @param categoryId int category id
     * @param offerId int offer id
     */
    updateSellingOptionsAjax(categoryId, offerId){
        let attributesUrl = jbdUtils.getAjaxUrl('getSellingOptionsAjax', 'offer', 'managecompanyoffer');

        jQuery.ajax({
            type: "GET",
            url: attributesUrl,
            data: {categoryId: categoryId, offerId: offerId},
            dataType: 'json',
            cache:false,
            success: function (data) {
                if (data == null) {
                    jQuery('#defaultQuantity').show();
                    jQuery('#noAttributesExplain').show();
                    jQuery('#customOptionsContent').hide();
                } else {
                    jQuery('#customOptionsContent').show();
                    jQuery('#noAttributesExplain').hide();
                    jQuery('#defaultQuantity').hide();
                }
                jQuery('#customOptionsContent').html(data);
            }
        });
    }

    updateQuantity(currentValue, offerId, $mainCatId) {
        let url = jbdUtils.getAjaxUrl('updateQuantityAjax', 'offer', 'offer');

        let attrVal = {};
        let newValue = jQuery('.jbtn-order-info').find(':input').first().val();
        let oldVal =  jQuery('#firstValue').val();
        jQuery('.jbtn-order-info').find(':input').each(function () {
            if (this.type == 'select-one' && this.name != 'quantity') {
                attrVal[this.id] = jQuery(this).val();
            } else if (this.type == 'select-one' && this.name == 'quantity') {
                jQuery("#quantity" + " option").each(function () {
                    if (jQuery(this).val() != '0') {
                        jQuery(this).hide();
                    }
                });
            }
        });

        jQuery.ajax({
            type: "GET",
            url: url,
            data: {selectedValues: attrVal, offerId: offerId, mainCatId: $mainCatId, oldVal: oldVal, newValue: newValue},
            dataType: 'json',
            cache:false,
            success: function (data) {
                jQuery('.jbtn-order-info').html('');
                jQuery('.jbtn-order-info').html(data);
            }
        });

        jbdOffers.checkAddToCartStatus();
    }

    /**
     * Shows price base on the offer
     *
     * @param div string div name
     * @param link string link name
     */
    showPriceBase(div, link) {
        jQuery("#" + link).css("display", "none");
        jQuery("#" + div).removeAttr("style");
    }

    /**
     * Show less price Base
     *
     * @param div string div name
     * @param link string link name
     */
    lessPriceBase(div, link) {
        jQuery("#" + div).css("display", "none");
        jQuery("#" + link).removeAttr("style");
    }

    /**
     * Load item address based on the address of the listing associated with it.
     *
     * @param limit boolean true or false if limit cities and regions is set on general settings
     */
    loadAddress(limit) {
        let companyId = jQuery('select#companyId option:selected').val();
        if (companyId == '') {
            alert(JBD.JText._("LNG_MISSING_OFFER_COMPANY"));
            return;
        }

        let offerUrl = jbdUtils.getAjaxUrl('getListingAddressAjax', 'offer', 'managecompanyoffer');

        jQuery.ajax({
            type: "GET",
            url: offerUrl,
            data: {companyId: companyId},
            dataType: 'json',
            cache:false,
            success: function (data) {
                if (data == null) {
                    alert(JBD.JText._("LNG_MISSING_DELETED_COMPANY"));
                } else {
                    jQuery('#route').val(data.address);
                    jQuery('#street_number').val(data.street_number);
                    jQuery('#area_id').val(data.area);
                    jQuery('#administrative_area_level_2').val(data.province);
                    jQuery('#postal_code').val(data.postalCode);
                    jQuery('#latitude').val(data.latitude);
                    jQuery('#longitude').val(data.longitude);

                    jQuery('#country').val(data.countryId);
                    if (limit) {
                        updateRegions(function () {
                            jQuery('#administrative_area_level_1').val(data.county);
                            updateCities(function () {
                                jQuery('#locality').val(data.city);
                            });
                        });
                    } else {
                        jQuery('#administrative_area_level_1').val(data.county);
                        jQuery('#locality').val(data.city);
                    }

                    if (typeof jbdAdminMapInstance !== 'undefined') {
                        jbdAdminMapInstance.focusLocation({
                            latitude: data.latitude,
                            longitude: data.longitude
                        });
                    }
                }
            }
        });
    }

    /**-- SITE FUNCTIONS --**/


    checkAddToCartStatus(){
        let enableStatus = true;
        jQuery('.jbtn-order-info').find(':input').each(function() {
            if (this.type == 'select-one'){
                if (jQuery(this).val() == '' || jQuery(this).val() == '0'){
                    enableStatus = false
                }
            }
        });

        if (enableStatus == true){
            jQuery('#addToCartButton').removeAttr('style');
            jQuery('.jbtn-order-btn').removeAttr('style');
        }else{
            jQuery('#addToCartButton').attr('style','pointer-events:none');
            jQuery('.jbtn-order-btn').attr('style','background-color:lightgray');
        }
        
        let count = 0;
        jQuery('.jbtn-order-info').find(':input').each(function() {
            if (this.type == 'select-one' && count == 0){
                jQuery('#firstValue').val(jQuery(this).val());
                count =1;
            }
        });
        
        //update the price based on the quantity
        var unitprice = jQuery("#offer-stock-price").attr("unitprice");
        var qty = jQuery("#quantity").val();
        var totalprice = unitprice * qty;
        jQuery("#total-price").html(totalprice);
        
    }

    /**
     * Reset the cart and add a new offer
     * 
     */
    
    resetCartAndAdd(offerId){
    	let resetCartUrl = jbdUtils.getAjaxUrl('emptyCartAjax', 'cart');

        jQuery.ajax({
            type: "GET",
            url: resetCartUrl,
            dataType: 'json',
            cache:false,
            success: function (data) {
            	 jbdOffers.addToCart(offerId);
            }
        });
    }
    
    /**
     * Checks the quantity selected and add them to cart on front end.
     *
     * @param offerId int offer ID
     */
    addToCart(offerId, qnt=0) {
    	let values = {};
        let quantity = jQuery('#quantity').val();
        
        if(qnt){
        	quantity = qnt;
        	values["quantity"] = qnt;
        }

        
        jQuery('.jbtn-order-info').find(':input').each(function() {
            if (this.type == 'select-one'){
                values[this.id] = jQuery(this).val();
            }
        });

        let urlAddToCart = jbdUtils.getAjaxUrl('addToCartAjax', 'cart');
        if (quantity == 0) {
            alert(JBD.JText._('LNG_PLEASE_SELECT_QUANTITY'));
            return;
        }

        let html = '<div class="jbd-container" style="display:none"><div class="jmodal-sm"><div class="jmodal-header"><p class="jmodal-header-title">' + JBD.JText._('LNG_ADDING_PRODUCT_TO_SHOPPING_CART') +'</p>'
        			+'<a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>  </div></div></div>';
        
        
        jQuery(html).appendTo('body').jbdModal();

        jQuery.ajax({
            type: "GET",
            url: urlAddToCart,
            data: {offerId: offerId, selectedData: values},
            dataType: 'json',
            cache:false,
            success: function (data) {
                jQuery.jbdModal.close();
                let count = 0;

                jbdOffers.checkAddToCartStatus();
                if(data === true){
                	jQuery('#cart-dialog').jbdModal();
                	jQuery('.jbtn-order-info').find(':input').each(function() {
                        if (this.type == 'select-one') {
                            if (count == 0) {
                                if (this.name == 'quantity'){
                                    jQuery(this).val('0');
                                }else {
                                    jQuery(this).val("");
                                    jQuery(this).trigger("change");
                                }
                                count = 1;
                            }
                        }
                    });
                }else{
                	jQuery('#cart-error').jbdModal();	
                }
                
            }
        });
    }

    /**
     * Render Offer Rating Criteria on Front End On Review Section
     *
     * @param imagePath string image path
     */
    renderOfferRatingCriteria(imagePath) {
        jQuery('.rating-criteria').rating({
            min: 0,
            max: 5,
            step: 0.5,
            stars: 5,
            starCaptions: {
                0.5: JBD.JText._('LNG_BAD'),
                1: JBD.JText._('LNG_BAD'),
                1.5: JBD.JText._('LNG_POOR'),
                2: JBD.JText._('LNG_POOR'),
                2.5: JBD.JText._('LNG_REGULAR'),
                3: JBD.JText._('LNG_REGULAR'),
                3.5: JBD.JText._('LNG_GOOD'),
                4: JBD.JText._('LNG_GOOD'),
                4.5: JBD.JText._('LNG_GORGEOUS'),
                5: JBD.JText._('LNG_GORGEOUS')
            },
            size: 'sm',
            showCaption: true,
            starCaptionClasses: {
                0.5: 'badge badge-danger',
                1: 'badge badge-danger',
                1.5: 'badge badge-warning',
                2: 'badge badge-warning',
                2.5: 'badge badge-info',
                3: 'badge badge-info',
                3.5: 'badge badge-primary',
                4: 'badge badge-primary',
                4.5: 'badge badge-success',
                5: 'badge badge-success'
            },
            clearCaption: JBD.JText._('LNG_NOT_RATED_YET'),
            clearCaptionClass: 'badge badge-default',
            hoverEnabled: true,
            hoverChangeCaption: true,
            hoverChangeStars: true,
            rtl: false,
        });

        jQuery('.rating-criteria').on('rating:change', function (event, value, caption) {
            jQuery(this).parent().parent().parent().children("input").val(value);
        });

        jQuery('.rating-criteria').on('rating:clear', function (event) {
            jQuery(this).parent().parent().parent().children("input").val(0);
        });

        jQuery('.rating-criteria').on('rating:reset', function (event) {
            jQuery(this).parent().parent().parent().children("input").val(0);
        });
    }

    /**
     * Render offer Reviews
     */
    renderOfferReviews() {
        jQuery('.rating-review').rating({
            min: 0,
            max: 5,
            step: 0.5,
            stars: 5,
            size: 'sm',
            showCaption: false,
            rtl: false,
            displayOnly: true,
        });

        jQuery('.rating-review').each(function () {
            jQuery(this).rating('update', this.title);
        });
    }

    /**
     * Show list mode on offers
     */
    showList() {
        jQuery("#offer-list-view").show();
        jQuery("#layout").hide();

        jQuery("#grid-view-link").removeClass("active");
        jQuery("#list-view-link").addClass("active");
    }

    /**
     * Show offers on grid mode
     */
    showGrid() {
        jQuery("#offer-list-view").hide();
        jQuery("#layout").show();

        jQuery("#grid-view-link").addClass("active");
        jQuery("#list-view-link").removeClass("active");
    }

    /**
     * Edit offer on front end CP
     *
     * @param offerId int offer ID
     */
    editOffer(offerId) {
        jQuery("#id").val(offerId);
        jQuery("#adminForm #task").val("managecompanyoffer.edit");
        jQuery("#adminForm").submit();
    }

    /**
     * Add new offer on front end CP
     */
    addOffer() {
        jQuery("#id").val(0);
        jQuery("#adminForm #task").val("managecompanyoffer.add");
        jQuery("#adminForm").submit();
    }

    /**
     * Delete an offer on front end CP
     *
     * @param offerId int offer ID
     */
    deleteOffer(offerId) {
        if (confirm(JBD.JText._('COM_JBUSINESS_DIRECTORY_OFFERS_CONFIRM_DELETE'))) {
            jQuery("#id").val(offerId);
            jQuery("#adminForm task").val("managecompanyoffers.delete");
            jQuery("#adminForm").submit();
        }
    }

    /**
     * Delete offer order on front end CP
     *
     * @param orderId int Order ID
     */
    deleteOrder(orderId) {
        if (confirm(JBD.JText._("COM_JBUSINESS_DIRECTORY_OFFER_ORDER_CONFIRM_DELETE"))) {
            jQuery("#id").val(orderId);
            jQuery("#adminForm #task").val("managecompanyofferorders.delete");
            jQuery("#adminForm").submit();
        }
    }

    /**
     * Delete Offer Coupon on front end CP
     *
     * @param couponId int coupon ID
     */
    deleteCoupon(couponId) {
        if (confirm(JBD.JText._("COM_JBUSINESS_DIRECTORY_COUPONS_CONFIRM_DELETE", true))) {
            jQuery("#id").val(couponId);
            jQuery("#adminForm #task").val("managecompanyoffercoupons.delete");
            jQuery("#adminForm").submit();
        }
    }

    /**
     * Delete offer message on front end CP
     *
     * @param id int Offer message ID
     */
    deleteOfferMessage(id) {
        if (confirm(JBD.JText._('COM_JBUSINESS_DIRECTORY_OFFER_MESSAGE_CONFIRM_DELETE'))) {
            jQuery("#id").val(id);
            jQuery("#adminForm #task").val("manageoffermessages.delete");
            jQuery("#adminForm").submit();
        }
    }

    /**
     * Show/hide the stock configuration section
     * 
     */
    displayOfferSelling(show) {
        if (show == 0) {
            jQuery('#offerSellingOptions').hide();
        }
        if (show == 1){
            jQuery('#offerSellingOptions').show();
        }
    }
    
    /**
     * Show/hide the stock configuration section
     * 
     */
    displayStockPrice(showHide = 0) {
        if (showHide == 0) {
            jQuery('.stock-price').hide();
        }
        
        if (showHide == 1){
            jQuery('.stock-price').show();
        }
    }

}

let jbdOffers = new JBDOffers();