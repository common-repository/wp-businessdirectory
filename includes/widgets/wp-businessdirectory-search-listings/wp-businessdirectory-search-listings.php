<?php
/**
 * @package    WBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2023 CMS Junkie. All rights reserved. 
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 

use MVC\Registry\Registry;
require_once BD_HELPERS_PATH.'/defines.php';
require_once BD_HELPERS_PATH.'/utils.php';
require_once BD_HELPERS_PATH.'/translations.php';
require_once( dirname(__FILE__)."/".'helper.php' );
/**
 * RailwayRervation_Search_Events_Widget widget.
 */
class WP_BusinessDirectory_Search_Listings_Widget extends WP_Widget
{

    /**
     * Register widget with WordPress.
     */
    function __construct()
    {
        parent::__construct('wp_businessdirectory_search_listings_widget', // Base ID
        esc_html__('WP-BusinessDirectory Search', 'text_domain'), // Name
        array(
               'description' => esc_html__('WP-BusinessDirectory Search Listings', 'text_domain')
        )) // Args
;
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args
     *            Widget arguments.
     * @param array $instance
     *            Saved values from database.
     */
    public function widget($args, $instance)
    {
        JBusinessUtil::includeCSSLibraries();

        JBusinessUtil::enqueueStyle('libraries/chosen/chosen.css');
        JBusinessUtil::enqueueStyle('libraries/range-slider/ion.rangeSlider.css');
        JBusinessUtil::enqueueStyle('libraries/range-slider/ion.rangeSlider.skinFlat.css');
        JBusinessUtil::enqueueStyle('css/search-module.css');
        JBusinessUtil::enqueueStyle('libraries/jquery/jquery-ui.css');
        
        wp_enqueue_style('wp_businessdirectory-search-style', WP_BUSINESSDIRECTORY_URL.'includes/widgets/wp-businessdirectory-search-listings/assets/css/style.css');
        JBusinessUtil::loadJQueryUI();
        
        JBusinessUtil::loadBaseScripts();
        JBusinessUtil::loadMapScripts();
       
        JBusinessUtil::enqueueScript('libraries/range-slider/ion.rangeSlider.js');
        wp_enqueue_script('wp-businessdirectory-search-listings-js', WP_BUSINESSDIRECTORY_URL.'includes/widgets/wp-businessdirectory-search-listings/assets/js/script.js');

        JBusinessUtil::loadJQueryChosen();

	    $session = JFactory::getSession();
        $params = new Registry($instance);
        
        $geoLocation = $session->get("geolocation");
        $appSettings = JBusinessUtil::getApplicationSettings();
        JBusinessUtil::loadSiteLanguage();
	    $this->loadLanguage();

        $key="";
        if(!empty($appSettings->google_map_key)) {
            $key="&key=".$appSettings->google_map_key;
        }

            $lang = JBusinessUtil::getLanguageTag();
            wp_enqueue_script('google-map', "https://maps.googleapis.com/maps/api/js?language=".$lang.$key."&libraries=geometry&libraries=places");
            
            $jsSettings = JBusinessUtil::addJSSettings();
            $jsSettings->isProfile = 1;
            
            
            if (!defined('JBD_UTILS_LOADED')) {
                $document = JFactory::getDocument();
                $document->addScriptDeclaration('
                    window.addEventListener("load",function() {
                        jbdUtils.setProperties(' . json_encode($jsSettings) . ');
                        jbdUtils .renderRadioButtons();
                    });
                ');
                
                echo JBusinessUtil::generateScriptDeclarations();

                define('JBD_UTILS_LOADED', 1);
            }
            
            $categories = array();
            $subCategories = array();
            $separateCategories  = $params->get('separateCategories');
            $availableCategories = $params->get('availableCategories');
            if (is_array($availableCategories) && count($availableCategories) > 0 && !($availableCategories[0] == 0 && count($availableCategories) == 1)) {
                $categories = modJBusinessDirectoryHelper::filterCategories($availableCategories);
                $params->set('separateCategories', 0);
                $separateCategories = 0;
            } else {
                $categories = modJBusinessDirectoryHelper::getMainCategories();
                $subCategories = modJBusinessDirectoryHelper::getSubCategories();

                if ($appSettings->enable_multilingual) {
                    JBusinessDirectoryTranslations::updateCategoriesTranslation($categories);
                    JBusinessDirectoryTranslations::updateCategoriesTranslation($subCategories);
                }

                if ($params->get('showSubCategories') && !$separateCategories) {
                    foreach ($categories as $category) {
                        foreach ($subCategories as $key => $subCat) {
                            if ($category->id == $subCat->parent_id) {
                                if (!isset($category->subcategories)) {
                                    $category->subcategories = array();
                                }
                                $category->subcategories[] = $subCat;
                            }
                        }
                    }
                }
            }

            $suggestedCategories = null;
            if ($params->get('showCategoriesSuggestion')) {
                $suggestedCategories = $params->get('suggestedCategories');
                $suggestedCategories = modJBusinessDirectoryHelper::filterCategories($suggestedCategories);
            }

            // $ipAddress = JBusinessUtil::getRequestIP();
            // $ipAddress = "5.15.250.165";
            // $location = JBusinessUtil::getLocationByIp($ipAddress);
            // $location = new stdClass;
            // $location->longitude="24.1588878";
            // $location->latitude="45.7832116";
            
            if(!empty($location)){
                //$nearByCities = JBusinessUtil::getNearbyCitiesByLocation($location->latitude, $location->longitude);
            }
            
            if ($appSettings->category_order == ORDER_ALPHABETICALLY && count($categories)>0) {
                require_once BD_HELPERS_PATH.'/category_lib.php';
                $categoryService = new JBusinessDirectorCategoryLib();
                $categories = $categoryService->sortCategories($categories, false, true);
            }
            
            if (is_array($availableCategories)) {
                reset($availableCategories);
                if (!(current($availableCategories)==0 && count($availableCategories)==1)) {
                    foreach ($subCategories as $key => $subCat) {
                        end($categories)->subcategories[] = $subCat;
                    }
                }
            }
            
            if ($params->get('showTypes')) {
                $types = modJBusinessDirectoryHelper::getTypes();
                $availableTypes = $params->get('availableTypes');
                if (is_array($availableTypes)) {
                    $availableTypes = array_filter($availableTypes);
                    if (!empty($availableTypes)) {
                        $result = array();
                        foreach ($types as $type) {
                            foreach ($availableTypes as $atype) {
                                if ($type->id == $atype) {
                                    $result[] = $type;
                                }
                            }
                        }
                        $types = $result;
                    }
                }
            }
            
            $regiongCities = modJBusinessDirectoryHelper::getCitiesRegions();
            
            if ($params->get('showCountries')) {
                $countries = modJBusinessDirectoryHelper::getCountries();
                if ($appSettings->enable_multilingual) {
                    JBusinessDirectoryTranslations::updateCountriesTranslation($countries);
                }
            }
            
            if ($params->get('showRegions')) {
                if ($appSettings->limit_cities_regions == 1) {
                    $regions = modJBusinessDirectoryHelper::getActivityRegions();
                } else {
                    $regions = modJBusinessDirectoryHelper::getRegions();
                }
            }
            
            if ($params->get('showProvince')) {
                $provinces = modJBusinessDirectoryHelper::getProvinces();
            }
            
            if ($params->get('showCities')) {
                if ($appSettings->limit_cities_regions == 1) {
                    $cities =  modJBusinessDirectoryHelper::getActivityCities();
                } else {
                    $cities =  modJBusinessDirectoryHelper::getCities();
                }
            }
            
            if ($params->get('showMap')) {
                $maxListings = $params->get('maxListings');
                if (empty($maxListings)) {
                    $maxListings = 200;
                }
                $companies =  modJBusinessDirectoryHelper::getCompanies($maxListings, $module->id, $params);
            }
            
            if ($appSettings->enable_multilingual) {
                //JBusinessDirectoryTranslations::updateCategoriesTranslation($categories);
                JBusinessDirectoryTranslations::updateTypesTranslation($types);
            }
            
            $maxRadius = $params->get("max-radius");
            if (empty($maxRadius)) {
                $maxRadius = 500;
            }
            
            $attributes = $params->get('customAttributes');
            $atrributesValues = $session->get('customAtrributes');
            if (!$params->get('preserve')) {
                $atrributesValues = array();
            }
            
            if (!empty($attributes)) {
                $customAttributes = modJBusinessDirectoryHelper::getCustomAttributes($attributes, $atrributesValues);
            }
            
            $mapHeight = $params->get('mapHeight');
            $mapWidth = $params->get('mapWidth');
            
            $menuItemId ="";
            if ($params->get('mItemId')) {
                $menuItemId="&Itemid=".$params->get('mItemId');
            }
            
            $formAction=JRoute::_('index.php?option=com_jbusinessdirectory'.$menuItemId);
            if (!$appSettings->enable_seo) {
                //$formAction = "index.php";
            }
                
            
            $choices = 0;
            if ($params->get('linklocation')) {
                if ($params->get('showCountries') && !$params->get('showRegions')) {
                    $choices = 1;
                } elseif ($params->get('showCountries')) {
                    $choices = 2;
                } elseif ($params->get('showRegions')) {
                    $choices = 3;
                }
            }
            
            $layoutType = $params->get('layout-type', 'horizontal');
            $moduleclass_sfx = htmlspecialchars((string)$params->get('moduleclass_sfx'));
            
            $radius = $session->get("radius");
            if (!isset($radius)) {
                $radius = $params->get('radius');
            }
            
            $bgStyle = !empty($params->get('bg-color'))?"padding: 20px;background-color:".$params->get('bg-color'):"";
            
            $styleClass = !empty($params->get('fieldLabel')) && $params->get('fieldLabel')==2?"search-style-3":"";
       
        echo $args['before_widget'];
        if (! empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }

        if($params->get('base-layout')=='default_style_2')
       	    require "tmpl/default_style_2.php";
        else
            require "tmpl/default.php";
        
        echo $args['after_widget'];
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance
     *            Previously saved values from database.
     */
    public function form($instance)
    {
        JBusinessUtil::loadSiteLanguage();
	    $this->loadLanguage();
        
        // Get the form.
        \JForm::addFormPath(dirname( __FILE__ ));
       
        try {
            
            $form = \JForm::getInstance("listings-search", "bd-search-listings", array('control' => '', 'load_data' => false), true);
            
            $fieldSet = $form->getFieldset("basic");
            
            $html = array();
            $html[] = '<div style="">';
            foreach ($fieldSet as $field) {
                $fname = str_replace("[]","",$field->name);
				$value        = isset($instance[$fname]) ? $instance[$fname] : "$field->value";
                $field->name = $this->get_field_name($field->name);
                $field->id = $this->get_field_id($field->id);
                $field->value =  esc_attr($value);
                
                $html[] = '<p>';
                $html[] = $field->label;
                $html[] = $field->input;
                $html[] = '</p>';
            }
            $html[] = '</div>';
            
            echo implode('', $html);
            
        } catch (\Exception $e) {
            dump($e);
            return false;
        }
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance
     *            Values just sent to be saved.
     * @param array $old_instance
     *            Previously saved values from database.
     *            
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance)
    {
        
        return $new_instance;
    }
    			
	public function loadLanguage(){
	    $language = JFactory::getLanguage();
	    $language_tag =  get_locale();
	    $language_tag = str_replace("_", "-", $language_tag);

	    $x = $language->load( 'mod_jbusiness_listing_search' ,
	        dirname(dirname( __FILE__ )."/wp-businessdirectory-search-listings") ,
	        $language_tag,
	        true
	        );
	}
} // class Foo_Widget