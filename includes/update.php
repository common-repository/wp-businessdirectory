<?php 

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class WPBDDeployment{
    
    const CURRENT_VERSION = WP_BUSINESSDIRECTORY_VERSION_NUM;
    const PLUGIN_BASE = WP_BUSINESSDIRECTORY_PLUGIN_BASE;

    /**
     * Class Constructor
     *
     * @since 1.1.2
     * @access public
     */
    public function __construct() {
        add_filter( 'pre_set_site_transient_update_plugins', [ $this, 'check_version' ] );
    }
    
    /**
     * Check version.
     *
     * @since 2.6.0
     * @access public
     *
     * @param object $transient Plugin updates data.
     *
     * @return object Plugin updates data.
     */
    public function check_version( $transient ) {
        // First transient before the real check.
        if ( ! isset( $transient->response ) ) {
            return $transient;
        }
        
        // Placeholder
        $stable_version = '0.0.0';
        
        if ( ! empty( $transient->response[ static::PLUGIN_BASE ]->new_version ) ) {
            $stable_version = $transient->response[ static::PLUGIN_BASE ]->new_version;
        }
        
        if ( !isset($this->deployment_info) ) {
            $this->deployment_info = $this->get_deployment_info();
        }
        
        // Can be false - if canary version is not available.
        if ( empty( $this->deployment_info ) ) {
            return $transient;
        }
        
        if ( ! version_compare( $this->deployment_info['new_version'], $stable_version, '>' ) ) {
            return $transient;
        }
        
        $deployment_info = $this->deployment_info;
        
        if(!empty($deployment_info["package"])){
            $appSettigns = JBusinessUtil::getApplicationSettings();
            
            $deployment_info["package"] .= "&orderId=".$appSettigns->order_id."&orderEmail=".$appSettigns->order_email;
        }
        
        // Check if plugin info is present. Default plugin info is present on the transient response. Check if transient response is empty.
        if ( ! empty( $transient->response[ static::PLUGIN_BASE ] ) ) {
            $deployment_info = array_merge( (array) $transient->response[ static::PLUGIN_BASE ], $deployment_info );
        }
        
        $transient->response[ static::PLUGIN_BASE ] = (object) $deployment_info;
        
        return $transient;
    }
    
    /**
     * Retrieve the deployment info from the remove server
     * 
     * @param unknown $force
     * @return array|false
     */
    protected function get_deployment_remote_info( $force ) {
        return WPBDApi::get_deployment_info( $force );
    }
    
    
    /**
     * Retrieve the deployment info
     * 
     * @return boolean
     */
    private function get_deployment_info() {
        global $pagenow;
        
        $force = 'update-core.php' === $pagenow && isset( $_GET['force-check'] ); // WPCS: XSS ok.
        
        $wpbd_deployment = $this->get_deployment_remote_info( $force );
        
        if ( empty( $wpbd_deployment['plugin_info']['new_version'] ) ) {
            return false;
        }
        
        $canary_version = $wpbd_deployment['plugin_info']['new_version'];
        
        if ( version_compare( $canary_version, static::CURRENT_VERSION, '<=' ) ) {
            return false;
        }
        
        return $wpbd_deployment['plugin_info'];
    }
}


