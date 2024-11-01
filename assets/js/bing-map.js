JBDBingMap = function () {
    this.key = null;
    this.map = null;
    this.infobox = null;
    this.markers = [];
    this.eventParams = null;
    this.autocompleteParams = null;
    this.callbackStatus = false;
    this.bounds = {
        "maxLat": -999,
        "minLng": 999,
        "minLat": 999,
        "maxLng": -999
    };

    this.construct = function (locations, params, mapType) {
        jbdMap.construct.call(this, locations, params, mapType);

        if (typeof this.params !== 'undefined' && this.params != null) {
            if (typeof this.params['key'] !== 'undefined') {
                this.key = this.params['key'];
            }
        }
    };

    this.initialize = function () {
        if (this.params != null && typeof this.params['no_map'] === 'undefined') {
            var mapdiv = document.getElementById(this.mapDiv);
            mapdiv.style.width = this.mapWidth;
            mapdiv.style.height = this.mapHeight;

            this.map = new Microsoft.Maps.Map('#' + this.mapDiv, {
                credentials: this.key,
                center: new Microsoft.Maps.Location(this.params['map_latitude'], this.params['map_longitude']),
                mapTypeId: Microsoft.Maps.MapTypeId.ROAD,
                zoom: this.zoom
            });

            if (this.callbackStatus) {
                if (this.eventParams != null) {
                    this.addMapListener(this.eventParams.event, this.eventParams.action);
                }

                if (this.autocompleteParams != null) {
                    this.initAutocomplete(this.autocompleteParams.element, this.autocompleteParams.action, this.autocompleteParams.preventSubmit);
                }
            }

            if(this.locations.length>0){
            	this.setMarkers();
        	}
            
            var self = this;
            if (this.params["map_clustering"] == 1) {
                Microsoft.Maps.loadModule("Microsoft.Maps.Clustering", function () {
                    var clusterLayer = new Microsoft.Maps.ClusterLayer(self.markers);
                    self.map.layers.insert(clusterLayer);
                });
            }

            if (this.params['autolocate'] == 1 && this.callbackStatus) {
                if (this.bounds.maxLat > -999) {
                    var box = new Microsoft.Maps.LocationRect.fromEdges(
                        this.bounds.maxLat,
                        this.bounds.minLng,
                        this.bounds.minLat,
                        this.bounds.maxLng
                    );

                    if (box.height != 0) {
                        this.map.setView({
                            bounds: box,
                            zoom: this.map.getZoom()
                        });
                    }
                }
            }
        }
    };

    this.setMarkers = function () {
        var self = this;

        var lastMarker = null;
        for (var i = 0; i < this.locations.length; i++) {
            var item = this.locations[i];

            //skip iteration if not defined
            if (item.length == 0 || item === 'undefined') {
                continue;
            }

            var marker = new Microsoft.Maps.Pushpin(new Microsoft.Maps.Location(item['latitude'], item['longitude']), 54);

            this.bounds.maxLat = this.bounds.maxLat <  parseFloat(item['latitude']) ?  parseFloat(item['latitude']) : this.bounds.maxLat;
            this.bounds.minLat = this.bounds.minLat >  parseFloat(item['latitude']) ?  parseFloat(item['latitude']) : this.bounds.minLat;
            this.bounds.maxLng = this.bounds.maxLng <  parseFloat(item['longitude']) ?  parseFloat(item['longitude']) : this.bounds.maxLng;
            this.bounds.minLng = this.bounds.minLng >  parseFloat(item['longitude']) ?  parseFloat(item['longitude']) : this.bounds.minLng;

            var markerImage;
            if (this.params["map_clustering"] != 1) {
                if (item.marker != '0') {
                    markerImage = item.marker;
                } else if (jbdUtils.mapMarker && jbdUtils.mapMarker.length) {
                    markerImage = jbdUtils.imageBaseUrl + jbdUtils.mapMarker;
                }
            }

            if (markerImage !== "") {
                marker.setOptions({
                    icon: markerImage
                });
            }

            var center = this.map.getCenter();
            this.infobox = new Microsoft.Maps.Infobox(center, {
                maxWidth: 263,
                maxHeight: 645,
                visible: false
            });

            marker.metadata = {
                description: 'description'
            };
            marker.metadata.description = item.content;

            var zIndex = 0;
            if (typeof item['zIndex'] !== 'undefined') {
                zIndex = item['zIndex'];
            }
            marker.metadata.zIndex = zIndex;

            var markerFunction  = function (e) {
                if (e.target.metadata.description.length > 0) {
                    self.infobox.setOptions({
                        location: e.target.getLocation(),
                        description: e.target.metadata.description,
                        visible: true
                    });
                }

                self.moveToLocation(e.target.getLocation());
            };

            if (this.params["isLayout"] == 1) {
                markerFunction  = function (e) {
                    var target = "#company" + e.target.metadata.zIndex;
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
                };
            }

            Microsoft.Maps.Events.addHandler(marker, 'mousedown', markerFunction);
            this.infobox.setMap(this.map);
            this.markers.push(marker);

            if (this.params["map_clustering"] != 1) {
                this.map.entities.push(marker);
            }

            if (typeof item['in_range'] !== 'undefined') {
                lastMarker = marker;
            }
        }

        if (this.params["has_location"] == 1 && this.params['longitude'] != '') {
            var myLatLng = {
                latitude: this.params["latitude"],
                longitude: this.params["longitude"]
            };

            this.addMarker(myLatLng);

            lastMarker = this.markers.pop();
            lastMarker.setOptions({
                icon: 'https://maps.google.com/mapfiles/kml/shapes/library_maps.png'
            });
        }

        if (this.params["radius"] > 0) {
            if (typeof this.params['longitude'] == 'undefined' && typeof this.params['latitude'] == 'undefined' || this.params['longitude'] == '') {
                this.params['longitude'] = this.params['map_longitude'];
                this.params['latitude'] = this.params['map_latitude'];
            }

            Microsoft.Maps.loadModule('Microsoft.Maps.SpatialMath', function () {
                if (typeof self.params['map_longitude'] !== 'undefined' && typeof self.params['map_latitude'] !== 'undefined') {
                    if (self.params['has_location'] == 1 || lastMarker != null) {
                        var center = lastMarker.getLocation();

                        var circle = new Microsoft.Maps.Polygon(center, center, center);
                        circle.metadata = {
                            center: center
                        };

                        self.setCircle(center, circle, self.params["radius"]);
                    }
                }
            });
        }
    };

    this.setCircle = function (center, circle, radius) {
        //Calculate circle locations.
        var locs = Microsoft.Maps.SpatialMath.getRegularPolygon(circle.metadata.center, radius, 36, Microsoft.Maps.SpatialMath.DistanceUnits.Miles);

        //Update the circles location.
        circle.setLocations(locs);
        this.map.entities.push(circle);
    };

    this.moveToLocation = function (location) {
        this.map.setView({
            center: new Microsoft.Maps.Location(location.latitude, location.longitude)
        });
    };

    this.addMarker = function (location, callback) {
        var marker = new Microsoft.Maps.Pushpin(new Microsoft.Maps.Location(location.latitude, location.longitude));
        this.map.entities.push(marker);
        this.markers.push(marker);

        if (typeof callback !== 'undefined') {
            callback();
        }
    };

    this.clearMarkers = function () {
        for (var i = this.map.entities.getLength() - 1; i >= 0; i--) {
            var pushpin = this.map.entities.get(i);
            if (pushpin instanceof Microsoft.Maps.Pushpin) {
                this.map.entities.removeAt(i);
            }
        }

        this.markers = [];
    };

    this.addMapListener = function (event, action) {
        if (this.callbackStatus) {
            Microsoft.Maps.Events.addHandler(this.map, event, function (e) {
                if (e.targetType === "map") {
                    var point = new Microsoft.Maps.Point(e.getX(), e.getY());
                    var location = e.target.tryPixelToLocation(point);
                    action(location);
                }
            });

            this.eventParams = null;
        } else {
            var params = {};
            params.event = event;
            params.action = action;
            this.eventParams = params;
        }
    };

    this.initAutocomplete = function (element, action, preventSubmit, focus_view, limitedCountries) {
        if (typeof focus_view === 'undefined') {
            focus_view = false;
        }

        if (this.callbackStatus) {
            var self = this;
            Microsoft.Maps.loadModule('Microsoft.Maps.AutoSuggest', function () {
                var options = {
                    maxResults: 5,
                    map: self.map
                };

                var elementId = jQuery(element).attr('id');
                var parentId = jQuery(element).parent().attr('id');
                var manager = new Microsoft.Maps.AutosuggestManager(options);
                manager.attachAutosuggest('#' + elementId, '#' + parentId, function (result) {
                    if (focus_view) {
                        self.clearMarkers();
                        self.addMarker(result.location);
                        self.map.setView({bounds: result.bestView});
                    }

                    action(self.formatSuggestionResponse(result));
                    jQuery('#as_container').css('visibility', 'hidden');
                });
            });
        } else {
            var params = {};
            params.element = element;
            params.action = action;
            params.preventSubmit = preventSubmit;
            this.autocompleteParams = params;
        }
    };

    this.formatSuggestionResponse = function (place) {
        var suggestionResult = [];

        suggestionResult["country"] = place.address.countryRegion;
        suggestionResult["locality"] = place.address.locality;
        suggestionResult["street_number"] = place.address.addressLine;
        suggestionResult["administrative_area_level_1"] = place.address.adminDistrict;
        suggestionResult["administrative_area_level_2"] = place.address.district;
        suggestionResult["postal_code"] = place.address.postalCode;
        suggestionResult["latitude"] = place.location.latitude;
        suggestionResult["longitude"] = place.location.longitude;

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

    this.setCallbackStatus = function (bool) {
        this.callbackStatus = bool
    };
};