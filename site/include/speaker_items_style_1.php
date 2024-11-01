<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

if (!isset($speakers)) {
	$speakers = $this->speakers;
}
if (!isset($speakerExpandedItems)) {
	$speakerExpandedItems = $this->speakerExpandedItems;
}
$user = JBusinessUtil::getUser();
?>
<div id="speakers" >
    <div id="conference-speakers-container"> 
    	<?php
			$date="";
			if (!empty($speakers)) {
				foreach ($speakers as $item) {
					$token = rand(); ?>
    				<div class="speaker-item">
    					<div class="column time">
    						<?php if (!empty($item->photo)) {?>
    							<img  alt="<?php echo htmlspecialchars($item->name, ENT_QUOTES) ?>" src="<?php echo BD_PICTURES_PATH.$item->photo?>"/>
    						<?php } else {?>
    							<img  alt="<?php echo htmlspecialchars($item->name, ENT_QUOTES) ?>" src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>" />
    						<?php } ?>
    					</div>
    					<div class="showDetails-<?php echo $token ?> column speaker-info <?php echo $speakerExpandedItems==1?"open-details":"" ?>" onclick="showDetails('<?php echo $token ?>');" style="border-color:<?php echo $item->typeColor?>">
    						<div class="location-link" style="color:<?php echo $item->typeColor?>">
    							<span><?php echo $item->typeName ?></span>
    							<?php if (!empty($item->country_logo)) {?>
    								<img class="country_flag" title="<?php echo htmlentities($item->country_name, ENT_QUOTES) ?>"
                                         alt="<?php echo htmlentities($item->country_name, ENT_QUOTES) ?>"
                                         src="<?php echo BD_PICTURES_PATH.$item->country_logo?>"/>
    							<?php } ?>
                                <?php if(!empty($item->bookmark)) { ?>
                                    <!-- Business Bookmarks -->
                                    <a id="bookmark-<?php echo $item->id ?>" href="javascript:jbdUtils.showUpdateBookmarkDialog(<?php echo $user->ID==0?"1":"0"?>, <?php echo $item->id ?>,<?php echo BOOKMARK_TYPE_SPEAKER ?>)"  title="<?php echo JText::_("LNG_UPDATE_BOOKMARK")?>" class="bookmark"><i class="la la-heart"></i></a>
                                <?php } else {?>
                                    <a id="bookmark-<?php echo $item->id ?>" href="javascript:jbdUtils.showAddBookmark(<?php echo $user->ID==0?"1":"0"?>, <?php echo $item->id ?>,<?php echo BOOKMARK_TYPE_SPEAKER ?>)" title="<?php echo JText::_("LNG_ADD_BOOKMARK")?>" class="bookmark"><i class="la la-heart-o"></i></a>
                                <?php } ?>
    						</div>
    						<h3 class=""><?php echo $item->name?></h3>
    						<div class="track"><?php echo $item->title.' '.$item->company_name ?></div>
    						<div class="details">
    							<div class="speaker-details">
                                    <div>
    									<?php if (!empty($item->company_logo)) {?>
                                            <img class="country_flag" style="height:40px;" alt="<?php echo htmlentities($item->company_name, ENT_QUOTES) ?>" src="<?php echo BD_PICTURES_PATH.$item->company_logo?>"/>
    									<?php } ?>
                                    </div>
    								<div class="future-sessions">
        								<?php if (!empty($item->sessions)) { ?>
        									<strong><?php echo JText::_("LNG_FUTURE_SESSIONS")?></strong>
        									<ul class="speaker-speakers">
        										<?php foreach ($item->sessions as $session) {	?>
        											<li>
        												<div class="row-fluid">
        													<div class="speaker-sessions">
        														
        														<a href="<?php  echo JBusinessUtil::getConferenceSessionLink($session[0], $session[2]) ?>">
        															<span class="session-name"><?php echo $session[1] ?></span>,
        															<span class="session-time"><i class=""></i><?php echo JBusinessUtil::convertTimeToFormat($session[3]).' - '.JBusinessUtil::convertTimeToFormat($session[4]) ?></span>,
        															<span class="session-date"><i class=""></i><?php echo JBusinessUtil::getShortDate($session[5]) ?></span>
        														</a>
        													</div>
        												</div>
        											</li>
        										<?php } ?>
        									</ul>
        								<?php } ?>
    								</div>
    								
    								<?php if (!empty($item->biography)) {?>
    									<div class="speaker-biography">
    										<strong><?php echo JText::_("LNG_BIOGRAPHY")?></strong>
    										<p><?php echo $item->biography ?></p>
    									</div>
    								<?php } ?>
    								
    								<?php if (!empty($item->short_biography)) {?>
    									<div class="speaker-additional-info">
    										<strong><?php echo JText::_("LNG_ADDITIONAL_INFORMATION")?></strong>
    										<p><?php echo $item->short_biography ?></p>
    									</div>
    								<?php } ?>
    								
    							
    								
    								<div class="speaker-social-icons-container">
    									<ul class="speaker-social-icons">
    										<?php if (!empty($item->facebook)) {?>
    											<li class="facebook">
    												<a href="<?php echo htmlspecialchars($item->facebook, ENT_QUOTES) ?>">
    													<i class="la la-facebook"></i>
    												</a>
    											</li>
    										<?php } ?>
    										<?php if (!empty($item->twitter)) {?>
    											<li class="linkedin">
    												<a href="<?php echo htmlspecialchars($item->twitter, ENT_QUOTES) ?>">
    													<i class="la la-twitter"></i>
    													
    												</a>
    											</li>
    										<?php } ?>
    										<?php if (!empty($item->linkedin)) {?>
    											<li class="linkedin">
    												<a href="<?php echo htmlspecialchars($item->linkedin, ENT_QUOTES) ?>">
    													<i class="la la-linkedin"></i>
    												</a>
    											</li>
    										<?php } ?>
    										
    										<?php if (!empty($item->additional_info_link)) {?>
    											<li class="">
    												<a href="<?php echo htmlspecialchars($item->additional_info_link, ENT_QUOTES)?>">
    													<i class="la la-globe"></i>
    												</a>
    											</li>
    										<?php } ?>
    										
    									</ul>
    								</div>
    							</div>
    						</div>
    					</div>
    					<div class="clear"></div>
    				</div>
    			<?php
				}
			} else {
			}
		?>
    	<div class="clear"></div>
    </div>
</div>

<script>
    window.addEventListener('load', function(){
        jQuery(".future-sessions").click(function(event){
            event.stopPropagation();
        });

        jQuery(".bookmark").click(function(event){
            event.stopPropagation();
        });
    });

    function showDetails(token){
        jQuery(".showDetails-"+token).toggleClass("open-details");
    }

</script>