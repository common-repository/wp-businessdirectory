<?php
/**
 * @package     JBD.Site
 * @subpackage  mod_jbusiness_user
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use MVC\Registry\Registry;
require_once BD_HELPERS_PATH.'/defines.php';
require_once BD_HELPERS_PATH.'/utils.php';
require_once BD_HELPERS_PATH.'/translations.php';
require_once( dirname(__FILE__)."/".'helper.php' );

class WP_BusinessDirectory_Users_Widget extends WP_Widget
{

	/**
	 * Register widget with WordPress.
	 */
	function __construct()
	{
		parent::__construct('wp_businessdirectory_users_widget', // Base ID
			esc_html__('WP-BusinessDirectory Users', 'text_domain'), // Name
			array(
				'description' => esc_html__('WP-BusinessDirectory Users', 'text_domain')
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
	    JBusinessUtil::loadSiteLanguage();
	    $this->loadLanguage();
		JBusinessUtil::enqueueStyle('libraries/modal/jquery.modal.css');
		JBusinessUtil::enqueueScript('libraries/modal/jquery.modal.js');

		JBusinessUtil::includeCSSLibraries();
		wp_enqueue_style('wp-businessdirectory-users-style', WP_BUSINESSDIRECTORY_URL.'includes/widgets/wp-businessdirectory-users/assets/css/style.css');

		$session = JFactory::getSession();
		$params = new Registry($instance);

		$appSettings = JBusinessUtil::getApplicationSettings();
		if ($appSettings->enable_multilingual) {
			JBusinessDirectoryTranslations::updateEntityTranslation($appSettings, TERMS_CONDITIONS_TRANSLATION);
			JBusinessDirectoryTranslations::updateEntityTranslation($appSettings, PRIVACY_POLICY_TRANSLATION);
		}
		$params->def('greeting', 1);
		
		$type             = ModJBusinessUserHelper::getType();
		$return           = ModJBusinessUserHelper::getReturnUrl($params, $type);
		$user             = JBusinessUtil::getUser();
		//$user->groups     = JBusinessUtil::getUserGroups();
		$layout           = $params->get('layout', 'default');
		
		$cartItemsCount = ModJBusinessUserHelper::getCartItemsCount();
		
		$businesUserGroups = $params->get("business_usergroups");
		$businesUserGroups[] = $appSettings->business_usergroup;

		$intersect = array();
		if(!empty($businesUserGroups)){
			$intersect  = array_intersect($businesUserGroups, $user->roles);
		}
		
		$dashboardLink = JRoute::_('index.php?option=com_jbusinessdirectory&view=userdashboard');
		if(!empty($intersect)){
			$dashboardLink = JRoute::_('index.php?option=com_jbusinessdirectory&view=useroptions');
		}
		
		echo $args['before_widget'];
		if (! empty($instance['title'])) {
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
		\JForm::addFormPath(dirname( __FILE__ ));

		try {
			$form = \JForm::getInstance("users", "bd-users", array('control' => '', 'load_data' => false), true);

			$fieldSet = $form->getFieldset("basic");

			$html = array();
			$html[] = '<div style="">';
			foreach ($fieldSet as $field)
			{
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

	    //load users language, needed for emails
	    $x = $language->load( 'mod_jbusiness_user' ,
	        dirname(dirname( __FILE__ )."/wp-businessdirectory-users") ,
	        $language_tag,
	        true
	        );
	}
} // class Foo_Widget