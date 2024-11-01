<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 
defined('_JEXEC') or die('Restricted access');
require_once BD_CLASSES_PATH.'/attributes/attributeservice.php';
jimport('joomla.application.module.helper');

$config = JBusinessUtil::getSiteConfig();
$title = JText::_("LNG_CATALOG").' | '.$config->sitename;
$title = JBusinessUtil::getPageTitle($title);
JBusinessUtil::setMetaData($title, "", "", true);
$menuItemId = JBusinessUtil::getActiveMenuItem();

$user = JBusinessUtil::getUser();
// this is where you want to load your module position
$modules = JModuleHelper::getModules('categories-catalog');
$appSettings = JBusinessUtil::getApplicationSettings();
$fullWidth = true;

//add the possibility to chage the view and layout from http params
$list_layout = JFactory::getApplication()->input->get('list_layout');
if(!empty($list_layout)) {
	$this->appSettings->search_result_view = $list_layout;
}

$view_mode = JFactory::getApplication()->input->get('view_mode');
if(!empty($view_mode)) { 
	$this->appSettings->search_view_mode = $view_mode;
}

$newTab = ($appSettings->open_listing_on_new_tab)?" target='_blank'":"";

?>

<?php if (!empty($this->params) && $this->params->get('show_page_heading')) { ?>
    <div class="page-header">
        <h1 class="title"> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
    </div>
<?php } ?>

<?php if(isset($modules) && count($modules)>0) { ?>
	<div class="company-categories">
		<?php 
		$fullWidth = false;
		foreach($modules as $module) {
			echo JModuleHelper::renderModule($module, array("style"=>"xhtml"));
		} ?>
	</div>
<?php } ?>

<div class="jbd-container">
    <div id="search-results" class="search-results <?php echo $fullWidth ?'search-results-full':'search-results-normal' ?>">
    	<div class="search-header">
    		<div class="jbd-search-statistics">
        		<div class="search-keyword">
                    <div class="result-counter"><?php echo $this->pagination->getResultsCounter()?></div>
                </div>
                <div>
                    <div class="search-options">
                        <?php if($this->appSettings->search_result_view != 5) { ?>
                            <?php if($this->appSettings->show_grid_list_option == 1) { ?>
                            	<div class="view-mode">
									<a id="grid-view-link" class="grid" href="javascript:jbdListings.showGrid()"><i class="la la-th-large"></i></a>
									<a id="list-view-link" class="list active" href="javascript:jbdListings.showList()"><i class="la la-list-ul"></i></a>
								</div>
                            <?php } ?>

                            <?php if($this->appSettings->show_search_map && $this->defaultAttributes['map'] != ATTRIBUTE_NOT_SHOW) { ?>
                            	<div class="map-view">
                                    <a id="map-link" class="map <?php echo $this->appSettings->map_auto_show != 1 ? 'active' : '' ?>" title="Grid" href="javascript:jbdUtils.showMap(true)">
                                        <i class="la la-map"></i></a>
                                 </div>
                            <?php } ?>
                        <?php } ?>

                        <?php if($this->appSettings->enable_rss == 1) { ?>
                            <div class="search-options-item">
                                <a class="map" onclick="jQuery('#rss-model').jbdModal();" target="_blank">
                                    <i class="la la-rss"></i>
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
    	
        	<div id="search-details">
        		<?php require_once JPATH_COMPONENT_SITE.'/include/letterfilter.php'; ?>
        	</div>
        </div>
    
    	<?php if($this->appSettings->search_result_view != 5 && $appSettings->show_search_map  && $this->defaultAttributes['map'] != ATTRIBUTE_NOT_SHOW) { ?>
    		<div id="companies-map-container" <?php echo !$this->appSettings->map_auto_show ? 'style="display:none;"' : '' ?>>
    			<?php require_once JPATH_COMPONENT_SITE.'/include/search-map.php' ?>
    		</div>
    	<?php } ?>
    
    	<?php 
    	require_once JPATH_COMPONENT_SITE.'/include/listings_grid_view.php';
    
    	if($this->appSettings->search_result_view == 1) {
    		require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_1.php';
    	} else if($this->appSettings->search_result_view == 2) {
    		require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_2.php';
    	} else if($this->appSettings->search_result_view == 3) {
    		require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_3.php';
    	} else if($this->appSettings->search_result_view == 4) {
    		require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_4.php';
    	} else if($this->appSettings->search_result_view == 5) {
    		require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_5.php';
    	} else if($this->appSettings->search_result_view == 6){
    		require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_6.php';
    	} else if($this->appSettings->search_result_view == 7){
    		require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_7.php';
    	}
    	else {
    		require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_1.php';
    	} ?>
    	
    	<div class="pagination" <?php echo $this->pagination->total==0 ? 'style="display:none"':''?>>
    		<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory') ?>" method="post" name="adminForm" id="adminForm">
    			<input type='hidden' name='option' value='com_jbusinessdirectory'/>
    			<input type='hidden' name='view' value='catalog' />
    			<input type='hidden' name='letter' id="letter" value='<?php echo $this->letter ?>' />
    			<?php echo $this->pagination->getListFooter(); ?>
    		</form>
    		<div class="clear"></div>
    	</div>
    	<div class="clear"></div>
    </div>
</div>

<?php echo JBusinessUtil::getRssModal($this->categoryOptions) ?>

<?php 
	require_once JPATH_COMPONENT_SITE.'/include/listings_list_utils.php';
?>

<script type="text/javascript">
    window.addEventListener('load', function(){
        jQuery(".chosen-select").chosen({width: "95%", disable_search_threshold: 5, search_contains: true, placeholder_text_single: "<?php echo JText::_('LNG_SELECT_OPTION')  ?>" , placeholder_text_multiple: "<?php echo JText::_('LNG_SELECT_OPTION')  ?>"});
    });
	window.addEventListener('load', function()	{

		<?php if($this->appSettings->enable_ratings){?>
	        jbdListings.renderListAverageRating();
		<?php } ?>
		
		jQuery('.button-toggle').click(function() {  
			if(!jQuery(this).hasClass("active")) {       
				jQuery(this).addClass('active');
			}
			jQuery('.button-toggle').not(this).removeClass('active'); // remove buttonactive from the others
		});

		<?php if ($this->appSettings->show_search_map) { ?>
            jbdUtils.showMap(true);
		<?php } ?>

		<?php if ($this->appSettings->search_view_mode == 1) { ?>
			jbdListings.showGrid();
		<?php } else { ?>
			jbdListings.showList();
		<?php } ?>
	});
</script>