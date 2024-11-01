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
if (!isset($sessionExpandedItems)) {
	$sessionExpandedItems = $this->sessionExpandedItems;
}
$user = JBusinessUtil::getUser();
$isSuperUser = UserService::isSuperUser($user->ID);

if(!isset($unregisteredUserJoin)){
	$unregisteredUserJoin = false;
}
?>

<div id="conferences">
    <div id="conference-sessions-container"> 
    	<?php
			$date="";
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
					$token = rand(); ?>
    				<div class="session-item" style="background-color: <?php echo !empty($item->color)?$item->color:""; ?>" >
    					<div class="column date">
    						<div class="">
    							<span>
    								<?php
										if ($item->date!=$date) {
											echo JBusinessUtil::getShortDate($item->date);
											$date=$item->date;
										} ?>
    							</span>
    						</div>
    					</div>
    					<div class="column time">
    						<div class="start ng-binding"><?php echo JBusinessUtil::convertTimeToFormat($item->start_time) ?></div>
    						<div class="end ng-binding"><?php echo JBusinessUtil::convertTimeToFormat($item->end_time) ?></div>
    					</div>
    					<div class="showDetails-<?php echo $token ?> column session-info <?php echo $sessionExpandedItems==1?"open-details":"" ?>" onclick="showDetails('<?php echo $token ?>');" data-conference-id="<?php echo $item->id?>" style="border-color:<?php echo !empty($item->categories[0][2])?$item->categories[0][2]:"inherit" ?>">
    						<div class="location-link">
                                <span><?php echo $item->location ?></span>

								<?php if(!$isSuperUser) { ?>
										<?php if (!empty($item->session_url) && date("Y-m-d") == $item->date && $canJoin) { ?>
											<a class="btn btn-success mr-2" style="padding: 3px 6px;" onclick="jbdUtils.registerStatAction(<?php echo $item->locationId ?>,<?php echo STATISTIC_ITEM_SESSION_LOCATION ?>,<?php echo STATISTIC_TYPE_VIEW ?>);jbdUtils.registerSessionJoinAction(<?php echo $item->id ?>, <?php echo $user->ID ?>,<?php echo $user->ID ==0 ? '1':'0'?>, <?php echo $canJoin ?>)" href="<?php echo $user->ID !=0 && $canJoin ? $item->session_url : 'javascript:void(0)' ?>"><?php echo JText::_("LNG_JOIN") ?></a>
										<?php } ?>
										<?php if($user->ID != 0 && $isRegistered) { ?>
											<a href="javascript:jbdUtils.showSessionUnregisterDialog(<?php echo $item->id ?>);" title="<?php echo JText::_("LNG_UNREGISTER_SESSION_USER")?>" class="bookmark mt-2" ><img src="<?php echo BD_ASSETS_FOLDER_PATH."images/session_remove.svg" ?>" alt="remove-icon" width="20px" height="20px"/></a>
										<?php } else if ($user->ID == 0 && count($item->registrations) < $item->capacity || $user->ID !=0 && !$isRegistered && count($item->registrations) < $item->capacity){ ?>
											<?php if(empty(JBusinessUtil::getOverlappingSessions($item))) {?>
											 	<a href="javascript:jbdUtils.showSessionRegisterDialog(<?php echo $user->ID ==0 ? "1":"0"?>,<?php echo $item->id ?>);" title="<?php echo JText::_("LNG_REGISTER_SESSION_USER")?>" class="bookmark mt-2" ><i class="icon list-plus"></i></a>
											<?php } else { ?>
												<a href="javascript:jbdUtils.showSessionUpdateDialog(<?php echo $item->id ?>, <?php echo $overlap[0] ?>, <?php echo $user->ID ?>);" title="<?php echo JText::_("LNG_REGISTER_SESSION_USER")?>" class="bookmark mt-2" ><i class="icon list-plus"></i></a>
											<?php } ?>
										<?php } ?>
									<?php } else { ?>										
										<a href="javascript:jbdUtils.showRegisteredUsersDialog(<?php echo $item->id ?>);" title="<?php echo JText::_("LNG_VIEW_REGISTERED_USERS")?>" class="bookmark" ><i class="la la-eye"></i></a>
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
    						<h3 class=""><?php echo $item->name?></h3>
							<?php if(!empty($item->categories)){ ?>
    							<div class="track" style="color:<?php echo $item->categories[0][2]?>"><?php echo $item->categories[0][1]?></div>
							<?php } ?>
    						<div class="details">
    							<div class="session-details">
    								<p class=""><?php echo $item->short_description?></p>
    								<ul class="session-speakers">
    									<?php
											if (!empty($item->speakers)) {
												foreach ($item->speakers as $spearker) {
													?>
    										<li>
    											<div class="speaker-container" data-speaker-id="<?php echo $spearker[0] ?>">
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
    												<div class="clear"></div>
    											</div>
    										</li>
    									<?php
												}
											} ?>
    								</ul>
    								<?php if (!empty($item->attachments)) {?>
    									<div class="session-attachments">
    										<ul>
    											<li><i class="la la-pdf"></i></li>
    											<?php foreach ($item->attachments as $attachment) { ?>	
    												<li><a target="_blank" href="<?php echo BD_ATTACHMENT_PATH.$attachment->path?>"><?php echo !empty($attachment->name)?$attachment->name:basename($attachment->path)?></a></li>
    											<?php }?>
    										</ul>
    									</div>
    								<?php } ?>
    								
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
    							</div>							
    							<div class="session-location">
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

		jQuery(".location-link").click(function(event){
			jQuery(this).parent().toggleClass("open-location");
			event.stopPropagation();
		});

        jQuery(".bookmark").click(function(event){
            event.stopPropagation();
        });

		jQuery(".speaker-container").click(function(event){
			speakerId = jQuery(this).attr('data-speaker-id');
            var url = jbdUtils.getAjaxUrl('increaseSpeakerClickAjax', 'conferencesessions', 'conferencesessions');
            url = url + '&speakerId='+speakerId;
			jQuery.ajax({
				url: url,
				type: 'GET'
			});
			event.stopPropagation();
		});	
		
	});

    function showDetails(token){
        jQuery(".showDetails-"+token).toggleClass("open-details");

        var cssClass = jQuery(".showDetails-"+token).attr('class');
        if(cssClass.indexOf('open') >= 0){
            cSessionId = jQuery(this).attr('data-conference-id');
            var url = jbdUtils.getAjaxUrl('increaseConferenceSessionClickAjax', 'conferencesessions', 'conferencesessions');
            url = url + '&cSessionId='+cSessionId;
            jQuery.ajax({
                url: url,
                type: 'GET'
            })
        }
    }

</script>