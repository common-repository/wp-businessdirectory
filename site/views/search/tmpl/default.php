<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 
defined('_JEXEC') or die('Restricted access');

$config = JBusinessUtil::getSiteConfig();
$document = JFactory::getDocument(); 
$appSettings = JBusinessUtil::getApplicationSettings();
$user = JBusinessUtil::getUser();
$newTab = ($appSettings->open_listing_on_new_tab)?" target='_blank'":"";

$base_url = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' ? 'https' : 'http' ) . '://' .  $_SERVER['HTTP_HOST'];
$url = $base_url . $_SERVER["REQUEST_URI"];

$title = "";
$description = "";
$keywords = "";
$image = "";

$items = array();
if(!empty($this->category->name))
	$items[] = $this->category->name;
if(!empty($this->type))
    $items[] = $this->type->name;
if(!empty($this->citySearch))
	$items[] = ucwords($this->city->cityName);
if(!empty($this->regionSearch))
	$items[] = ucwords($this->region->regionName);
if(!empty($this->provinceSearch))
    $items[]= ucwords($this->provinceSearch);
if(!empty($this->countrySearch) && !empty($this->country))
	$items[]= $this->country->country_name;

$items[] = JText::_("LNG_BUSINESS_LISTINGS_PAGE_TITLE");

$title = implode(" - ",$items);
$title .= " - ".$config->sitename;

$pageTitle = JBusinessUtil::getPageTitle($title);
if(!empty($pageTitle)){
    $title = $pageTitle;
}

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

JBusinessUtil::setMetaData($title, $description, $keywords, false);
JBusinessUtil::setFacebookMetaData($title, $description,$image, $url);

$enforceURL = JFactory::getApplication()->input->getInt('enforceURL', null);

$enableSearchFilter = $appSettings->enable_search_filter;
$fullWidth = true;
$mposition = "dir-search-listing-top";
$topModules = JModuleHelper::getModules($mposition);
$mposition = "dir-search-listing";
$bottomModules = JModuleHelper::getModules($mposition);
if(!empty($this->category)){
    $mposition = "dir-search-".$this->category->alias;
    $btModules = JModuleHelper::getModules($mposition);
    if(!empty($btModules)){
        $bottomModules = $btModules;
    }
}

if(($enableSearchFilter) || !empty($topModules) || !empty($bottomModules)){
    $fullWidth = false;
}

//add the possibility to chage the view and layout from http params
$list_layout = JFactory::getApplication()->input->get('list_layout');
if(!empty($list_layout)) {
	$appSettings->search_result_view = $list_layout;
}

$isFixedMapLayout = false;
if ($this->appSettings->search_result_view == 5 ) {
    $isFixedMapLayout = true;
}

$view_mode = JFactory::getApplication()->input->get('view_mode');
if(!empty($view_mode)) {
	$appSettings->search_view_mode = $view_mode;
}

$setCategory = isset($this->category)?1:0;
$categId = isset($this->categoryId)?$this->categoryId:0;

$showClear = 0;
$url = "index.php?option=com_jbusinessdirectory&view=search";

$searchResultsPositions=array(3,10);
$searchModules = array();
foreach($searchResultsPositions as $position){
    $searchModules[$position] = JModuleHelper::getModules("search-results-".$position);
}

$countryId = isset($_COOKIE['default-country'])?$_COOKIE['default-country']:0;
$country = JBusinessUtil::getCountry($countryId);

$region = !empty($this->regionSearch)?$this->escape($this->regionSearch): "";
$city = !empty($this->citySearch)?$this->escape($this->citySearch): "";

$app = JFactory::getApplication();
$active = $app->getMenu()->getActive();
if(!empty($active))
    $this->params = $active->getParams();
?>

<div id="jbd-container" class="jbd-container">
    <?php if (!empty($this->params) && $this->params->get('show_page_heading') && !empty($this->params->get('page_heading'))) { ?>
        <div class="page-header">
            <h1 class="title"> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
        </div>
    <?php } ?>

    <?php if ( is_active_sidebar( 'wpbd-listings-center' ) ) { ?>
        <div class="search-modules">
            <?php dynamic_sidebar( 'wpbd-listings-center' ); ?>
        </div>
    <?php } ?>

    <?php if(!$isFixedMapLayout){ ?>
        <div class="row">
            <?php if(!$fullWidth && $appSettings->search_filter_type == 2){?>
                <div class="col-lg-3">

                </div>
            <?php }?>
            <div class="col-lg">
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
                                
                            <?php if($appSettings->search_result_view != 5 && $appSettings->search_result_view != 9) { ?>
                                <?php if($appSettings->show_grid_list_option == 1) { ?>
                                    <div class="view-mode">
                                        <a id="grid-view-link" class="grid" href="javascript:jbdListings.showGrid()"><i class="la la-th-large"></i></a>
                                        <a id="list-view-link" class="list active" href="javascript:jbdListings.showList()"><i class="la la-list-ul"></i></a>
                                    </div>
                                <?php } ?>

                                <?php if($this->appSettings->show_search_map && $this->defaultAttributes['map'] != ATTRIBUTE_NOT_SHOW) { ?>
                                    <div id="search-map" class="view-mode">
                                        <a id="map-link" class="map" title="<?php echo JText::_("LNG_MAP")?>" href="javascript:jbdUtils.showMap(true)">
                                            <i class="la la-map"></i>
                                        </a>
                                        </div>
                                <?php } ?>
                            <?php } ?>

                            <?php if($this->appSettings->enable_rss == 1) { ?>
                                <div class="view-mode">
                                    <a href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=directoryrss.getCompaniesRss') ?>" target="_blank">
                                        <i class="la la-rss"></i>
                                    </a>
                                </div>
                            <?php } ?>
                            
                            <?php if($this->appSettings->enable_advanced_search_filter == 1) { ?>
                                <div id="filter-button" class="view-mode" >
                                    <?php echo JText::_('LNG_MORE_FILTERS')?>
                                    <i class="la la-filter"></i><i class="la la-close"></i>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>

    <div class="row">
    <?php if(!$fullWidth && !$isFixedMapLayout && $appSettings->search_filter_type == 2){?>
    	<div class="col-lg-3">
            <?php if ( is_active_sidebar( 'wpbd-listings-top' ) ) { ?>
        		<div class="search-modules">
                    <?php dynamic_sidebar( 'wpbd-listings-top' ); ?>
        		</div>
        	<?php } ?>	
            <?php if($enableSearchFilter && !$isFixedMapLayout){
                if ($appSettings->search_filter_type == 2) { ?>
                    <div class="search-filter-container" >
                        <div class="filters-header">
                            <i class="icon filters"></i> <?php echo JText::_("LNG_FILTERS") ?>
                        </div>
                        <div id="search-filters-react" class="vertical-filter">
                        </div>
                    </div>
                <?php }
            } ?>
    					
            <?php if ( is_active_sidebar( 'wpbd-listings-bottom' ) ) { ?>
        		<div class="search-modules">
                    <?php dynamic_sidebar( 'wpbd-listings-bottom' ); ?>
        		</div>
        	<?php } ?>	
    	</div>
    <?php }?>
    <div class="col-lg">
        <div id="search-results" class="search-results">
            <?php if (!$isFixedMapLayout) { ?>
                <div class="search-header">
                    <div id="search-filters-react-horizontal">
                    </div>

                    <?php if ($appSettings->search_filter_type != 2) { ?>
                        <div id="search-filters-react" class="horizontal-filter-container">
                        </div>
                    <?php } ?>

                    <div id="search-filters-react-container">
                    </div>
                    
                    <?php
                        //require "search_filter_params.php";
                    ?>

                    <?php if(isset($this->category) && $appSettings->show_cat_description && !empty($this->category->description) && (!empty($this->selectedCategories) && count($this->selectedCategories)<=1)) { ?>
                        <div class="category-container">
                            <div class="row">
                                <?php if(!empty($this->category->imageLocation)) { ?>
                                    <div class="col-md-3">
                                        <div class="categoy-image"><img alt="<?php echo $this->category->name?>" src="<?php echo BD_PICTURES_PATH.$this->category->imageLocation ?>"></div>
                                    </div>
                                <?php } ?>
                                <div class="col-md">
                                    <h1 class="category-title"><?php echo $this->category->name?></h1>
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

                    <div id="search-details">
                        <div id="search-module" class="search-module">
                            <?php
                                $modules = JModuleHelper::getModules("search-middle");
                                foreach($modules as $module) {
                                    echo JModuleHelper::renderModule($module, array("style"=>"xhtml"));
                                }
                            ?>
                            <div class="clear"></div>
                        </div>
                    </div>
                </div>
            <?php } ?>


            <?php $actionUrl = JBusinessUtil::getForActionURL($countryId, $region, $city, $this->categoryId, $enforceURL); ?>
        	<form action="<?php echo $actionUrl ?>" method="<?php echo $appSettings->submit_method ?>" name="adminForm" id="adminForm">
        	
        		<div id="listing-more-filter" class="listing-filter">
        			<div class="filter-actions" onclick="jQuery('#adminForm').submit();">
        				<div class="filter-action">
        					<i class="la la-filter"></i><br/>
        					<?php echo JText::_('LNG_FILTER')?>
        				</div>
        				<div class="filter-action" onclick="jbdUtils.resetMoreFilter()">
        					<i class="la la-close"></i><br/>
        					<?php echo JText::_('LNG_CLEAR')?>
        				</div>
        			</div>
        			<div>
        				<ul>
                            <?php
                             $moreFilters = JBusinessUtil::getMoreSearchFilterOptions();
                             foreach ($moreFilters as $filterKey => $filter){ ?>
                                 <li>
                                     <input class="" type="checkbox" name="<?php echo $filter->value; ?>" id="<?php echo $filter->value; ?>" value="1" onclick="jbdUtils.checkMoreFilterRule('<?php echo $filter->value; ?>')" <?php echo isset($this->moreFilters[$filter->value])?"checked":"" ?>>
                                     <label class="checkbox-label" for=""><?php echo $filter->text;?> </label>
                                 </li>
                            <?php } ?>
        				</ul>
        			</div>	
        		</div>
        	
        		<?php if($this->appSettings->search_result_view != 5 && $this->appSettings->search_result_view != 9 && $appSettings->show_search_map  && $this->defaultAttributes['map'] != ATTRIBUTE_NOT_SHOW) { ?>
        			<div id="companies-map-container" <?php echo !$this->appSettings->map_auto_show ? 'style="display:none;"' : '' ?> >
        				<?php require JPATH_COMPONENT_SITE.'/include/search-map.php' ?>
        			</div>
        		<?php } ?>
        
                <?php if(!$isFixedMapLayout){ ?>
        		    <?php require_once JPATH_COMPONENT_SITE.'/include/listings_grid_view.php'; ?>
                <?php } ?>
                
                <div id="list-view-container" class="loading" >
                    <?php 
                        if($appSettings->search_result_view == 1) {
                            require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_1.php';
                        } else if($appSettings->search_result_view == 2) {
                            require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_2.php';
                        } else if($appSettings->search_result_view == 3) {
                            require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_3.php';
                        } else if($appSettings->search_result_view == 4) {
                            require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_4.php';
                        } else if($appSettings->search_result_view == 5) {
                            require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_5.php';
                        } else if($appSettings->search_result_view == 6) {
                            require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_6.php';
                        } else if($appSettings->search_result_view == 7) {
                            require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_7.php';
                        } else if($appSettings->search_result_view == 8) {
                            require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_8.php';
                        } else if($appSettings->search_result_view == 9) {
                            require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_9.php';
                        }else {
                            require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_1.php';
                        } 
                    ?>

                    <div class="shimmer-list shimmer-container" <?php echo $appSettings->search_results_loading == 0 || $appSettings->search_view_mode == 1?'style="display: none"':'' ?>>
                        <div class="shimmer-list-item result-item">
                            <div class="row">
                                <div class="col-lg-3 pr-lg-0">
                                    <div class="shimmer-bg item-image-cover"></div>
                                </div>
                                <div class="col-lg-9">
                                    <div class="px-3">
                                        <div class="title shimmer-bg">
                                        </div>
                                        <div class="description">
                                            <div class="shimmer-bg content-line"></div>
                                            <div class="shimmer-bg content-line"></div>
                                            <div class="shimmer-bg content-line"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="shimmer-list-item result-item">
                            <div class="row">
                                <div class="col-lg-3 pr-lg-0">
                                    <div class="shimmer-bg item-image-cover"></div>
                                </div>
                                <div class="col-lg-9">
                                    <div class="px-3">
                                        <div class="title shimmer-bg">
                                        </div>
                                        <div class="description">
                                            <div class="shimmer-bg content-line"></div>
                                            <div class="shimmer-bg content-line"></div>
                                            <div class="shimmer-bg content-line"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="shimmer-list-item result-item">
                            <div class="row">
                                <div class="col-lg-3 pr-lg-0">
                                    <div class="shimmer-bg item-image-cover"></div>
                                </div>
                                <div class="col-lg-9">
                                    <div class="px-3">
                                        <div class="title shimmer-bg">
                                        </div>
                                        <div class="description">
                                            <div class="shimmer-bg content-line"></div>
                                            <div class="shimmer-bg content-line"></div>
                                            <div class="shimmer-bg content-line"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if($this->appSettings->search_results_loading){ ?>
                    <?php if(!$isFixedMapLayout){ ?>
                        <div id="load-more-btn" class="row" style="display:none">
                            <div class="col-12 text-center mt-4 btn-container">
                                <button type="button" class="load-more-js btn btn-outline-primary" onclick="jbdUtils.loadMoreItems()" ><?php echo JText::_("LNG_LOAD_MORE")?></button>
                            </div>
                        </div>
                    <?php } ?>
                    <input type="hidden" id="start" value="0"/>
                    <input type="hidden" id="total-results" value="<?php echo $this->pagination->total ?>"/>
        		    <input type='hidden' name='list_layout' id="list_layout" value='<?php echo $appSettings->search_result_view ?>' />
                    <input type='hidden' name='grid_layout' id="grid_layout" value='<?php echo $appSettings->search_result_grid_view ?>' />
                <?php } ?>

				<?php if ($this->pagination->get('pages.total') > 1 && $appSettings->search_result_view != 9 && !$this->appSettings->search_results_loading) { ?>
	        		<div class="pagination-container pagination">
	        			<?php echo $this->pagination->getListFooter(); ?>
	        			<div class="clear"></div>
	        		</div>
				<?php } ?>
        		
        		<input type='hidden' name='controller' value='search' />
                <input type='hidden' name='option' value='com_jbusinessdirectory' />
        		<input type='hidden' name='categories' id="categories-filter" value='<?php echo !empty($this->categories)?$this->categories:"" ?>' />
        		<input type='hidden' name='view' value='search' />
        		<input type='hidden' name='categoryId' id='categoryId' value='<?php echo !empty($this->categoryId)?$this->categoryId:"0" ?>' />
        		<input type='hidden' name='searchkeyword' id="searchkeyword" value='<?php echo !empty($this->searchkeyword)?$this->searchkeyword:'' ?>' />
        		<input type='hidden' name='letter' id="letter" value='<?php echo !empty($this->letter)?$this->letter:'' ?>' />
        		<input type='hidden' name="categorySearch" id="categorySearch" value='<?php echo !empty($this->categorySearch)?$this->categorySearch: '' ?>' />
        		<input type='hidden' name='citySearch' id='city-search' value="<?php echo !empty($this->citySearch)?$this->escape($this->citySearch): "" ?>" />
                <input type='hidden' name='regionSearch' id='region-search' value="<?php echo !empty($this->regionSearch)?$this->escape($this->regionSearch): "" ?>" />
                <input type='hidden' name='areaSearch' id='area-search' value="<?php echo !empty($this->areSaearch)?$this->escape($this->areSaearch): "" ?>" />
                <input type='hidden' name='provinceSearch' id='province-search' value="<?php echo !empty($this->provinceSearch)?$this->escape($this->provinceSearch): "" ?>" />
        		<input type='hidden' name='countrySearch' id='country-search' value='<?php echo !empty($this->countrySearch)?$this->countrySearch: '' ?>' />
        		<input type='hidden' name='typeSearch' id='type-search' value='<?php echo !empty($this->typeSearch)?$this->typeSearch: '' ?>' />
        		<input type='hidden' name='zipcode' id="zipcode" value="<?php echo !empty($this->zipCode)?$this->escape($this->zipCode): "" ?>" />
        		<input type='hidden' name='geo-latitude' id="geo-latitude" value="<?php echo !empty($this->location)?$this->escape($this->location["latitude"]): "" ?>" />
        		<input type='hidden' name='geo-longitude' id="geo-longitude" value="<?php echo !empty($this->location)?$this->escape($this->location["longitude"]): "" ?>" />
        		<input type='hidden' name='radius' id="radius" value='<?php echo !empty($this->radius)?$this->radius: '' ?>' />
        		<input type='hidden' name='featured' id="featured" value='<?php echo !empty($this->featured)?$this->featured: '' ?>' />
        		<input type='hidden' name='filter-by-fav' id="filter-by-fav" value='<?php echo !empty($this->filterByFav)?$this->filterByFav: '' ?>' />
        		<input type='hidden' name='filter_active' id="filter_active" value="<?php echo !empty($this->filterActive)?$this->filterActive: '' ?>" />
        		<input type='hidden' name='selectedParams' id='selectedParams' value="<?php echo !empty($this->selectedParams["selectedParams"])?$this->escape($this->selectedParams["selectedParams"]):"" ?>" />
        		<input type='hidden' name='form_submited' id="form_submited" value="1" />
                <input type="hidden" name="resetSearch" id="resetSearch" value="">
                <input type='hidden' name='moreParams' id='moreParams' value="<?php echo !empty($this->moreFilters)?$this->escape(implode(';',$this->moreFilters).';'):"" ?>" />
                <input type='hidden' name='orderBy' id='orderBy' value="<?php echo !empty($this->orderBy)?$this->orderBy:"" ?>" />
                <input type='hidden' name='only-local' id='only-local' value="<?php echo !empty($this->onlyLocal)?$this->onlyLocal: "" ?>" />
                <input type='hidden' name='preserve' id='preserve' value='<?php echo !empty($this->preserve)?$this->preserve: '' ?>' />
        		
        		<?php if(!empty($this->customAtrributes)){ ?>
        			<?php foreach($this->customAtrributes as $key=>$val){?>
        				<input type='hidden' class="attribute-search-class" name='attribute_<?php echo $key?>' value='<?php echo $val ?>' />
        			<?php } ?>
        		<?php } ?>
        		
        	</form>
        	<div class="clear"></div>
        </div>
    </div>
    </div>

    <div id="login-notice" class="jbd-container" style="display:none">
        <div class="jmodal-sm">
            <div class="jmodal-header">
                <p class="jmodal-header-title"><?php echo JText::_('LNG_INFO') ?></p>
                <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
            </div>
            <div class="jmodal-body">
                <p>
                    <?php echo JText::_('LNG_YOU_HAVE_TO_BE_LOGGED_IN') ?>
                </p>
                <p>
                    <a href="<?php echo JBusinessUtil::getLoginUrl($url); ?>"><?php echo JText::_('LNG_CLICK_LOGIN') ?></a>
                </p>
            </div>
        </div>
    </div>

	<?php //echo JBusinessUtil::getRssModal($this->categoryOptions) ?>
</div>
<?php
require_once JPATH_COMPONENT_SITE . '/include/bookmark_utils.php';
require_once JPATH_COMPONENT_SITE.'/include/listings_list_utils.php';


$showNotice = ($appSettings->enable_reviews_users && $user->ID ==0);
?>

<script>
window.addEventListener('load', function() {
    <?php if($appSettings->search_result_view == 5 || $this->appSettings->search_result_view == 5) { ?>
        jbdUtils.setProperty('search_filter_type',1);
    <?php } ?>

    jbdUtils.setProperty('get_items_url',jbdUtils.getAjaxUrl('getItemsAjax', 'search'));
    jbdUtils.setProperty('initialize_get_items_url',jbdUtils.getAjaxUrl('initializeGetItemsAjax', 'search'));
    jbdUtils.setProperty('filter_item_type', JBDConstants.ITEM_TYPE_BUSINESS);
    jbdUtils.setProperty('ajax_search_filter_type',jbdUtils.getProperty('search_filter_type'));

    <?php if($appSettings->search_filter_type == 2){ ?> 
        jbdUtils.setProperty("move-search-filter", true);
    <?php } ?>

    <?php if($this->pagination->total > 0 && $this->appSettings->search_results_loading){ ?>
        jbdUtils.loadMoreItems();
    <?php }else{ ?>
        jQuery("#list-view-container").removeClass("loading");
        jQuery("#jbd-grid-view").removeClass("loading");
    <?php } ?>

    <?php if($enableSearchFilter && ($this->pagination->total > 0 || !empty($this->selectedParams["selectedParams"]))){ ?>
        const e = React.createElement;
        ReactDOM.render(
             e(SearchFilter, {searchFilterType:jbdUtils.getProperty('search_filter_type'), showSearchFilterParams:true, itemType: JBDConstants.ITEM_TYPE_BUSINESS}),
             document.querySelector('#search-filters-react')
        );

    <?php } ?>

	<?php if($appSettings->enable_ratings &&  !$this->appSettings->search_results_loading){?>
        jbdListings.renderListAverageRating();
	<?php } ?>

	<?php
	   $jinput = JFactory::getApplication()->input;
	   
	   $load = $jinput->get("geo-latitude");
		if(empty($load)){
		    $load = $jinput->get("latitude");
		}
		$geolocation =$jinput->get("geolocation");
		if($geolocation && empty($load) && empty($this->form_submited)){ ?>
			if (navigator.geolocation) {
				navigator.geolocation.getCurrentPosition(jbdUtils.addCoordinatesToUrl);
			}
	<?php } ?>
   
	jQuery(".chosen").chosen({width:"165px", disable_search_threshold: 5, inherit_select_classes: true , placeholder_text_single: "<?php echo JText::_('LNG_SELECT_OPTION')  ?>" , placeholder_text_multiple: "<?php echo JText::_('LNG_SELECT_OPTION')  ?>"});
    jQuery(".chosen-select").chosen({width: "95%", disable_search_threshold: 5, search_contains: true, placeholder_text_single: "<?php echo JText::_('LNG_SELECT_OPTION')  ?>" , placeholder_text_multiple: "<?php echo JText::_('LNG_SELECT_OPTION')  ?>"});

    jQuery('.button-toggle').click(function() {
		if(!jQuery(this).hasClass("active")) {
			//jQuery(this).addClass('active');
		}
		jQuery('.button-toggle').not(this).removeClass('active'); // remove buttonactive from the others
	});

	jQuery('#filter-button').click(function() {  
		jQuery(this).toggleClass("active");
		jQuery(".listing-filter").toggleClass("open");

		if(jQuery(this).hasClass("active")){
			jQuery('html, body').animate({
			    scrollTop: jQuery("#listing-more-filter").offset().top
			}, 1000);
		}
	});

	if (jQuery("#moreParams").val().length > 0){
        jQuery(this).toggleClass("active");
        jQuery(".listing-filter").toggleClass("open");
    }

    <?php if ($appSettings->show_search_map && $appSettings->map_auto_show && $this->pagination->total > 0) { ?>
        jbdUtils.showMap(true);
    <?php } ?>

	<?php if ($appSettings->search_view_mode == 1 && $appSettings->search_result_view != 5) { ?>
        jbdListings.showGrid();
	<?php } else { ?>
        jbdListings.showList();
	<?php }?>

	//disable all empty fields to have a nice url
    <?php if($appSettings->submit_method=="get"){?>
	    jQuery('#adminForm').submit(function() {
	    	jQuery(':input', this).each(function() {
                if(jQuery(this).attr("name")!="categoryId"){
	                this.disabled = !(jQuery(this).val());
                }
	        });
	
	    	jQuery('#adminForm select').each(function() {
		    	if(!(jQuery(this).val()) || jQuery(this).val()==0){
	            	jQuery(this).attr('disabled', 'disabled');
		    	}
	        });
	    });

     <?php }?>

    jbdListings.collapseSearchFilter();
 	if(window.innerWidth<400){
 		jQuery(".search-filter").css("display","none");
 	}

 	jbdUtils.applyReadMore();
    jbdListings.setCategoryStatus(<?php echo isset($this->category)?'true':'false' ?>, <?php echo isset($this->categoryId)?$this->categoryId:0; ?>);

});


</script>