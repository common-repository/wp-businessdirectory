JBDMap = function () {
    this.locations = null;
    this.params = null;
    this.mapType = null;
    this.mapDiv = null;
    this.mapWidth = null;
    this.mapHeight = null;
    this.zoom = null;
    this.callbackStatus = false;
    this.mapInstances = [];

    this.construct = function (locations, params, mapType) {
        this.locations = locations;
        this.params = params;
        this.mapType = typeof mapType !== 'undefined' ? mapType : parseInt(jbdUtils.mapType);

        var map_zoom = jbdUtils.mapDefaultZoom;
        if (typeof this.params !== 'undefined' && this.params != null) {
            // default to company
            var mapDivId = "company-map";
            if (typeof this.params['map_div'] !== 'undefined') {
                mapDivId = this.params['map_div'];
            }

            var mapId = '';
            if (typeof this.params['tmapId'] !== 'undefined') {
                mapId = this.params['tmapId'];
            }
            this.mapDiv = mapDivId + '' + mapId;

            if (typeof this.params['map_zoom'] !== 'undefined') {
                map_zoom = this.params['map_zoom'];
            }

            var map_width = '100%';
            var map_height = '450px';
            if (typeof this.params["map_width"] !== 'undefined') {
                map_width = this.params["map_width"];
            }
            if (typeof this.params["map_height"] !== 'undefined') {
                map_height = this.params["map_height"];
            }

            this.mapWidth = map_width;
            this.mapHeight = map_height;
        }

        this.zoom = map_zoom;
    };

    this.loadMapScript = function () {
        var mapObject = null;
        var waitCallback = false;

        if (typeof jbd_map_gdpr !== "undefined") {
            if (!jbd_map_gdpr) {
                this.openMapGDPRModal();
                return false;
            }
        }
        
        switch (this.mapType) {
            case 1:
                if (typeof JBDGoogleMap !== "undefined") {
                    mapObject = new JBDGoogleMap();
                } else {
                    console.warn("Map object not found");
                }
                break;
            case 2:
                if (typeof JBDBingMap !== "undefined") {
                    mapObject = new JBDBingMap();
                } else {
                    console.warn("Map object not found");
                    break;
                }
                if (typeof this.params !== 'undefined') {
                    mapObject.construct(this.locations, this.params, this.mapType);
                    var instance = mapObject.getInstance();
                    // do not create instances with the same configuration twice
                    var found = false;
                    if (this.mapInstances.length > 0) {
                        for (var j = 0; j <= this.mapInstances.length; j++) {
                            if (this.mapInstances.hasOwnProperty(j)) {
                                // identify instance by map ID
                                if ((this.mapInstances[j].getMapId() === this.params['tmapId'])
                                    || (this.mapInstances.length > 0 && typeof this.params['tmapId'] === 'undefined')) {
                                    found = true;
                                }
                            }
                        }
                    }

                    if (!found) {
                        this.mapInstances.push(instance);
                    }
                }
                waitCallback = true;
                break;

            case 3:
                if (typeof JBDOpenMap !== "undefined") {
                    mapObject = new JBDOpenMap();
                } else {
                    console.warn("Map object not found");
                }
                break;

            default:
                if (typeof JBDGoogleMap !== "undefined") {
                    mapObject = new JBDGoogleMap();
                } else {
                    console.warn("Map object not found");
                }
        }

        if (waitCallback && this.callbackStatus) {
            // when callback is ready, initialize the map for all instances
            for (var i in this.mapInstances) {
                if (this.mapInstances.hasOwnProperty(i)) {
                    this.mapInstances[i].setCallbackStatus(true);
                    this.mapInstances[i].initialize();
                }
            }
            this.mapInstances = [];
            this.callbackStatus = false;
        } else if (!waitCallback) {
            mapObject.construct(this.locations, this.params, this.mapType);
            mapObject.initialize();
        }

        return mapObject;
    };

    this.loadMapScriptCallback = function () {
        this.callbackStatus = true;
        return this.loadMapScript();
    };

    this.setMapWidth = function(width) {
        this.mapWidth = width;
    };

    this.setMapHeight = function(height) {
        this.mapHeight = height;
    };

    this.openMapGDPRModal = function() {
        jQuery('#map-gdpr-modal').jbdModal();
    };

    this.acceptMapGDPR = function() {
        var urlAcceptMapGdpr = jbdUtils.url+'&task=companies.acceptMapGDPR';
        var self = this;
        jQuery.ajax({
            type: 'GET',
            url: urlAcceptMapGdpr,
            dataType: 'json',
            success: function(){
                jbd_map_gdpr = true;
                jQuery.jbdModal.close();
                self.loadMapScript();
            }
        });
    }
};