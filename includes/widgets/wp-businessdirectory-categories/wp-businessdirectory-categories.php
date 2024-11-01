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
require_once BD_CLASSES_PATH .'/services/QuoteService.php';

require_once( dirname(__FILE__)."/".'helper.php' );

/**
 * WP_BusinessDirectory_Categories_Widget
 */
class WP_BusinessDirectory_Categories_Widget extends WP_Widget
{
	/**
	 * Register widget with WordPress.
	 */
	function __construct()
	{
		parent::__construct('wp_businessdirectory_categories_widget', // Base ID
			esc_html__('WP-BusinessDirectory Categories', 'text_domain'), // Name
			array(
				'description' => esc_html__('WP-BusinessDirectory Categories', 'text_domain')
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
        $params  = new Registry($instance);
		JBusinessUtil::includeCSSLibraries();

		wp_enqueue_style('wp_businessdirectory-categories-style', WP_BUSINESSDIRECTORY_URL.'includes/widgets/wp-businessdirectory-categories/assets/css/style.css');
		wp_enqueue_script('wp_businessdirectory-categories-script', WP_BUSINESSDIRECTORY_URL.'includes/widgets/wp-businessdirectory-categories/assets/js/script.js');

		JBusinessUtil::loadBaseScripts();
		
        $appSettings = JBusinessUtil::getApplicationSettings();

		$jsSettings = JBusinessUtil::addJSSettings();
		$jsSettings->isProfile = 1;
		
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

		if (strpos($params->get('viewtype'), 'slider')!==false) {
			JBusinessUtil::enqueueStyle('libraries/slick/slick.css');
			JBusinessUtil::enqueueScript('libraries/slick/slick.js');
		} else {
			JBusinessUtil::enqueueStyle('libraries/metis-menu/metisMenu.css');
			JBusinessUtil::enqueueScript('libraries/metis-menu/metisMenu.js');
		}

		$moduleclass_sfx = htmlspecialchars((string)$params->get('moduleclass_sfx'));
		JBusinessUtil::includeCSSLibraries();
		JBusinessUtil::loadSiteLanguage();
	    $this->loadLanguage();
		JBusinessUtil::includeValidation();

        $helper = new modJBusinessCategoriesHelper();

		$categoriesIds = $params->get('categoryIds');
		
		if (!JBusinessUtil::isAppInstalled(JBD_APP_QUOTE_REQUESTS)) {
			$params->set('linkquoterequests', 0);
		}

		if (strpos($params->get('viewtype'), 'slider')!==false) {
			$categories = $helper->getCategoriesByIdsOnSlider($params, $categoriesIds);
		} elseif ($params->get('viewtype') == 'menu') {
			$categories = $helper->getCategoriesByIdsOnMenu($categoriesIds);
		} else {
			$categories = $helper->getCategories($params, $categoriesIds, true);
		}

        if ($appSettings->category_order == ORDER_ALPHABETICALLY && !empty($categories)) {
            require_once(BD_HELPERS_PATH.'/category_lib.php');
            $categoryService = new JBusinessDirectorCategoryLib();
            $categories = $categoryService->sortCategoryView($categories);
        }

        if($appSettings->enable_multilingual) {
            JBusinessDirectoryTranslations::updateCategoriesTranslation($categories);
		}

		$linkQuoteRequests = $params->get('linkquoterequests');
		if ($linkQuoteRequests && JBusinessUtil::isAppInstalled(JBD_APP_QUOTE_REQUESTS)) {
			JBusinessUtil::enqueueStyle('libraries/modal/jquery.modal.css');
			JBusinessUtil::enqueueScript('libraries/modal/jquery.modal.js');

			echo QuoteService::initializeQuoteRequets();
		}

		$showRelated = $params->get('related-categories');

		$viewAllLink = JRoute::_('index.php?option=com_jbusinessdirectory&view=categories&categoryType='.CATEGORY_TYPE_BUSINESS);
		$span = $params->get('phoneGridOption', 'col-12').' '.$params->get('tabletGridOption', 'col-md-6').' '.$params->get('desktopGridOption', "col-lg-4");

		if ($params->get('viewtype') == 'menu') {
			require "tmpl/default.php";
		} else if ($params->get('viewtype') == 'slider') {
			require "tmpl/default_slider.php";
		} else if ($params->get('viewtype') == 'simple') {
			require "tmpl/default_simple.php";
		} else if ($params->get('viewtype') == 'accordion') {
            require "tmpl/default_accordion.php";
        } else if ($params->get('viewtype') == 'grid_icons') {
            require "tmpl/default_grid_icons.php";
        } else if ($params->get('viewtype') == 'grid') {
            require "tmpl/default_grid.php";
        } else if ($params->get('viewtype') == 'boxes') {
            require "tmpl/default_boxes.php";
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

			$form = \JForm::getInstance("categories", "bd-categories", array('control' => '', 'load_data' => false), true);

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

	    $x = $language->load( 'mod_jbusinesscategories' ,
	        dirname(dirname( __FILE__ )."/wp-businessdirectory-categories") ,
	        $language_tag,
	        true
	        );
	}
}