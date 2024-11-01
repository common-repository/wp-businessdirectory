/**
 * JBD Open Map javascript class
 */
class JBDOpenMap extends JBDMapAbstract
{
    /**
     * Class constructor
     *
     * @param locations array with location to put on map
     * @param params array options of the map
     * @param mapType int map type
     */
    constructor(locations, params, mapType)
    {
        super(locations, params, mapType);

    	this.infoboxTemplate = "<div style='overflow:hidden;'>{content}</div>";
    	this.bounds = {
	        "maxLat": -999,
	        "minLng": 999,
	        "minLat": 999,
	        "maxLng": -999
	    };
    }

    /**
     * Initialize Map class,add the event listener's and set the markers and display it
     */
    initialize()
    {
        if(this.mapInitialized){
            return;
        }

        if (this.params != null && typeof this.params['no_map'] === 'undefined') {
            var parent = jQuery('#' + this.mapDiv).parent();
            jQuery('#' + this.mapDiv).remove();
            parent.append
            (
                '<div id="' + this.mapDiv + '" ' +
                'style="width:' + this.mapWidth + ';height:' + this.mapHeight + ';z-index:1">' +
                '</div>'
            );

            this.map = L.map(this.mapDiv, {
                dragging: !L.Browser.mobile,
                fullscreenControl: true,
            }).setView(
                [this.params['map_latitude'], this.params['map_longitude']],
                this.zoom
            );

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(this.map);

            if(this.locations && this.locations.length>0){
            	this.setMarkers(this.locations);
        	}
        }
    }


    /**
     * Set map markers on the map and associate related options and attributes
     */
    setMarkers(locations)
    {
        var markerClusters;

        console.debug(jbdUtils.getProperty('enable_map_clustering'));
        if (jbdUtils.getProperty('enable_map_clustering') == 1 && !this.params['is_admin']) {
            markerClusters = L.markerClusterGroup();
        }

        var lastMarker = null;
        
        let iconSize = [32,32];
	
        let markerSize = jbdUtils.getProperty('marker_size');
        if (markerSize.length != 0) {
            iconSize = [markerSize.width, markerSize.height];
        }

        if(locations.length>0){
	        for (var i = 0; i < locations.length; i++) {
	            var item = locations[i];
	
	            //skip iteration if not defined
	            if (item.length == 0 || item === 'undefined') {
	                continue;
	            }
	
	            this.bounds.maxLat = this.bounds.maxLat < parseFloat(item['latitude']) ? parseFloat(item['latitude']) : this.bounds.maxLat;
	            this.bounds.minLat = this.bounds.minLat > parseFloat(item['latitude']) ? parseFloat(item['latitude']) : this.bounds.minLat;
	            this.bounds.maxLng = this.bounds.maxLng < parseFloat(item['longitude']) ? parseFloat(item['longitude']) : this.bounds.maxLng;
	            this.bounds.minLng = this.bounds.minLng > parseFloat(item['longitude']) ? parseFloat(item['longitude']) : this.bounds.minLng;
	
	            var markerImage;
	            if (jbdUtils.getProperty('enable_map_clustering') != 1 || locations.length == 1 || jbdUtils.getProperty('mapMarker')) {
	                if (item.marker != '0') {
	                    markerImage = item.marker;
	                } else if (jbdUtils.getProperty('mapMarker') && jbdUtils.getProperty('mapMarker').length) {
	                    markerImage = jbdUtils.getProperty('imageBaseUrl') + jbdUtils.getProperty('mapMarker');
	                }
	            }
	
	            var zIndex = 0;
	            if (typeof item['zIndex'] !== 'undefined') {
	                zIndex = item['zIndex'];
	            }
	
	            var popup = this.infoboxTemplate.replace('{content}', item['content']);
	            var marker = L.marker([item['latitude'], item['longitude']]);
	
	            marker.zIndex = zIndex;
	            if (this.params['isLayout'] != 1) {
	                marker.bindPopup(popup);
	            } else {
	                var markersLayer = L.featureGroup().addTo(this.map);
	
	                markersLayer.on("click", function (event) {
	                    var clickedMarker = event.layer;
	                    var target = "#company" + clickedMarker.zIndex;
	                    //window.location.href = target;

                        jQuery([document.documentElement, document.body]).animate({
                            scrollTop: (jQuery(target).offset().top - 150)
                        }, 1000);
	
	                    setTimeout(function () {
	                        jQuery(target).fadeOut(1000).fadeIn(500);
	                    }, 1000);
	                });
	
	                marker.addTo(markersLayer);
	            }

	            if (this.params['markerButton'] !== "undefined") {
                    var self = this;
                    jQuery(".show-marker").click(function () {
                        var companyID = jQuery(this).closest('.grid-item-holder').attr('id');
                        var id = companyID.match(/\d/g);
                        id = id.join('');

                        for (var i = 0; i < self.markers.length; i++) {
                            if (self.markers[i].zIndex == id) {
                                self.map.setView(
                                    [self.markers[i].getLatLng().lat, self.markers[i].getLatLng().lng],
                                    16
                                );
                            }
                        }
                    });
                }
	
	            if (jbdUtils.getProperty('enable_map_clustering')  != 1 || locations.length == 1) {
	                marker.addTo(this.map);
	            }
	
	            if (markerImage !== "" && typeof markerImage !== 'undefined') {
	                
	
	                var icon = L.icon({
	                    iconUrl: markerImage,
	                    iconSize: iconSize,
	                });
	
	                marker.setIcon(icon);
	            }
	            markerImage = '';


	            if (jbdUtils.getProperty('enable_map_clustering') == 1 && !this.params['is_admin']) {
                    console.debug("add to cluster");
	                markerClusters.addLayer(marker);
	            }
	
	            if (this.markers !== undefined){
	            	this.markers.push(marker);
	            } else {
	            	this.markers = Array(marker);
	            }

	            if (typeof item['in_range'] !== 'undefined') {
	                lastMarker = marker;
	            }

                if(item['activity_radius']){
                    //console.debug(item['activity_radius']);
                    L.circle([item['latitude'], item['longitude']], item['activity_radius'] * 1600).addTo(this.map);
                }
	        }
	
	        if (jbdUtils.getProperty('enable_map_clustering')  == 1 && !this.params['is_admin']) {
	            this.map.addLayer(markerClusters);
	        }
        }

        if (this.params["has_location"] == 1 && this.params["latitude"]!='' && this.params["latitude"]!=0) {
            var myLatLng = {
                latitude: this.params["latitude"],
                longitude: this.params["longitude"]
            };

            this.addMarker(myLatLng);

            lastMarker = this.markers.pop();
            let locationMarker = jbdUtils.getProperty('location_map_marker') ? jbdUtils.getProperty('imageBaseUrl') + jbdUtils.getProperty('location_map_marker') : 'https://maps.google.com/mapfiles/kml/shapes/library_maps.png';
            var tmpIcon = L.icon({
                iconUrl: locationMarker,
                iconSize: iconSize,
            });
            lastMarker.setIcon(tmpIcon);
        }

        if (this.params["radius"] > 0) {
            if (typeof this.params['longitude'] == 'undefined' && typeof this.params['latitude'] == 'undefined' || this.params['longitude'] == '') {
                this.params['longitude'] = this.params['map_longitude'];
                this.params['latitude'] = this.params['map_latitude'];
            }

            if (this.params['has_location'] == 1 || lastMarker != null) {
                var center = lastMarker.getLatLng();
                L.circle([center.lat, center.lng], this.params["radius"] * 1600).addTo(this.map);
            }
        }

        this.map.invalidateSize(false);

        if (jbdUtils.getProperty('map_enable_auto_locate') == 1) {
            this.map.fitBounds([
                [this.bounds.maxLat, this.bounds.maxLng],
                [this.bounds.minLat, this.bounds.minLng]
            ]);
        }
    }

    /**
     * Move to location when setting markers
     *
     * @param location array with location settings
     */
    moveToLocation(location)
    {
        this.map.setView([location.latitude, location.longitude]);
    }

    /**
     * Add marker on map and associate the callback with it
     * @param location array location lat and lang
     * @param callback callback function
     */
    addMarker(location, callback)
    {
        var marker = L.marker([location.latitude, location.longitude]);
        if (jbdUtils.getProperty('enable_map_clustering')  != 1){
            marker.addTo(this.map);
        }
        
        if(this.markers !== undefined){
        	this.markers.push(marker);
        }else{
        	this.markers = Array(marker);
        }

        if (typeof callback !== 'undefined') {
            callback();
        }
    }

    /**
     * Remove map markers
     */
    clearMarkers()
    {
        for (var i = 0; i < this.markers.length; i++) {
            this.map.removeLayer(this.markers[i]);
        }
    }

    /**
     * Add map listener
     *
     * @param event string event type to associate with the event
     * @param action function to call
     */
    addMapListener(event, action)
    {
        this.map.on(event, function (e) {
            var location = {};
            location.latitude = e.latlng.lat;
            location.longitude = e.latlng.lng;
            action(location);
        });
    }

    /**
     * Initiate map autocomplete so when locations are searched on input field suggestions will be shown
     *
     * @param element object javascript object of input field
     * @param action function called
     * @param preventSubmit bool prevent submit form
     * @param focus_view
     * @param limitedCountries array with only the countries that are allowed to search on
     */
    static initAutocomplete(element, action, preventSubmit, focus_view, limitedCountries)
    {
        var self = this;

        if (limitedCountries == null){
            limitedCountries = [];
        }

        jQuery(element).keyup(function (e) {
            var key = event.keyCode || event.charCode;
            if(key == 38 || key ==40){
                return;
            }
            var query = jQuery(element).val();
            var countyLimit = '';
            if (limitedCountries.length > 0) {
                countyLimit = '&countrycodes='+limitedCountries.join();
            }
            var url = "https://nominatim.openstreetmap.org/search?format=json&addressdetails=1&q="+query+countyLimit;
            var res;

            jQuery(element).autocomplete({
                source: function (request, response) {
                    jQuery.ajax({
                        type: "GET",
                        url: url,
                        dataType: 'json',
                        success: function (data) {
                            response(jQuery.map(data, function (item) {
                                return {
                                    label: item.display_name,
                                    value: item.place_id
                                };
                            }));

                            res = data;
                        }
                    });
                },
                select: function (event, ui) {
                    event.preventDefault();
                    jQuery(element).val(ui.item.label);
                  
                    for (var i in res) {
                        if (res.hasOwnProperty(i)) {
                            if (res[i].place_id == ui.item.value) {
                                if (focus_view) {
                                    if (typeof jbdAdminMapInstance !== 'undefined') {
                                        jbdAdminMapInstance.autocompleteFocus(res[i].lat, res[i].lon);
                                    }
                                }
                                action(JBDOpenMap.formatSuggestionResponse(res[i]));
                            }
                        }
                    }
                }
            });
        });
    }

    /**
     * Add marker for the location search and focus map on it
     *
     * @param lat string map latitude location
     * @param long string map longitude location
     */
    autocompleteFocus(lat, long)
    {
        this.clearMarkers();
        let loc = {};
        loc.latitude = lat;
        loc.longitude = long;
        this.addMarker(loc);
        this.moveToLocation(loc);
    }

    /**
     * Focuses the given location.
     *  
     * @param {latitude, longitude, clearMarkers, addMarker} params 
     */
    focusLocation(params)
    {
        let loc = {};
        loc.latitude = params.latitude;
        loc.longitude = params.longitude;
        
        this.moveToLocation(loc);

        if (typeof params.clearMarkers === 'undefined' || !params.clearMarkers) {
            this.clearMarkers();
        }

        if (typeof params.addMarker === 'undefined' || params.addMarker) {
            this.addMarker(loc);
        }
    }

    /**
     * Reserve Geocoding
     *
     * @param location
     * @param action
     * @returns {boolean}
     */
    static reverseGeocoding(location, action)
    {
        let lon = location.longitude;
        let lat = location.latitude;
        let response;

        let url = "https://nominatim.openstreetmap.org/reverse.php?format=json&lat=" + lat + "&lon=" + lon;

        jQuery.ajax({
            type: "GET",
            url: url,
            dataType: 'json',
            success: (data) => {
                response = JBDOpenMap.formatSuggestionResponse(data);

                action(response.country_code);
            }
        });
    }

    /**
     * Response handle for the function loadInitAutocomplete
     * @param place object response
     * @param component_form string component form name
     * @returns {[]}
     */
    static formatSuggestionResponse(place)
    {
        let suggestionResult = [];

        let config = JSON.parse(jbdUtils.getProperty('autocomplete_config'));

        console.debug(place);
        console.debug(config.openstreet);

        for (let att in config.openstreet) {
            if (config.openstreet.hasOwnProperty(att)) {
                for (let i = 0; i < config.openstreet[att].length; i++) {
                    let alternative = config.openstreet[att][i];
                    if (typeof place.address[alternative] !== 'undefined') {
                        suggestionResult[att] = place.address[alternative];
                        break;
                    }
                }
            }
        }

        suggestionResult["latitude"] = place.lat;
        suggestionResult["longitude"] = place.lon;

        return suggestionResult;
    }
}