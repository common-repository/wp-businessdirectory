<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');


JBusinessUtil::includeValidation();

JBusinessUtil::loadJQueryUI();

JBusinessUtil::enqueueStyle('libraries/jquery/jquery.timepicker.css');
JBusinessUtil::enqueueScript('libraries/jquery/jquery.timepicker.min.js');

JBusinessUtil::enqueueScript('libraries/dropzone/dropzone.js');
JBusinessUtil::enqueueStyle('libraries/dropzone/dropzone.css');
JBusinessUtil::enqueueStyle('libraries/dropzone/basic.css');

JBusinessUtil::enqueueScript('libraries/cropper/cropper.js');
JBusinessUtil::enqueueStyle('libraries/cropper/cropper.css');
JBusinessUtil::enqueueScript('libraries/cropper/canvas-toBlob.js');

JBusinessUtil::enqueueScript('libraries/chosen/ajax-chosen.min.js');

JBusinessUtil::enqueueStyle('libraries/quill/quill.css');
JBusinessUtil::enqueueScript('libraries/quill/quill.min.js');

// following translations will be used in js
JText::script('LNG_VIDEO');
JText::script('LNG_SOUND');
JText::script('LNG_DELETE_LOCATION_CONF');
JText::script('LNG_LOCATION_DELETE_FAILED');
JText::script('LNG_CONTACT');
JText::script('LNG_ADD_BREAK');
JText::script('LNG_CLOSED');
JText::script('LNG_SERVICE');
JText::script('LNG_TESTIMONIAL');
JText::script('LNG_FILE_ALLREADY_ADDED');
JText::script('LNG_ERROR_ADDING_FILE');
JText::script('LNG_ERROR_ADDING_FILE');
JText::script('LNG_ERROR_GD_LIBRARY');
JText::script('LNG_ERROR_RESIZING_FILE');
JText::script('LNG_IMAGE_SIZE_WARNING');
JText::script('LNG_SELECT_OPTION');
JText::script('LNG_MEMBER');

JHtml::script('jui/fielduser.min.js');


/**
 * The HTML  View.
 */
class JBusinessDirectoryViewCompany extends JBusinessDirectoryAdminView {
	protected $item;
	protected $state;
	protected $packages;
	protected $claimDetails;

	/**
	 * Display the view
	 */
	public function display($tpl = null) {
		$this->appSettings = JBusinessUtil::getApplicationSettings();
		//Temporary notice to warn users about form validation
		//JFactory::getApplication()->enqueueMessage(JText::_("LNG_DISABLED_VAL"), 'warning');
		
		$this->item	 = $this->get('Item');
		$this->state = $this->get('State');
		$this->translations = JBusinessDirectoryTranslations::getAllTranslations(BUSSINESS_DESCRIPTION_TRANSLATION, $this->item->id);
		$this->translationsSlogan = JBusinessDirectoryTranslations::getAllTranslations(BUSSINESS_SLOGAN_TRANSLATION, $this->item->id);
		$this->translationsMeta = JBusinessDirectoryTranslations::getAllTranslations(BUSINESS_META_TRANSLATION, $this->item->id);
		$this->customTabTranslation = JBusinessDirectoryTranslations::getAllTranslations(CUSTOM_TAB_TRANSLATION, $this->item->id);
		$this->languages = JBusinessUtil::getLanguages();

		$this->categoryOptions = JBusinessUtil::getCategoriesOptions(true, CATEGORY_TYPE_BUSINESS);
		$this->containerCategories = JBusinessUtil::getContainerCategories(CATEGORY_TYPE_BUSINESS);
		$this->membershipOptions = $this->get('MembershipOptions');
		$this->claimDetails = $this->get('ClaimDetails');

		$this->openingStatusOptions = $this->get('OpeningStatusOptions');
		$this->weekDays = $this->get('WorkingDays');

		//current package info
		if ($this->appSettings->enable_packages){
			$packageId = 0;
		    if(!empty($this->item->package->id)) {
				$this->package = JBusinessUtil::getPackage($this->item->package->id);
				$this->packageFeatures = JBusinessDirectoryHelper::getDefaultPackageFeatures($this->package);
				$this->customAttributes = JBusinessUtil::getPackagesAttributes($this->package);
				$packageId = $this->item->package->id;
			}

			//get all upgrade packages - cannot downgrade
			$price = 0;
			if (!empty($this->item->lastActivePackage) && $this->item->lastActivePackage->expired == false) {
				$price = $this->item->lastActivePackage->price;
			}

			$this->packageOptions = JBusinessDirectoryHelper::getPackageOptions(0, false, $packageId);

			if (empty($this->packageOptions)) {
				$app = JFactory::getApplication();
				$app->enqueueMessage(JText::_("LNG_NO_ACTIVE_PACKAGE"), 'warning');
				$app->redirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=companies', false));
			}
		}
		
		$lang = JBusinessUtil::getLanguageTag();
		$key="";
		if (!empty($this->appSettings->google_map_key)) {
			$key="&key=".$this->appSettings->google_map_key;
		}
		
		$this->location = $this->get('Location');
	
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors), 500);
			return false;
		}

		if ($this->appSettings->edit_form_mode == 3) {
			$tpl = "sections";
		}
		
		$this->addToolbar($this->item, $this->claimDetails);
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since   1.6
	 */
	protected function addToolbar($item, $claimDetails) {
		$canDo = JBusinessDirectoryHelper::getActions();
		$input = JFactory::getApplication()->input;
		$input->set('hidemainmenu', true);
		$isNew = ($this->item->id == 0);

		JToolbarHelper::title(JText::_($isNew ? 'COM_JBUSINESSDIRECTORY_NEW_COMPANY' : 'COM_JBUSINESSDIRECTORY_EDIT_COMPANY'), 'menu.png');

		if ($canDo->get('core.edit')) {
			JToolbarHelper::apply('company.apply');
			JToolbarHelper::save('company.save');
		}

		if (!$isNew) {
			JToolbarHelper::save2copy('company.save2copy');
			$url  = JBusinessUtil::getCompanyLink($this->item);
			JToolbarHelper::link($url, JText::_('LNG_VIEW_BUSINESS'), "link");
		}

		if (isset($claimDetails) && $claimDetails->status == 0) {
			JToolBarHelper::divider();
			JToolBarHelper::custom('company.aproveClaim', 'publish.png', 'publish.png', JText::_("LNG_APPROVE_CLAIM"), false, false);
			JToolBarHelper::custom('company.disaproveClaim', 'unpublish.png', 'unpublish.png', JText::_("LNG_DISAPPROVE_CLAIM"), false, false);
			JToolBarHelper::divider();
		}

		// if($item->review_status == 0){
		// 	JToolBarHelper::custom('company.submitReview', 'batch.png', 'batch.png', JText::_("LNG_SUBMIT_REVIEW"), false, false);
		// }

		JToolbarHelper::cancel('company.cancel', 'JTOOLBAR_CLOSE');
		
		JToolbarHelper::divider();
		JToolBarHelper::help('', false, DOCUMENTATION_URL.'businessdiradmin.html#manage-companies');
	}
}
