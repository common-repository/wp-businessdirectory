/**
 * JBD Events javascript class
 */
class JBDEvents {
    /**-- ADMNIN FUNCTIONS --**/

    /**
     * Shows modal for recurring events on event save
     *
     * @param task string name of the event
     */
    showSaveDialog(task) {
        jQuery("#adminForm #task").val(task);
        jQuery('#edit-event-dialog').jbdModal();
    }

    /**
     * Do not create recurring events and hide the options for recurring events
     */
    repeatNone() {
        jQuery("#repeat-options").hide();
    }

    /**
     * Shows recurring event options for daily repeats.
     */
    repeatDaily() {
        //console.debug("repeat daily");
        jQuery("#repeat-options").show();
        jQuery("#week-days-group").hide();
        jQuery("#monthly-repeat").hide();
    }

    /**
     * Shows recurring event options for weekly repeats.
     */
    repeatWeekly() {
        jQuery("").hide();
        jQuery("#repeat-options").show();
        jQuery("#week-days-group").show();
        jQuery("#monthly-repeat").hide();
    }

    /**
     * Shows recurring event options for monthly repeats.
     */
    repeatMonthly() {
        jQuery("#repeat-options").show();
        jQuery("#week-days-group").hide();
        jQuery("#monthly-repeat").show();
    }

    /**
     * Shows recurring event options for yearly repeats.
     */
    repeatYearly() {
        jQuery("#repeat-options").show();
        jQuery("#week-days-group").hide();
        jQuery("#monthly-repeat").hide();
    }

    /**
     * Enable recurring event options for the occurrences of the event and disable the end date of the recurring
     */
    endsOnOccurances() {
        jQuery("#rend_date").prop('disabled', true);
        jQuery("#occurrences").prop('disabled', false);
    }

    /**
     * Enable recurring event options for the end date of the recurring and disable the occurrences of the event
     */
    endsOnDate() {
        jQuery("#rend_date").prop('disabled', false);
        jQuery("#occurrences").prop('disabled', true);
    }

    /**
     * Edit only current event on the series of recurring events
     */
    editCurrentEvent() {
        jQuery("#edit_mode").val(1);
        JBD.submitform(jQuery("#task").val(), document.getElementById('item-form'));
        jQuery.jbdModal.close();
    }

    /**
     * Edit only the following events on the series of recurring events
     */
    editAllFollowignEvents() {
        jQuery("#edit_mode").val(2);
        JBD.submitform(jQuery("#task").val(), document.getElementById('item-form'));
        jQuery.jbdModal.close();
    }

    /**
     * Edit all events on the series of recurring events
     */
    editAllSeriesEvents() {
        jQuery("#edit_mode").val(3);
        JBD.submitform(jQuery("#task").val(), document.getElementById('item-form'));
        jQuery.jbdModal.close();
    }

    /**
     * Add the dates to the form fields to save them on event save
     */
    saveDates() {
        let start_time = jQuery('#start_time').val();
        let end_time = jQuery('#end_time').val();
        let doors_open_time = jQuery('#doors_open_time').val();
        let booking_open_time = jQuery('#booking_open_time').val();
        let booking_close_time = jQuery('#booking_close_time').val();

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

    /**
     * Update the attributes on category selection. If linking of categories and attributes is enabled then the attributes
     * will be shown based on the category selection for the item
     *
     * @param categoryId int category id
     * @param eventId int event id
     */
    updateAttributes(categoryId, eventId) {
        let attributesUrl = jbdUtils.getAjaxUrl('getAttributesAjax', 'event', 'managecompanyevent');

        jQuery.ajax({
            type: "GET",
            url: attributesUrl,
            data: {categoryId: categoryId, eventId: eventId},
            dataType: 'json',
            cache:false,
            success: function (data) {
                jQuery('#customFieldsContent').html(data);
                jQuery(".chosen-select").chosen({width: "95%", disable_search_threshold: 5, search_contains: true, placeholder_text_single: JBD.JText._('LNG_SELECT_OPTION'), placeholder_text_multiple: JBD.JText._('LNG_SELECT_OPTION')});
            }
        });
    }

    /**
     * Load item address based on the address of the listing associated with it.
     *
     * @param limit boolean true or false if limit cities and regions is set on general settings
     */
    loadAddress(limit) {
        let companyId = jQuery('select#company_id option:selected').val();
        if (companyId == '') {
            alert(JBD.JText._("LNG_MISSING_EVENT_COMPANY"));
            return;
        }

        let eventUrl = jbdUtils.getAjaxUrl('getListingAddressAjax', 'event', 'managecompanyevent');

        jQuery.ajax({
            type: "GET",
            url: eventUrl,
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

    /**
     * Show join event dialog
     */
    showCompanyListDialog() {
        jQuery('#company-list').jbdModal();
    }

    /**
     * called when needed to join an event and if the user is logged in then show login notice otherwise show join event
     * dialog
     *
     * @param userId int user id
     */
    joinEvent(userId) {
        if (userId == 0) {
            jbdUtils.showLoginNotice();
        } else {
            jbdEvents.showCompanyListDialog();
        }
    }

    /**
     * Join event function. Associates the selected companies with the event.
     *
     * @param event_id int event ID
     */
    associateCompanies(event_id) {
        let urlAssociateCompanies = jbdUtils.getAjaxUrl('associateCompaniesAjax', 'event');

        let eventId = event_id;
        let selectedValues = jQuery('#userAssociatedCompanies').val();
        let companyIds;
        if (Array.isArray(selectedValues)) {
            companyIds = selectedValues.join();
        } else {
            companyIds = -1;
        }


        jQuery("#company-list button").each(function () {
            jQuery(this).attr("disabled", "disabled");
        });

        //loading button animation if validation is OK
        jQuery("#company-list .jmodal-btn").addClass("loader");


        let successMessage = jQuery('#associated-companies-message').html();

        jQuery.ajax({
            type: "GET",
            url: urlAssociateCompanies,
            data: {companyIds: companyIds, eventId: eventId},
            dataType: 'json',
            cache:false,
            success: function () {
                if (Array.isArray(selectedValues)) {
                    jQuery(successMessage).appendTo('body').jbdModal();
                    jQuery("#company-list .jmodal-btn").removeClass("loader");
                }
                else {
                    jQuery.jbdModal.close();
                }
                location.reload();
            }
        });
    }

    /**
     * Show event in list mode
     *
     * @param view int view
     */
    showEventList(view) {
        jQuery("#list-view-container").show();
        jQuery("#jbd-grid-view").hide();

        jQuery(".result-counter").show();
        jQuery(".pagination").show();
        
        jQuery(".search-toggles .sortby").show();
        jQuery(".search-toggles .orderBy").show();

        jQuery("#grid-view-link").removeClass("active");
        jQuery("#list-view-link").addClass("active");
    }

    /**
     * Show event in grid mode
     *
     * @param hidePagination boolean if set to yes then hide the pagination
     */
    showGrid(hidePagination = false) {
        jQuery("#list-view-container").hide();
        jQuery("#jbd-grid-view").show();

        if (hidePagination) {
            jQuery(".result-counter").hide();
            jQuery(".pagination").hide();
        }
        jQuery(".search-toggles .sortby").hide();
        jQuery(".search-toggles .orderBy").hide();

        if (typeof calendarOptions !== 'undefined') {
            jQuery('#events-calendar').fullCalendar(calendarOptions);
        }

        jQuery("#grid-view-link").addClass("active");
        jQuery("#list-view-link").removeClass("active");
    }

    /**
     * Search events on the dates interval
     *
     * @param startDate string start date of the interval
     * @param endDate string end date of the interval
     */
    setSearchDates(startDate, endDate) {
        jQuery("#adminForm #startDate").val(startDate);
        jQuery("#adminForm #endDate").val(endDate);
        jQuery("#adminForm input[name=limitstart]").val(0);
        
        jbdUtils.initializeGetItemsAjax();
    }

    /**
     * Edit event on front end CP
     *
     * @param eventId int event ID that will be edited
     */
    editEvent(eventId) {
        jQuery("#adminForm #id").val(eventId);
        jQuery("#adminForm #task").val("managecompanyevent.edit");
        jQuery("#adminForm").submit();
    }

    /**
     * Create new event on front end CP
     */
    addDirEvent() {
        jQuery("#adminForm #id").val(0);
        jQuery("#adminForm #task").val("managecompanyevent.add");
        jQuery("#adminForm").submit();
    }

    /**
     * Delete event on front end CP
     *
     * @param eventId int event ID
     */
    deleteDirEvent(eventId) {
        jQuery("#adminForm #id").val(eventId);

        if (confirm(JBD.JText._('COM_JBUSINESS_DIRECTORY_EVENTS_CONFIRM_DELETE'))) {
            jQuery("#adminForm #id").val(eventId);
            jQuery("#adminForm #task").val("managecompanyevents.delete");
            jQuery("#adminForm").submit();
        }
    }

    /**
     * Show delete recurring modal.
     */
    showDeleteDialog() {
        jQuery('#delete-event-dialog').jbdModal();
    }

    /**
     * Delete only the current event on the recurring series
     */
    deleteEvent() {
        jQuery("#delete_mode").val(1);
        JBD.submitform('managecompanyevents.delete');
        jQuery.jbdModal.close();
    }

    /**
     * Delete all the following events on the recurring series
     */
    deleteAllFollowignEvents() {
        jQuery("#delete_mode").val(2);
        JBD.submitform('managecompanyevents.delete');
        jQuery.jbdModal.close();
    }

    /**
     * Delete all events on the recurring series
     */
    deleteAllSeriesEvents() {
        jQuery("#delete_mode").val(3);
        JBD.submitform('managecompanyevents.delete');
        jQuery.jbdModal.close();
    }

    /**
     * Submit form after select an appointment
     */
    selectAppointment() {
        jQuery("#adminForm").submit();
    }

    /**
     * Delete appointment by it ID.
     *
     * @param appointmentId int appointment ID
     */
    deleteAppointment(appointmentId) {
        if (confirm(JBD.JText._("COM_JBUSINESS_DIRECTORY_EVENT_APPOINTMENT_CONFIRM_DELETE"))) {
            jQuery("#adminForm #id").val(appointmentId);
            jQuery("#adminForm #task").val("managecompanyeventappointments.delete");
            jQuery("#adminForm").submit();
        }
    }

    /**
     * Confirm appointment on front end CP
     *
     * @param appointmentId int appointment ID
     */
    confirmAppointment(appointmentId) {
        jQuery("#adminForm #id").val(appointmentId);
        jQuery("#adminForm #task").val("managecompanyeventappointments.confirm");
        jQuery("#adminForm").submit();
    }

    /**
     * Deny appointment on front end CP
     *
     * @param appointmentId int appointment ID
     */
    denyAppointment(appointmentId) {
        jQuery("#adminForm #id").val(appointmentId);
        jQuery("#adminForm #task").val("managecompanyeventappointments.deny");
        jQuery("#adminForm").submit();
    }

    /**
     * Delete event Reservation on front end CP
     *
     * @param bookingId int booking ID
     */
    deleteEventReservation(bookingId) {
        if (confirm(JBD.JText._("COM_JBUSINESS_DIRECTORY_EVENT_RESERVATION_CONFIRM_DELETE"))) {
            jQuery("#adminForm #id").val(bookingId);
            jQuery("#adminForm #task").val("managecompanyeventreservations.delete");
            jQuery("#adminForm").submit();
        }
    }

    /**
     * Submit form on ticket selection
     */
    selectTicket() {
        jQuery("#adminForm").submit();
    }

    /**
     * Add new event ticket on front end CP
     */
    addEventTicket() {
        jQuery("#adminForm #id").val(0);
        jQuery("#adminForm #task").val("managecompanyeventticket.add");
        jQuery("#adminForm").submit();
    }

    /**
     * Delete event ticket on front end CP
     *
     * @param ticketId int ticket ID
     */
    deleteTicket(ticketId) {
        if (confirm(JBD.JText._("COM_JBUSINESS_DIRECTORY_EVENT_TICKET_CONFIRM_DELETE"))) {
            jQuery("#adminForm #id").val(ticketId);
            jQuery("#adminForm #task").val("managecompanyeventtickets.delete");
            jQuery("#adminForm").submit();
        }
    }

    /**
     * Duplicate event ticket on front end CP
     *
     * @param ticketId int ticket ID
     */
    duplicateTicket(ticketId) {
        jQuery("#adminForm #id").val(ticketId);
        jQuery("#adminForm #task").val("managecompanyeventticket.duplicate");
        jQuery("#adminForm").submit();
    }

    /**
     * delete event message by its ID on front end CP
     *
     * @param id int message ID
     */
    deleteEventMessage(id) {
        if (confirm(JBD.JText._('COM_JBUSINESS_DIRECTORY_EVENT_MESSAGE_CONFIRM_DELETE'))) {
            jQuery("#adminForm #id").val(id);
            jQuery("#adminForm #task").val("manageeventmessages.delete");
            jQuery("#adminForm").submit();
        }
    }
}

let jbdEvents = new JBDEvents();