<?php
/**
 * @package    JHotelReservation
 *
 * @author CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
/**
 * The HTML  View.
 */
class JBusinessDirectoryViewLanguage extends JBusinessDirectoryAdminView {
	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null) {
		
		$this->file = $this->get('File');
		
		$layout = $this->getLayout();
		if (method_exists($this, $layout)) {
			$tpl = $this->$layout();
		}
		$this->setLayout($layout);
		
		$this->addToolbar();
		parent::display($tpl);
	}
	
	

	protected function addToolbar() {
		$input = JFactory::getApplication()->input;
		$input->set('hidemainmenu', true);
		JToolbarHelper::title(JText::_('LNG_LANGUAGE', true), 'menu.png');

		$layout = JFactory::getApplication()->input->get('layout');

		if ($layout == 'create') {
			JToolbarHelper::save('language.store');
		} else {
			JToolbarHelper::apply('language.apply');
			JToolbarHelper::save('language.save');
			JToolbarHelper::custom('language.send_email', 'mail', 'mail' , JTEXT::_('LNG_SEND_EMAIL'), false, false);

		}

		JToolbarHelper::cancel('language.cancel', 'LNG_CLOSE');
		
		JToolbarHelper::divider();
		JToolbarHelper::help('JHELP_JBUSINESSDIRECTORY_COMPANY_TYPE_EDIT');
	}
}
