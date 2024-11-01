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

if (!isset($sessions)) {
	$sessions = $this->sessions;
}

if (!isset($speakerExpandedItems)) {
	$speakerExpandedItems = $this->speakerExpandedItems;
}
$user = JBusinessUtil::getUser();
?>

<div id="speakers" class="speakers-style-2">
    <div id="conference-speakers-container"> 
    	<?php
			$date="";
			if (!empty($speakers)) {
				foreach ($speakers as $item) {
					$token = rand(); ?>
    				<div class="speaker-item shadow-border">
    					<div class="speaker-photo <?php echo $logoType?>">
    						<?php if (!empty($item->photo)) {?>
    							<img  alt="<?php echo htmlspecialchars($item->name, ENT_QUOTES) ?>" src="<?php echo BD_PICTURES_PATH.$item->photo?>"/>
    						<?php } else {?>
    							<img  alt="<?php echo htmlspecialchars($item->name, ENT_QUOTES) ?>" src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>" />
    						<?php } ?>
    					</div>
    					<div class="showDetails-<?php echo $token ?> column speaker-info <?php echo $speakerExpandedItems==1?"open-details":"" ?>" onclick="showDetails('<?php echo $token ?>');" style="border-color:<?php echo $item->typeColor?>">
    						<div class="row">
    							<div class="col-md-8">
    								<h3 class=""><?php echo $item->name?></h3>
    								<div class="track"><?php echo $item->title.' '.$item->company_name ?></div>
    							</div>
    							<div class="col-md-4">
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

    							</div>
    						</div>
    						<div class="details">
    							<div class="speaker-details">
                                    <div class="right">
    									<?php if (!empty($item->company_logo)) {?>
                                            <img class="country_flag" style="height:50px;" alt="<?php echo htmlentities($item->company_name, ENT_QUOTES) ?>" src="<?php echo BD_PICTURES_PATH.$item->company_logo?>"/>
    									<?php } ?>
                                    </div>
    								<div class="future-sessions">
        								<?php if (!empty($item->sessions)) { ?>
        									<strong><?php echo JText::_("LNG_FUTURE_SESSIONS")?></strong>
        									<ul class="speaker-speakers">
        										<?php foreach ($item->sessions as $sess) {
                                                    if (empty($sess) || empty($sess[0]) || empty($sessions[$sess[0]])) {
                                                        continue;
                                                    }
                                                    $session = $sessions[$sess[0]];
                                                    $tokenSession = rand();
                                                ?>
        											<li>
        												<div class="row-fluid">
        													<div class="speaker-sessions">
        														<a href="javascript:showSessionDetails('<?php echo $tokenSession ?>')">
        															<span class="session-name"><?php echo $session->name ?></span>,
        															<span class="session-time"><i class=""></i><?php echo JBusinessUtil::convertTimeToFormat($session->start_time).' - '.JBusinessUtil::convertTimeToFormat($session->end_time) ?></span>,
        															<span class="session-date"><i class=""></i><?php echo JBusinessUtil::getShortWeekDate($session->date) ?></span>
        														</a>
        													</div>
        												</div>
        												
        												<div id="session-details-<?php echo $tokenSession ?>" class="column session-info session-item" onclick="showSessionDetails('<?php echo $tokenSession ?>');" data-conference-id="<?php echo $session->id?>" style="background-color:<?php echo !empty($item->categories[0][2])?$item->categories[0][2]:"inherit" ?>" >
                                    						<h3 class=""><?php echo $session->name?></h3>
                                    						<div class="location-info" style="color:<?php echo !empty($item->categories[0][2])?$item->categories[0][2]:"inherit" ?>"><?php echo $session->location ." / ".JBusinessUtil::convertTimeToFormat($session->start_time)." - ".JBusinessUtil::convertTimeToFormat($session->end_time)?></div>
                                    						<div class="details">
                                    							<div class="session-details">
                                    								<p class=""><?php echo $session->short_description?></p>
                                    								<ul class="session-speakers">
                                    									<?php
																		if (!empty($session->speakers)) {
																			foreach ($session->speakers as $spearker) {
																				?>
                                    										<li>
                                    											<div class="row-fluid speaker-container" data-speaker-id="<?php echo $spearker[0] ?>">
                                    												<div class="speaker-image">
                                    													<a href="<?php  echo JBusinessUtil::getSpeakerLink($spearker[0], $spearker[2]) ?>">
                                    														<?php if (!empty($spearker[3])) {?>
                                    															<img  alt="<?php echo $spearker[1] ?>" src="<?php echo BD_PICTURES_PATH.$spearker[3]?>"/>
                                    														<?php } else {?>
                                    															<img  alt="<?php echo $spearker[1] ?>" src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>" />
                                    														<?php } ?>
                                    													</a>
                                    												</div>
                                    												<div class="speaker-details">
                                    													<h3><a href="<?php  echo JBusinessUtil::getSpeakerLink($spearker[0], $spearker[2]) ?>"><?php echo $spearker[1] ?></a></h3>
                                    													<p><?php echo $spearker[4].", ".$spearker[5] ?>
                                    												</div>
                                    											</div>
                                    										</li>
                                    									<?php
																			}
																		} ?>
                                    								</ul>
                                    								
                                    								<ul class="session-sponsors">
                                    									<?php
																		if (!empty($session->companies)) {
																			foreach ($session->companies as $company) {?>
                                    											<?php if (!empty($company[3]) && $company[5] == 1) {?>
                                    												<li class="sponsor">
                                    													<a style="color: transparent !important;" href="<?php echo $company[4];?>"><img  alt="<?php echo $company[1] ?>" src="<?php echo BD_PICTURES_PATH.$company[3]?>"/></a>
                                    												</li>
                                    											<?php } ?>
                                    										<?php } ?>
                                    									<?php
																		} ?>
                                    								</ul>
                                    								
                                    								<?php if (!empty($session->attachments) || !empty($session->video)) {?>
                                        								<div class="right view-resources">
                                        									<a href="<?php  echo JBusinessUtil::getConferenceSessionLink($session->id, $session->alias) ?>"><?php echo JText::_("LNG_VIEW_VIDEO_AND_RESOURCES")?></a>
                                        								</div>
                                    								<?php } ?>
                                    							</div>							
                                    						</div>
                                    					</div>
        											</li>
        										<?php
					} ?>
        									</ul>
        								<?php } ?>
        								<div class="clear"></div>
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

	jQuery(".location-link").click(function(event){
		jQuery(this).parent().toggleClass("open-location");
		event.stopPropagation();
	});	

	jQuery(".speaker-container").click(function(event){
		speakerId = jQuery(this).attr('data-speaker-id');
        let url = jbdUtils.getAjaxUrl('increaseSpeakerClickAjax', 'conferencesessions', 'conferencesessions');
        url = url + '&speakerId='+speakerId;
        jQuery.ajax({
			url: url,
			type: 'GET'
		});
		event.stopPropagation();
	});	

	jQuery(".future-sessions").click(function(event){
		event.stopPropagation();
	});


	jQuery(".session-info").click(function(event){
		event.stopPropagation();
	});

	jQuery(".session-speaker").click(function(event){
		event.stopPropagation();
	});
});

function showDetails(token){
    jQuery(".showDetails-"+token).toggleClass("open-details");

    var cssClass = jQuery(".showDetails-"+token).attr('class');
    if(cssClass.indexOf('open') >= 0){
        cSessionId = jQuery(this).attr('data-conference-id');
        let url = jbdUtils.getAjaxUrl('increaseConferenceSessionClickAjax', 'conferencesessions', 'conferencesessions');
        url = url + '&cSessionId='+cSessionId;
        jQuery.ajax({
            url: url,
            type: 'GET'
        })
    }
}

function showSessionDetails(token){
    jQuery("#session-details-"+token).toggleClass("open");
    
}
</script>