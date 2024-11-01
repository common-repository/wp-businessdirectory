<?php
/**
 * @package    WBusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2023 CMS Junkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
use MVC\Registry\Registry;

require_once BD_HELPERS_PATH . '/defines.php';
require_once BD_HELPERS_PATH . '/utils.php';
require_once BD_HELPERS_PATH . '/translations.php';
require_once(dirname(__FILE__) . "/" . 'helper.php');

/**
 * WP_BusinessDirectory_Search_Events_Widget
 */
class WP_BusinessDirectory_Search_Events_Widget extends WP_Widget
{

	/**
	 * Register widget with WordPress.
	 */
	function __construct()
	{
		parent::__construct('wp_businessdirectory_search_events_widget', // Base ID
			esc_html__('WP-BusinessDirectory Events Search', 'text_domain'), // Name
			array(
				'description' => esc_html__('WP-BusinessDirectory Search Events', 'text_domain')
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
		JBusinessUtil::enqueueStyle('/css/search-module.css');
		JBusinessUtil::enqueueStyle('libraries/jquery/jquery-ui.css');
		
		JBusinessUtil::loadJQueryUI();

		JBusinessUtil::loadBaseScripts();
		JBusinessUtil::loadMapScripts();
		
		JBusinessUtil::enqueueScript('libraries/range-slider/ion.rangeSlider.js');
		JBusinessUtil::loadJQueryChosen();
		
		wp_enqueue_script('wp-businessdirectory-search-events-js', WP_BUSINESSDIRECTORY_URL.'includes/widgets/wp-businessdirectory-search-events/assets/js/script.js');
		JBusinessUtil::enqueueScript('libraries/chosen/chosen.jquery.min.js');

		JBusinessUtil::enqueueStyle('libraries/jquery/jquery.timepicker.css');
		JBusinessUtil::enqueueScript('libraries/jquery/jquery.timepicker.min.js');

        wp_enqueue_script('moment');
		JBusinessUtil::enqueueScript('libraries/date/daterangepicker.js');
		JBusinessUtil::enqueueStyle('libraries/date/daterangepicker.css');

		$appSettings = JBusinessUtil::getApplicationSettings();
		$key="";
		if (!empty($appSettings->google_map_key)) {
			$key="&key=".$appSettings->google_map_key;
		}
		$lang = JBusinessUtil::getLanguageTag();

		JBusinessUtil::loadSiteLanguage();
	    $this->loadLanguage();
		$session = JFactory::getSession();
		$params  = new Registry($instance);

		$preserve = $params->get('preserve');

		$geoLocation = $session->get("geolocation");
		$startDate = $session->get('ev-startDate') && $preserve?$session->get('ev-startDate'):"";
		$endDate = $session->get('ev-endDate') && $preserve?$session->get('ev-endDate'):"";
		
		$jsSettings = JBusinessUtil::addJSSettings();
		$jsSettings->isProfile = 1;
        wp_enqueue_script('google-map', "https://maps.googleapis.com/maps/api/js?language=".$lang.$key."&libraries=geometry&libraries=places");
		
		if (!defined('JBD_UTILS_LOADED')) {
			$document = JFactory::getDocument();
			$document->addScriptDeclaration('
				window.addEventListener("load",function() {
					jbdUtils.setProperties(' . json_encode($jsSettings) . ');
				});		
			');
			echo JBusinessUtil::generateScriptDeclarations();
			define('JBD_UTILS_LOADED', 1);
		}
		
		
		if ($params->get('showCountries')) {
			$countries = modJBusinessEventSearchHelper::getCountries();
			if ($appSettings->enable_multilingual) {
				JBusinessDirectoryTranslations::updateCountriesTranslation($countries);
			}
		}
		
		if ($params->get('showTypes')) {
			$types =  modJBusinessEventSearchHelper::getTypes();
		}
		
		if ($params->get('showProvince')) {
			$provinces = modJBusinessEventSearchHelper::getProvinces();
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
		
		$categories = array();
		$subCategories = array();
		$separateCategories  = $params->get('separateCategories');
		$availableCategories = $params->get('availableCategories');
		if (is_array($availableCategories) && !($availableCategories[0]==0 && count($availableCategories)==1)) {
			$categories = modJBusinessEventSearchHelper::getMainCategories();
			$subCategories = modJBusinessEventSearchHelper::getSubCategories();
		
			if ($appSettings->enable_multilingual) {
				JBusinessDirectoryTranslations::updateCategoriesTranslation($categories);
				JBusinessDirectoryTranslations::updateCategoriesTranslation($subCategories);
			}
		
			$availableCategories = array_filter($availableCategories);
			if (!empty($availableCategories)) {
				foreach ($categories as $key => $category) {
					if (!in_array($category->id, $availableCategories)) {
						unset($categories[$key]);
					}
				}
				foreach ($subCategories as $key => $category) {
					if (!in_array($category->id, $availableCategories)) {
						unset($subCategories[$key]);
					}
				}
			}
			foreach ($categories as $category) {
				foreach ($subCategories as $key => $subCat) {
					if ($category->id == $subCat->parent_id) {
						if (!isset($category->subcategories)) {
							$category->subcategories = array();
						}
						$category->subcategories[] = $subCat;
						unset($subCategories[$key]);
					}
				}
			}
			$params->set('separateCategories', 0);
			$separateCategories = 0;
		} elseif ($params->get('showCategories')) {
			$categories = modJBusinessEventSearchHelper::getMainCategories();
			$subCategories = modJBusinessEventSearchHelper::getSubCategories();
		
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
		
		$appSettings = JBusinessUtil::getApplicationSettings();
		if ($params->get('showCities')) {
			if ($appSettings->limit_cities_regions ==1) {
				$cities =  modJBusinessEventSearchHelper::getActivityCities();
			} else {
				$cities =  modJBusinessEventSearchHelper::getCities();
			}
		}
		
		$maxRadius = $params->get("max-radius");
		if (empty($maxRadius)) {
			$maxRadius = 500;
		}
		
		if ($params->get('showMap')) {
			$maxEvents = $params->get('maxEvents');
			if (empty($maxEvents)) {
				$maxEvents = 200;
			}
			$events =  modJBusinessEventSearchHelper::getEvents($maxEvents);
		}
		
		if ($appSettings->enable_multilingual) {
			JBusinessDirectoryTranslations::updateEventTypesTranslation($types);
		}
		
		if ($params->get('showRegions')) {
			if ($appSettings->limit_cities_regions == 1) {
				$regions = modJBusinessEventSearchHelper::getActivityRegions();
			} else {
				$regions = modJBusinessEventSearchHelper::getRegions();
			}
		}
		
		$attributes = $params->get('customAttributes');
		$atrributesValues = $session->get('customAtrributes');
		if (!$params->get('preserve')) {
			$atrributesValues = array();
		}
		
		if (!empty($attributes)) {
			$customAttributes = modJBusinessEventSearchHelper::getCustomAttributes($attributes, $atrributesValues);
		}
		
		$menuItemId ="";
		if ($params->get('mItemId')) {
			$menuItemId="&Itemid=".$params->get('mItemId');
		}
		
		$layoutType = $params->get('layout-type', 'horizontal');
		$moduleclass_sfx = htmlspecialchars((string)$params->get('moduleclass_sfx'));
		
		$radius = JFactory::getApplication()->input->getInt("radius");
		if (!isset($radius)) {
			$radius = $params->get('radius');
		}
		
		$bgStyle = !empty($params->get('bg-color'))?"padding: 20px;background-color:".$params->get('bg-color'):"";

		echo $args['before_widget'];
		if (!empty($instance['title'])) {
			echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
		}

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
		\JForm::addFormPath(dirname(__FILE__));

		try {

			$form = \JForm::getInstance("events-search", "bd-search-events", array('control' => '', 'load_data' => false), true);

			$fieldSet = $form->getFieldset("basic");

			$html   = array();
			$html[] = '<div style="">';
			foreach ($fieldSet as $field) {
				$fname = str_replace("[]","",$field->name);
				$value        = isset($instance[$fname]) ? $instance[$fname] : "$field->value";
				$field->name  = $this->get_field_name($field->name);
				$field->id    = $this->get_field_id($field->id);
				$field->setValue($value);

				$html[] = '<p>';
				$html[] = $field->label;
				$html[] = $field->input;
				$html[] = '</p>';
			}
			$html[] = '</div>';

			echo implode('', $html);

		}
		catch (\Exception $e) {
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

	    $x = $language->load( 'mod_jbusiness_event_search' ,
	        dirname(dirname( __FILE__ )."/wp-businessdirectory-search-events") ,
	        $language_tag,
	        true
	        );
	}
}