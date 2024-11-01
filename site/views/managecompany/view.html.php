<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

require_once(BD_HELPERS_PATH.'/helper.php');

JBusinessUtil::loadJQueryUI();


JBusinessUtil::enqueueStyle('libraries/jquery/jquery.timepicker.css');
JBusinessUtil::enqueueScript('libraries/jquery/jquery.timepicker.min.js');

JBusinessUtil::enqueueScript('libraries/dropzone/dropzone.js');
JBusinessUtil::enqueueScript('libraries/cropper/cropper.js');
JBusinessUtil::enqueueStyle('libraries/cropper/cropper.css');

JBusinessUtil::enqueueScript('libraries/cropper/canvas-toBlob.js');

JBusinessUtil::enqueueStyle('libraries/dropzone/dropzone.css');
JBusinessUtil::enqueueStyle('libraries/dropzone/basic.css');

JBusinessUtil::enqueueStyle('libraries/jquery/jquery-ui.css');
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

JBusinessUtil::includeValidation();

class JBusinessDirectoryViewManageCompany extends JViewLegacy {
	public function __construct() {
		parent::__construct();
	}
	
	
	public function display($tpl = null) {
		$this->appSettings = JBusinessUtil::getApplicationSettings();

		$this->item = $this->get('Item');
		$this->state = $this->get('State');
		$this->total = $this->get('Total');

		$this->customTabTranslation = JBusinessDirectoryTranslations::getAllTranslations(CUSTOM_TAB_TRANSLATION, $this->item->id);
		$this->translations = JBusinessDirectoryTranslations::getAllTranslations(BUSSINESS_DESCRIPTION_TRANSLATION, $this->item->id);
		$this->translationsSlogan = JBusinessDirectoryTranslations::getAllTranslations(BUSSINESS_SLOGAN_TRANSLATION, $this->item->id);
		$this->customTabTranslation = JBusinessDirectoryTranslations::getAllTranslations(CUSTOM_TAB_TRANSLATION, $this->item->id);
		$this->languages = JBusinessUtil::getLanguages();
		$this->membershipOptions = $this->get('MembershipOptions');
		$this->openingStatusOptions = $this->get('OpeningStatusOptions');

		//current package info
		if ($this->appSettings->enable_packages){
			$packageId = 0;
			if(!empty($this->item->package->id)) {
				$this->package = JBusinessUtil::getPackage($this->item->package->id);
				$packages = array($this->package);
				JBusinessUtil::packagesPriceVat($packages);
				$this->packageFeatures = JBusinessDirectoryHelper::getDefaultPackageFeatures($this->package);
				$this->customAttributes = JBusinessUtil::getPackagesAttributes($this->package);
				$packageId = $this->item->package->id;
			}
			
			//get all upgrade packages - cannot downgrade
			$price = 0;
			if (!empty($this->item->lastActivePackage) && $this->item->lastActivePackage->expired == false) {
				$price = $this->item->lastActivePackage->price;
			}

			$this->packageOptions = JBusinessDirectoryHelper::getPackageOptions($price, true, $packageId);
	
			if(empty($this->packageOptions)){
				$app = JFactory::getApplication();
				$app->enqueueMessage(JText::_("LNG_NO_ACTIVE_PACKAGE"), 'warning');
				$app->redirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanies', false));
			}
		}
		
		$this->weekDays = $this->get('WorkingDays');
		
		if ($this->appSettings->enable_multilingual) {
			JBusinessDirectoryTranslations::updateEntityTranslation($this->appSettings, TERMS_CONDITIONS_TRANSLATION);
			JBusinessDirectoryTranslations::updateEntityTranslation($this->appSettings, PRIVACY_POLICY_TRANSLATION);
		}
		
		$this->categoryOptions = JBusinessUtil::getCategoriesOptions(true, CATEGORY_TYPE_BUSINESS);
		$this->containerCategories = JBusinessUtil::getContainerCategories(CATEGORY_TYPE_BUSINESS);
				
		$this->actions = JBusinessDirectoryHelper::getActions();
		$this->editors = $this->get('Editors');
		$this->location = $this->get('Location');
		$user = JBusinessUtil::getUser();

		if ($this->item->userId != $user->ID && $this->item->id != 0 && !in_array($user->ID, $this->editors)) {
			$msg = JText::_("LNG_ACCESS_RESTRICTED");
			$app = JFactory::getApplication();
			$app->enqueueMessage($msg);
			$app->redirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanies', false));
		}
		
		$lang = JBusinessUtil::getLanguageTag();
		$key="";
		if (!empty($this->appSettings->google_map_key)) {
			$key="&key=".$this->appSettings->google_map_key;
		}

		$layout = JFactory::getApplication()->input->get("layout");

		if (!empty($layout)) {
			$this->setLayout($layout);
		}
	
		if ($this->total >= $this->appSettings->max_business && !empty($this->appSettings->max_business) && $this->item->id == 0) {
			$msg = JText::_("LNG_MAX_BUSINESS_LISTINGS_REACHED");
			$app =JFactory::getApplication();
			$app->enqueueMessage($msg);
			$app->redirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanies', false));
		}
		
		parent::display($tpl);
	}
	
	public function displayCompanyCategories($categories, $level) {
		ob_start(); ?>
			
		<select class="category-box" id="category<?php echo $level ?>"
				onchange ="jbdListings.displaySubcategories('category<?php echo $level ?>',<?php echo $level ?>,4)">
			<option value=""></option>	
		<?php
			foreach ($categories as $cat) {
				if (isset($cat[0]->name)) {?>
					<option value="<?php echo $cat[0]->id?>"><?php echo $cat[0]->name?></option>
						
					<?php
					}
			} ?>
			</select>
			<?php
			$buff = ob_get_contents();
		ob_end_clean();
		return $buff;
	}
		
	public function displayCompanyCategoriesOptions($categories) {
		ob_start();
		foreach ($categories as $cat) {
			if (isset($cat[0]->name)) {?>
				<option value="<?php echo $cat[0]->id?>"><?php echo $cat[0]->name?></option>
				<?php
				}
		}

		$buff = ob_get_contents();
		ob_end_clean();
		return $buff;
	}
}
?>
