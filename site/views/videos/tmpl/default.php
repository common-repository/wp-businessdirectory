<?php // no direct access
/**
 * @copyright	Copyright (C) 2008-2009 CMSJunkie. All rights reserved.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.module.helper');

$document = JFactory::getDocument();
$config = JBusinessUtil::getSiteConfig();

$base_url = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' ? 'https' : 'http' ) . '://' .  $_SERVER['HTTP_HOST'];
$url = $base_url . $_SERVER["REQUEST_URI"];

$title = "";
$description = "";
$keywords = "";
$image = "";

$items = array();
$items[] = JText::_("LNG_VIDEOS");
if(!empty($this->category->name))
	$items[] = $this->category->name;

$title = implode(" | ",$items);
$title .= " | ".$config->sitename;

if (isset($this->category)){
	if (!empty($this->category->meta_title)){
		$title = $this->category->meta_title;
	}
	if (!empty($this->category->meta_description)){
		$description = $this->category->meta_description;
	}
	if (!empty($this->category->meta_keywords)){
		$keywords =  $this->category->meta_keywords;
	}
	if (!empty($this->category->imageLocation)){
		$image = $this->category->imageLocation;
	}
}

$title = JBusinessUtil::getPageTitle($title);
JBusinessUtil::setMetaData($title, $description, $keywords, true);
JBusinessUtil::setFacebookMetaData($title, $description,$image, $url);

$menuItemId = JBusinessUtil::getActiveMenuItem();

$fullWidth = true;
$enableSearchFilter = $this->appSettings->enable_search_filter_offers;
$mposition = "dir-search-offers-top";
$topModules = JModuleHelper::getModules($mposition);
$mposition = "dir-search-offers";
if (!empty($this->category)) {
	$mposition = "dir-search-offers-" . $this->category->alias;
}
$bottomModules = JModuleHelper::getModules($mposition);

if($enableSearchFilter || !empty($topModules) || !empty($bottomModules)){
	$fullWidth = false;
}

//add the possibility to chage the view and layout from http params
$grid_layout = JFactory::getApplication()->input->get('grid_layout');
if(!empty($grid_layout)) {
	$this->appSettings->offer_search_results_grid_view = $grid_layout;
}

$list_layout = JFactory::getApplication()->input->get('list_layout');
if(!empty($list_layout)) {
	$this->appSettings->offer_search_results_list_view = $list_layout;
}

$view_mode = JFactory::getApplication()->input->get('view_mode',null);
if(isset($view_mode)) {
	$this->appSettings->offers_view_mode = $view_mode;
}

$setCategory = isset($this->category)?1:0;
$categId = isset($this->categoryId)?$this->categoryId:0;
$showClear = 0;
?>

<?php if (!empty($this->params) && $this->params->get('show_page_heading', 1)) { ?>
    <div class="page-header">
        <h1 class="title"> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
    </div>
<?php } ?>

<div id="jbd-container" class="jbd-container jbd-container videos">
    <div class="row" id="filterCategoryItems">
        <div class="col-md">
            <div id="search-results" class="search-results">
                <div class="search-header">
                    <div class="row">
                        <div class="col-12">
                            <div class="jbd-search-statistics">
                                <div class="search-keyword">
                                    <div class="result-counter"><?php echo $this->pagination->getResultsCounter()?></div>
                                </div>
                                <div>
                                    <div class="search-options">
                                        <div class="jbd-select-box">
                                            <i class="la la-sort"></i>
                                            <select name="orderBy" class="chosen jbd-select" onchange="jbdUtils.changeOrder(this.value)">
												<?php echo JHtml::_('select.options', $this->sortByOptions, 'value', 'text',  $this->orderBy);?>
                                            </select>
                                        </div>

                                    </div>
                                </div>
                            </div>

							<?php require_once 'search_params.php'; ?>

							<?php if(isset($this->category) && $this->appSettings->show_cat_description && !empty($this->category->description)) { ?>
                                <div class="category-container">
                                    <div class="row">
										<?php if(!empty($this->category->imageLocation)) { ?>
                                            <div class="col-md-3">
                                                <div class="categoy-image"><img alt="<?php echo $this->category->name?>" src="<?php echo BD_PICTURES_PATH.$this->category->imageLocation ?>"></div>
                                            </div>
										<?php } ?>
                                        <div class="col-md">
                                            <h3><?php echo $this->category->name?></h3>
                                            <div>
                                                <div id="category-description" class="dir-cat-description">
                                                    <div class="intro-text">
														<?php echo JBusinessUtil::truncate(JHTML::_("content.prepare", $this->category->description),300) ?>
														<?php if(strlen(strip_tags($this->category->description))>strlen(strip_tags(JBusinessUtil::truncate(JHTML::_("content.prepare", $this->category->description),300)))){?>
                                                            <a class="cat-read-more" href="javascript:void(0)" onclick="jQuery('#category-description').toggleClass('open')">
																<?php echo JText::_("LNG_MORE") ?> </a>
														<?php } ?>
                                                    </div>
                                                    <div class="full-text">
														<?php echo JHTML::_("content.prepare", $this->category->description) ?>
                                                        <a class="cat-read-more" href="javascript:void(0)" onclick="jQuery('#category-description').toggleClass('open')">
															<?php echo JText::_("LNG_LESS") ?> </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
							<?php } ?>
                        </div>
                    </div>
                </div>


                <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory'.$menuItemId) ?>" method="post" name="adminForm" id="adminForm">
					<?php
                        require_once "videos_list_style_1.php";
					?>
                    <div class="pagination" <?php echo $this->pagination->total==0 ? 'style="display:none"':''?>>
						<?php echo $this->pagination->getListFooter(); ?>
                        <div class="clear"></div>
                    </div>

                    <input type='hidden' name='option' value='com_jbusinessdirectory'/>
                    <input type='hidden' name='view' value='videos' />
                    <input type='hidden' id="categories-filter" name='categories' value='<?php echo isset($this->categories)?$this->categories:"" ?>' />
                    <input type='hidden' id="categoryId" name='categoryId'  value='<?php echo isset($this->categoryId)?$this->categoryId:"0" ?>' />
                    <input type='hidden' name='searchkeyword' id="searchkeyword" value='<?php echo isset($this->searchkeyword)?$this->searchkeyword:'' ?>' />
                    <input type='hidden' name='selectedParams' id='selectedParams' value='<?php echo !empty($this->selectedParams["selectedParams"])?$this->selectedParams["selectedParams"]:"" ?>' />
                    <input type='hidden' name='resetSearch' id="resetSearch" value="" />
                    <input type='hidden' name='orderBy' id='orderBy' value="<?php echo !empty($this->orderBy)?$this->orderBy:"" ?>" />
                </form>
                <div class="clear"></div>
            </div>
        </div>
    </div>
</div>

<script>
    window.addEventListener('load', function(){

        jQuery(".chosen").chosen({width:"165px", disable_search_threshold: 5, inherit_select_classes: true , placeholder_text_single: "<?php echo JText::_('LNG_SELECT_OPTION')  ?>" , placeholder_text_multiple: "<?php echo JText::_('LNG_SELECT_OPTION')  ?>"});
        jQuery(".chosen-select").chosen({width: "95%", disable_search_threshold: 5, search_contains: true, placeholder_text_single: "<?php echo JText::_('LNG_SELECT_OPTION')  ?>" , placeholder_text_multiple: "<?php echo JText::_('LNG_SELECT_OPTION')  ?>"});

        jQuery("#filter-switch").click(function(){
            //jQuery("#search-filter").toggleClass("open");
            jQuery("#search-filter").slideToggle(500);
            if (jQuery('#search-filter').height()<10){
                jQuery(this).html("<?php echo JText::_("LNG_HIDE_FILTER")?>")
            }else{
                jQuery(this).html("<?php echo JText::_("LNG_SHOW_FILTER")?>")
            }
        });
    });
</script>
