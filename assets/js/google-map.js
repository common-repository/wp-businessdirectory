JBDGoogleMap = function () {
    this.map = null;
    this.panorama = null;
    this.markers = [];

    this.construct = function (locations, params, mapType) {
        jbdMap.construct.call(this, locations, params, mapType);
    };

    this.initialize = function () {
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
    };

    this.setMarkers = function () {
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

            if (typeof item['marker'] !== 'undefined' && item['marker'] != '0') {
                pinImage = {
                    url: item['marker'],
                    scaledSize: new google.maps.Size(32, 37),
                    origin: new google.maps.Point(0,0),
                    anchor: new google.maps.Point(anchor_left, anchor_top)
                };
            }
            else if (jbdUtils.mapMarker && jbdUtils.mapMarker.length) {
                pinImage = {
                    url: jbdUtils.imageBaseUrl + jbdUtils.mapMarker,
                    scaledSize: new google.maps.Size(32, 37),
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
            this.markers.push(marker);
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
            jQuery(".btn-show-marker").click(function () {
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

        if (this.params["map_clustering"] == 1) {
            mcOptions = {
                imagePath: this.params['imagePath'] + "mapcluster/m"
            };
            var markerCluster = new MarkerClusterer(this.map, markers, mcOptions);
        }

        if (this.params["has_location"] == 1 && this.params['longitude'] != '') {
            var pinImage = new google.maps.MarkerImage("https://maps.google.com/mapfiles/kml/shapes/library_maps.png",
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
        if (this.params['autolocate'] == 1) {
            this.map.fitBounds(bounds);
        }

        var listener = google.maps.event.addListener(this.map, "idle", function () {
            if (self.map.getZoom() > 16) self.map.setZoom(16);
            google.maps.event.removeListener(listener);
        });
    };

    this.toggleBounce = function (marker) {
        if (marker.getAnimation() !== null) {
            marker.setAnimation(null);
        } else {
            marker.setAnimation(google.maps.Animation.BOUNCE);
        }
    };

    this.toggleStreetView = function () {
        var toggle = this.panorama.getVisible();
        if (toggle == false) {
            this.panorama.setVisible(true);
        } else {
            this.panorama.setVisible(false);
        }
    };

    this.moveToLocation = function (location) {
        var center = new google.maps.LatLng(location.latitude, location.longitude);
        this.map.panTo(center);
    };

    this.addMarker = function (location, callback) {
        var loc = new google.maps.LatLng(location.latitude, location.longitude);
        var marker = new google.maps.Marker({
            position: loc,
            map: this.map
        });
        this.markers.push(marker);

        if (typeof callback !== 'undefined') {
            callback();
        }
    };

    this.clearMarkers = function () {
        for (var i = 0; i < this.markers.length; i++) {
            this.markers[i].setMap(null);
        }
        this.markers = [];
    };

    this.addMapListener = function (event, action) {
        google.maps.event.addListener(this.map, event, function (e) {
            var location = {};
            location.latitude = e.latLng.lat();
            location.longitude = e.latLng.lng();
            action(location);
        });
    };

    this.initAutocomplete = function (element, action, preventSubmit, focus_view, limitedCountries) {
        var self = this;
        var place;
        var autocomplete;

        if (typeof limitedCountries !== 'undefined') {
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
            action(self.formatSuggestionResponse(place));

            if (typeof self.params['no_map'] === 'undefined') {
                if (place.geometry.viewport) {
                    self.map.fitBounds(place.geometry.viewport);
                }

                self.map.setCenter(place.geometry.location);
                var loc = {};
                loc.latitude = place.geometry.location.lat();
                loc.longitude = place.geometry.location.lng();
                self.clearMarkers();
                self.addMarker(loc);
            }
        });

        if (preventSubmit) {
            google.maps.event.addDomListener(element, 'keydown', function (e) {
                if (e.keyCode == 13 && jQuery('.pac-container:visible').length) {
                    e.preventDefault();
                }
            });
        }
    };

    this.reverseGeocoding = function(location, action) {
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
    };

    this.formatSuggestionResponse = function (place) {
        var suggestionResult = [];

        if (typeof component_form !== 'undefined') {
            for (var j = 0; j < place.address_components.length; j++) {
                var att = place.address_components[j].types[0];

                suggestionResult[att] = place.address_components[j][component_form[att]];
            }
        }

        if (typeof suggestionResult['locality'] === 'undefined') {
            suggestionResult['locality'] = suggestionResult['administrative_area_level_1'];
        }

        suggestionResult['latitude']  = place.geometry.location.lat();
        suggestionResult['longitude'] = place.geometry.location.lng();

        return suggestionResult;
    };

    this.hasMap = function () {
        return typeof this.params['no_map'] === 'undefined';
    };

    this.getMap = function () {
        return this.map;
    };

    this.getMapId = function () {
        if (typeof this.params['tmapId'] !== 'undefined') {
            return this.params['tmapId'];
        } else {
            return null;
        }
    };

    this.getInstance = function () {
        return this;
    };
};