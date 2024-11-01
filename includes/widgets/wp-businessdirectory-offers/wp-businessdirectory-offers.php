<?php
/**
 * @package    WBusinessDirectory
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
 * WP_BusinessDirectory_Offers_Widget
 */
class WP_BusinessDirectory_Offers_Widget extends WP_Widget
{
	/**
	 * Register widget with WordPress.
	 */
	function __construct()
	{
		parent::__construct('wp_businessdirectory_offers_widget', // Base ID
			esc_html__('WP-BusinessDirectory Offers', 'text_domain'), // Name
			array(
				'description' => esc_html__('WP-BusinessDirectory Offers', 'text_domain')
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

		JBusinessUtil::enqueueStyle('css/common.css');
		JBusinessUtil::enqueueStyle('css/line-awesome.css');
		wp_enqueue_style('wp_businessdirectory-offers-style', WP_BUSINESSDIRECTORY_URL.'includes/widgets/wp-businessdirectory-offers/assets/css/style.css');
		wp_enqueue_script('wp_businessdirectory-offers-script', WP_BUSINESSDIRECTORY_URL.'includes/widgets/wp-businessdirectory-offers/assets/js/script.js');

		$session = JFactory::getSession();
		$params  = new Registry($instance);

		$appSettings = JBusinessUtil::getApplicationSettings();

		if ($params->get('viewtype') == 'slider') {
			JBusinessUtil::enqueueStyle('libraries/slick/slick.css');
			JBusinessUtil::enqueueScript('libraries/slick/slick.js');
		}
		
		JBusinessUtil::enqueueStyle('libraries/modal/jquery.modal.css');
		JBusinessUtil::enqueueScript('libraries/modal/jquery.modal.js');
		
		if ($appSettings->enable_ratings) {
			JBusinessUtil::enqueueStyle('libraries/star-rating/star-rating.css');
			JBusinessUtil::enqueueScript('libraries/star-rating/star-rating.js');
		}

		JBusinessUtil::loadSiteLanguage();
	    $this->loadLanguage();
		JBusinessUtil::loadBaseScripts();
		JBusinessUtil::loadClasses();
		
		//load items through cache mechanism
		// $cache = Factory::getCache('mod_jbusiness_offers', '');
		// if ($cache->contains($module->id) && $params->get("cache")) {
		// 	$items = $cache->get($module->id);
		// } else {
		// 	$items = modJBusinessOffersHelper::getList($params);
		// 	$cache->store($items, $module->id, 'mod_jbusiness_offers');
		// }
		
		$items = modJBusinessOffersHelper::getList($params);

		$showLocation = $params->get('showLocation');
		$showListingName = $params->get('showlistingName');
		$showFreeText = $params->get('showOfferFree');
		
		$appSettings = JBusinessUtil::getApplicationSettings();
		if ($appSettings->enable_multilingual) {
			JBusinessDirectoryTranslations::updateOffersTranslation($items);
		}
		
		$moduleclass_sfx = htmlspecialchars((string)$params->get('moduleclass_sfx'));
		
		$backgroundCss="";
		if ($params->get('backgroundColor')) {
			$backgroundCss = "background-color:".$params->get('backgroundColor').";";
		}
		
		$borderCss="";
		if ($params->get('borderColor')) {
			$borderCss="border-color:".$params->get('borderColor').";";
		}
		
		$categoryParam = "";
		$categoriesIds = $params->get('categoryIds');
		if (isset($categoriesIds) && count($categoriesIds)>0 && $categoriesIds[0]!= 0 && $categoriesIds[0]!= "") {
			$categoryParam="&categorySearch=".$categoriesIds[0];
		}
		
		$itemType ="";
		if ($params->get('itemType')) {
			$itemType="&itemType=".$params->get('itemType');
		}
		
		$menuItemId ="";
		if ($params->get('mItemId')) {
			$menuItemId="&Itemid=".$params->get('mItemId');
		}
		$viewAllLink = JRoute::_('index.php?option=com_jbusinessdirectory&view=offers&resetSearch=1'.$menuItemId.$categoryParam.$itemType);
		
		$newTab = ($appSettings->open_listing_on_new_tab)?" target='_blank'":"";
		$span = $params->get('layout-type')=="vertical"?"col-12":$params->get('phoneGridOption', 'col-12').' '.$params->get('tabletGridOption', 'col-md-6').' '.$params->get('desktopGridOption', "col-lg-4");

		OfferSellingService::getCartModal();

		if ($params->get('viewtype') == 'default') {
			require "tmpl/default.php";
		} else if ($params->get('viewtype') == 'slider') {
			require "tmpl/default_slider.php";
		} else if ($params->get('viewtype') == 'discount') {
			require "tmpl/default_discount.php";
		}

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

			$form = \JForm::getInstance("offers", "bd-offers", array('control' => '', 'load_data' => false), true);

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

	    $x = $language->load( 'mod_jbusiness_offers' ,
	        dirname(dirname( __FILE__ )."/wp-businessdirectory-offers") ,
	        $language_tag,
	        true
	        );
	}
}