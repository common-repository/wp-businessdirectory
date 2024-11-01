<?php
/**
 * @package     JBD.Libraries
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     https://www.gnu.org/licenses/agpl-3.0.en.html; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

use MVC\Utilities\ArrayHelper;

/**
 * Utility class for creating HTML Grids
 *
 * @since  1.5
 */
abstract class JHtmlJBDGrid {
	/**
	 * Display a boolean setting widget.
	 *
	 * @param   integer  $i        The row index.
	 * @param   integer  $value    The value of the boolean field.
	 * @param   string   $taskOn   Task to turn the boolean setting on.
	 * @param   string   $taskOff  Task to turn the boolean setting off.
	 *
	 * @return  string   The boolean setting widget.
	 *
	 * @since   1.6
	 *
	 * @deprecated  4.0 This is only used in hathor and will be removed without replacement
	 */
	public static function boolean($i, $value, $taskOn = null, $taskOff = null) {
		// Load the behavior.
		static::behavior();
		;

		// Build the title.
		$title = $value ? JText::_('JYES') : JText::_('JNO');
		$title = JHtml::_('tooltipText', $title, JText::_('JGLOBAL_CLICK_TO_TOGGLE_STATE'), 0);

		// Build the <a> tag.
		$bool = $value ? 'true' : 'false';
		$task = $value ? $taskOff : $taskOn;
		$toggle = (!$task) ? false : true;

		if ($toggle) {
			return '<a class="grid_' . $bool . ' hasTooltip" title="' . $title . '" rel="{id:\'cb' . $i . '\', task:\'' . $task
				. '\'}" href="#toggle"></a>';
		} else {
			return '<a class="grid_' . $bool . '"></a>';
		}
	}

	/**
	 * Method to sort a column in a grid
	 *
	 * @param   string  $title          The link title
	 * @param   string  $order          The order field for the column
	 * @param   string  $direction      The current direction
	 * @param   string  $selected       The selected ordering
	 * @param   string  $task           An optional task override
	 * @param   string  $new_direction  An optional direction for the new column
	 * @param   string  $tip            An optional text shown as tooltip title instead of $title
	 * @param   string  $form           An optional form selector
	 *
	 * @return  string
	 *
	 * @since   1.5
	 */
	public static function sort($title, $order, $direction = 'asc', $selected = '', $task = null, $new_direction = 'asc', $tip = '', $form = null) {
		JHtml::_('behavior.core');
		JHtml::_('bootstrap.popover');

		$direction = strtolower($direction);
		$icon = array('arrow-up-3', 'arrow-down-3');
		$index = (int) ($direction === 'desc');

		if ($order != $selected) {
			$direction = $new_direction;
		} else {
			$direction = $direction === 'desc' ? 'asc' : 'desc';
		}

		if ($form) {
			$form = ', document.getElementById(\'' . $form . '\')';
		}

		$html = '<a href="#" onclick="JBD.tableOrdering(\'' . $order . '\',\'' . $direction . '\',\'' . $task . '\'' . $form . ');return false;"'
			. ' class="hasPopover" title="' . htmlspecialchars(JText::_($tip ?: $title)) . '"'
			. ' data-content="' . htmlspecialchars(JText::_('JGLOBAL_CLICK_TO_SORT_THIS_COLUMN')) . '" data-placement="top">';

		if (isset($title['0']) && $title['0'] === '<') {
			$html .= $title;
		} else {
			$html .= JText::_($title);
		}

		if ($order == $selected) {
			$html .= '<span class="icon-' . $icon[$index] . '"></span>';
		}

		$html .= '</a>';

		return $html;
	}

	/**
	 * Method to check all checkboxes in a grid
	 *
	 * @param   string  $name    The name of the form element
	 * @param   string  $tip     The text shown as tooltip title instead of $tip
	 * @param   string  $action  The action to perform on clicking the checkbox
	 *
	 * @return  string
	 *
	 * @since   3.1.2
	 */
	public static function checkall($name = 'checkall-toggle', $tip = 'JGLOBAL_CHECK_ALL', $action = 'JBD.checkAll(this)') {
		JHtml::_('behavior.core');
		;

		return '<input type="checkbox" name="' . $name . '" value="" class="hasTooltip" title="' . JHtml::_('tooltipText', $tip)
			. '" onclick="' . $action . '" />';
	}

	/**
	 * Method to create a checkbox for a grid row.
	 *
	 * @param   integer  $rowNum      The row index
	 * @param   integer  $recId       The record id
	 * @param   boolean  $checkedOut  True if item is checked out
	 * @param   string   $name        The name of the form element
	 * @param   string   $stub        The name of stub identifier
	 *
	 * @return  mixed    String of html with a checkbox if item is not checked out, null if checked out.
	 *
	 * @since   1.5
	 */
	public static function id($rowNum, $recId, $checkedOut = false, $name = 'cid', $stub = 'cb') {
		$content ='<div class="d-flex justify-content-center align-items-center">';
		$content .='<div class="jradio">';
		$content .= '<input type="checkbox" id="' . $stub . $rowNum . '" name="' . $name . '[]" value="' . $recId   . '" onclick="JBD.isChecked(this.checked);" />';
		$content .='<label for="' . $stub . $rowNum . '"></label>';
		$content .='</div>';
		$content .='</div>';
			
		return $checkedOut ? '' : $content;
	}


	/**
	 * Method to create an icon for saving a new ordering in a grid
	 *
	 * @param   array   $rows   The array of rows of rows
	 * @param   string  $image  The image [UNUSED]
	 * @param   string  $task   The task to use, defaults to save order
	 *
	 * @return  string
	 *
	 * @since   1.5
	 */
	public static function order($rows, $image = 'filesave.png', $task = 'saveorder') {
		return '<a href="javascript:saveorder('
			. (count($rows) - 1) . ', \'' . $task . '\')" rel="tooltip" class="saveorder btn btn-micro pull-right" title="'
			. JText::_('JLIB_HTML_SAVE_ORDER') . '"><span class="icon-menu-2"></span></a>';
	}

	

	/**
	 * Method to build the behavior script and add it to the document head.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 *
	 * @deprecated  4.0 This is only used in hathor and will be removed without replacement
	 */
	public static function behavior() {
		static $loaded;

		if (!$loaded) {
			// Include jQuery
			JHtml::_('jquery.framework');

			// Build the behavior script.
			$js = '
		jQuery(function($){
			$actions = $(\'a.move_up, a.move_down, a.grid_true, a.grid_false, a.grid_trash\');
			$actions.each(function(){
				$(this).on(\'click\', function(){
					args = JSON.decode(this.rel);
					JBD.listItemTask(args.id, args.task);
				});
			});
			$(\'input.check-all-toggle\').each(function(){
				$(this).on(\'click\', function(){
					if (this.checked) {
						$(this).closest(\'form\').find(\'input[type="checkbox"]\').each(function(){
							this.checked = true;
						})
					}
					else {
						$(this).closest(\'form\').find(\'input[type="checkbox"]\').each(function(){
							this.checked = false;
						})
					}
				});
			});
		});';

			// Add the behavior to the document head.
			$document = JFactory::getDocument();
			$document->addScriptDeclaration($js);

			$loaded = true;
		}
	}
	
	/**
	 * Returns an action on a grid
	 *
	 * @param   integer       $i               The row index
	 * @param   string        $task            The task to fire
	 * @param   string|array  $prefix          An optional task prefix or an array of options
	 * @param   string        $text            An optional text to display [unused - @deprecated 4.0]
	 * @param   string        $active_title    An optional active tooltip to display if $enable is true
	 * @param   string        $inactive_title  An optional inactive tooltip to display if $enable is true
	 * @param   boolean       $tip             An optional setting for tooltip
	 * @param   string        $active_class    An optional active HTML class
	 * @param   string        $inactive_class  An optional inactive HTML class
	 * @param   boolean       $enabled         An optional setting for access control on the action.
	 * @param   boolean       $translate       An optional setting for translation.
	 * @param   string        $checkbox	       An optional prefix for checkboxes.
	 *
	 * @return  string  The HTML markup
	 *
	 * @since   1.6
	 */
	public static function action(
		$i,
		$task,
		$prefix = '',
		$text = '',
		$active_title = '',
		$inactive_title = '',
		$tip = false,
		$active_class = '',
		$inactive_class = '',
		$enabled = true,
		$translate = true,
		$checkbox = 'cb'
	) {
		if (is_array($prefix)) {
			$options = $prefix;
			$active_title = array_key_exists('active_title', $options) ? $options['active_title'] : $active_title;
			$inactive_title = array_key_exists('inactive_title', $options) ? $options['inactive_title'] : $inactive_title;
			$tip = array_key_exists('tip', $options) ? $options['tip'] : $tip;
			$active_class = array_key_exists('active_class', $options) ? $options['active_class'] : $active_class;
			$inactive_class = array_key_exists('inactive_class', $options) ? $options['inactive_class'] : $inactive_class;
			$enabled = array_key_exists('enabled', $options) ? $options['enabled'] : $enabled;
			$translate = array_key_exists('translate', $options) ? $options['translate'] : $translate;
			$checkbox = array_key_exists('checkbox', $options) ? $options['checkbox'] : $checkbox;
			$prefix = array_key_exists('prefix', $options) ? $options['prefix'] : '';
		}
		
		if ($tip) {
			;
			
			$title = $enabled ? $active_title : $inactive_title;
			$title = $translate ? JText::_($title) : $title;
			$title = JHtml::_('tooltipText', $title, '', 0);
		}
		
		
		
		$html[] = '<div class="d-flex align-items-center justify-content-center">';
			
		if ($enabled) {
			$html[] = '<a class="jtable-btn ' . ($active_class === 'publish' ? ' active' : '') . ($tip ? ' hasTooltip' : '') . '"';
			$html[] = ' href="javascript:void(0);" onclick="return JBD.listItemTask(\'' . $checkbox . $i . '\',\'' . $prefix . $task . '\')"';
			$html[] = $tip ? ' title="' . $title . '"' : '';
			$html[] = '>';
			$html[] = '<span class="' . $active_class . '" aria-hidden="true"></span>';
			$html[] = '</a>';
		} else {
			$html[] = '<a class="jtable-btn disabled jgrid' . ($tip ? ' hasTooltip' : '') . '"';
			$html[] = $tip ? ' title="' . $title . '"' : '';
			$html[] = '>';
			
			if ($active_class === 'protected') {
				$html[] = '<span class="icon-lock"></span>';
			} else {
				$html[] = '<span class="' . $inactive_class . '"></span>';
			}
			
			$html[] = '</a>';
		}
		
		$html[] = '</div>';
		
		
		return implode($html);
	}
	
	/**
	 * Returns an action on a grid
	 *
	 * @param   integer       $i               The row index
	 * @param   string        $task            The task to fire
	 * @param   string|array  $prefix          An optional task prefix or an array of options
	 * @param   string        $text            An optional text to display [unused - @deprecated 4.0]
	 * @param   string        $active_title    An optional active tooltip to display if $enable is true
	 * @param   string        $inactive_title  An optional inactive tooltip to display if $enable is true
	 * @param   boolean       $tip             An optional setting for tooltip
	 * @param   string        $active_class    An optional active HTML class
	 * @param   string        $inactive_class  An optional inactive HTML class
	 * @param   boolean       $enabled         An optional setting for access control on the action.
	 * @param   boolean       $translate       An optional setting for translation.
	 * @param   string        $checkbox	       An optional prefix for checkboxes.
	 *
	 * @return  string  The HTML markup
	 *
	 * @since   1.6
	 */
	public static function actionState(
		$i,
		$state,
		$task,
		$prefix = '',
		$text = '',
		$active_title = '',
		$inactive_title = '',
		$tip = false,
		$active_class = '',
		$inactive_class = '',
		$enabled = true,
		$translate = true,
		$checkbox = 'cb',
		$identifier='',
		$itemId = null
	) {
		if (is_array($prefix)) {
			$options = $prefix;
			$active_title = array_key_exists('active_title', $options) ? $options['active_title'] : $active_title;
			$inactive_title = array_key_exists('inactive_title', $options) ? $options['inactive_title'] : $inactive_title;
			$tip = array_key_exists('tip', $options) ? $options['tip'] : $tip;
			$active_class = array_key_exists('active_class', $options) ? $options['active_class'] : $active_class;
			$inactive_class = array_key_exists('inactive_class', $options) ? $options['inactive_class'] : $inactive_class;
			$enabled = array_key_exists('enabled', $options) ? $options['enabled'] : $enabled;
			$translate = array_key_exists('translate', $options) ? $options['translate'] : $translate;
			$checkbox = array_key_exists('checkbox', $options) ? $options['checkbox'] : $checkbox;
			$prefix = array_key_exists('prefix', $options) ? $options['prefix'] : '';
		}
		
		$checked = $state==1?'checked="checked"':'';
		
		
		if ($tip) {
			;
			
			$title = $enabled ? $active_title : $inactive_title;
			$title = $translate ? JText::_($title) : $title;
			$title = JHtml::_('tooltipText', $title, '', 0);
		}
		
		
		$html[] = '<div class="d-flex align-items-center justify-content-center">';
		$html[] = '<div class="jtoggle">';
		
		if ($enabled) {
			$html[] = '<input id="jtoggle-'.$identifier.'-'.$itemId.'" type="checkbox"'. $checked . ($tip ? ' hasTooltip' : '') . ' onclick="return jbdUtils.listItemTaskAjax(\'' . $checkbox . $i . '\',\'' ."jtoggle-".$identifier.'-' . $itemId . '\',\'' . $prefix . $task . '\')"';
			$html[] = $tip ? ' title="' . $title . '"' : '';
			$html[] = '>';
			
			$html[] = '<label for="jtoggle-11"><span></span></label>';
		} else {
			$html[] = '<a class="jtable-btn disabled jgrid' . ($tip ? ' hasTooltip' : '') . '"';
			$html[] = $tip ? ' title="' . $title . '"' : '';
			$html[] = '>';
			
			if ($active_class === 'protected') {
				$html[] = '<span class="icon-lock"></span>';
			} else {
				$html[] = '<span class="' . $inactive_class . '"></span>';
			}
			
			$html[] = '</a>';
		}
		
		$html[] = '</div>';
		$html[] = '</div>';
		
		
		return implode($html);
	}
	
	/**
	 * Returns a state on a grid
	 *
	 * @param   array         $states     array of value/state. Each state is an array of the form
	 *                                    (task, text, active title, inactive title, tip (boolean), HTML active class, HTML inactive class)
	 *                                    or ('task'=>task, 'text'=>text, 'active_title'=>active title,
	 *                                    'inactive_title'=>inactive title, 'tip'=>boolean, 'active_class'=>html active class,
	 *                                    'inactive_class'=>html inactive class)
	 * @param   integer       $value      The state value.
	 * @param   integer       $i          The row index
	 * @param   string|array  $prefix     An optional task prefix or an array of options
	 * @param   boolean       $enabled    An optional setting for access control on the action.
	 * @param   boolean       $translate  An optional setting for translation.
	 * @param   string        $checkbox   An optional prefix for checkboxes.
	 *
	 * @return  string  The HTML markup
	 *
	 * @since   1.6
	 */
	public static function state($states, $value, $i, $prefix = '', $enabled = true, $translate = true, $checkbox = 'cb', $itemId = null) {
		if (is_array($prefix)) {
			$options = $prefix;
			$enabled = array_key_exists('enabled', $options) ? $options['enabled'] : $enabled;
			$translate = array_key_exists('translate', $options) ? $options['translate'] : $translate;
			$checkbox = array_key_exists('checkbox', $options) ? $options['checkbox'] : $checkbox;
			$prefix = array_key_exists('prefix', $options) ? $options['prefix'] : '';
		}
		
		$identifier="state";
		
		$state = ArrayHelper::getValue($states, (int) $value, $states[0]);
		$task = array_key_exists('task', $state) ? $state['task'] : $state[0];
		$text = array_key_exists('text', $state) ? $state['text'] : (array_key_exists(1, $state) ? $state[1] : '');
		$active_title = array_key_exists('active_title', $state) ? $state['active_title'] : (array_key_exists(2, $state) ? $state[2] : '');
		$inactive_title = array_key_exists('inactive_title', $state) ? $state['inactive_title'] : (array_key_exists(3, $state) ? $state[3] : '');
		$tip = array_key_exists('tip', $state) ? $state['tip'] : (array_key_exists(4, $state) ? $state[4] : false);
		$active_class = array_key_exists('active_class', $state) ? $state['active_class'] : (array_key_exists(5, $state) ? $state[5] : '');
		$inactive_class = array_key_exists('inactive_class', $state) ? $state['inactive_class'] : (array_key_exists(6, $state) ? $state[6] : '');
		
		return static::actionState(
			$i,
			$value,
			$task,
			$prefix,
			$text,
			$active_title,
			$inactive_title,
			$tip,
			$active_class,
			$inactive_class,
			$enabled,
			$translate,
			$checkbox,
			$identifier,
			$itemId
		);
	}
	
	public static function approve($task, $value, $i, $prefix = '', $enabled = true, $checkbox = 'cb', $publish_up = null, $publish_down = null, $itemId = null) {
		$value = $value==1?0:$value;
		$value = $value==2?1:$value;
		
		$identifier="approval";
		
		$translate ="";
		$tip = true;
		$active_title=$value==1?JText::_("LNG_APPROVED"):JText::_("LNG_APROVED");
		$inactive_title="";
		$text=$value==1?JText::_("LNG_APPROVED"):JText::_("LNG_APROVED");
		$active_class="";
		$inactive_class="";
		
		return static::actionState(
			$i,
			$value,
			$task,
			$prefix,
			$text,
			$active_title,
			$inactive_title,
			$tip,
			$active_class,
			$inactive_class,
			$enabled,
			$translate,
			$checkbox,
			$identifier,
			$itemId
		);
	}
	
	
	/**
	 * Returns a published state on a grid
	 *
	 * @param   integer       $value         The state value.
	 * @param   integer       $i             The row index
	 * @param   string|array  $prefix        An optional task prefix or an array of options
	 * @param   boolean       $enabled       An optional setting for access control on the action.
	 * @param   string        $checkbox      An optional prefix for checkboxes.
	 * @param   string        $publish_up    An optional start publishing date.
	 * @param   string        $publish_down  An optional finish publishing date.
	 *
	 * @return  string  The HTML markup
	 *
	 * @see     JHtmlJGrid::state()
	 * @since   1.6
	 */
	public static function published($value, $i, $prefix = '', $enabled = true, $checkbox = 'cb', $publish_up = null, $publish_down = null, $itemId = null) {
		if (is_array($prefix)) {
			$options = $prefix;
			$enabled = array_key_exists('enabled', $options) ? $options['enabled'] : $enabled;
			$checkbox = array_key_exists('checkbox', $options) ? $options['checkbox'] : $checkbox;
			$prefix = array_key_exists('prefix', $options) ? $options['prefix'] : '';
		}
		
		$states = array(
			1 => array('unpublish', 'JPUBLISHED', 'JLIB_HTML_UNPUBLISH_ITEM', 'JPUBLISHED', true, 'publish', 'publish'),
			0 => array('publish', 'JUNPUBLISHED', 'JLIB_HTML_PUBLISH_ITEM', 'JUNPUBLISHED', true, 'unpublish', 'unpublish'),
			2 => array('unpublish', 'JARCHIVED', 'JLIB_HTML_UNPUBLISH_ITEM', 'JARCHIVED', true, 'archive', 'archive'),
			-2 => array('publish', 'JTRASHED', 'JLIB_HTML_PUBLISH_ITEM', 'JTRASHED', true, 'trash', 'trash'),
		);
		
		// Special state for dates
		if ($publish_up || $publish_down) {
			$nullDate = JFactory::getDbo()->getNullDate();
			$nowDate = JFactory::getDate()->toUnix();
			
			$tz = JBusinessUtil::getUser()->getTimezone();
			
			$publish_up = ($publish_up != $nullDate) ? JFactory::getDate($publish_up, 'UTC')->setTimeZone($tz) : false;
			$publish_down = ($publish_down != $nullDate) ? JFactory::getDate($publish_down, 'UTC')->setTimeZone($tz) : false;
			
			// Create tip text, only we have publish up or down settings
			$tips = array();
			
			if ($publish_up) {
				$tips[] = JText::sprintf('JLIB_HTML_PUBLISHED_START', JHtml::_('date', $publish_up, JText::_('DATE_FORMAT_LC5'), 'UTC'));
			}
			
			if ($publish_down) {
				$tips[] = JText::sprintf('JLIB_HTML_PUBLISHED_FINISHED', JHtml::_('date', $publish_down, JText::_('DATE_FORMAT_LC5'), 'UTC'));
			}
			
			$tip = empty($tips) ? false : implode('<br />', $tips);
			
			// Add tips and special titles
			foreach ($states as $key => $state) {
				// Create special titles for published items
				if ($key == 1) {
					$states[$key][2] = $states[$key][3] = 'JLIB_HTML_PUBLISHED_ITEM';
					
					if ($publish_up > $nullDate && $nowDate < $publish_up->toUnix()) {
						$states[$key][2] = $states[$key][3] = 'JLIB_HTML_PUBLISHED_PENDING_ITEM';
						$states[$key][5] = $states[$key][6] = 'pending';
					}
					
					if ($publish_down > $nullDate && $nowDate > $publish_down->toUnix()) {
						$states[$key][2] = $states[$key][3] = 'JLIB_HTML_PUBLISHED_EXPIRED_ITEM';
						$states[$key][5] = $states[$key][6] = 'expired';
					}
				}
				
				// Add tips to titles
				if ($tip) {
					$states[$key][1] = JText::_($states[$key][1]);
					$states[$key][2] = JText::_($states[$key][2]) . '<br />' . $tip;
					$states[$key][3] = JText::_($states[$key][3]) . '<br />' . $tip;
					$states[$key][4] = true;
				}
			}
			
			return static::state($states, $value, $i, array('prefix' => $prefix, 'translate' => !$tip), $enabled, true, $checkbox, $itemId);
		}
		
		return static::state($states, $value, $i, $prefix, $enabled, true, $checkbox, $itemId);
	}
	
	/**
	 * Returns an isDefault state on a grid
	 *
	 * @param   integer       $value     The state value.
	 * @param   integer       $i         The row index
	 * @param   string|array  $prefix    An optional task prefix or an array of options
	 * @param   boolean       $enabled   An optional setting for access control on the action.
	 * @param   string        $checkbox  An optional prefix for checkboxes.
	 *
	 * @return  string  The HTML markup
	 *
	 * @see     JHtmlJGrid::state()
	 * @since   1.6
	 */
	public static function isdefault($value, $i, $prefix = '', $enabled = true, $checkbox = 'cb') {
		if (is_array($prefix)) {
			$options = $prefix;
			$enabled = array_key_exists('enabled', $options) ? $options['enabled'] : $enabled;
			$checkbox = array_key_exists('checkbox', $options) ? $options['checkbox'] : $checkbox;
			$prefix = array_key_exists('prefix', $options) ? $options['prefix'] : '';
		}
		
		$states = array(
			0 => array('setDefault', '', 'JLIB_HTML_SETDEFAULT_ITEM', '', 1, 'unfeatured', 'unfeatured'),
			1 => array('unsetDefault', 'JDEFAULT', 'JLIB_HTML_UNSETDEFAULT_ITEM', 'JDEFAULT', 1, 'featured', 'featured'),
		);
		
		return static::state($states, $value, $i, $prefix, $enabled, true, $checkbox);
	}
	
	/**
	 * Returns an array of standard published state filter options.
	 *
	 * @param   array  $config  An array of configuration options.
	 *                          This array can contain a list of key/value pairs where values are boolean
	 *                          and keys can be taken from 'published', 'unpublished', 'archived', 'trash', 'all'.
	 *                          These pairs determine which values are displayed.
	 *
	 * @return  string  The HTML markup
	 *
	 * @since   1.6
	 */
	public static function publishedOptions($config = array()) {
		// Build the active state filter options.
		$options = array();
		
		if (!array_key_exists('published', $config) || $config['published']) {
			$options[] = JHtml::_('select.option', '1', 'JPUBLISHED');
		}
		
		if (!array_key_exists('unpublished', $config) || $config['unpublished']) {
			$options[] = JHtml::_('select.option', '0', 'JUNPUBLISHED');
		}
		
		if (!array_key_exists('archived', $config) || $config['archived']) {
			$options[] = JHtml::_('select.option', '2', 'JARCHIVED');
		}
		
		if (!array_key_exists('trash', $config) || $config['trash']) {
			$options[] = JHtml::_('select.option', '-2', 'JTRASHED');
		}
		
		if (!array_key_exists('all', $config) || $config['all']) {
			$options[] = JHtml::_('select.option', '*', 'JALL');
		}
		
		return $options;
	}
	
	/**
	 * Returns a checked-out icon
	 *
	 * @param   integer       $i           The row index.
	 * @param   string        $editorName  The name of the editor.
	 * @param   string        $time        The time that the object was checked out.
	 * @param   string|array  $prefix      An optional task prefix or an array of options
	 * @param   boolean       $enabled     True to enable the action.
	 * @param   string        $checkbox    An optional prefix for checkboxes.
	 *
	 * @return  string  The HTML markup
	 *
	 * @since   1.6
	 */
	public static function checkedout($i, $editorName, $time, $prefix = '', $enabled = false, $checkbox = 'cb') {
		;
		
		if (is_array($prefix)) {
			$options = $prefix;
			$enabled = array_key_exists('enabled', $options) ? $options['enabled'] : $enabled;
			$checkbox = array_key_exists('checkbox', $options) ? $options['checkbox'] : $checkbox;
			$prefix = array_key_exists('prefix', $options) ? $options['prefix'] : '';
		}
		
		$text = $editorName . '<br />' . JHtml::_('date', $time, JText::_('DATE_FORMAT_LC')) . '<br />' . JHtml::_('date', $time, 'H:i');
		$active_title = JHtml::_('tooltipText', JText::_('JLIB_HTML_CHECKIN'), $text, 0);
		$inactive_title = JHtml::_('tooltipText', JText::_('JLIB_HTML_CHECKED_OUT'), $text, 0);
		
		return static::action(
			$i,
			'checkin',
			$prefix,
			JText::_('JLIB_HTML_CHECKED_OUT'),
			html_entity_decode($active_title, ENT_QUOTES, 'UTF-8'),
			html_entity_decode($inactive_title, ENT_QUOTES, 'UTF-8'),
			true,
			'checkedout',
			'checkedout',
			$enabled,
			false,
			$checkbox
		);
	}
	
	/**
	 * Creates an order-up action icon.
	 *
	 * @param   integer       $i         The row index.
	 * @param   string        $task      An optional task to fire.
	 * @param   string|array  $prefix    An optional task prefix or an array of options
	 * @param   string        $text      An optional text to display
	 * @param   boolean       $enabled   An optional setting for access control on the action.
	 * @param   string        $checkbox  An optional prefix for checkboxes.
	 *
	 * @return  string  The HTML markup
	 *
	 * @since   1.6
	 */
	public static function orderUp($i, $task = 'orderup', $prefix = '', $text = 'JLIB_HTML_MOVE_UP', $enabled = true, $checkbox = 'cb') {
		if (is_array($prefix)) {
			$options = $prefix;
			$text = array_key_exists('text', $options) ? $options['text'] : $text;
			$enabled = array_key_exists('enabled', $options) ? $options['enabled'] : $enabled;
			$checkbox = array_key_exists('checkbox', $options) ? $options['checkbox'] : $checkbox;
			$prefix = array_key_exists('prefix', $options) ? $options['prefix'] : '';
		}
		
		return static::action($i, $task, $prefix, $text, $text, $text, false, 'uparrow', 'uparrow_disabled', $enabled, true, $checkbox);
	}
	
	/**
	 * Creates an order-down action icon.
	 *
	 * @param   integer       $i         The row index.
	 * @param   string        $task      An optional task to fire.
	 * @param   string|array  $prefix    An optional task prefix or an array of options
	 * @param   string        $text      An optional text to display
	 * @param   boolean       $enabled   An optional setting for access control on the action.
	 * @param   string        $checkbox  An optional prefix for checkboxes.
	 *
	 * @return  string  The HTML markup
	 *
	 * @since   1.6
	 */
	public static function orderDown($i, $task = 'orderdown', $prefix = '', $text = 'JLIB_HTML_MOVE_DOWN', $enabled = true, $checkbox = 'cb') {
		if (is_array($prefix)) {
			$options = $prefix;
			$text = array_key_exists('text', $options) ? $options['text'] : $text;
			$enabled = array_key_exists('enabled', $options) ? $options['enabled'] : $enabled;
			$checkbox = array_key_exists('checkbox', $options) ? $options['checkbox'] : $checkbox;
			$prefix = array_key_exists('prefix', $options) ? $options['prefix'] : '';
		}
		
		return static::action($i, $task, $prefix, $text, $text, $text, false, 'downarrow', 'downarrow_disabled', $enabled, true, $checkbox);
	}
}
