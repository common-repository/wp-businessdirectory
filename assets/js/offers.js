//******************************************************//
//*********************** ADMIN ************************//
//******************************************************//


//************--Offer Section--*****************//
function saveDates() {
    var start_time = jQuery('#publish_start_time').val();
    var end_time = jQuery('#publish_end_time').val();

    if (start_time == '')
        jQuery('#start_time').attr('value', '');
    if (end_time == '')
        jQuery('#end_time').attr('value', '');
}

function updateAttributes(categoryId, offerId) {
    if (jbdUtils.isProfile != 1) {
        var attributesUrl = jbdUtils.url + '&task=offer.getAttributesAjax';
    } else {
        var attributesUrl = jbdUtils.url + '&task=managecompanyoffer.getAttributesAjax';
    }

    jQuery.ajax({
        type: 'GET',
        url: attributesUrl,
        data: {categoryId: categoryId, offerId: offerId},
        dataType: 'json',
        success: function (data) {
            jQuery('#customFieldsContent').html(data);
            jQuery(".chosen-select").chosen({width: "95%", disable_search_threshold: 5, search_contains: true});
        }
    });
}

function uncheckAllCategories() {
    jQuery('#categories option').attr('selected', false);
    jQuery('#main_subcategory').empty();
    jQuery('#main_subcategory option').trigger('liszt:updated');
    jQuery('#main_subcategory option').trigger("chosen:updated");
    jQuery('#categories').trigger('liszt:updated');
    jQuery('#categories').trigger("chosen:updated");

    if (jbdUtils.enable_attribute_category == 1) {
        resetAttributes();
    }
}

function showPriceBase(div, link) {
    jQuery("#" + link).css("display", "none");
    jQuery("#" + div).removeAttr("style");
}

function lessPriceBase(div, link) {
    jQuery("#" + div).css("display", "none");
    jQuery("#" + link).removeAttr("style");
}

function loadAddress(limit) {
    var companyId = jQuery('select#companyId option:selected').val();
    if (companyId == '') {
        alert(JBD.JText._("LNG_MISSING_OFFER_COMPANY"));
        return;
    }
    if (jbdUtils.isProfile == 1) {
        var offerUrl = jbdUtils.url + '&task=managecompanyoffer.getListingAddressAjax';
    } else {
        var offerUrl = jbdUtils.url + '&task=offer.getListingAddressAjax';
    }

    jQuery.ajax({
        type: 'GET',
        url: offerUrl,
        data: {companyId: companyId},
        dataType: 'json',
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

                var params = [];
                params['map_zoom'] = 15;
                params['map_latitude'] = data.latitude;
                params['map_longitude'] = data.longitude;
                params['latitude'] = data.latitude;
                params['longitude'] = data.longitude;
                updateMap(params);
            }
        }
    });
}

//************--End Offer Section--*****************//


//******************************************************//
//*********************** SITE *************************//
//******************************************************//

//************--Offer Details Section--*****************//
function printOffer(offerUrl) {
    var winref = window.open(offerUrl, 'windowName', 'width=1050,height=700');
    winref.window.focus();
    setTimeout(function () {
        winref.window.print();
    }, 2000);
}

function addToCart(offerId) {
    var urlAddToCart = jbdUtils.url + '&task=cart.addToCartAjax';
    var quantity = jQuery('#quantity').val();

    if (quantity == 0) {
        alert(JBD.JText._('LNG_PLEASE_SELECT_QUANTITY'));
        return;
    }
    
    var html= '<h3 style="color:#000">' + JBD.JText._('LNG_ADDING_PRODUCT_TO_SHOPPING_CART') + '</h3>';
	jQuery(html).appendTo('body').jbdModal();
	
    jQuery.ajax({
        type: "GET",
        url: urlAddToCart,
        data: {offerId: offerId, quantity: quantity},
        dataType: 'json',
        success: function (data) {
            jQuery.jbdModal.close();
        	jQuery('#cart-dialog').jbdModal();
        }
    });
}

function renderOfferRatingCriteria(imagePath) {
    jQuery('.rating-criteria').rating({
        min:0,
        max:5,
        step:0.5,
        stars:5,
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

    jQuery('.rating-criteria').on('rating:change', function(event, value, caption) {
        jQuery(this).parent().parent().parent().children("input").val(value);
    });

    jQuery('.rating-criteria').on('rating:clear', function(event) {
        jQuery(this).parent().parent().parent().children("input").val(0);
    });

    jQuery('.rating-criteria').on('rating:reset', function(event) {
        jQuery(this).parent().parent().parent().children("input").val(0);
    });
}

function renderOfferReviews() {
    jQuery('.rating-review').rating({
        min:0,
        max:5,
        step:0.5,
        stars:5,
        size: 'sm',
        showCaption: false,
        rtl: false,
        displayOnly: true,
    });
    jQuery('.rating-review').each(function() {
        jQuery(this).rating('update',this.title);
    });
}

//***********--End Offer Details Section--************//

//************--Offer List Section--*****************//
function changeRadius(radius) {
    jQuery("#radius").val(radius);
    jQuery("#adminForm").submit();
}

function changeOrder(orderField) {
    jQuery("#orderBy").val(orderField);
    jQuery("#adminForm").submit();
}

function showList() {
    jQuery("#offer-list-view").show();
    jQuery("#layout").hide();

    jQuery("#grid-view-link").removeClass("active");
    jQuery("#list-view-link").addClass("active");
}

function showGrid() {
    jQuery("#offer-list-view").hide();
    jQuery("#layout").show();
    
    jQuery("#grid-view-link").addClass("active");
    jQuery("#list-view-link").removeClass("active");
}

function chooseCategory(categoryId) {
    jQuery("#adminForm #categoryId").val(categoryId);
    jQuery("#adminForm input[name=limitstart]").val(0);
    jQuery("#adminForm").submit();
}

//************--End Offer List Section--*****************//

//************--Manage Offers Section--*****************//
function editOffer(offerId) {
    jQuery("#id").val(offerId);
    jQuery("#task").val("managecompanyoffer.edit");
    jQuery("#adminForm").submit();
}

function addOffer() {
    jQuery("#id").val(0);
    jQuery("#task").val("managecompanyoffer.add");
    jQuery("#adminForm").submit();
}

function deleteOffer(offerId) {
    if (confirm(JBD.JText._('COM_JBUSINESS_DIRECTORY_OFFERS_CONFIRM_DELETE'))) {
        jQuery("#id").val(offerId);
        jQuery("#task").val("managecompanyoffers.delete");
        jQuery("#adminForm").submit();
    }
}

//***************--End Manage Offers Section--******************//

//************--Manage Offer Orders Section--******************//
function deleteOrder(orderId) {
    if (confirm(JBD.JText._("COM_JBUSINESS_DIRECTORY_OFFER_ORDER_CONFIRM_DELETE"))) {
        jQuery("#id").val(orderId);
        jQuery("#task").val("managecompanyofferorders.delete");
        jQuery("#adminForm").submit();
    }
}

//************--End Manage Offer Orders Section--*****************//

//************--Manage Offer Coupons Section--********************//
function deleteCoupon(couponId) {
    if (confirm(JBD.JText._("COM_JBUSINESS_DIRECTORY_COUPONS_CONFIRM_DELETE", true))) {
        jQuery("#id").val(couponId);
        jQuery("#task").val("managecompanyoffercoupons.delete");
        jQuery("#adminForm").submit();
    }
}

//************--End Manage Offer Coupons Section--****************//

//************--Manage Offer Messages Section--********************//
function deleteOfferMessage(id) {
    if (confirm(JBD.JText._('COM_JBUSINESS_DIRECTORY_OFFER_MESSAGE_CONFIRM_DELETE'))) {
        jQuery("#id").val(id);
        jQuery("#task").val("manageoffermessages.delete");
        jQuery("#adminForm").submit();
    }
}

//************--End Manage Offer Coupons Section--****************//