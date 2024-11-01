/**
 * JBD Google map class
 */
class JBDGoogleMap extends JBDMapAbstract{

    /**
     * Class constructor
     *
     * @param locations array with location to put on map
     * @param params array options of the map
     * @param mapType int map type
     */
    constructor(locations, params, mapType){
      
    	super(locations, params, mapType);
    }

    /**
     * Initialize Map class,add the event listener's and set the markers and display it
     */
    initialize()
    {
        if (this.params != null &&  typeof this.params['no_map'] === 'undefined') {
            var center = 0;
            if (typeof this.params['map_longitude'] !== 'undefined' && typeof this.params['map_latitude'] !== 'undefined') {
                center = new google.maps.LatLng(this.params['map_latitude'], this.params['map_longitude']);
            }

            var search_styles = [
            	  {
            		    "featureType": "administrative",
            		    "elementType": "geometry",
            		    "stylers": [
            		      {
            		        "visibility": "off"
            		      }
            		    ]
            		  },
            		  {
            		    "featureType": "poi",
            		    "stylers": [
            		      {
            		        "visibility": "off"
            		      }
            		    ]
            		  },
            		  {
            		    "featureType": "road",
            		    "elementType": "labels.icon",
            		    "stylers": [
            		      {
            		        "visibility": "off"
            		      }
            		    ]
            		  },
            		  {
            		    "featureType": "transit",
            		    "stylers": [
            		      {
            		        "visibility": "off"
            		      }
            		    ]
            		  }
            		];

            var mapOptions = {
                zoom: this.zoom,
                scrollwheel: false,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                center: center
            };
            
            mapOptions.styles = search_styles;

            var mapdiv = document.getElementById(this.mapDiv);
            mapdiv.style.width = this.mapWidth;
            mapdiv.style.height = this.mapHeight;

            this.map = new google.maps.Map(mapdiv, mapOptions);

            if(this.locations.length>0){
            	this.setMarkers();
        	}

            if (this.params['panorama'] == 1 && this.locations.length>0) {
                var company = this.locations[0];
                var myLatLng = new google.maps.LatLng(company.latitude, company.longitude);

                this.panorama = this.map.getStreetView();
                this.panorama.setPosition(myLatLng);
                this.panorama.setPov(/** @type {google.maps.StreetViewPov} */({
                    heading: 265,
                    pitch: 0
                }));
            }
        }
    }

    /**
     * Set map markers on the map and associate related options and attributes
     */
    setMarkers()
    {
        // Add markers to the map

        // Marker sizes are expressed as a Size of X,Y
        // where the origin of the image (0,0) is located
        // in the top left of the image.

        // Origins, anchor positions and coordinates of the marker
        // increase in the X direction to the right and in
        // the Y direction down.

        var bounds = new google.maps.LatLngBounds();
        var markers = [];
        var self = this;

        var lastMarker = null;
        for (var i = 0; i < this.locations.length; i++) {
            var item = this.locations[i];

            //skip iteration if not defined
            if (item.length == 0 || item === 'undefined') {
                continue;
            }

            var pinImage = null;

            var markerHeight = 32;
            var markerWidth  = 32;

            var anchor_left = markerWidth / 2;
            var anchor_top  = markerHeight;

            let width = 32;
            let height = 37;
            
            let markerSize = jbdUtils.getProperty('marker_size');
            if (markerSize && markerSize.length != 0) {
                width = markerSize.width;
                height = markerSize.height;
            }

            if (typeof item['marker'] !== 'undefined' && item['marker'] != '0') {
                pinImage = {
                    url: item['marker'],
                    scaledSize: new google.maps.Size(width, height),
                    origin: new google.maps.Point(0,0),
                    anchor: new google.maps.Point(anchor_left, anchor_top)
                };
            } 
            else if (jbdUtils.getProperty('mapMarker') && jbdUtils.getProperty('mapMarker').length) {
                pinImage = {
                    url: jbdUtils.getProperty('imageBaseUrl') + jbdUtils.getProperty('mapMarker'),
                    scaledSize: new google.maps.Size(width, height),
                    origin: new google.maps.Point(0,0),
                    anchor: new google.maps.Point(anchor_left, anchor_top)
                }; 
            }

            var myLatLng = new google.maps.LatLng(item['latitude'], item['longitude']);

            //Check Markers array for duplicate position and offset a little
            if (markers.length != 0 && false) {
                for (i = 0; i < markers.length; i++) {
                    var existingMarker = markers[i];
                    var pos = existingMarker.getPosition();
                    var distance = google.maps.geometry.spherical.computeDistanceBetween(myLatLng, pos);
                    if (distance < 50 && false) {
                        var a = 360.0 / markers.length;
                        var newLat = pos.lat() + -.00004 * Math.cos((+a * i) / 180 * Math.PI);  //x
                        var newLng = pos.lng() + -.00004 * Math.sin((+a * i) / 180 * Math.PI);  //Y
                        myLatLng = new google.maps.LatLng(newLat, newLng);
                    }
                }
            }

            var zIndex = 0;
            if (typeof item['zIndex'] !== 'undefined') {
                zIndex = item['zIndex'];
            }

            var marker = new google.maps.Marker({
                position: myLatLng,
                map: this.map,
                icon: pinImage,
                animation: google.maps.Animation.DROP,
                title: item['title'],
                zIndex: zIndex
            });

            if (typeof item['in_range'] !== 'undefined') {
                lastMarker = marker;
            }

            markers.push(marker);
            if(this.markers !== undefined){
            	this.markers.push(marker);
            }else{
            	this.markers = Array(marker);
            }
            
            if (this.params["isLayout"] == 1) {
                (function (Marker) {
                    google.maps.event.addListener(marker, 'click', function () {
                        var target = "#company" + this.getZIndex();
                        window.location = target;

                        jQuery(target).fadeOut(1, function () {
                            jQuery(target).css("background-color", "#469021").fadeIn(500);
                        });

                        setTimeout(function () {
                            jQuery(target).removeClass('selected-company');
                            jQuery(target).fadeOut(1, function () {
                                jQuery(target).css("background-color", "transparent").fadeIn(700);
                            });
                        }, 1200);
                    });
                }(marker));
            } else {
                var contentBody = item['content'];
                var infowindow = new google.maps.InfoWindow({
                    content: contentBody,
                    maxWidth: 210
                });

                google.maps.event.addListener(marker, 'click', function (contentBody) {
                    return function () {
                        infowindow.setContent(contentBody);
                        infowindow.open(this.map, this);
                    }
                }(contentBody));
            }

            bounds.extend(myLatLng);
        }

        if (this.params["isLayout"] == 1) {
            jQuery(".show-marker").click(function () {
                var companyID = jQuery(this).closest('.grid-item-holder').attr('id');
                var id = companyID.match(/\d/g);
                id = id.join('');

                for (i = 0; i < markers.length; i++) {
                    if (markers[i].getZIndex() == id) {
                        self.map.setZoom(16);
                        self.map.setCenter(markers[i].getPosition());
                    }
                }
            });
        }

        if (jbdUtils.getProperty('enable_map_clustering')  == 1 && !this.params['is_admin']) {
            let mcOptions = {
                imagePath: this.params['imagePath'] + "mapcluster/m"
            };
            var markerCluster = new MarkerClusterer(this.map, markers, mcOptions);
        }

        if (this.params["has_location"] == 1 && this.params['longitude'] != '') {
            pinImage = new google.maps.MarkerImage("https://maps.google.com/mapfiles/kml/shapes/library_maps.png",
                new google.maps.Size(31, 34),
                new google.maps.Point(0, 0),
                new google.maps.Point(10, 34)
            );

            myLatLng = new google.maps.LatLng(this.params["latitude"], this.params["longitude"]);
            marker = new google.maps.Marker({
                position: myLatLng,
                map: this.map,
                icon: pinImage
            });

            lastMarker = marker;
        }

        if (this.params["radius"] > 0) {
            // Add circle overlay and bind to marker

            if (typeof this.params['longitude'] == 'undefined' && typeof this.params['latitude'] == 'undefined' || this.params['longitude'] == '') {
                this.params['longitude'] = this.params['map_longitude'];
                this.params['latitude'] = this.params['map_latitude'];
            }

            if (typeof this.params['longitude'] !== 'undefined' && typeof this.params['latitude'] !== 'undefined' && this.params['longitude'] !== '') {
                this.map.setCenter(new google.maps.LatLng(this.params['latitude'], this.params['longitude']));
                var circle = new google.maps.Circle({
                    map: this.map,
                    radius: this.params['radius'] * 1600,
                    strokeColor: "#006CD9",
                    strokeOpacity: 0.7,
                    strokeWeight: 2,
                    fillColor: "#006CD9",
                    fillOpacity: 0.15
                });

                if (this.params["has_location"] == 1 || lastMarker != null) {
                    circle.bindTo('center', lastMarker, 'position');
                }
            }
        }

        bounds.extend(myLatLng);
        if (jbdUtils.getProperty('map_enable_auto_locate') == 1) {
            this.map.fitBounds(bounds);
        }

        var listener = google.maps.event.addListener(this.map, "idle", function () {
            if (self.map.getZoom() > 16) self.map.setZoom(16);
            google.maps.event.removeListener(listener);
        });
    }

    /**
     * Add toggle animation for the markers when added on map
     *
     * @param marker object map marker
     */
    toggleBounce(marker)
    {
        if (marker.getAnimation() !== null) {
            marker.setAnimation(null);
        } else {
            marker.setAnimation(google.maps.Animation.BOUNCE);
        }
    }

    /**
     * Add toggle street view for the location on map
     */
    toggleStreetView()
    {
        var toggle = this.panorama.getVisible();
        if (toggle == false) {
            this.panorama.setVisible(true);
        } else {
            this.panorama.setVisible(false);
        }
    }

    /**
     * Move to location when setting markers
     *
     * @param location array with location settings
     */
    moveToLocation(location)
    {
        var center = new google.maps.LatLng(location.latitude, location.longitude);
        this.map.panTo(center);
    }

    /**
     * Add marker on map and associate the callback with it
     * @param location array location lat and lang
     * @param callback callback function
     */
    addMarker(location, callback)
    {
        var loc = new google.maps.LatLng(location.latitude, location.longitude);
        var marker = new google.maps.Marker({
            position: loc,
            map: this.map
        });
        this.markers.push(marker);

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
            this.markers[i].setMap(null);
        }
        this.markers = [];
    }

    /**
     * Add map listener
     *
     * @param event string event type to associate with the event
     * @param action function to call
     */
    addMapListener(event, action)
    {
        google.maps.event.addListener(this.map, event, function (e) {
            var location = {};
            location.latitude = e.latLng.lat();
            location.longitude = e.latLng.lng();
            action(location);
        });
    };

    /**
     * Initiate map autocomplete so when locations are searched on input field suggestions will be shown
     *
     * @param element object javascript object of input field
     * @param action function called
     * @param preventSubmit bool prevent submit form
     * @param focus_view
     * @param limitedCountries array with only the countries that are allowed to search on
     * @param componentForm string component form name
     */
    static initAutocomplete(element, action, preventSubmit, focus_view, limitedCountries, componentForm)
    {
        var self = this;
        var place;
        var autocomplete;

        if (typeof limitedCountries !== 'undefined' && limitedCountries !== null) {
            autocomplete = new google.maps.places.Autocomplete(element, {
                types: ['geocode'],
                componentRestrictions: {
                    country: limitedCountries
                }
            });
        } else {
            autocomplete = new google.maps.places.Autocomplete(element, {
                types: ['geocode']
            });
        }

        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            place = autocomplete.getPlace();
            action(JBDGoogleMap.formatSuggestionResponse(place, componentForm));

            if (focus_view) {
                if (typeof jbdAdminMapInstance !== 'undefined') {
                    jbdAdminMapInstance.autocompleteFocus(place);
                }
            }
        });

        if (preventSubmit) {
            google.maps.event.addDomListener(element, 'keydown', function (e) {
                if (e.keyCode == 13 && jQuery('.pac-container:visible').length) {
                    e.preventDefault();
                }
            });
        }
    }

    /**
     * Add marker for the location search and focus map on it
     *
     * @param place object map location
     */
    autocompleteFocus(place)
    {
        this.map.setCenter(place.geometry.location);

        if (place.geometry.viewport) {
            this.map.fitBounds(place.geometry.viewport);
        }

        let loc = {};
        loc.latitude = place.geometry.location.lat();
        loc.longitude = place.geometry.location.lng();

        this.clearMarkers();
        this.addMarker(loc);
    }

    /**
     * Focuses the given location.
     *  
     * @param {latitude, longitude, clearMarkers, addMarker} params 
     */
    focusLocation(params)
    {
        var loc = new google.maps.LatLng(params.latitude, params.longitude);
        var marker = new google.maps.Marker({
            position: loc,
            map: this.map
        });

        this.map.setCenter(marker.getPosition());

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
        var geocoder = new google.maps.Geocoder();
        var latlng = new google.maps.LatLng(location.latitude, location.longitude);
        var country;

        geocoder.geocode({'latLng': latlng}, function(results, status) {
            if (status === google.maps.GeocoderStatus.OK) {
                if (results[1]) {
                    //find country
                    for (var i=0; i<results[0].address_components.length; i++) {
                        for (var b=0;b<results[0].address_components[i].types.length;b++) {
                            if (results[0].address_components[i].types[b] === "country") {
                                //get the country data
                                country = results[0].address_components[i];
                                break;
                            }
                        }
                    }
                    //set country code
                    action(country.short_name);
                }
            }
        });
    }

    /**
     * Response handle for the function loadInitAutocomplete
     * @param place object response
     * @param component_form string component form name
     * @returns {[]}
     */
    static formatSuggestionResponse(place, component_form)
    {
        var suggestionResult = [];

        let config = JSON.parse(jbdUtils.getProperty('autocomplete_config'));

        let addressComponents = [];
        for (let i = 0; i < place.address_components.length; i++) {
            addressComponents[place.address_components[i].types[0]] = place.address_components[i];
        }

        for (let component in addressComponents) {
        	if (addressComponents.hasOwnProperty(component)){
	            for (let att in config.google) {
	                for (let i = 0; i < config.google[att].length; i++) {
	                	let alternative = config.google[att][i];
	                    if (typeof addressComponents[alternative] !== 'undefined') {
	                        suggestionResult[att] = addressComponents[alternative]["long_name"];
	                        break;
	                    }
	                }
	            }
        	}
        }

        suggestionResult['latitude']  = place.geometry.location.lat();
        suggestionResult['longitude'] = place.geometry.location.lng();

        return suggestionResult;
    }
}