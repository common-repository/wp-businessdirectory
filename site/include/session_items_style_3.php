<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
if (!isset($conferenceSessions)) {
	$conferenceSessions = $this->conferenceSessions;
}

if (!isset($speakers)) {
	$speakers = $this->speakers;
}
if (!isset($sessionExpandedItems)) {
	$sessionExpandedItems = $this->sessionExpandedItems;
}
$user = JBusinessUtil::getUser();
$isSuperUser = UserService::isSuperUser($user->ID);
$days = JBusinessUtil::getSessionsDays($conferenceSessions);
$currentDay = date("Y-m-d");
$found = false;
foreach($days as $day){
	if($currentDay == $day){
		$found = true;
	}
}

if(!isset($unregisteredUserJoin)){
	$unregisteredUserJoin = false;
}
?>

<div id="conferences" class="sessions-style-2">
	<div class="d-flex <?php echo !empty($showSessionDays)?"justify-content-between":"justify-content-end"?>">
		<?php if(!empty($showSessionDays)){ ?>
			<div class="day-tabs">
				<?php foreach($days as $day){ ?>
					<div class="day-tab js-tab-<?php echo $day ?>" data-day="<?php echo $day?>"> 
						<?php echo JBusinessUtil::getShortWeekDate($day)?>
					</div>
				<?php } ?>
			</div>
		<?php } ?>
		
		<?php if(!empty($showFavFilter) && !empty($user->ID)){ ?>
			<div class="session-filter text-right">
				<div class="fav-filter js-only-fav"><span class="only-fav-text"><?php echo JText::_("LNG_ONLY_FAV"); ?></span><span class="show-all-text"><?php echo JText::_("LNG_SHOW_ALL"); ?></span></div>
			</div>
		<?php } ?>
	</div>
    <div id="conference-sessions-container"> 
    	<?php
			$date="";
			$time="";
			if (!empty($conferenceSessions)) {
				foreach ($conferenceSessions as $item) {
					if(!isset($item->registrations)) {
						$item->registrations = array();
					}
                    $overlap = JBusinessUtil::getOverlappingSessions($item);					
					$isRegistered = JBusinessUtil::array_search_recursive($user->ID, $item->registrations);
					if($isRegistered || !$isRegistered && $unregisteredUserJoin) {
						$canJoin = true;
					} else {
						$canJoin = false;
					}
					$token = rand();

		?>
					<div class="session-item shadow-border js-session-day js-day-<?php echo $item->date?> <?php echo !empty($item->bookmark)?"js-bookmarked":"js-not-bookmarked"  ?>"  style="background-color: <?php echo !empty($item->color)?$item->color:""; ?>"  onclick="showDetails('<?php echo $token ?>');" >
    					<div class="showDetails-<?php echo $token ?> column session-info <?php echo $sessionExpandedItems==1?"open-details":"" ?>" data-conference-id="<?php echo $item->id?>" onclick="showDetails('<?php echo $token ?>');">
    						<h3 class=""><?php echo $item->name?></h3>
    						<div class="location-info" style="color:<?php echo !empty($item->categories[0][2])?$item->categories[0][2]:"inherit" ?>">

								<?php if(!empty($item->categories)){
									$cats=array();
									foreach($item->categories as $cat){
										$cats[] = "<span style=\"color:".(!empty($cat[2])?$cat[2]:"inherit")."\">". $cat[1]."</span>";
									}	
								 
									$categories = implode(", ",$cats);
								?>
									<div class="session-categories"><?php echo $categories ?></div>
								<?php } ?>


                                <?php echo $item->location ." / ".JBusinessUtil::convertTimeToFormat($item->start_time)." - ".JBusinessUtil::convertTimeToFormat($item->end_time)." ". (!empty($item->time_zone)?JText::_('LNG_GMT')." ".$item->time_zone:"") ?>
								<i class="la la-angle-up"></i> <i class="la la-angle-down"></i> 
								

                                <div class="right d-flex align-items-center">
									<?php if(!$isSuperUser) { ?>
										<?php if (!empty($item->session_url) && date("Y-m-d") == $item->date && $canJoin) { ?>
											<a class="btn btn-success mr-2" style="padding: 3px 6px;" onclick="jbdUtils.registerStatAction(<?php echo $item->locationId ?>,<?php echo STATISTIC_ITEM_SESSION_LOCATION ?>,<?php echo STATISTIC_TYPE_VIEW ?>);jbdUtils.registerSessionJoinAction(<?php echo $item->id ?>, <?php echo $user->ID ?>,<?php echo $user->ID ==0 ? '1':'0'?>, <?php echo $canJoin ?>)" href="<?php echo $user->ID !=0 && $canJoin ? $item->session_url : 'javascript:void(0)' ?>"><?php echo JText::_("LNG_JOIN") ?></a>
										<?php } ?>
										<?php if($user->ID != 0 && $isRegistered) { ?>
											<a href="javascript:jbdUtils.showSessionUnregisterDialog(<?php echo $item->id ?>);" title="<?php echo JText::_("LNG_UNREGISTER_SESSION_USER")?>" class="session-action-icon" ><img src="<?php echo BD_ASSETS_FOLDER_PATH."images/session_remove.svg" ?>" alt="remove-icon" width="20px" height="20px"/></a>
										<?php } else if ($user->ID == 0 && count($item->registrations) < $item->capacity || $user->ID !=0 && !$isRegistered && count($item->registrations) < $item->capacity){ ?>
											<?php if(empty(JBusinessUtil::getOverlappingSessions($item))) {?>
											 	<a href="javascript:jbdUtils.showSessionRegisterDialog(<?php echo $user->ID ==0 ? "1":"0"?>,<?php echo $item->id ?>);" title="<?php echo JText::_("LNG_REGISTER_SESSION_USER")?>" class="session-action-icon" ><i class="icon list-plus"></i></a>
											<?php } else { ?>
												<a href="javascript:jbdUtils.showSessionUpdateDialog(<?php echo $item->id ?>, <?php echo $overlap[0] ?>, <?php echo $user->ID ?>);" title="<?php echo JText::_("LNG_REGISTER_SESSION_USER")?>" class="session-action-icon" ><i class="icon list-plus"></i></a>
											<?php } ?>
										<?php } ?>
									<?php } else { ?>										
										<a href="javascript:jbdUtils.showRegisteredUsersDialog(<?php echo $item->id ?>);" title="<?php echo JText::_("LNG_VIEW_REGISTERED_USERS")?>" class="session-action-icon" ><i class="la la-eye"></i></a>
                                    <?php } ?>

                                    <?php if(!empty($item->bookmark)) { ?>
                                        <a id="bookmark-<?php echo $item->id ?>" href="javascript:jbdUtils.showUpdateBookmarkDialog(<?php echo $user->ID==0?"1":"0"?>, <?php echo $item->id ?>,<?php echo BOOKMARK_TYPE_SESSION ?>)"  title="<?php echo JText::_("LNG_UPDATE_BOOKMARK")?>" class="bookmark"><i class="la la-heart"></i></a>
                                    <?php } else {?>
                                        <a id="bookmark-<?php echo $item->id ?>" href="javascript:jbdUtils.showAddBookmark(<?php echo $user->ID==0?"1":"0"?>, <?php echo $item->id ?>,<?php echo BOOKMARK_TYPE_SESSION ?>)" title="<?php echo JText::_("LNG_ADD_BOOKMARK")?>" class="bookmark"><i class="la la-heart-o"></i></a>
                                    <?php } ?>
                                    <?php if(!empty($item->register_url)) { ?>
                                        <a href="<?php echo $item->register_url?>" class=""><i class="la la-arrow-circle-o-right"></i> <?php echo JText::_("LNG_REGISTER")?></a>
                                    <?php } ?>
								</div>
                            </div>
    						<div class="details">
    							<div class="session-details">
    								<p class="session-short-description"><?php echo $item->short_description?></p>
    								<ul class="session-speakers">
    									<?php
											if (!empty($item->speakers)) {
												foreach ($item->speakers as $spk) {
													if (empty($speakers[$spk[0]])) {
														continue;
													}
													$speaker = $speakers[$spk[0]];
													$tokenSpeaker = rand(); ?>
    										<li>
    											<div class="session-speaker" data-speaker-id="<?php echo $speaker->name ?>" id="speaker-details-<?php echo $tokenSpeaker ?>" onclick="showSpeakerDetails('<?php echo $tokenSpeaker ?>');">
    												<div class="speaker-image">
    													<?php if (!empty($speaker->photo)) {?>
    														<img  alt="<?php echo $speaker->name ?>" src="<?php echo BD_PICTURES_PATH.$speaker->photo?>"/>
    													<?php } else {?>
    														<img  alt="<?php echo $speaker->name ?>" src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>" />
    													<?php } ?>
    												</div>
    												<div class="speaker-info">
    													<div class="left">
    														<h3><?php echo $speaker->name ?></h3>
    														<?php if (!empty($speaker->country_logo)) {?>
                                								<img class="country_flag" title="<?php echo htmlentities($speaker->country_name, ENT_QUOTES) ?>"
                                                                     alt="<?php echo htmlentities($speaker->country_name, ENT_QUOTES) ?>"
                                                                     src="<?php echo BD_PICTURES_PATH.$speaker->country_logo?>"/>
                                							<?php } ?>
    														<div class="clear"></div>
    														<div><?php echo $speaker->title." ".$speaker->company_name ?> </div>
    														<div><?php echo $speaker->typeName ?> <i class="la la-angle-up"></i> <i class="la la-angle-down"></i></div>
    													</div>
    												</div>
    												<div class="clear"></div>
    												<div class="speaker-details">
                                                        <div class="right">
                                                            <?php if (!empty($speaker->company_logo)) {?>
                                                                <img class="country_flag" style="height:50px;" alt="<?php echo htmlentities($speaker->company_name, ENT_QUOTES) ?>" src="<?php echo BD_PICTURES_PATH.$speaker->company_logo?>"/>
                                                            <?php } ?>
                                                        </div>
                           								<div class="future-sessions">
                            								<?php if (!empty($speaker->sessions)) {?>
                            									<strong><?php echo JText::_("LNG_FUTURE_SESSIONS")?></strong>
                            									<ul class="speaker-speakers">
                            										<?php foreach ($speaker->sessions as $session) {	?>
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
                            								
                        								<?php if (!empty($speaker->biography)) {?>
                        									<div id="speaker-biography-<?php echo $item->id."-".$speaker->id ?>" class="speaker-biography">
                        										<strong><?php echo JText::_("LNG_BIOGRAPHY")?></strong>
                        										<div class="intro-text">
                                    								<?php echo JBusinessUtil::truncate(JHTML::_("content.prepare", $speaker->biography), 300) ?>
                                    								<?php if (strlen(strip_tags($speaker->biography))>strlen(strip_tags(JBusinessUtil::truncate(JHTML::_("content.prepare", $speaker->biography), 300)))) {?>
                                    									<a class="read-more" href="javascript:void(0)" onclick="jQuery('#speaker-biography-<?php echo $item->id."-".$speaker->id ?>').toggleClass('open')">
                                    										<?php echo JText::_("LNG_MORE") ?> </a>
                                    								<?php } ?>
                                    							</div>
                                    							<div class="full-text">
                                    								<?php echo JHTML::_("content.prepare", $speaker->biography) ?>
                                    								<a class="read-more" href="javascript:void(0)" onclick="jQuery('#speaker-biography-<?php echo $item->id."-".$speaker->id ?>').toggleClass('open')">
                                    										<?php echo JText::_("LNG_LESS") ?> </a>
                                    							</div>
                        									</div>
                        								<?php } ?>
                        								
                        								<?php if (!empty($speaker->short_biography)) {?>
                        									<div class="speaker-additional-info">
                        										<strong><?php echo JText::_("LNG_ADDITIONAL_INFORMATION")?></strong>
                        										<p><?php echo $speaker->short_biography ?></p>
                        									</div>
                        								<?php } ?>
                            								
                        								<div class="speaker-social-icons-container">
                        									<ul class="speaker-social-icons">
                        										<?php if (!empty($speaker->facebook)) {?>
                        											<li class="facebook">
                        												<a href="<?php echo htmlspecialchars($speaker->facebook, ENT_QUOTES) ?>">
                        													<i class="la la-facebook-f"></i>
                        												</a>
                        											</li>
                        										<?php } ?>
                        										<?php if (!empty($speaker->twitter)) {?>
                        											<li class="linkedin">
                        												<a href="<?php echo htmlspecialchars($speaker->twitter, ENT_QUOTES) ?>">
                        													<i class="la la-twitter"></i>
                        													
                        												</a>
                        											</li>
                        										<?php } ?>
                        										<?php if (!empty($speaker->linkedin)) {?>
                        											<li class="linkedin">
                        												<a href="<?php echo htmlspecialchars($speaker->linkedin, ENT_QUOTES) ?>">
                        													<i class="la la-linkedin"></i>
                        												</a>
                        											</li>
                        										<?php } ?>
                        										
                        										<?php if (!empty($speaker->additional_info_link)) {?>
                        											<li class="">
                        												<a href="<?php echo htmlspecialchars($speaker->additional_info_link, ENT_QUOTES)?>">
                        													<i class="la la-globe"></i>
                        												</a>
                        											</li>
                        										<?php } ?>
                        										
                        									</ul>
                        								</div>
                        							</div>
    											</div>
    										</li>
    									<?php
												}
											} ?>
    								</ul>

    								<ul class="session-sponsors">
    									<?php
											if (!empty($item->companies)) {
												foreach ($item->companies as $company) {?>
    											<?php if (!empty($company[3]) && $company[5] == 1) {?>
    												<li>
    													<a style="color: transparent !important;" href="<?php echo $company[4];?>"><img  alt="<?php echo $company[1] ?>" src="<?php echo BD_PICTURES_PATH.$company[3]?>"/></a>
    												</li>
    											<?php } ?>
    										<?php } ?>
    									<?php
											} ?>
    								</ul>
    								
    								<?php if (!empty($item->attachments) || !empty($item->video)) {?>
        								<div class="right view-resources">
        									<a href="<?php  echo JBusinessUtil::getConferenceSessionLink($item->id, $item->alias) ?>"><?php echo JText::_("LNG_VIEW_VIDEO_AND_RESOURCES")?></a>
        								</div>
    								<?php } ?>
    							</div>							
    							<div class="session-location" style="display: none">
    								<?php if (!empty($item->locationImage)) { ?>
    									<img src="<?php echo BD_PICTURES_PATH . $item->locationImage ?>"/>
    									<?php
									} else {
										echo JText::_("LNG_NO_IMAGES");
									} ?>
    							</div>								
    						</div>
    					</div>

						<?php require "session_register.php" ?>

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
		
		<?php if(!empty($showSessionDays)){ ?>
			jQuery(".js-session-day").hide();

			<?php if($found){?>
				jQuery(".js-day-<?php echo $currentDay ?>").show();
				jQuery(".js-tab-<?php echo$currentDay ?>").addClass("active");
				
			<?php }else{?>
				jQuery(".js-day-<?php echo $days[0] ?>").show();
				jQuery(".js-tab-<?php echo $days[0] ?>").addClass("active");
			<?php }?>
		<?php } ?>

		jQuery(".day-tab").click(function(){
			jQuery(".js-session-day").hide(500);
			let day = jQuery(this).attr("data-day");
			jQuery(".js-day-"+day).show(800);

			jQuery(".day-tab").removeClass("active");
			jQuery(".js-tab-"+day).addClass("active");
		});

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

        jQuery(".read-more").click(function(event){
            event.stopPropagation();
        });

        jQuery(".session-speaker").click(function(event){
            event.stopPropagation();
        });

        jQuery(".bookmark").click(function(event){
            event.stopPropagation();
        });

		jQuery(".js-only-fav").click(function(event){
			jQuery(this).toggleClass("open-fav");
			jQuery(".js-not-bookmarked").toggleClass("hide");
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

	function showSpeakerDetails(token){
        jQuery("#speaker-details-"+token).toggleClass("open");
    }

</script>