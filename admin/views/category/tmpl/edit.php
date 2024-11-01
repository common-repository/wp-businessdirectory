<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.keepalive');
jimport('joomla.html.pane');

JBusinessUtil::includeColorPicker();
$app = JFactory::getApplication();

$options = array(
	'onActive' => 'function(title, description){
	description.setStyle("display", "block");
	title.addClass("open").removeClass("closed");
}',
	'onBackground' => 'function(title, description){
	description.setStyle("display", "none");
	title.addClass("closed").removeClass("open");
}',
	'startOffset' => 0,  // 0 starts on the first tab, 1 starts the second, etc...
	'useCookie' => true, // this must not be a string. Don't use quotes.
);
$jbdTabs = new JBDTabs();
$jbdTabs->setOptions($options);
?>

<script type="text/javascript">
window.addEventListener('load', function() {
	JBD.submitbutton = function(task) {

		JBD.submitform(task, document.getElementById('item-form'));
    }
})
</script>

<div id="jbd-container" class="jbd-container jbd-edit-container">
	<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-horizontal">
        <div class="row">
            <div class="col-md-7">
                <div class="row">
                    <div class="col-md-12">
                        <fieldset class="boxed">
                            <h2> <?php echo JText::_('LNG_CATEGORY_DETAILS');?></h2>
                            <div class="form-container">
                                <div class="form-group">
                                    <label for="name"><?php echo JText::_('LNG_NAME') ?><?php echo JBusinessUtil::showMandatory(ATTRIBUTE_MANDATORY) ?></label>
                                    <?php
                                    if($this->appSettings->enable_multilingual) {
                                        echo $jbdTabs->startTabSet('tab_group_name');
                                        foreach( $this->languages as $k=>$lng ){
                                            echo $jbdTabs->addTab('tab_group_name', 'tab-' . $lng, $k);
                                            $langContent = isset($this->translations[$lng."_name"])?$this->translations[$lng."_name"]:"";
                                            if($lng == JBusinessUtil::getLanguageTag() && empty($langContent)){
                                                $langContent = $this->item->name;
                                            }
                                            $langContent=$this->escape($langContent);
                                            echo "<input type='text' name='name_$lng' id='name_$lng' class='input_txt form-control validate[required]' value=\"".stripslashes($langContent)."\"  maxLength='100'>";
                                            echo $jbdTabs->endTab();
                                        }
                                        echo $jbdTabs->endTabSet();
                                    } else { ?>
                                        <input type="text" name="name" id="name" class="input_txt form-control validate[required]" value="<?php echo $this->item->name ?>"  maxLength="100">
                                    <?php } ?>
                                </div>
                                <div class="form-group">
                                    <label for="name"><?php echo JText::_('LNG_ALIAS')?> </label>
                                    <input type="text"	name="alias" id="alias"  placeholder="<?php echo JText::_('LNG_AUTO_GENERATE_FROM_NAME')?>" class="input_txt form-control text-input" value="<?php echo $this->item->alias ?>"  maxLength="100">
                                </div>
                                <div class="form-group">
                                    <label for="name"><?php echo JText::_('LNG_PARENT')?> </label>
                                    <select id="parent_id" name="parent_id" class="form-control select inputbox input-medium">
                                        <?php echo JHtml::_('select.options', $this->categoryOptions, 'value', 'text', $this->item->parent_id);?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="type"><?php echo JText::_('LNG_TYPE')?><?php echo JBusinessUtil::showMandatory(ATTRIBUTE_MANDATORY)?> </label>
                                    <select id="type" name="type" class="form-control select inputbox input-medium input_sel validate[required]" disabled>
                                        <?php echo JHtml::_('select.options', $this->types, 'value', 'text', $this->typeSelected); ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="icon"><?php echo JText::_('LNG_ICON')?></label>
                                    <select id="icon-holder" name="icon" data-placeholder="<?php echo JText::_('LNG_CHOOSE_ICON') ?>" class="icon-select">
                                        <option value=""></option>
                                        <option data-icon="x"><?php echo JText::_('LNG_NONE')?></option>
                                        <option data-icon="la-adjust">adjust</option>
                                        <option data-icon="la-adn">adn</option>
                                        <option data-icon="la-align-center">align-center</option>
                                        <option data-icon="la-align-justify">align-justify</option>
                                        <option data-icon="la-align-left">align-left</option>
                                        <option data-icon="la-align-right">align-right</option>
                                        <option data-icon="la-ambulance">ambulance</option>
                                        <option data-icon="la-anchor">anchor</option>
                                        <option data-icon="la-android">android</option>
                                        <option data-icon="la-angellist">angellist</option>
                                        <option data-icon="la-angle-double-down">angle-double-down</option>
                                        <option data-icon="la-angle-double-left">angle-double-left</option>
                                        <option data-icon="la-angle-double-right">angle-double-right</option>
                                        <option data-icon="la-angle-double-up">angle-double-up</option>
                                        <option data-icon="la-angle-down">angle-down</option>
                                        <option data-icon="la-angle-left">angle-left</option>
                                        <option data-icon="la-angle-right">angle-right</option>
                                        <option data-icon="la-angle-up">angle-up</option>
                                        <option data-icon="la-apple">apple</option>
                                        <option data-icon="la-archive">archive</option>
                                        <option data-icon="la-area-chart">area-chart</option>
                                        <option data-icon="la-arrow-circle-down">arrow-circle-down</option>
                                        <option data-icon="la-arrow-circle-left">arrow-circle-left</option>
                                        <option data-icon="la-arrow-circle-o-down">arrow-circle-o-down</option>
                                        <option data-icon="la-arrow-circle-o-left">arrow-circle-o-left</option>
                                        <option data-icon="la-arrow-circle-o-right">arrow-circle-o-right</option>
                                        <option data-icon="la-arrow-circle-o-up">arrow-circle-o-up</option>
                                        <option data-icon="la-arrow-circle-right">arrow-circle-right</option>
                                        <option data-icon="la-arrow-circle-up">arrow-circle-up</option>
                                        <option data-icon="la-arrow-down">arrow-down</option>
                                        <option data-icon="la-arrow-left">arrow-left</option>
                                        <option data-icon="la-arrow-right">arrow-right</option>
                                        <option data-icon="la-arrow-up">arrow-up</option>
                                        <option data-icon="la-arrows">arrows</option>
                                        <option data-icon="la-arrows-alt">arrows-alt</option>
                                        <option data-icon="la-arrows-h">arrows-h</option>
                                        <option data-icon="la-arrows-v">arrows-v</option>
                                        <option data-icon="la-asterisk">asterisk</option>
                                        <option data-icon="la-at">at</option>
                                        <option data-icon="la-automobile">automobile</option>
                                        <option data-icon="la-backward">backward</option>
                                        <option data-icon="la-ban">ban</option>
                                        <option data-icon="la-bank">bank</option>
                                        <option data-icon="la-bar-chart">bar-chart</option>
                                        <option data-icon="la-bar-chart-o">bar-chart-o</option>
                                        <option data-icon="la-barcode">barcode</option>
                                        <option data-icon="la-bars">bars</option>
                                        <option data-icon="la-beer">beer</option>
                                        <option data-icon="la-behance">behance</option>
                                        <option data-icon="la-behance-square">behance-square</option>
                                        <option data-icon="la-bell">bell</option>
                                        <option data-icon="la-bell-o">bell-o</option>
                                        <option data-icon="la-bell-slash">bell-slash</option>
                                        <option data-icon="la-bell-slash-o">bell-slash-o</option>
                                        <option data-icon="la-bicycle">bicycle</option>
                                        <option data-icon="la-binoculars">binoculars</option>
                                        <option data-icon="la-birthday-cake">birthday-cake</option>
                                        <option data-icon="la-bitbucket">bitbucket</option>
                                        <option data-icon="la-bitbucket-square">bitbucket-square</option>
                                        <option data-icon="la-bitcoin">bitcoin</option>
                                        <option data-icon="la-bold">bold</option>
                                        <option data-icon="la-bolt">bolt</option>
                                        <option data-icon="la-bomb">bomb</option>
                                        <option data-icon="la-book">book</option>
                                        <option data-icon="la-bookmark">bookmark</option>
                                        <option data-icon="la-bookmark-o">bookmark-o</option>
                                        <option data-icon="la-briefcase">briefcase</option>
                                        <option data-icon="la-btc">btc</option>
                                        <option data-icon="la-bug">bug</option>
                                        <option data-icon="la-building">building</option>
                                        <option data-icon="la-building">building-o</option>
                                        <option data-icon="la-bullhorn">bullhorn</option>
                                        <option data-icon="la-bullseye">bullseye</option>
                                        <option data-icon="la-bus">bus</option>
                                        <option data-icon="la-cab">cab</option>
                                        <option data-icon="la-calculator">calculator</option>
                                        <option data-icon="la-calendar">calendar</option>
                                        <option data-icon="la-calendar-o">calendar-o</option>
                                        <option data-icon="la-camera">camera</option>
                                        <option data-icon="la-camera-retro">camera-retro</option>
                                        <option data-icon="la-car">car</option>
                                        <option data-icon="la-caret-down">caret-down</option>
                                        <option data-icon="la-caret-left">caret-left</option>
                                        <option data-icon="la-caret-right">caret-right</option>
                                        <option data-icon="la-caret-square-o-down">caret-square-o-down</option>
                                        <option data-icon="la-caret-square-o-left">caret-square-o-left</option>
                                        <option data-icon="la-caret-square-o-right">caret-square-o-right</option>
                                        <option data-icon="la-caret-square-o-up">caret-square-o-up</option>
                                        <option data-icon="la-caret-up">caret-up</option>
                                        <option data-icon="la-cc">cc</option>
                                        <option data-icon="la-cc-amex">cc-amex</option>
                                        <option data-icon="la-cc-discover">cc-discover</option>
                                        <option data-icon="la-cc-mastercard">cc-mastercard</option>
                                        <option data-icon="la-cc-paypal">cc-paypal</option>
                                        <option data-icon="la-cc-stripe">cc-stripe</option>
                                        <option data-icon="la-cc-visa">cc-visa</option>
                                        <option data-icon="la-certificate">certificate</option>
                                        <option data-icon="la-chain">chain</option>
                                        <option data-icon="la-chain-broken">chain-broken</option>
                                        <option data-icon="la-check">check</option>
                                        <option data-icon="la-check-circle">check-circle</option>
                                        <option data-icon="la-check-circle-o">check-circle-o</option>
                                        <option data-icon="la-check-square">check-square</option>
                                        <option data-icon="la-check-square-o">check-square-o</option>
                                        <option data-icon="la-chevron-circle-down">chevron-circle-down</option>
                                        <option data-icon="la-chevron-circle-left">chevron-circle-left</option>
                                        <option data-icon="la-chevron-circle-right">chevron-circle-right</option>
                                        <option data-icon="la-chevron-circle-up">chevron-circle-up</option>
                                        <option data-icon="la-chevron-down">chevron-down</option>
                                        <option data-icon="la-chevron-left">chevron-left</option>
                                        <option data-icon="la-chevron-right">chevron-right</option>
                                        <option data-icon="la-chevron-up">chevron-up</option>
                                        <option data-icon="la-child">child</option>
                                        <option data-icon="la-circle">circle</option>
                                        <option data-icon="la-circle-o">circle-o</option>
                                        <option data-icon="la-circle-o-notch">circle-o-notch</option>
                                        <option data-icon="la-circle-thin">circle-thin</option>
                                        <option data-icon="la-clipboard">clipboard</option>
                                        <option data-icon="la-clock-o">clock-o</option>
                                        <option data-icon="la-close">close</option>
                                        <option data-icon="la-cloud">cloud</option>
                                        <option data-icon="la-cloud-download">cloud-download</option>
                                        <option data-icon="la-cloud-upload">cloud-upload</option>
                                        <option data-icon="la-cny">cny</option>
                                        <option data-icon="la-code">code</option>
                                        <option data-icon="la-code-fork">code-fork</option>
                                        <option data-icon="la-codepen">codepen</option>
                                        <option data-icon="la-coffee">coffee</option>
                                        <option data-icon="la-cog">cog</option>
                                        <option data-icon="la-cogs">cogs</option>
                                        <option data-icon="la-columns">columns</option>
                                        <option data-icon="la-comment">comment</option>
                                        <option data-icon="la-comment-o">comment-o</option>
                                        <option data-icon="la-comments">comments</option>
                                        <option data-icon="la-comments-o">comments-o</option>
                                        <option data-icon="la-compass">compass</option>
                                        <option data-icon="la-compress">compress</option>
                                        <option data-icon="la-copy">copy</option>
                                        <option data-icon="la-copyright">copyright</option>
                                        <option data-icon="la-credit-card">credit-card</option>
                                        <option data-icon="la-crop">crop</option>
                                        <option data-icon="la-crosshairs">crosshairs</option>
                                        <option data-icon="la-css3">css3</option>
                                        <option data-icon="la-cube">cube</option>
                                        <option data-icon="la-cubes">cubes</option>
                                        <option data-icon="la-cut">cut</option>
                                        <option data-icon="la-utensils">cutlery</option>
                                        <option data-icon="la-dashboard">dashboard</option>
                                        <option data-icon="la-database">database</option>
                                        <option data-icon="la-dedent">dedent</option>
                                        <option data-icon="la-delicious">delicious</option>
                                        <option data-icon="la-desktop">desktop</option>
                                        <option data-icon="la-deviantart">deviantart</option>
                                        <option data-icon="la-digg">digg</option>
                                        <option data-icon="la-dollar">dollar</option>
                                        <option data-icon="la-dot-circle-o">dot-circle-o</option>
                                        <option data-icon="la-download">download</option>
                                        <option data-icon="la-dribbble">dribbble</option>
                                        <option data-icon="la-dropbox">dropbox</option>
                                        <option data-icon="la-drupal">drupal</option>
                                        <option data-icon="la-edit">edit</option>
                                        <option data-icon="la-eject">eject</option>
                                        <option data-icon="la-ellipsis-h">ellipsis-h</option>
                                        <option data-icon="la-ellipsis-v">ellipsis-v</option>
                                        <option data-icon="la-empire">empire</option>
                                        <option data-icon="la-envelope">envelope</option>
                                        <option data-icon="la-envelope-o">envelope-o</option>
                                        <option data-icon="la-envelope-square">envelope-square</option>
                                        <option data-icon="la-eraser">eraser</option>
                                        <option data-icon="la-eur">eur</option>
                                        <option data-icon="la-euro">euro</option>
                                        <option data-icon="la-exchange">exchange</option>
                                        <option data-icon="la-exclamation">exclamation</option>
                                        <option data-icon="la-exclamation-circle">exclamation-circle</option>
                                        <option data-icon="la-exclamation-triangle">exclamation-triangle</option>
                                        <option data-icon="la-expand">expand</option>
                                        <option data-icon="la-external-link">external-link</option>
                                        <option data-icon="la-external-link-square">external-link-square</option>
                                        <option data-icon="la-eye">eye</option>
                                        <option data-icon="la-eye-slash">eye-slash</option>
                                        <option data-icon="la-eyedropper">eyedropper</option>
                                        <option data-icon="la-facebook">facebook</option>
                                        <option data-icon="la-facebook-square">facebook-square</option>
                                        <option data-icon="la-fast-backward">fast-backward</option>
                                        <option data-icon="la-fast-forward">fast-forward</option>
                                        <option data-icon="la-fax">fax</option>
                                        <option data-icon="la-female">female</option>
                                        <option data-icon="la-fighter-jet">fighter-jet</option>
                                        <option data-icon="la-file">file</option>
                                        <option data-icon="la-file-archive-o">file-archive-o</option>
                                        <option data-icon="la-file-audio-o">file-audio-o</option>
                                        <option data-icon="la-file-code-o">file-code-o</option>
                                        <option data-icon="la-file-excel-o">file-excel-o</option>
                                        <option data-icon="la-file-image-o">file-image-o</option>
                                        <option data-icon="la-file-movie-o">file-movie-o</option>
                                        <option data-icon="la-file-o">file-o</option>
                                        <option data-icon="la-file-pdf-o">file-pdf-o</option>
                                        <option data-icon="la-file-photo-o">file-photo-o</option>
                                        <option data-icon="la-file-picture-o">file-picture-o</option>
                                        <option data-icon="la-file-powerpoint-o">file-powerpoint-o</option>
                                        <option data-icon="la-file-sound-o">file-sound-o</option>
                                        <option data-icon="la-file-text">file-text</option>
                                        <option data-icon="la-file-text-o">file-text-o</option>
                                        <option data-icon="la-file-video-o">file-video-o</option>
                                        <option data-icon="la-file-word-o">file-word-o</option>
                                        <option data-icon="la-file-zip-o">file-zip-o</option>
                                        <option data-icon="la-files-o">files-o</option>
                                        <option data-icon="la-film">film</option>
                                        <option data-icon="la-filter">filter</option>
                                        <option data-icon="la-fire">fire</option>
                                        <option data-icon="la-fire-extinguisher">fire-extinguisher</option>
                                        <option data-icon="la-flag">flag</option>
                                        <option data-icon="la-flag-checkered">flag-checkered</option>
                                        <option data-icon="la-flag-o">flag-o</option>
                                        <option data-icon="la-flash">flash</option>
                                        <option data-icon="la-flask">flask</option>
                                        <option data-icon="la-flickr">flickr</option>
                                        <option data-icon="la-floppy-o">floppy-o</option>
                                        <option data-icon="la-folder">folder</option>
                                        <option data-icon="la-folder-o">folder-o</option>
                                        <option data-icon="la-folder-open">folder-open</option>
                                        <option data-icon="la-folder-open-o">folder-open-o</option>
                                        <option data-icon="la-font">font</option>
                                        <option data-icon="la-forward">forward</option>
                                        <option data-icon="la-foursquare">foursquare</option>
                                        <option data-icon="la-frown-o">frown-o</option>
                                        <option data-icon="la-futbol-o">futbol-o</option>
                                        <option data-icon="la-gamepad">gamepad</option>
                                        <option data-icon="la-gavel">gavel</option>
                                        <option data-icon="la-gbp">gbp</option>
                                        <option data-icon="la-ge">ge</option>
                                        <option data-icon="la-gear">gear</option>
                                        <option data-icon="la-gears">gears</option>
                                        <option data-icon="la-gift">gift</option>
                                        <option data-icon="la-git">git</option>
                                        <option data-icon="la-git-square">git-square</option>
                                        <option data-icon="la-github">github</option>
                                        <option data-icon="la-github-alt">github-alt</option>
                                        <option data-icon="la-github-square">github-square</option>
                                        <option data-icon="la-gittip">gittip</option>
                                        <option data-icon="la-glass">glass</option>
                                        <option data-icon="la-globe">globe</option>
                                        <option data-icon="la-google">google</option>
                                        <option data-icon="la-google-plus">google-plus</option>
                                        <option data-icon="la-google-plus-square">google-plus-square</option>
                                        <option data-icon="la-google-wallet">google-wallet</option>
                                        <option data-icon="la-graduation-cap">graduation-cap</option>
                                        <option data-icon="la-group">group</option>
                                        <option data-icon="la-h-square">h-square</option>
                                        <option data-icon="la-hacker-news">hacker-news</option>
                                        <option data-icon="la-hand-o-down">hand-o-down</option>
                                        <option data-icon="la-hand-o-left">hand-o-left</option>
                                        <option data-icon="la-hand-o-right">hand-o-right</option>
                                        <option data-icon="la-hand-o-up">hand-o-up</option>
                                        <option data-icon="la-hdd-o">hdd-o</option>
                                        <option data-icon="la-header">header</option>
                                        <option data-icon="la-headphones">headphones</option>
                                        <option data-icon="la-heart">heart</option>
                                        <option data-icon="la-heart-o">heart-o</option>
                                        <option data-icon="la-history">history</option>
                                        <option data-icon="la-home">home</option>
                                        <option data-icon="la-hospital-o">hospital-o</option>
                                        <option data-icon="la-html5">html5</option>
                                        <option data-icon="la-ils">ils</option>
                                        <option data-icon="la-image">image</option>
                                        <option data-icon="la-inbox">inbox</option>
                                        <option data-icon="la-indent">indent</option>
                                        <option data-icon="la-info">info</option>
                                        <option data-icon="la-info-circle">info-circle</option>
                                        <option data-icon="la-inr">inr</option>
                                        <option data-icon="la-instagram">instagram</option>
                                        <option data-icon="la-institution">institution</option>
                                        <option data-icon="la-ioxhost">ioxhost</option>
                                        <option data-icon="la-italic">italic</option>
                                        <option data-icon="la-joomla">joomla</option>
                                        <option data-icon="la-jpy">jpy</option>
                                        <option data-icon="la-jsfiddle">jsfiddle</option>
                                        <option data-icon="la-key">key</option>
                                        <option data-icon="la-keyboard-o">keyboard-o</option>
                                        <option data-icon="la-krw">krw</option>
                                        <option data-icon="la-language">language</option>
                                        <option data-icon="la-laptop">laptop</option>
                                        <option data-icon="la-lastfm">lastfm</option>
                                        <option data-icon="la-lastfm-square">lastfm-square</option>
                                        <option data-icon="la-leaf">leaf</option>
                                        <option data-icon="la-legal">legal</option>
                                        <option data-icon="la-lemon-o">lemon-o</option>
                                        <option data-icon="la-level-down">level-down</option>
                                        <option data-icon="la-level-up">level-up</option>
                                        <option data-icon="la-life-bouy">life-bouy</option>
                                        <option data-icon="la-life-buoy">life-buoy</option>
                                        <option data-icon="la-life-ring">life-ring</option>
                                        <option data-icon="la-life-saver">life-saver</option>
                                        <option data-icon="la-lightbulb-o">lightbulb-o</option>
                                        <option data-icon="la-line-chart">line-chart</option>
                                        <option data-icon="la-link">link</option>
                                        <option data-icon="la-linkedin">linkedin</option>
                                        <option data-icon="la-linkedin-square">linkedin-square</option>
                                        <option data-icon="la-linux">linux</option>
                                        <option data-icon="la-list">list</option>
                                        <option data-icon="la-list-alt">list-alt</option>
                                        <option data-icon="la-list-ol">list-ol</option>
                                        <option data-icon="la-list-ul">list-ul</option>
                                        <option data-icon="la-location-arrow">location-arrow</option>
                                        <option data-icon="la-lock">lock</option>
                                        <option data-icon="la-long-arrow-down">long-arrow-down</option>
                                        <option data-icon="la-long-arrow-left">long-arrow-left</option>
                                        <option data-icon="la-long-arrow-right">long-arrow-right</option>
                                        <option data-icon="la-long-arrow-up">long-arrow-up</option>
                                        <option data-icon="la-magic">magic</option>
                                        <option data-icon="la-magnet">magnet</option>
                                        <option data-icon="la-mail-forward">mail-forward</option>
                                        <option data-icon="la-mail-reply">mail-reply</option>
                                        <option data-icon="la-mail-reply-all">mail-reply-all</option>
                                        <option data-icon="la-male">male</option>
                                        <option data-icon="la-map-marker-alt">map-marker</option>
                                        <option data-icon="la-maxcdn">maxcdn</option>
                                        <option data-icon="la-meanpath">meanpath</option>
                                        <option data-icon="la-medkit">medkit</option>
                                        <option data-icon="la-meh-o">meh-o</option>
                                        <option data-icon="la-microphone">microphone</option>
                                        <option data-icon="la-microphone-slash">microphone-slash</option>
                                        <option data-icon="la-minus">minus</option>
                                        <option data-icon="la-minus-circle">minus-circle</option>
                                        <option data-icon="la-minus-square">minus-square</option>
                                        <option data-icon="la-minus-square-o">minus-square-o</option>
                                        <option data-icon="la-mobile">mobile</option>
                                        <option data-icon="la-mobile">mobile-phone</option>
                                        <option data-icon="la-money-bill-alt">money</option>
                                        <option data-icon="la-moon-o">moon-o</option>
                                        <option data-icon="la-mortar-board">mortar-board</option>
                                        <option data-icon="la-music">music</option>
                                        <option data-icon="la-navicon">navicon</option>
                                        <option data-icon="la-newspaper-o">newspaper-o</option>
                                        <option data-icon="la-openid">openid</option>
                                        <option data-icon="la-outdent">outdent</option>
                                        <option data-icon="la-pagelines">pagelines</option>
                                        <option data-icon="la-paint-brush">paint-brush</option>
                                        <option data-icon="la-paper-plane">paper-plane</option>
                                        <option data-icon="la-paper-plane-o">paper-plane-o</option>
                                        <option data-icon="la-paperclip">paperclip</option>
                                        <option data-icon="la-paragraph">paragraph</option>
                                        <option data-icon="la-paste">paste</option>
                                        <option data-icon="la-pause">pause</option>
                                        <option data-icon="la-paw">paw</option>
                                        <option data-icon="la-paypal">paypal</option>
                                        <option data-icon="la-pencil">pencil</option>
                                        <option data-icon="la-pencil-square">pencil-square</option>
                                        <option data-icon="la-pencil-square-o">pencil-square-o</option>
                                        <option data-icon="la-phone">phone</option>
                                        <option data-icon="la-phone-square">phone-square</option>
                                        <option data-icon="la-photo">photo</option>
                                        <option data-icon="la-picture-o">picture-o</option>
                                        <option data-icon="la-pie-chart">pie-chart</option>
                                        <option data-icon="la-pied-piper">pied-piper</option>
                                        <option data-icon="la-pied-piper-alt">pied-piper-alt</option>
                                        <option data-icon="la-pinterest">pinterest</option>
                                        <option data-icon="la-pinterest-square">pinterest-square</option>
                                        <option data-icon="la-plane">plane</option>
                                        <option data-icon="la-play">play</option>
                                        <option data-icon="la-play-circle">play-circle</option>
                                        <option data-icon="la-play-circle-o">play-circle-o</option>
                                        <option data-icon="la-plug">plug</option>
                                        <option data-icon="la-plus">plus</option>
                                        <option data-icon="la-plus-circle">plus-circle</option>
                                        <option data-icon="la-plus-square">plus-square</option>
                                        <option data-icon="la-plus-square-o">plus-square-o</option>
                                        <option data-icon="la-power-off">power-off</option>
                                        <option data-icon="la-print">print</option>
                                        <option data-icon="la-puzzle-piece">puzzle-piece</option>
                                        <option data-icon="la-qq">qq</option>
                                        <option data-icon="la-qrcode">qrcode</option>
                                        <option data-icon="la-question">question</option>
                                        <option data-icon="la-question-circle">question-circle</option>
                                        <option data-icon="la-quote-left">quote-left</option>
                                        <option data-icon="la-quote-right">quote-right</option>
                                        <option data-icon="la-ra">ra</option>
                                        <option data-icon="la-random">random</option>
                                        <option data-icon="la-rebel">rebel</option>
                                        <option data-icon="la-recycle">recycle</option>
                                        <option data-icon="la-reddit">reddit</option>
                                        <option data-icon="la-reddit-square">reddit-square</option>
                                        <option data-icon="la-refresh">refresh</option>
                                        <option data-icon="la-remove">remove</option>
                                        <option data-icon="la-renren">renren</option>
                                        <option data-icon="la-reorder">reorder</option>
                                        <option data-icon="la-repeat">repeat</option>
                                        <option data-icon="la-reply">reply</option>
                                        <option data-icon="la-reply-all">reply-all</option>
                                        <option data-icon="la-retweet">retweet</option>
                                        <option data-icon="la-rmb">rmb</option>
                                        <option data-icon="la-road">road</option>
                                        <option data-icon="la-rocket">rocket</option>
                                        <option data-icon="la-rotate-left">rotate-left</option>
                                        <option data-icon="la-rotate-right">rotate-right</option>
                                        <option data-icon="la-rouble">rouble</option>
                                        <option data-icon="la-rss">rss</option>
                                        <option data-icon="la-rss-square">rss-square</option>
                                        <option data-icon="la-rub">rub</option>
                                        <option data-icon="la-ruble">ruble</option>
                                        <option data-icon="la-rupee">rupee</option>
                                        <option data-icon="la-save">save</option>
                                        <option data-icon="la-scissors">scissors</option>
                                        <option data-icon="la-search">search</option>
                                        <option data-icon="la-search-minus">search-minus</option>
                                        <option data-icon="la-search-plus">search-plus</option>
                                        <option data-icon="la-send">send</option>
                                        <option data-icon="la-send-o">send-o</option>
                                        <option data-icon="la-share">share</option>
                                        <option data-icon="la-share-alt">share-alt</option>
                                        <option data-icon="la-share-alt-square">share-alt-square</option>
                                        <option data-icon="la-share-square">share-square</option>
                                        <option data-icon="la-share-square-o">share-square-o</option>
                                        <option data-icon="la-shekel">shekel</option>
                                        <option data-icon="la-sheqel">sheqel</option>
                                        <option data-icon="la-shield">shield</option>
                                        <option data-icon="la-shopping-cart">shopping-cart</option>
                                        <option data-icon="la-sign-in">sign-in</option>
                                        <option data-icon="la-sign-out">sign-out</option>
                                        <option data-icon="la-signal">signal</option>
                                        <option data-icon="la-sitemap">sitemap</option>
                                        <option data-icon="la-skype">skype</option>
                                        <option data-icon="la-slack">slack</option>
                                        <option data-icon="la-sliders">sliders</option>
                                        <option data-icon="la-slideshare">slideshare</option>
                                        <option data-icon="la-smile-o">smile-o</option>
                                        <option data-icon="la-soccer-ball-o">soccer-ball-o</option>
                                        <option data-icon="la-sort">sort</option>
                                        <option data-icon="la-sort-alpha-asc">sort-alpha-asc</option>
                                        <option data-icon="la-sort-alpha-desc">sort-alpha-desc</option>
                                        <option data-icon="la-sort-amount-asc">sort-amount-asc</option>
                                        <option data-icon="la-sort-amount-desc">sort-amount-desc</option>
                                        <option data-icon="la-sort-asc">sort-asc</option>
                                        <option data-icon="la-sort-desc">sort-desc</option>
                                        <option data-icon="la-sort-down">sort-down</option>
                                        <option data-icon="la-sort-numeric-asc">sort-numeric-asc</option>
                                        <option data-icon="la-sort-numeric-desc">sort-numeric-desc</option>
                                        <option data-icon="la-sort-up">sort-up</option>
                                        <option data-icon="la-soundcloud">soundcloud</option>
                                        <option data-icon="la-space-shuttle">space-shuttle</option>
                                        <option data-icon="la-spinner">spinner</option>
                                        <option data-icon="la-spoon">spoon</option>
                                        <option data-icon="la-spotify">spotify</option>
                                        <option data-icon="la-square">square</option>
                                        <option data-icon="la-square-o">square-o</option>
                                        <option data-icon="la-stack-exchange">stack-exchange</option>
                                        <option data-icon="la-stack-overflow">stack-overflow</option>
                                        <option data-icon="la-star">star</option>
                                        <option data-icon="la-star-half">star-half</option>
                                        <option data-icon="la-star-half-o">star-half-o</option>
                                        <option data-icon="la-star-half-full">star-half-full</option>
                                        <option data-icon="la-star-half-o">star-half-o</option>
                                        <option data-icon="la-star-o">star-o</option>
                                        <option data-icon="la-steam">steam</option>
                                        <option data-icon="la-steam-square">steam-square</option>
                                        <option data-icon="la-step-backward">step-backward</option>
                                        <option data-icon="la-step-forward">step-forward</option>
                                        <option data-icon="la-stethoscope">stethoscope</option>
                                        <option data-icon="la-stop">stop</option>
                                        <option data-icon="la-strikethrough">strikethrough</option>
                                        <option data-icon="la-stumbleupon">stumbleupon</option>
                                        <option data-icon="la-stumbleupon-circle">stumbleupon-circle</option>
                                        <option data-icon="la-subscript">subscript</option>
                                        <option data-icon="la-suitcase">suitcase</option>
                                        <option data-icon="la-sun-o">sun-o</option>
                                        <option data-icon="la-superscript">superscript</option>
                                        <option data-icon="la-support">support</option>
                                        <option data-icon="la-table">table</option>
                                        <option data-icon="la-tablet">tablet</option>
                                        <option data-icon="la-tachometer">tachometer</option>
                                        <option data-icon="la-tag">tag</option>
                                        <option data-icon="la-tags">tags</option>
                                        <option data-icon="la-tasks">tasks</option>
                                        <option data-icon="la-taxi">taxi</option>
                                        <option data-icon="la-tencent-weibo">tencent-weibo</option>
                                        <option data-icon="la-terminal">terminal</option>
                                        <option data-icon="la-text-height">text-height</option>
                                        <option data-icon="la-text-width">text-width</option>
                                        <option data-icon="la-th">th</option>
                                        <option data-icon="la-th-large">th-large</option>
                                        <option data-icon="la-th-list">th-list</option>
                                        <option data-icon="la-thumb-tack">thumb-tack</option>
                                        <option data-icon="la-thumbs-down">thumbs-down</option>
                                        <option data-icon="la-thumbs-o-down">thumbs-o-down</option>
                                        <option data-icon="la-thumbs-o-up">thumbs-o-up</option>
                                        <option data-icon="la-thumbs-up">thumbs-up</option>
                                        <option data-icon="la-ticket">ticket</option>
                                        <option data-icon="la-times">times</option>
                                        <option data-icon="la-times-circle">times-circle</option>
                                        <option data-icon="la-times-circle-o">times-circle-o</option>
                                        <option data-icon="la-tint">tint</option>
                                        <option data-icon="la-toggle-down">toggle-down</option>
                                        <option data-icon="la-toggle-left">toggle-left</option>
                                        <option data-icon="la-toggle-off">toggle-off</option>
                                        <option data-icon="la-toggle-on">toggle-on</option>
                                        <option data-icon="la-toggle-right">toggle-right</option>
                                        <option data-icon="la-toggle-up">toggle-up</option>
                                        <option data-icon="la-trash">trash</option>
                                        <option data-icon="la-trash">trash-o</option>
                                        <option data-icon="la-tree">tree</option>
                                        <option data-icon="la-trello">trello</option>
                                        <option data-icon="la-trophy">trophy</option>
                                        <option data-icon="la-truck">truck</option>
                                        <option data-icon="la-try">try</option>
                                        <option data-icon="la-tty">tty</option>
                                        <option data-icon="la-tumblr">tumblr</option>
                                        <option data-icon="la-tumblr-square">tumblr-square</option>
                                        <option data-icon="la-turkish-lira">turkish-lira</option>
                                        <option data-icon="la-twitch">twitch</option>
                                        <option data-icon="la-twitter">twitter</option>
                                        <option data-icon="la-twitter-square">twitter-square</option>
                                        <option data-icon="la-umbrella">umbrella</option>
                                        <option data-icon="la-underline">underline</option>
                                        <option data-icon="la-undo">undo</option>
                                        <option data-icon="la-university">university</option>
                                        <option data-icon="la-unlink">unlink</option>
                                        <option data-icon="la-unlock">unlock</option>
                                        <option data-icon="la-unlock-alt">unlock-alt</option>
                                        <option data-icon="la-unsorted">unsorted</option>
                                        <option data-icon="la-upload">upload</option>
                                        <option data-icon="la-usd">usd</option>
                                        <option data-icon="la-user">user</option>
                                        <option data-icon="la-user-md">user-md</option>
                                        <option data-icon="la-users">users</option>
                                        <option data-icon="la-video-camera">video-camera</option>
                                        <option data-icon="la-vimeo-square">vimeo-square</option>
                                        <option data-icon="la-vine">vine</option>
                                        <option data-icon="la-vk">vk</option>
                                        <option data-icon="la-volume-down">volume-down</option>
                                        <option data-icon="la-volume-off">volume-off</option>
                                        <option data-icon="la-volume-up">volume-up</option>
                                        <option data-icon="la-warning">warning</option>
                                        <option data-icon="la-wechat">wechat</option>
                                        <option data-icon="la-weibo">weibo</option>
                                        <option data-icon="la-weixin">weixin</option>
                                        <option data-icon="la-wheelchair">wheelchair</option>
                                        <option data-icon="la-wifi">wifi</option>
                                        <option data-icon="la-windows">windows</option>
                                        <option data-icon="la-won">won</option>
                                        <option data-icon="la-wordpress">wordpress</option>
                                        <option data-icon="la-wrench">wrench</option>
                                        <option data-icon="la-xing">xing</option>
                                        <option data-icon="la-xing-square">xing-square</option>
                                        <option data-icon="la-yahoo">yahoo</option>
                                        <option data-icon="la-yelp">yelp</option>
                                        <option data-icon="la-yen">yen</option>
                                        <option data-icon="la-youtube">youtube</option>
                                        <option data-icon="la-youtube-play">youtube-play</option>
                                        <option data-icon="la-youtube-square">youtube-square</option>
                                    </select>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="bootstrap-tags form-group" id="keywords-container">                                    	
                                            <label for="keywords"><?php echo JText::_('LNG_KEYWORDS')?></label>
                                            <p class="small"><?php echo JText::_('LNG_CATEGORY_KEYWORD_INFO')?></p>
                                            <input type="text" data-role="tagsinput" style="display: none" name="keywords" class="form-control" id="keywords" value="<?php echo $this->item->keywords ?>" maxlength="250" />
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="color"> <?php echo JText::_('LNG_COLOR')?> </label>
                                    <input type="text" name="color" class="minicolors  form-control " id="colorpicker" value="<?php echo $this->item->color ?>" />
                                    <a href="javascript:clearColor()"><?php echo JText::_("LNG_CLEAR")?></a>
                                </div>
                                <div class="form-group">
                                    <label for="category_published"><?php echo JText::_('LNG_STATUS')?><?php echo JBusinessUtil::showMandatory(ATTRIBUTE_MANDATORY)?> </label>
                                    <fieldset id="show_time_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio" class="validate[required]" name="published" id="publishedcat1" value="1" <?php echo $this->item->published==1? 'checked="checked"' :""?> />
                                        <label class="btn" for="publishedcat1"><?php echo JText::_('LNG_PUBLISHED')?></label>
                                        <input type="radio" class="validate[required]" name="published" id="publishedcat0" value="0" <?php echo $this->item->published==0? 'checked="checked"' :""?> />
                                        <label class="btn" for="publishedcat0"><?php echo JText::_('LNG_UNPUBLISHED')?></label>
                                    </fieldset>
                                </div>

                                <div class="form-group">
                                    <label for="user_as_container"><?php echo JText::_('LNG_USE_CAT_AS_CONTAINER')?></label>
                                    <fieldset id="show_time_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio" class="validate[required]" name="user_as_container" id="user_as_container1" value="1" <?php echo $this->item->user_as_container==1? 'checked="checked"' :""?> />
                                        <label class="btn" for="user_as_container1"><?php echo JText::_('LNG_YES')?></label>
                                        <input type="radio" class="validate[required]" name="user_as_container" id="user_as_container0" value="0" <?php echo empty($this->item->user_as_container) ? 'checked="checked"' :""?> />
                                        <label class="btn" for="user_as_container0"><?php echo JText::_('LNG_NO')?></label>
                                    </fieldset>
                                </div>

                                <div class="form-group">
                                    <label for="description_id"><?php echo JText::_('LNG_DESCRIPTION')?>  &nbsp;&nbsp;&nbsp;</label>
                                    <?php
                                        if($this->appSettings->enable_multilingual) {
                                            echo $jbdTabs->startTabSet('tab_groupsd_desc');
                                            foreach( $this->languages as $k=>$lng ) {
                                                echo $jbdTabs->addTab('tab_groupsd_desc', 'tab-'.$lng, $k);
                                                $langContent = isset($this->translations[$lng])?$this->translations[$lng]:"";
                                                if($lng==JBusinessUtil::getLanguageTag() && empty($langContent)){
                                                    $langContent = $this->item->description;
                                                }
                                                $editor = JBusinessUtil::getEditor();
                                                echo $editor->display('description_'.$lng, $langContent, '100%', '450', '70', '10', false);
                                                echo $jbdTabs->endTab();
                                            }
                                            echo $jbdTabs->endTabSet();
                                        } else {
                                                $editor = JBusinessUtil::getEditor();
                                                echo $editor->display('description', $this->item->description, '100%', '450', '70', '10', false);
                                        }
                                    ?>
                                </div>
                                <div class="form-divider"></div>
                                <div class="form-group">
                                    <label><?php echo JText::_('LNG_IMAGE'); ?></label>
                                    <div class="jupload logo-jupload">
                                        <div class="jupload-header">
                                        </div>
                                        <div class="jupload-body">
                                            <div class="jupload-files">
                                                <div class="jupload-files-img image-fit-contain" id="picture-preview">
                                                    <?php if(!empty($this->item->imageLocation)) {
                                                        echo "<img  id='itemImg' src='".BD_PICTURES_PATH.$this->item->imageLocation."'/>";
                                                    }else{
                                                        echo "<i class='la la-image'></i>";
                                                    } ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="jupload-options">
                                            <div class="jupload-options-btn jupload-actions">
                                                <label for="imageUploader" class="btn btn-outline-success"><?php echo JText::_("LNG_UPLOAD")?></label>
                                                <a name="" id="" class="" href="javascript:uploadInstance.removeImage()" role="button"><i class="la la-trash"></i></a>
                                            </div>
                                            <div class="">
                                                <?php echo JText::_("LNG_SELECT_IMAGE_TYPE") ?>
                                            </div>
                                        </div>
                                        <div class="jupload-footer">
                                            <fieldset>
                                                <p class="hint"><?php echo JText::_('LNG_LOGO_MAX_SIZE'); ?></p>
                                                <input type="hidden" name="imageLocation" id="imageLocation" value="<?php echo $this->item->imageLocation?>">
                                                <input type="file" id="imageUploader" name="uploadLogo" size="50">
                                            </fieldset>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label><?php echo JText::_('LNG_MARKER');?></label>
                                    <div class="picture-preview" id="marker-picture-preview">
                                        <?php
                                        if(!empty($this->item->markerLocation)) {
                                            echo "<img class='img-thumbnail border p-4 mb-2' id='markerImg' src='".BD_PICTURES_PATH.$this->item->markerLocation."'/>";
                                        } ?>
                                    </div>
                                    <div class="form-upload-elem">
                                        <div class="form-upload">
                                            <input type="hidden" name="markerLocation" id="marker-imageLocation" value="<?php echo $this->item->markerLocation ?>">
                                            <input type="file" id="marker-imageUploader" name="markerfile" size="50">
                                        </div>
                                        <a class="btn btn-danger btn-sm" href="javascript:uploadInstance.removeImage('marker-');"><?php echo JText::_("LNG_REMOVE") ?></a>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label><?php echo JText::_('LNG_ICON');?></label>
                                    <div class="picture-preview" id="iconImgpicture-preview">
                                        <?php
                                        if(!empty($this->item->iconImgLocation)) {
                                            echo "<img class='img-thumbnail border p-4 mb-2' id='iconImg' src='".BD_PICTURES_PATH.$this->item->iconImgLocation."'/>";
                                        } ?>
                                    </div>
                                    <div class="form-upload-elem">
                                        <div class="form-upload">
                                            <input type="hidden" name="iconImgLocation" id="iconImgimageLocation" value="<?php echo $this->item->iconImgLocation ?>">
                                            <input type="file" id="iconImgimageUploader" name="iconfile" size="50">
                                        </div>
                                        <a class="btn btn-danger btn-sm" href="javascript:uploadInstance.removeImage('iconImg');"><?php echo JText::_("LNG_REMOVE") ?></a>
                                    </div>
                                </div>                                
                            </div>
                        </fieldset>
                        <fieldset class="boxed">
                            <h2> <?php echo JText::_('LNG_METADATA_INFORMATION');?></h2>
                            <p> <?php echo JText::_('LNG_CATEGORY_METADATA_INFORMATION_TEXT');?>.</p>
                            <div class="form-container">
                                <div class="form-box">
                                    <div class="form-group">
                                        <label for="meta_title"><?php echo JText::_('LNG_META_TITLE')?></label>
                                        <?php
                                        if($this->appSettings->enable_multilingual) {
                                            echo $jbdTabs->startTabSet('tab_groupsd_id');
                                            foreach( $this->languages  as $k=>$lng ) {
                                                echo $jbdTabs->addTab('tab_groupsd_id', 'tab-'.$lng, $k);
                                                $langContent = isset($this->translationsMeta[$lng."_name"])?$this->translationsMeta[$lng."_name"]:"";
                                                if($lng==JBusinessUtil::getLanguageTag() && empty($langContent)){
                                                    $langContent = $this->item->meta_title;
                                                }
                                                $langContent=$this->escape($langContent);
                                                echo "<input type='text' name='meta_title_$lng' id='meta_title_$lng' class='input_txt form-control' value=\"".stripslashes($langContent)."\"  maxLength='100'>";
                                                echo $jbdTabs->endTab();
                                            }
                                            echo $jbdTabs->endTabSet();
                                        } else { ?>
                                            <input type="text" name="meta_title" id="meta_title" class="input_txt form-control" value="<?php echo $this->escape($this->item->meta_title) ?>"  maxLength="100">
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="form-box">
                                    <div class="form-group">
                                        <label for="meta_description"><?php echo JText::_('LNG_META_DESCRIPTION')?></label>
                                        <?php
                                        if($this->appSettings->enable_multilingual) {
                                            echo $jbdTabs->startTabSet('tab_groupsd_id');
                                            foreach( $this->languages  as $k=>$lng ) {
                                                echo $jbdTabs->addTab('tab_groupsd_id', 'tab-'.$lng, $k);
                                                $langContent = isset($this->translationsMeta[$lng])?$this->translationsMeta[$lng]:"";
                                                if($lng==JBusinessUtil::getLanguageTag() && empty($langContent)){
                                                    $langContent = $this->item->meta_description;
                                                }
                                                $langContent=$this->escape($langContent);
                                                echo "<textarea name='meta_description_$lng' id='meta_description_$lng' class='h-auto form-control' rows='4' maxLength='255'>$langContent</textarea>";
                                                echo $jbdTabs->endTab();
                                            }
                                            echo $jbdTabs->endTabSet();
                                        } else { ?>
                                            <textarea name="meta_description" id="meta_description" rows="4" class='h-auto form-control' maxLength="255"><?php echo $this->item->meta_description ?></textarea>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="bootstrap-tags form-box" id="keywords-container">
                                <label for="meta_keywords"><?php echo JText::_('LNG_KEYWORDS')?> </label>
                                <p class="small"><?php echo JText::_('LNG_CATEGORY_KEYWORD_INFO')?></p>
                                <?php
                                if($this->appSettings->enable_multilingual) {
                                    echo $jbdTabs->startTabSet('tab_groupsd_id');
                                    foreach( $this->languages  as $k=>$lng ) {
                                        echo $jbdTabs->addTab('tab_groupsd_id', 'tab-'.$lng, $k);
                                        $langContent = isset($this->translationsMeta[$lng."_short"])?$this->translationsMeta[$lng."_short"]:"";
                                        if($lng==JBusinessUtil::getLanguageTag() && empty($langContent)){
                                            $langContent = $this->item->meta_keywords;
                                        }
                                        $langContent=$this->escape($langContent);
                                        echo "<input type='text'  data-role='tagsinput' name='meta_keywords_$lng' id='meta_keywords_$lng' class='input_txt' value=\"".stripslashes($langContent)."\"  maxLength='100'>";
                                        echo $jbdTabs->endTab();
                                    }
                                    echo $jbdTabs->endTabSet();
                                } else { ?>
                                    <input type="text" data-role="tagsinput" name="meta_keywords" class="input_txt" id="meta_keywords" value="<?php echo $this->item->meta_keywords ?>" maxlength="150" />
                                <?php } ?>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
		<input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName()?>" /> 
		<input type="hidden" name="task" id="task" value="" />
		<input type="hidden" name="id" value="<?php echo $this->item->id ?>" /> 
		<input type="hidden" name="view" id="view" value="company" />
		<input type="hidden" name="type" value="<?php echo $this->typeSelected ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>
</div>

<?php JBusinessUtil::loadUploadScript(); ?>

<script>

    var companyFolder = '<?php echo CATEGORY_PICTURES_PATH ?>';
    var companyFolderPath = '<?php echo JBusinessUtil::getUploadUrl() ?>&t=<?php echo strtotime("now")?>&picture_type=<?php echo PICTURE_TYPE_LOGO?>&_path_type=1&_target=<?php echo urlencode(CATEGORY_PICTURES_PATH)?>&croppable=1';
    var catIconImgFolderPath = '<?php echo JBusinessUtil::getUploadUrl() ?>&t=<?php echo strtotime("now")?>&picture_type=<?php echo PICTURE_TYPE_CATEGORY_ICON?>&_path_type=1&_target=<?php echo urlencode(CATEGORY_PICTURES_PATH)?>';
    var markerFolderPath = '<?php echo JBusinessUtil::getUploadUrl() ?>&t=<?php echo strtotime("now")?>&picture_type=<?php echo PICTURE_TYPE_MARKER?>&_path_type=1&_target=<?php echo urlencode(CATEGORY_PICTURES_PATH)?>';
    var removePath = '<?php echo JBusinessUtil::getUploadUrl('remove') ?>&_path_type=2&_filename=';

    var uploadInstance;

	window.addEventListener('load', function() {

        uploadInstance = JBDUploadHelper.getUploadInstance({
            'removePath': removePath
        });

        uploadInstance.imageUploader(companyFolder, companyFolderPath);
        uploadInstance.imageUploader(companyFolder, markerFolderPath, "marker-");
        uploadInstance.imageUploader(companyFolder, catIconImgFolderPath, "iconImg");


		if (jQuery("#descriptionCounter").val()) {
            jQuery("#descriptionCounter").val(parseInt(jQuery("#description").attr('maxlength')) - jQuery("#description").val().length);
        }

		jQuery("#icon-holder").val("<?php echo $this->item->icon ?>");
		
		jQuery("#icon-holder").chosenIcon({
			disable_search_threshold: 10
		});

		jQuery("#parent_id").chosen({
			disable_search_threshold: 10
		});

		jQuery("#type").chosen({
			disable_search_threshold: 10
		});
		JBD.submitbutton = function(task) {

			var defaultLang="<?php echo JBusinessUtil::getLanguageTag() ?>";

			jQuery("#item-form").validationEngine('detach');
			var evt = document.createEvent("HTMLEvents");
			evt.initEvent("click", true, true);
			var tab = ("tab-"+defaultLang);
			if(!(document.getElementsByClassName(tab)[0] === undefined || document.getElementsByClassName(tab)[0] === null))
				document.getElementsByClassName(tab)[0].dispatchEvent(evt);
			if (task == 'category.cancel' || task == 'category.aprove' || task == 'category.disaprove' || !jbdUtils.validateCmpForm(false, false)){
				JBD.submitform(task, document.getElementById('item-form'));
			}

			jQuery("#item-form").validationEngine('attach');
		}

        jQuery('.bootstrap-tagsinput input').on('keypress', function(e){
            if (e.keyCode == 13){
                e.keyCode = 188;
                e.preventDefault();
            }
        });
	});

	function calculateLenght(){
		var obj = jQuery("#description");
		var max = parseInt(obj.attr('maxlength'));

		if(obj.val().length > max){
			obj.val(obj.val().substr(0, obj.attr('maxlength')));
		}

		jQuery("#descriptionCounter").val((max - obj.val().length));
	}

	function clearColor(){
		jQuery("#colorpicker").val("");
		jQuery(".minicolors-swatch").html("");
	}
</script>