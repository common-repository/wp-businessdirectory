<?php
/**
 * @package    WPBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2023 CMS Junkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */

use MVC\Registry\Registry;

require_once BD_HELPERS_PATH . '/defines.php';
require_once BD_HELPERS_PATH . '/utils.php';
require_once BD_HELPERS_PATH . '/translations.php';
require_once(dirname(__FILE__) . "/" . 'helper.php');

/**
 * WP_BusinessDirectory_Listings_Widget
 */
class WP_BusinessDirectory_Listings_Widget extends WP_Widget
{
	/**
	 * Register widget with WordPress.
	 */
	function __construct()
	{
		parent::__construct('wp_businessdirectory_listings_widget', // Base ID
			esc_html__('WP-BusinessDirectory Listings', 'text_domain'), // Name
			array(
				'description' => esc_html__('WP-BusinessDirectory Listings', 'text_domain')
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
		JBusinessUtil::loadBaseScripts();
		JBusinessUtil::loadMapScripts();
		JBusinessUtil::setMenuItemId();

		$appSettings = JBusinessUtil::getApplicationSettings();
	    
		JBusinessUtil::enqueueStyle('css/common.css');
		JBusinessUtil::enqueueStyle('css/line-awesome.css');
		JBusinessUtil::enqueueStyle('libraries/range-slider/ion.rangeSlider.css');
		JBusinessUtil::enqueueStyle('libraries/range-slider/ion.rangeSlider.skinFlat.css');
		JBusinessUtil::enqueueStyle('libraries/star-rating/star-rating.css');
		wp_enqueue_style('wp_businessdirectory-listings-style', WP_BUSINESSDIRECTORY_URL.'includes/widgets/wp-businessdirectory-listings/assets/css/style.css');
		wp_enqueue_style('wp_businessdirectory-listings-latest-module', WP_BUSINESSDIRECTORY_URL.'includes/widgets/wp-businessdirectory-listings/assets/css/latest-module.css');

		wp_enqueue_script('wp_businessdirectory-listings-script', WP_BUSINESSDIRECTORY_URL.'includes/widgets/wp-businessdirectory-listings/assets/js/script.js');

		$session = JFactory::getSession();
		$params  = new Registry($instance);
		
		if ($params->get('viewtype') == 'slider' || $params->get('viewtype') == 'slider_2' || $params->get('viewtype') == 'tier') {
			JBusinessUtil::enqueueStyle('libraries/slick/slick.css');
			JBusinessUtil::enqueueScript('libraries/slick/slick.js');
		}
		
		if ($appSettings->enable_ratings) {
			JBusinessUtil::enqueueStyle('libraries/star-rating/star-rating.css');
			JBusinessUtil::enqueueScript('libraries/star-rating/star-rating.js');
		}
		
		JBusinessUtil::enqueueStyle('libraries/modal/jquery.modal.css');
		JBusinessUtil::enqueueScript('libraries/modal/jquery.modal.js');
		
		require_once BD_HELPERS_PATH.'/translations.php';
		
		// Include the syndicate functions only once
		require_once __DIR__ . '/helper.php';
		
		JBusinessUtil::includeCSSLibraries();
		JBusinessUtil::loadSiteLanguage();
	    $this->loadLanguage();
		
		//load items through cache mechanism
		$items = modJBusinessListingsHelper::getList($params);

		if (!empty($items)) {
			foreach ($items as $company) {
				if (GET_DATA_FROM_YELP) {
					$yelpData = JBusinessUtil::getYelpData($company->yelp_id, false);
					if (isset($yelpData->error) || empty($company->yelp_id)) {
						$company->review_score = 0;
					} else {
						$company->review_score = $yelpData->rating;
					}
				}
				if (!empty($params->get('truncate_title')) && strlen($company->name)>$params->get('truncate_title')) {
					$company->name = substr($company->name, 0, $params->get('truncate_title'))."...";
				}
			}
		}
		
		$newTab = ($appSettings->open_listing_on_new_tab)?" target='_blank'":"";
		$jsSettings = JBusinessUtil::addJSSettings();
		$jsSettings->isProfile = 1;
		
		$token = rand(10, 1000);
		
		if (!defined('JBD_UTILS_LOADED')) {
			$document  = JFactory::getDocument();
			$document->addScriptDeclaration('
				window.addEventListener("load",function() {
					jbdUtils.setProperties(' . json_encode($jsSettings) . ');
				});
			');
			echo JBusinessUtil::generateScriptDeclarations();
			define('JBD_UTILS_LOADED', 1);
		}
		
		$document  = JFactory::getDocument();
		$campaignCall = "";
		$campaignCallClass = "";
		if (!empty($params->get('plan')) && !empty($params->get('only_campaign'))) {
			$campaignCallClass = 'campaignCall';
			$campaignCall =
				"
					var campaignUrl = jbdUtils.getAjaxUrl('decreaseCampaignBudget', 'campaign', 'managecampaign');
					var planId = '".$params->get('plan')."';
		
					jQuery.ajax({
						type:'GET',
						url: campaignUrl,
						data: { planId: planId, companyId: companyId },
						dataType: 'json',
						cache: false,
						success: function(data) {
							console.log(data);
						}
					});
				";
		}
		$document->addScriptDeclaration("  
			window.addEventListener('load',function() {
				jQuery('.campaignCall').on('click', function() {
				  companyId = jQuery(this).attr('data-companyId');
				  ".$campaignCall."
				});
			});
		");
		
		if ($appSettings->enable_multilingual) {
			JBusinessDirectoryTranslations::updateBusinessListingsTranslation($items);
			JBusinessDirectoryTranslations::updateBusinessListingsSloganTranslation($items);
		}
		
		$moduleclass_sfx = htmlspecialchars((string)$params->get('moduleclass_sfx'));
		
		$backgroundCss="";
		if ($params->get('backgroundColor')) {
			$backgroundCss = "background-color:".$params->get('backgroundColor').";";
		}
		
		$borderCss="";
		if ($params->get('borderColor')) {
			$borderCss="border: 1px solid ".$params->get('borderColor').";";
		}
		
		$menuItemId ="";
		if ($params->get('mItemId')) {
			$menuItemId="&Itemid=".$params->get('mItemId');
		}
		
		$categoryParam = "";
		$categoriesIds = $params->get('categoryIds');
		if(!is_array($categoriesIds)){
        	$categoriesIds = explode(",",$categoriesIds);
        }
		if (isset($categoriesIds) && count($categoriesIds)>0 && $categoriesIds[0]!= 0 && $categoriesIds[0]!= "") {
			$categoryParam="&categorySearch=".$categoriesIds[0];
		}
		
		$viewAllLink = JRoute::_('index.php?option=com_jbusinessdirectory&view=search'.$menuItemId.$categoryParam);
		
		$jinput = JFactory::getApplication()->input;
		$geoLocationParams = "";
		$latitude = $jinput->get("latitude");
		$longitude = $jinput->get("longitude");
		
		if (!empty($latitude)) {
			$geoLocationParams = "?geo-latitude=$latitude&geo-longitude=$longitude&geolocation=1&radius=50";
		}
		
		$span = $params->get('layout-type')=="vertical"?"col-12":$params->get('phoneGridOption', 'col-12').' '.$params->get('tabletGridOption', 'col-md-6').' '.$params->get('desktopGridOption', "col-lg-4");
		$viewType = (!empty($params->get('viewtype')) && ($params->get('viewtype') != "simple")) ?  "_".$params->get('viewtype') : "_simple";
		
		require_once "tmpl/default".$viewType.".php";

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
			$form = \JForm::getInstance("listings", "bd-listings", array('control' => '', 'load_data' => false), true);

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

	    $x = $language->load( 'mod_jbusiness_listings' ,
	        dirname(dirname( __FILE__ )."/wp-businessdirectory-listings") ,
	        $language_tag,
	        true
	        );
	}
}