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

$appSettings = JBusinessUtil::getApplicationSettings();
$enableSEO = $appSettings->enable_seo;
$enablePackages = $appSettings->enable_packages;
$enableRatings = $appSettings->enable_ratings;
$enableNumbering = $appSettings->enable_numbering;
$user = JBusinessUtil::getUser();

$limitStart = JFactory::getApplication()->input->getInt('limitstart', 0);
if (empty($limitStart)) {
	$limitStart  = JFactory::getApplication()->input->get('start', 0);
}

if(!$appSettings->search_results_loading) {
    $total_page_string = $this->pagination->getPagesCounter();
    $current_page = substr((string)$total_page_string, 5, 1);
    if (($current_page === 1) || $total_page_string==null) {
        $limitStart = 0;
    }
}

$showData = !($user->ID==0 && $appSettings->show_details_user == 1);
?>

<div id="jbd-results-list-container" itemscope itemtype="http://schema.org/ItemList" class="list-style-8" <?php echo $appSettings->search_view_mode?'style="display: none"':'' ?>>
   <?php
		if (!empty($this->companies)) {
			$itemCount = 1;
			foreach ($this->companies as $index=>$company) {
                $showLogo = $this->defaultAttributes["logo"] != ATTRIBUTE_NOT_SHOW && (isset($company->packageFeatures) && in_array(SHOW_COMPANY_LOGO, $company->packageFeatures) || !$enablePackages);
    ?>
		<?php
		  if (!empty($searchModules) && isset($searchModules[$index])) {
		  	foreach ($searchModules[$index] as $module) {
		  		?>
		          <div class="search-result-module">
		          	<?php echo JModuleHelper::renderModule($module, array("style"=>"xhtml")); ?>
		          </div>
		    <?php
		  	}
		  } ?>
        <div class="result-item shadow-border <?php echo isset($company->featured) && $company->featured==1?"featured":"" ?>" style="<?php echo !empty($company->featured) && !empty($appSettings->listing_featured_bg)?"background-color: $appSettings->listing_featured_bg":"" ?>">
        	<div itemscope itemprop="itemListElement" itemtype="http://schema.org/ListItem">
                <span style="display:none;" itemprop="position"><?php echo $itemCount ?></span>
                <div itemscope itemprop="item" itemtype="http://schema.org/LocalBusiness">
                    <div class="row">
					    <?php if ($showLogo) { ?>
                            <div class="col-md-2 pr-md-0">
                                <div class="item-image jitem-card" itemprop="logo" itemscope itemtype="http://schema.org/ImageObject">
                                    <a <?php echo $newTab; ?> href="<?php echo JBusinessUtil::getCompanyLink($company)?>"></a>
                                    <?php if (!empty($company->logoLocation)) {?>
                                        <img title="<?php echo $this->escape($company->name)?>" alt="<?php echo $this->escape($company->name)?>" src="<?php echo BD_PICTURES_PATH.$company->logoLocation ?>" itemprop="contentUrl" >
                                    <?php } else { ?>
                                        <img title="<?php echo $this->escape($company->name)?>" alt="<?php echo $this->escape($company->name)?>" src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>" itemprop="contentUrl" >
                                    <?php } ?>
                              	</div>
                             </div>
                        <?php } ?>
                      	<div class="col-md">
                      		<div class="result-content <?php echo !$showLogo?"no-image":""?>">
                                <h3 class="item-name">
                                    <a <?php echo $newTab; ?> href="<?php echo JBusinessUtil::getCompanyLink($company)?>"><?php echo $enableNumbering? "<span>".($index + $limitStart + 1).". </span>":""?><span itemprop="name"><?php echo $company->name ?></span></a>
                                    <?php if (!empty($company->recommended) && $appSettings->show_recommended) { ?>
                                        <div class="recommended" title="<?php echo JText::_("LNG_RECOMMENDED") ?>"><i class="la la-star"></i> </div>
                                    <?php } ?>
                                </h3>
                                <div class="item-element">
                                	  <ul class="horizontal-list">
                                        <?php if ($showData && !empty($company->website) && (isset($company->packageFeatures) && in_array(WEBSITE_ADDRESS, $company->packageFeatures) || !$enablePackages)) {
                                            if ($appSettings->enable_link_following) {
                                                $followLink = (isset($company->packageFeatures) && in_array(LINK_FOLLOW, $company->packageFeatures) && $enablePackages) ? 'rel="follow noopener"' : 'rel="nofollow noopener"';
                                            } else {
                                                $followLink ='rel="noopener"';
                                            } ?>
                                            <li><a <?php echo $followLink ?> title="<?php echo $this->escape($company->name)?> Website" target="_blank" onclick="jbdUtils.registerStatAction(<?php echo $company->id ?>,<?php echo STATISTIC_ITEM_BUSINESS ?>,<?php echo STATISTIC_TYPE_WEBSITE_CLICK ?>)"  href="<?php echo $this->escape($company->website) ?>"><?php echo JText::_('LNG_WEBSITE') ?></a></li>
                                        <?php } ?>
                                        <?php if ($showData && !empty($company->latitude) && !empty($company->longitude) && (isset($company->packageFeatures) && in_array(GOOGLE_MAP, $company->packageFeatures) || !$enablePackages)) {?>
                                                <li><a target="_blank" href="<?php echo JBusinessUtil::getDirectionURL($this->location, $company) ?>"><?php echo JText::_("LNG_GET_MAP_DIRECTIONS")?></a></li>
                                        <?php } ?>
                                          <li><a <?php echo $newTab; ?> href="<?php echo JBusinessUtil::getCompanyLink($company)?>"> <?php echo JText::_('LNG_MORE_INFO') ?></a></li>
                                          <?php if ($appSettings->show_contact_cards) {?>
                                            <li><a rel="nofollow" target="_blank" href="<?php echo JRoute::_("index.php?option=com_jbusinessdirectory&task=companies.generateQrCode&itemId=".$company->id); ?>"><?php echo JText::_("LNG_QR_CODE")?></a></li>
                                            <li><a rel="nofollow" href="<?php echo JRoute::_("index.php?option=com_jbusinessdirectory&task=companies.generateVCard&itemId=".$company->id); ?>"><?php echo JText::_("LNG_VCARD")?></a></li>
                                          <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <?php if (!empty($company->customAttributes) && $appSettings->show_custom_attributes) { ?>
                            <div class="col-md-4">
                                <div class="item-info-container">
                                    <div class="custom-attributes-list-view">
                                        <?php if (isset($company->customAttributes)) {
                                            $renderedContent = AttributeService::renderAttributesFront($company->customAttributes, $enablePackages, $company->packageFeatures);
                                            echo $renderedContent;
                                        } ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="col-md-1 pt-2">
                            <?php if(!empty($company->bookmark)) { ?>
                                <!-- Business Bookmarks -->
                                <a id="bookmark-<?php echo $company->id ?>" href="javascript:jbdUtils.showUpdateBookmarkDialog(<?php echo $user->ID==0?"1":"0"?>, <?php echo $company->id ?>,<?php echo BOOKMARK_TYPE_BUSINESS ?>)"  title="<?php echo JText::_("LNG_UPDATE_BOOKMARK")?>" class="bookmark"><i class="la la-heart"></i></a>
                            <?php } else {?>
                                <a id="bookmark-<?php echo $company->id ?>" href="javascript:jbdUtils.showAddBookmark(<?php echo $user->ID==0?"1":"0"?>, <?php echo $company->id ?>,<?php echo BOOKMARK_TYPE_BUSINESS ?>)" title="<?php echo JText::_("LNG_ADD_BOOKMARK")?>" class="bookmark"><i class="la la-heart-o"></i></a>
                            <?php } ?>

                            <?php if (isset($company->featured) && $company->featured==1) { ?>
                                <div class="featured-text">
                                    <?php echo JText::_("LNG_FEATURED")?>
                                </div>
                            <?php } ?>
                        </div>
                   	</div>
              	</div>
    		</div>
    	</div>
        <?php $itemCount++; ?>
     	<?php
			} ?>
    <?php
		} ?>
</div>