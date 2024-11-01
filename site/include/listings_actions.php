<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
?>
<div class="result-item-actions">
    <?php if ($showData && (isset($company->packageFeatures) && in_array(PHONE, $company->packageFeatures) || !$enablePackages)) { ?>
        <?php if (!empty($company->phone)) { ?>
            <div class="horizontal-element phone" itemprop="telephone">
                <a href="tel:<?php echo $this->escape($company->phone); ?>" title="<?php echo JText::_('LNG_CALL') ?> <?php echo $this->escape($company->name) ?>"><i class="icon phone-circle"></i></a>
            </div>
        <?php } ?>
    <?php } ?>

    <?php if ($showData && !empty($company->website) && (isset($company->packageFeatures) && in_array(WEBSITE_ADDRESS, $company->packageFeatures) || !$enablePackages)) {
        if ($appSettings->enable_link_following) {
            $followLink = (isset($company->packageFeatures) && in_array(LINK_FOLLOW, $company->packageFeatures) && $enablePackages) ? 'rel="follow noopener"' : 'rel="nofollow noopener"';
        } else {
            $followLink = 'rel="noopener"';
        } ?>
        <div class="horizontal-element">
            <a target="_blank" <?php echo $followLink ?> title="<?php echo $this->escape($company->name) ?> <?php echo JText::_('LNG_WEBSITE') ?>" onclick="jbdUtils.registerStatAction(<?php echo $company->id ?>,<?php echo STATISTIC_ITEM_BUSINESS ?>,<?php echo STATISTIC_TYPE_WEBSITE_CLICK ?>)" href="<?php echo $this->escape($company->website) ?>"><i class="icon link-circle"></i></a>
        </div>
    <?php } ?>

    <?php if($appSettings->search_result_view != 2) { ?>
        <?php if ($showData && (isset($company->packageFeatures) && in_array(CONTACT_FORM, $company->packageFeatures) || !$enablePackages)) { ?>
            <?php if ($appSettings->show_contact_form) { ?>
                <div class="horizontal-element">
                    <a href="javascript:jbdListings.showContactCompanyList(<?php echo $company->id ?>,<?php echo $showData ? "1" : "0" ?>, '<?php echo $company->name ?>', '<?php echo $company->logoLocation ?>',  '<?php echo $company->business_cover_image ?>', <?php echo $company->review_score ?>)" title="<?php echo JText::_('LNG_CONTACT') ?> <?php echo $this->escape($company->name) ?>"><i class="icon envelope-circle"></i></a>
                </div>
            <?php } ?>
        <?php } ?>
    <?php } ?>

    <?php if(!empty($company->bookmark)) { ?>
        <!-- Business Bookmarks -->
        <!--div class="horizontal-element">
            <a id="bookmark-<?php echo $company->id ?>" href="javascript:jbdUtils.showUpdateBookmarkDialog(<?php echo $user->ID==0?"1":"0"?>, <?php echo $company->id ?>,<?php echo BOOKMARK_TYPE_BUSINESS ?>)"  title="<?php echo JText::_("LNG_UPDATE_BOOKMARK")?>" class="bookmark"><i class="icon heart-circle"></i></a>
        </div-->
    <?php } else {?>
        <!--div class="horizontal-element">
            <a id="bookmark-<?php echo $company->id ?>" href="javascript:jbdUtils.showAddBookmark(<?php echo $user->ID==0?"1":"0"?>, <?php echo $company->id ?>,<?php echo BOOKMARK_TYPE_BUSINESS ?>)" title="<?php echo JText::_("LNG_ADD_BOOKMARK")?>" class="bookmark"><i class="icon heart-o-circle"></i></a>
        </div -->
    <?php } ?>

    <?php if ($appSettings->show_contact_cards) { ?>
        <div class="horizontal-element">
            <a rel="nofollow" target="_blank" href="<?php echo JRoute::_("index.php?option=com_jbusinessdirectory&task=companies.generateQrCode&itemId=" . $company->id); ?>" title="<?php echo $this->escape($company->name) ?> <?php echo JText::_('LNG_QR_CODE') ?>"><i class="icon qr-code-circle"></i></a>
        </div>
        <div class="horizontal-element">
            <a rel="nofollow" href="<?php echo JRoute::_("index.php?option=com_jbusinessdirectory&task=companies.generateVCard&itemId=" . $company->id); ?>" title="<?php echo $this->escape($company->name) ?> <?php echo JText::_('LNG_VCARD') ?>"><i class="icon vcard-circle"></i></a>
        </div>
    <?php } ?>
</div>