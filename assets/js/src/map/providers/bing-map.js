/**
 * JBD Bing map class
 */
class JBDBingMap extends JBDMapAbstract
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

    	this.bounds = {
	        "maxLat": -999,
	        "minLng": 999,
	        "minLat": 999,
	        "maxLng": -999
	    };

        if (typeof this.params !== 'undefined' && this.params != null) {
            if (typeof this.params['key'] !== 'undefined') {
                this.key = this.params['key'];
            }
        }
    }

    /**
     * Checks if map is loaded and if it is not then initialize it
     */
    checkifMapLoaded() {
		var flag = false;
				
		if (typeof(Microsoft.Maps.Location) === "function" && typeof(Microsoft.Maps.Location.prototype) === "object") {
			flag = true;
		}
		
		if(flag == false) {
		   window.setTimeout(this.checkifMapLoaded.bind(this), 500); /* this checks the flag every 100 milliseconds*/
		}else{
			console.debug("Microsoft.Maps.Location exists");
			this.initializeMap();
		} 
	}

    /**
     * Used to call checkifMapLoaded function
     */
    initialize(){
		this.checkifMapLoaded();
    }

    /**
     * Initialize Map class,add the event listener's and set the markers and display it
     */
    initializeMap()
    {
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

            if (this.eventParams != null) {
                this.addMapListener(this.eventParams.event, this.eventParams.action);
            }

            if(this.locations.length>0){
            	this.setMarkers();
        	}
            
            var self = this;
            if (jbdUtils.getProperty('enable_map_clustering') == 1 && !this.params['is_admin']) {
                Microsoft.Maps.loadModule("Microsoft.Maps.Clustering", function () {
                    var clusterLayer = new Microsoft.Maps.ClusterLayer(self.markers);
                    self.map.layers.insert(clusterLayer);
                });
            }

            if (jbdUtils.getProperty('map_enable_auto_locate') == 1) {
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
    }

    /**
     * Set map markers on the map and associate related options and attributes
     */
    setMarkers()
    {
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

            let markerImage = '';
            if (jbdUtils.getProperty('enable_map_clustering') != 1 || !this.params['is_admin']) {
                if (item.marker != '0') {
                    markerImage = item.marker;
                } else if (jbdUtils.getProperty('mapMarker') && jbdUtils.getProperty('mapMarker').length) {
                    markerImage = jbdUtils.getProperty('imageBaseUrl') + jbdUtils.getProperty('mapMarker');
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
            
            if (this.markers !== undefined) {
            	this.markers.push(marker);
            } else{
            	this.markers = Array(marker);
            }

            if (jbdUtils.getProperty('enable_map_clustering') != 1 || !this.params['is_admin']) {
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
    }

    /**
     * Add a circle on the map
     *
     * @param center array with lat and lang
     * @param circle circle with the radius set
     * @param radius radius from the center
     */
    setCircle(center, circle, radius)
    {
        //Calculate circle locations.
        var locs = Microsoft.Maps.SpatialMath.getRegularPolygon(circle.metadata.center, radius, 36, Microsoft.Maps.SpatialMath.DistanceUnits.Miles);

        //Update the circles location.
        circle.setLocations(locs);
        this.map.entities.push(circle);
    }

    /**
     * Move to location when setting markers
     *
     * @param location array with location settings
     */
    moveToLocation(location)
    {
        this.map.setView({
            center: new Microsoft.Maps.Location(location.latitude, location.longitude)
        });
    }

    /**
     * Add marker on map and associate the callback with it
     * @param location array location lat and lang
     * @param callback callback function
     */
    addMarker(location, callback)
    {
        var marker = new Microsoft.Maps.Pushpin(new Microsoft.Maps.Location(location.latitude, location.longitude));
        this.map.entities.push(marker);
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
        for (var i = this.map.entities.getLength() - 1; i >= 0; i--) {
            var pushpin = this.map.entities.get(i);
            if (pushpin instanceof Microsoft.Maps.Pushpin) {
                this.map.entities.removeAt(i);
            }
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
        Microsoft.Maps.Events.addHandler(this.map, event, function (e) {
            if (e.targetType === "map") {
                var point = new Microsoft.Maps.Point(e.getX(), e.getY());
                var location = e.target.tryPixelToLocation(point);
                action(location);
            }
        });

        this.eventParams = null;
    }

    /**
     * Trigger the autocomplete so when locations are searched on input field suggestions will be shown
     *
     * @param element object javascript object of input field
     * @param action function called
     * @param preventSubmit bool prevent submit form
     * @param focus_view
     * @param limitedCountries array with only the countries that are allowed to search on
     */
    static triggerAutocomplete(element, action, preventSubmit, focus_view, limitedCountries) {
		var flag = false;
		
		if (typeof(Microsoft.Maps.Location) === "function" && typeof(Microsoft.Maps.Location.prototype) === "object"
			&& typeof(Microsoft.Maps.loadModule) === "function" && typeof(Microsoft.Maps.loadModule.prototype) === "object") {
			flag = true;
		}
		
		if (flag == false) {
		   window.setTimeout(this.triggerAutocomplete.bind(this,element, action, preventSubmit, focus_view, limitedCountries), 500); /* this checks the flag every 100 milliseconds*/
		} else {
			console.debug("Microsoft.Maps.loadModule exists");
			JBDBingMap.loadInitAutocomplete(element, action, preventSubmit, focus_view, limitedCountries);
		} 
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
    static initAutocomplete(element, action, preventSubmit, focus_view, limitedCountries){
		JBDBingMap.triggerAutocomplete(element, action, preventSubmit, focus_view, limitedCountries);
    }

    /**
     * Load locations that are searched on input field suggestions
     *
     * @param element object javascript object of input field
     * @param action function called
     * @param preventSubmit bool prevent submit form
     * @param focus_view
     * @param limitedCountries array with only the countries that are allowed to search on
     */
    static loadInitAutocomplete(element, action, preventSubmit, focus_view, limitedCountries)
    {
		/*console.debug("loadInitAutocomplete");
		console.debug(element);
		console.debug(action);
		console.debug(preventSubmit);
		console.debug(focus_view);
*/		
        if (typeof focus_view === 'undefined') {
            focus_view = false;
        }
        
        Microsoft.Maps.loadModule('Microsoft.Maps.AutoSuggest', function () {
            var options = {
                maxResults: 5
            };

            var elementId = jQuery(element).attr('id');
            var parentId = jQuery(element).parent().attr('id');
            var manager = new Microsoft.Maps.AutosuggestManager(options);

            if (typeof mapInstance === 'undefined' && typeof jbdAdminMapInstance === 'undefined') {
                jQuery('#'+parentId).append('<div style="display:none" id="bing-fake-autocomplete"></div>');  
                let map = new Microsoft.Maps.Map(document.getElementById('bing-fake-autocomplete'));
            }

            manager.attachAutosuggest('#' + elementId, '#' + parentId, function (result) {
                if (focus_view) {
                    if (typeof jbdAdminMapInstance !== 'undefined') {
                        jbdAdminMapInstance.autocompleteFocus(result);
                    }
                }

                action(JBDBingMap.formatSuggestionResponse(result));
                jQuery('#as_container').css('visibility', 'hidden');
            });
        });
    }

    /**
     * Add marker for the location search and focus map on it
     *
     * @param result object map location
     */
    autocompleteFocus(result)
    {
        this.clearMarkers();
        this.addMarker(result.location);
        this.map.setView({bounds: result.bestView});
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

        this.map.setView({
            center: new Microsoft.Maps.Location(params.latitude, params.longitude)
        });

        if (typeof params.clearMarkers === 'undefined' || !params.clearMarkers) {
            this.clearMarkers();
        }

        if (typeof params.addMarker === 'undefined' || params.addMarker) {
            this.addMarker(loc);
        }
    }

    /**
     * Response handle for the function loadInitAutocomplete
     * @param place object response
     * @returns {[]}
     */
    static formatSuggestionResponse(place)
    {
        var suggestionResult = [];

        let config = JSON.parse(jbdUtils.getProperty('autocomplete_config'));

        for (let att in config.bing) {
            if (config.bing.hasOwnProperty(att)) {
                for (let i = 0; i < config.bing[att].length; i++) {
                    let alternative = config.bing[att][i];
                    if (typeof place.address[alternative] !== 'undefined') {
                        suggestionResult[att] = place.address[alternative];
                        break;
                    }
                }
            }
        }

        suggestionResult["latitude"] = place.location.latitude;
        suggestionResult["longitude"] = place.location.longitude;

        return suggestionResult;
    }
}