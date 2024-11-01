/**
 * JBD Abstract map class
 */
class JBDMapAbstract
{
    /**
     * Class constructor
     *
     * @param locations array with locations
     * @param params array with params
     * @param mapType int map type
     */
    constructor(locations, params, mapType)
    {
    	
        this.locations = locations;
        this.params = params;
        this.mapType = typeof mapType !== 'undefined' ? mapType : parseInt(jbdUtils.getProperty('mapType'));
        this.params['is_admin'] = typeof this.params['is_admin'] !== "undefined" ? this.params['is_admin'] : false;

        let map_zoom = jbdUtils.getProperty('mapDefaultZoom');
        if (typeof this.params !== 'undefined' && this.params != null) {
            // default to company
            let mapDivId = "company-map";
            if (typeof this.params['map_div'] !== 'undefined') {
                mapDivId = this.params['map_div'];
            }

            let mapId = '';
            if (typeof this.params['tmapId'] !== 'undefined') {
                mapId = this.params['tmapId'];
            }
            this.mapDiv = mapDivId + '' + mapId;

            if (typeof this.params['map_zoom'] !== 'undefined') {
                map_zoom = this.params['map_zoom'];
            }

            let map_width = '100%';
            let map_height = '450px';
            if (typeof this.params["map_width"] !== 'undefined') {
                map_width = this.params["map_width"];
            }
            if (typeof this.params["map_height"] !== 'undefined') {
                map_height = this.params["map_height"];
            }

            this.mapWidth = map_width;
            this.mapHeight = map_height;
        }

        this.zoom = parseInt(map_zoom);
        this.mapInitialized = false;
    }

    /**
     * Set map width
     *
     * @param width double map width
     */
    setMapWidth(width)
    {
        this.mapWidth = width;
    }

    /**
     * Set map height
     *
     * @param height double map height
     */
    setMapHeight(height)
    {
        this.mapHeight = height;
    }

    /**
     * Return boolean if there is map enabled or not
     *
     * @returns {boolean}
     */
    hasMap()
    {
        return typeof this.params['no_map'] === 'undefined';
    }

    /**
     * Return map object
     *
     * @returns {*}
     */
    getMap()
    {
        return this.map;
    }

    /**
     * Return map Id or null if nothing is found
     * @returns {null|*}
     */
    getMapId()
    {
        if (typeof this.params['tmapId'] !== 'undefined') {
            return this.params['tmapId'];
        } else {
            return null;
        }
    }

    /**
     * Return an instance of this class
     *
     * @returns {JBDMapAbstract}
     */
    getInstance()
    {
        return this;
    }

    /**
     * Set the map locations
     * 
     * @param locations 
     */
    setLocations(locations, resetLocations){
        
        if(resetLocations){
            this.locations = null;
        }

        if(!this.locations){
            this.locations =locations;
        }else{
            this.locations =this.locations.concat(locations);
        }
        this.mapInitialized = false;

        this.initialize();
    }
}