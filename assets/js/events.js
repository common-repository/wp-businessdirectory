//******************************************************//
//*********************** ADMIN ************************//
//******************************************************//

//************--Event Section--*****************//
function showSaveDialog(task) {
    jQuery("#task").val(task);
    jQuery('#edit-event-dialog').jbdModal();
}

function repeatNone() {
    jQuery("#repeat-options").hide();
}

function repeatDaily() {
    console.debug("repeat daily");
    jQuery("#repeat-options").show();
    jQuery("#week-days-group").hide();
    jQuery("#monthly-repeat").hide();
}

function repeatWeekly() {
    jQuery("").hide();
    jQuery("#repeat-options").show();
    jQuery("#week-days-group").show();
    jQuery("#monthly-repeat").hide();
}

function repeatMonthly() {
    jQuery("#repeat-options").show();
    jQuery("#week-days-group").hide();
    jQuery("#monthly-repeat").show();
}

function repeatYearly() {
    jQuery("#repeat-options").show();
    jQuery("#week-days-group").hide();
    jQuery("#monthly-repeat").hide();
}

function endsOnOccurances() {
    jQuery("#rend_date").prop('disabled', true);
    jQuery("#occurrences").prop('disabled', false);
}

function endsOnDate() {
    jQuery("#rend_date").prop('disabled', false);
    jQuery("#occurrences").prop('disabled', true);
}

function editCurrentEvent() {
    jQuery("#edit_mode").val(1);
    JBD.submitform(jQuery("#task").val(), document.getElementById('item-form'));
    jQuery.jbdModal.close();
}

function editAllFollowignEvents() {
    jQuery("#edit_mode").val(2);
    JBD.submitform(jQuery("#task").val(), document.getElementById('item-form'));
    jQuery.jbdModal.close();
}

function editAllSeriesEvents() {
    jQuery("#edit_mode").val(3);
    JBD.submitform(jQuery("#task").val(), document.getElementById('item-form'));
    jQuery.jbdModal.close();
}

function checkAll() {
	jQuery('#associated-listings option').prop('selected', true);
    jQuery('#associated-listings').trigger('chosen:updated');
}

function uncheckAll() {
    jQuery('#associated-listings option').prop('selected', false);
    jQuery('#associated-listings').trigger('chosen:updated');
}

function uncheckAllCategories() {
    jQuery('#categories option').attr('selected', false);
    jQuery('#main_subcategory').empty();
    jQuery('#main_subcategory option').trigger('chosen:updated');
    jQuery('#main_subcategory option').trigger("chosen:updated");
    jQuery('#categories').trigger('chosen:updated');
    jQuery('#categories').trigger("chosen:updated");

    if (jbdUtils.enable_attribute_category == 1) {
        resetAttributes();
    }
}

function saveDates() {
    var start_time = jQuery('#start_time').val();
    var end_time = jQuery('#end_time').val();
    var doors_open_time = jQuery('#doors_open_time').val();
    var booking_open_time = jQuery('#booking_open_time').val();
    var booking_close_time = jQuery('#booking_close_time').val();

    if (start_time == '')
        jQuery('#start_time').attr('value', '');
    if (end_time == '')
        jQuery('#end_time').attr('value', '');
    if (doors_open_time == '')
        jQuery('#doors_open_time').attr('value', '');
    if (booking_open_time == '')
        jQuery('#booking_open_time').attr('value', '');
    if (booking_close_time == '')
        jQuery('#booking_close_time').attr('value', '');
}

function updateAttributes(categoryId, eventId) {
    if (jbdUtils.isProfile != 1) {
        var attributesUrl = jbdUtils.url + '&task=event.getAttributesAjax';
    } else {
        var attributesUrl = jbdUtils.url + '&task=managecompanyevent.getAttributesAjax';
    }

    jQuery.ajax({
        type: 'GET',
        url: attributesUrl,
        data: {categoryId: categoryId, eventId: eventId},
        dataType: 'json',
        success: function (data) {
            jQuery('#customFieldsContent').html(data);
            jQuery(".chosen-select").chosen({width: "95%", disable_search_threshold: 5, search_contains: true});
        }
    });
}

function loadAddress(limit) {
    var companyId = jQuery('select#company_id option:selected').val();
    if (companyId == '') {
        alert(JBD.JText._("LNG_MISSING_EVENT_COMPANY"));
        return;
    }
    if (jbdUtils.isProfile == 1) {
        var eventUrl = jbdUtils.url + '&task=managecompanyevent.getListingAddressAjax';
        } else {
        var eventUrl = jbdUtils.url + '&task=event.getListingAddressAjax';
    }

    jQuery.ajax({
        type: "GET",
        url: eventUrl,
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

//************--End Event Section--*****************//


//*****************************************************//
//*********************** SITE ************************//
//*****************************************************//

//************--Event Details Section--*****************//
function printEvent(url) {
    var winref = window.open(url, 'windowName', 'width=1050,height=700');
    winref.window.focus();
    setTimeout(function () {
        winref.window.print();
    }, 1000);

}

function showCompanyListDialog() {
	jQuery('#company-list').jbdModal();
}

function joinEvent(userId) {
    if (userId == 0) {
        showLoginNotice();
    } else {
        showCompanyListDialog();
    }
}

function associateCompanies(event_id) {
	console.debug(jbdUtils.url);
    var urlAssociateCompanies = jbdUtils.url + '&task=event.associateCompaniesAjax';

    var eventId = event_id;
    var selectedValues = jQuery('#userAssociatedCompanies').val();
    var companyIds;
    if (Array.isArray(selectedValues))
        companyIds = selectedValues.join();
    else
        companyIds = -1;

    var successMessage = jQuery('#associated-companies-message').html();

    jQuery.ajax({
        type: 'GET',
        url: urlAssociateCompanies,
        data: {companyIds: companyIds, eventId: eventId},
        dataType: 'json',
        success: function () {
            if (Array.isArray(selectedValues)) {
            	jQuery(successMessage).appendTo('body').jbdModal();
            }
            else {
                jQuery.jbdModal.close();
            }
        }
    });
}

//**********--End Event Details Section--*************//

//************--Event List Section--*****************//
function changeRadius(radius) {
    jQuery("#radius").val(radius);
    jQuery("#adminForm").submit();
}

function changeOrder(orderField) {
    jQuery("#orderBy").val(orderField);
    jQuery("#adminForm").submit();
}

function showList(listId) {
    if (listId == 1)
        showList_1();
    else if (listId == 2)
        showList_2();
}

function showEventList(view) {
	jQuery(".event-list-style-1").show();
    jQuery(".event-list-style-2").show();
    jQuery("#grid-layout").hide();
    jQuery(".result-counter").show();
    jQuery(".pagination").show();
    jQuery(".search-toggles .sortby").show();
    jQuery(".search-toggles .orderBy").show();

    jQuery("#grid-view-link").removeClass("active");
    jQuery("#list-view-link").addClass("active");
}

function showGrid(hidePagination) {
    if (hidePagination == 'undefined'){
        hidePagination = false;
    }

    jQuery(".event-list-style-1").hide();
    jQuery(".event-list-style-2").hide();
    jQuery("#grid-layout").show();
    
    if(hidePagination){
    	jQuery(".result-counter").hide();
	    jQuery(".pagination").hide();
	}
    jQuery(".search-toggles .sortby").hide();
    jQuery(".search-toggles .orderBy").hide();

    if(typeof calendarOptions !== 'undefined'){
    	jQuery('#events-calendar').fullCalendar(calendarOptions);
	}

    jQuery("#grid-view-link").addClass("active");
    jQuery("#list-view-link").removeClass("active");
}

function chooseCategory(categoryId) {
    jQuery("#adminForm #categoryId").val(categoryId);
    jQuery("#adminForm input[name=limitstart]").val(0);
    jQuery("#adminForm").submit();
}

function setSearchDates(startDate, endDate){
	jQuery("#adminForm #startDate").val(startDate);
	jQuery("#adminForm #endDate").val(endDate);
    jQuery("#adminForm input[name=limitstart]").val(0);
    jQuery("#adminForm").submit();
}


//************--End Event List Section--*****************//

//************--Manage Events Section--*****************//
function editEvent(eventId) {
    jQuery("#id").val(eventId);
    jQuery("#task").val("managecompanyevent.edit");
    jQuery("#adminForm").submit();
}

function addDirEvent() {
    jQuery("#id").val(0);
    jQuery("#task").val("managecompanyevent.add");
    jQuery("#adminForm").submit();
}

function deleteDirEvent(eventId) {
    jQuery("#id").val(eventId);

    if (confirm(JBD.JText._('COM_JBUSINESS_DIRECTORY_EVENTS_CONFIRM_DELETE'))) {
        jQuery("#id").val(eventId);
        jQuery("#task").val("managecompanyevents.delete");
        jQuery("#adminForm").submit();
    }
}

function showDeleteDialog() {
	jQuery('#delete-event-dialog').jbdModal();
}

function deleteEvent() {
    jQuery("#delete_mode").val(1);
    JBD.submitform('managecompanyevents.delete');
    jQuery.jbdModal.close();
}

function deleteAllFollowignEvents() {
    jQuery("#delete_mode").val(2);
    JBD.submitform('managecompanyevents.delete');
    jQuery.jbdModal.close();
}

function deleteAllSeriesEvents() {
    jQuery("#delete_mode").val(3);
    JBD.submitform('managecompanyevents.delete');
    jQuery.jbdModal.close();
}

//************--End Manage Events Section--*****************//

//************--Manage Events Appointments Section--*****************//
function selectAppointment() {
    jQuery("#adminForm").submit();
}

function deleteAppointment(appointmentId) {
    if (confirm(JBD.JText._("COM_JBUSINESS_DIRECTORY_EVENT_APPOINTMENT_CONFIRM_DELETE"))) {
        jQuery("#id").val(appointmentId);
        jQuery("#task").val("managecompanyeventappointments.delete");
        jQuery("#adminForm").submit();
    }
}

function confirmAppointment(appointmentId) {
    jQuery("#id").val(appointmentId);
    jQuery("#task").val("managecompanyeventappointments.confirm");
    jQuery("#adminForm").submit();
}

function denyAppointment(appointmentId) {
    jQuery("#id").val(appointmentId);
    jQuery("#task").val("managecompanyeventappointments.deny");
    jQuery("#adminForm").submit();
}

//************--End Manage Events Appointments Section--*****************//

//************--Manage Events Reservation Section--*****************//
function selectReservation() {
    jQuery("#adminForm").submit();
}

function deleteEventReservation(bookingId) {
    if (confirm(JBD.JText._("COM_JBUSINESS_DIRECTORY_EVENT_RESERVATION_CONFIRM_DELETE"))) {
        jQuery("#id").val(bookingId);
        jQuery("#task").val("managecompanyeventreservations.delete");
        jQuery("#adminForm").submit();
    }
}

//*********--End Manage Events Reservation Section--*************//

//************--Manage Events Tickets Section--*****************//
function selectTicket() {
    jQuery("#adminForm").submit();
}

function addEventTicket() {
    jQuery("#id").val(0);
    jQuery("#task").val("managecompanyeventticket.add");
    jQuery("#adminForm").submit();
}

function deleteTicket(ticketId) {
    if (confirm(JBD.JText._("COM_JBUSINESS_DIRECTORY_EVENT_TICKET_CONFIRM_DELETE"))) {
        jQuery("#id").val(ticketId);
        jQuery("#task").val("managecompanyeventtickets.delete");
        jQuery("#adminForm").submit();
    }
}

function duplicateTicket(ticketId) {
    jQuery("#id").val(ticketId);
    jQuery("#task").val("managecompanyeventticket.duplicate");
    jQuery("#adminForm").submit();
}

//**********--End Manage Events Tickets Section--**************//

//************--Manage Offer Messages Section--********************//
function deleteEventMessage(id) {
    if (confirm(JBD.JText._('COM_JBUSINESS_DIRECTORY_EVENT_MESSAGE_CONFIRM_DELETE'))) {
        jQuery("#id").val(id);
        jQuery("#task").val("manageeventmessages.delete");
        jQuery("#adminForm").submit();
    }
}

//************--End Manage Offer Coupons Section--****************//