JBDOpenMap = function () {
    this.map = null;
    this.infobox = null;
    this.infoboxTemplate = "<div style='overflow:hidden;'>{content}</div>";
    this.markers = [];
    this.bounds = {
        "maxLat": -999,
        "minLng": 999,
        "minLat": 999,
        "maxLng": -999
    };

    this.construct = function (locations, params, mapType) {
        jbdMap.construct.call(this, locations, params, mapType);
    };

    this.initialize = function () {
        if (this.params != null && typeof this.params['no_map'] === 'undefined') {
            var parent = jQuery('#' + this.mapDiv).parent();
            jQuery('#' + this.mapDiv).remove();
            parent.append
            (
                '<div id="' + this.mapDiv + '" ' +
                'style="width:' + this.mapWidth + ';height:' + this.mapHeight + ';z-index:1">' +
                '</div>'
            );

            this.map = L.map(this.mapDiv).setView(
                [this.params['map_latitude'], this.params['map_longitude']],
                this.zoom
            );

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(this.map);

            if(this.locations.length>0){
            	this.setMarkers();
        	}
            
            this.map.invalidateSize(false);

            if (this.params['autolocate'] == 1) {
                if (this.bounds.maxLat > -999) {
                    this.map.fitBounds([
                        [this.bounds.maxLat, this.bounds.maxLng],
                        [this.bounds.minLat, this.bounds.minLng]
                    ]);
                }
            }
        }
    };

    this.setMarkers = function () {
        var markerClusters;
        if (this.params["map_clustering"] == 1) {
            markerClusters = L.markerClusterGroup();
        }

        var lastMarker = null;
        for (var i = 0; i < this.locations.length; i++) {
            var item = this.locations[i];

            //skip iteration if not defined
            if (item.length == 0 || item === 'undefined') {
                continue;
            }

            this.bounds.maxLat = this.bounds.maxLat < parseFloat(item['latitude']) ? parseFloat(item['latitude']) : this.bounds.maxLat;
            this.bounds.minLat = this.bounds.minLat > parseFloat(item['latitude']) ? parseFloat(item['latitude']) : this.bounds.minLat;
            this.bounds.maxLng = this.bounds.maxLng < parseFloat(item['longitude']) ? parseFloat(item['longitude']) : this.bounds.maxLng;
            this.bounds.minLng = this.bounds.minLng > parseFloat(item['longitude']) ? parseFloat(item['longitude']) : this.bounds.minLng;

            var markerImage;
            if (this.params["map_clustering"] != 1) {
                if (item.marker != '0') {
                    markerImage = item.marker;
                } else if (jbdUtils.mapMarker && jbdUtils.mapMarker.length) {
                    markerImage = jbdUtils.imageBaseUrl + jbdUtils.mapMarker;
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

                marker.addTo(markersLayer);

                var self = this;
                jQuery(".btn-show-marker").click(function () {
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

            if (this.params["map_clustering"] != 1) {
                marker.addTo(this.map);
            }

            if (markerImage !== "" && typeof markerImage !== 'undefined') {
                var icon = L.icon({
                    iconUrl: markerImage
                });

                marker.setIcon(icon);
            }

            if (this.params["map_clustering"] == 1) {
                markerClusters.addLayer(marker)
            }

            this.markers.push(marker);

            if (typeof item['in_range'] !== 'undefined') {
                lastMarker = marker;
            }
        }

        if (this.params["map_clustering"] == 1) {
            this.map.addLayer(markerClusters);
        }

        if (this.params["has_location"] == 1) {
            var myLatLng = {
                latitude: this.params["latitude"],
                longitude: this.params["longitude"]
            };

            this.addMarker(myLatLng);

            lastMarker = this.markers.pop();
            var tmpIcon = L.icon({
                iconUrl: 'https://maps.google.com/mapfiles/kml/shapes/library_maps.png'
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
    };

    this.moveToLocation = function (location) {
        this.map.setView([location.latitude, location.longitude]);
    };

    this.addMarker = function (location, callback) {
        var marker = L.marker([location.latitude, location.longitude]).addTo(this.map);
        this.markers.push(marker);

        if (typeof callback !== 'undefined') {
            callback();
        }
    };

    this.clearMarkers = function () {
        for (var i = 0; i < this.markers.length; i++) {
            this.map.removeLayer(this.markers[i]);
        }
    };

    this.addMapListener = function (event, action) {
        this.map.on(event, function (e) {
            var location = {};
            location.latitude = e.latlng.lat;
            location.longitude = e.latlng.lng;
            action(location);
        });
    };

    this.initAutocomplete = function (element, action, preventSubmit, focus_view, limitedCountries) {
        var self = this;

        jQuery(element).keyup(function (e) {
            var query = jQuery(element).val();
            var countyLimit = '';
            if (typeof limitedCountries !== 'undefined') {
                if (limitedCountries.length > 0) {
                    countyLimit = '&countrycodes=' + limitedCountries.join();
                }
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
                                if (typeof self.params['no_map'] === 'undefined') {
                                    self.clearMarkers();
                                    var loc = {};
                                    loc.latitude = res[i].lat;
                                    loc.longitude = res[i].lon;
                                    self.addMarker(loc);
                                    self.moveToLocation(loc);
                                }
                                action(self.formatSuggestionResponse(res[i]));
                            }
                        }
                    }
                }
            });
        });
    };

    this.reverseGeocoding = function (location, action) {
        var lon = location.longitude;
        var lat = location.latitude;
        var response;

        var url = "https://nominatim.openstreetmap.org/reverse.php?format=json&lat=" + lat + "&lon=" + lon;
        var self = this;

        jQuery.ajax({
            type: "GET",
            url: url,
            dataType: 'json',
            success: function (data) {
                response = self.formatSuggestionResponse(data);

                action(response.country_code);
            }
        });
    };

    this.formatSuggestionResponse = function (place) {
        var suggestionResult = [];

        var locality = '';
        if (typeof place.address.city !== 'undefined') {
            locality = place.address.city;
        } else if (typeof place.address.town !== 'undefined') {
            locality = place.address.town;
        }

        var street_number = '';
        if (typeof place.address.street_number !== 'undefined') {
            street_number = place.address.street_number;
        } else if (typeof place.address.house_number !== 'undefined') {
            street_number = place.address.house_number;
        }

        var street = '';
        if (typeof place.address.street !== 'undefined') {
            street = place.address.street;
        } else if (typeof place.address.road !== 'undefined') {
            street = place.address.road;
        } else if (typeof place.address.suburb !== 'undefined') {
            street = place.address.suburb;
        }

        suggestionResult["route"] = street;
        suggestionResult["street_number"] = street_number;
        suggestionResult["locality"] = locality;
        suggestionResult["country"] = place.address.country;
        suggestionResult["administrative_area_level_1"] = place.address.county;
        suggestionResult["administrative_area_level_2"] = place.address.state;
        suggestionResult["postal_code"] = place.address.postcode;
        suggestionResult["latitude"] = place.lat;
        suggestionResult["longitude"] = place.lon;
        suggestionResult["country_code"] = place.address.country_code;

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