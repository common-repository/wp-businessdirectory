<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 
defined('_JEXEC') or die('Restricted access');

$document = JFactory::getDocument();
$config = JBusinessUtil::getSiteConfig();

$title = JText::_("LNG_COMPANY_CATEGORIES");
if($this->categoryType == CATEGORY_TYPE_EVENT)
	$title = JText::_("LNG_EVENT_CATEGORIES");
else if($this->categoryType == CATEGORY_TYPE_OFFER)
	$title = JText::_("LNG_OFFER_CATEGORIES");

$title .=' | '.$config->sitename;

$title = JBusinessUtil::getPageTitle($title);
JBusinessUtil::setMetaData($title, "", "", true);

$view_mode = JFactory::getApplication()->input->get('view_style');
if(!empty($view_mode)) {
	$this->appSettings->category_view = $view_mode;
}
?>

<div class="jbd-container">
	<?php if (!empty($this->params) && $this->params->get('show_page_heading')) { ?>
		<div class="page-header">
			<h1 class="title"> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
		</div>
	<?php }else{ ?>
		<div class="categories-header">
			<h1><?php echo JText::_("LNG_EXPLORE_BY_CATEGORIES")?></h1>
			<div class="categories-text">
				<?php echo JText::_("LNG_EXPLORE_BY_CATEGORIES_TEXT")?>
			</div>
		</div>
	<?php } ?>

    <?php
    	$categories=$this->categories;
    	$appSettings=$this->appSettings;
    
      	if($this->appSettings->category_view==1){
            require_once JPATH_COMPONENT_SITE.'/include/categories_style_1.php';
    	}else if($this->appSettings->category_view==3){
    		require_once JPATH_COMPONENT_SITE.'/include/categories_style_3.php';
    	}
    	else if($this->appSettings->category_view==4){
    		require_once JPATH_COMPONENT_SITE.'/include/categories_style_4.php';
    	}
        else if($this->appSettings->category_view==5){
            require_once JPATH_COMPONENT_SITE.'/include/categories_style_5.php';
        }
    	else{
    		require_once JPATH_COMPONENT_SITE."/include/categories_style_2.php";
    	}
    ?>
</div>