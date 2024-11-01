/**
 * JBD Map helper class
 */
class JBDMapHelper
{

    /**
     * Load map script and initialize their instances with the locations and other params
     *
     * @param locations array locations with their lat and lang for each of them
     * @param params array params with settings
     * @param mapType int map type
     * @returns {null|boolean}
     */
    static loadMapScript(locations, params, mapType)
    {
        let mapObject = null;

        if (typeof jbd_map_gdpr !== "undefined") {
            if (!jbd_map_gdpr) {
                JBDMapHelper.openMapGDPRModal(locations, params, mapType);
                return false;
            }
        }

        if (typeof mapType === "undefined") {
            mapType = jbdUtils.getProperty('mapType');
        }

        mapType = parseInt(mapType);
        switch(mapType) {
            case JBDConstants.MAP_TYPE_GOOGLE:
                if (typeof JBDGoogleMap !== "undefined") {
                    mapObject = new JBDGoogleMap(locations, params, mapType);
                } else {
                    console.warn("Google Map object not found");
                }
                break;

            case JBDConstants.MAP_TYPE_BING:
                if (typeof JBDBingMap !== "undefined") {
                    mapObject = new JBDBingMap(locations, params, mapType);
                } else {
                    console.warn("Bing Map object not found");
                }
                break;

            case JBDConstants.MAP_TYPE_OSM:
                if (typeof JBDOpenMap !== "undefined") {
                    mapObject = new JBDOpenMap(locations, params, mapType);
                } else {
                    console.warn("OSM Map object not found");
                }
                break;

            default:
                if (typeof JBDGoogleMap !== "undefined") {
                    mapObject = new JBDGoogleMap(locations, params, mapType);
                } else {
                    console.warn("Google Map object not found");
                }
        }

        if (typeof JBDMapHelper.map_instances === 'undefined') {
            JBDMapHelper.map_instances = [];
        }

        mapObject.initialize();

        JBDMapHelper.map_instances[mapObject.getMapId()] = mapObject;

        return mapObject;
    }

    /**
     * Return map class based on the type that is requested
     *
     * @param mapType int map type
     * @returns {JBDGoogleMap|JBDBingMap|JBDOpenMap}
     */
    static getMapClass(mapType)
    {
        if (typeof mapType === 'undefined') {
            mapType = jbdUtils.getProperty('mapType');
        }

        mapType = parseInt(mapType);

        let mapClass;
        switch (mapType) {
            case JBDConstants.MAP_TYPE_GOOGLE:
                mapClass = JBDGoogleMap;
                break;
            case JBDConstants.MAP_TYPE_BING:
                mapClass = JBDBingMap;
                break;
            case JBDConstants.MAP_TYPE_OSM:
                mapClass = JBDOpenMap;
                break;

            default:
                mapClass = JBDGoogleMap;
        }

        return mapClass;
    }

    /**
     * Open modal with GDPR details
     *
     * @param locations array locations with their lat and lang for each of them
     * @param params array params with settings
     * @param mapType int map type
     */
    static openMapGDPRModal(locations, params, mapType)
    {
        let modal = jQuery('#map-gdpr-modal');
        modal.jbdModal();
        modal.on('hidden.bs.modal', function () {
            return false;
        });

        jQuery('#gdpr-deny-btn').on('click', (event) => {
            jQuery.jbdModal.close();
            return false;
        });

        jQuery('#gdpr-accept-btn').on('click', (event) => {
            JBDMapHelper.acceptMapGDPR(locations, params, mapType);
        });
    }

    /**
     * Accept map GPDPR rules and update them with ajax
     *
     * @param locations array locations with their lat and lang for each of them
     * @param params array params with settings
     * @param mapType int map type
     */
    static acceptMapGDPR(locations, params, mapType)
    {
        let urlAcceptMapGdpr = jbdUtils.getAjaxUrl('acceptMapGDPRAjax', 'companies');
        jQuery.ajax({
            type:"GET",
            url: urlAcceptMapGdpr,
            dataType: 'json',
            success: () => {
                jbd_map_gdpr = true;
                jQuery.jbdModal.close();

                JBDMapHelper.loadMapScript(locations, params, mapType);
            }
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
     * @param componentForm string component form name
     */
    static initAutocomplete(element, action, preventSubmit, focus_view, limitedCountries, componentForm)
    {
        let mapClass = JBDMapHelper.getMapClass();
        
        if (typeof componentForm === 'undefined') {
            componentForm = jbdUtils.getComponentForm(); 
        }

        mapClass.initAutocomplete(element, action, preventSubmit, focus_view, limitedCountries, componentForm);
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
        if (jbdUtils.getProperty('mapType') == JBDConstants.MAP_TYPE_BING) {
            return false;
        }

        let mapClass = JBDMapHelper.getMapClass();

        mapClass.reverseGeocoding(location, action);
    }

    /**
     * Return map instance based on the map ID
     *
     * @param mapId int map ID
     * @returns {null}
     */
    static getMapInstance(mapId)
    {
        let mapInstance = null;
        if (typeof JBDMapHelper.map_instances !== 'undefined') {
            if (typeof JBDMapHelper.map_instances[mapId] !== 'undefined') {
                mapInstance = JBDMapHelper.map_instances[mapId];
            }
        }

        return mapInstance;
    }
}