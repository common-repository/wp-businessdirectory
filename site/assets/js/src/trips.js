/**
 * JBD Trips javascript class
 */
 class JBDTrips {
    /**-- ADMNIN FUNCTIONS --**/

    /**
     * Constructor
     */
     constructor() {
        this.todayDate = new Date();
    }

    /**
     * Shows modal for recurring trips on trip save
     *
     * @param task string name of the trip
     */
    showSaveDialog(task) {
        jQuery("#adminForm #task").val(task);
        jQuery('#edit-trip-dialog').jbdModal();
    }

    /**
     * Do not create recurring trips and hide the options for recurring trips
     */
    repeatNone() {
        jQuery("#repeat-options").hide();
        jQuery("#datepicker-calendar").hide();
    }

    /**
     * Shows recurring trip options for daily repeats.
     */
     repeatDaily() {
        console.debug("repeat daily");
        jQuery("#repeat-options").show();
        jQuery("#week-days-group").hide();
        jQuery("#monthly-repeat").hide();
        jQuery("#datepicker-calendar").hide();

    }

    /**
     * Shows recurring trip options for weekly repeats.
     */
    repeatWeekly() {
        jQuery("").hide();
        jQuery("#repeat-options").show();
        jQuery("#week-days-group").show();
        jQuery("#monthly-repeat").hide();
        jQuery("#datepicker-calendar").hide();

    }

    /**
     * Shows recurring trip options for monthly repeats.
     */
    repeatMonthly() {
        jQuery("#repeat-options").show();
        jQuery("#week-days-group").hide();
        jQuery("#monthly-repeat").show();
        jQuery("#datepicker-calendar").hide();

    }

    /**
     * Shows recurring trip options for yearly repeats.
     */
    repeatYearly() {
        jQuery("#repeat-options").show();
        jQuery("#week-days-group").hide();
        jQuery("#monthly-repeat").hide();
        jQuery("#datepicker-calendar").hide();

    }

    /**
     * Shows recurring trip options for yearly repeats.
     */
     repeatByDate() {
        jQuery("#repeat-options").hide();
        jQuery("#week-days-group").hide();
        jQuery("#monthly-repeat").hide();
        jQuery("#datepicker-calendar").show();
    }


    /**
     * Enable recurring trip options for the occurrences of the trip and disable the end date of the recurring
     */
    endsOnOccurances() {
        jQuery("#rend_date").prop('disabled', true);
        jQuery("#occurrences").prop('disabled', false);
    }

    /**
     * Enable recurring trip options for the end date of the recurring and disable the occurrences of the trip
     */
    endsOnDate() {
        jQuery("#rend_date").prop('disabled', false);
        jQuery("#occurrences").prop('disabled', true);
    }

    handleUncompleteBookings(tripId) {
        let urlBookings = jbdUtils.getAjaxUrl('handleUncompleteBookingsAjax', 'trip');
        let self = this;

        jQuery.ajax({
            type: "GET",
            url: urlBookings,
            data: {tripId},
            dataType: 'json',
            cache: false,
            success: function () {
                //create calendar with available dates
                self.createTripCalendar(tripId)
            }
        });
    }


    /**
     * Get get trip dates by ajax
     */
     createTripCalendar(tripId) {
       
        let urlGetDays = jbdUtils.getAjaxUrl('getTripAvailableDatesAjax', 'trip');

        jQuery.ajax({
            type: "GET",
            url: urlGetDays,
            data: {tripId},
            dataType: 'json',
            cache: false,
            success: function (data) {
                var firstDate = jQuery.datepicker.formatDate('mm/dd/yy', new Date(data[0]));
                
                jQuery('#datepicker').datepicker('destroy');
                jQuery('#datepicker').datepicker({
                    beforeShowDay: function (date) {
                        let string = jQuery.datepicker.formatDate('mm-dd-yy', date);
                        return [data.indexOf(string) != -1]
                    },
                    onSelect: function (date) {
                        jQuery('#tripDate').val(date)
                    },
                    defaultDate: firstDate
                }); 
                
                jQuery('#tripDate').val(firstDate)
                if(data.length > 0) {
                    jQuery('#trip-action-container').html('<a href="javascript:document.tripBookingForm.submit()" class="btn btn-success">Book Now</a>')
                }
            }
        });
    }
}

let jbdTrips = new JBDTrips();