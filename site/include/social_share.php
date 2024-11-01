<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

$itemId = '';
$itemType = '';
$appSettings = JBusinessUtil::getApplicationSettings();

$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' ? 'https' : 'http') . '://' .  $_SERVER['HTTP_HOST'];
$url = $base_url . $_SERVER["REQUEST_URI"];

$app = JFactory::getApplication();
$input = $app->input;
$view = $input->get('view');

$langageTab = JBusinessUtil::getLanguageTag();
$langageTab = str_replace("-", "_", $langageTab);

?>

<div id="fb-root"></div>

<div class="jbd-social-container">
    <?php if ($appSettings->show_view_count && !isset($disableButton)) { ?>
        <div class="view-counter">
        	<i class="icon eye"></i>
        	<span>
                <?php
					switch ($view) {
						case "companies":
							echo $this->company->viewCount;
							break;
						case "event":
							echo  $this->event->view_count;
							break;
						case "offer":
							echo $this->offer->viewCount ;
							break;
						case "conference":
							echo $this->conference->viewCount ;
							break;
						case "conferencesession":
							echo $this->conferenceSession->viewCount ;
							break;
					}
				?>
            </span>
        </div>
    <?php } ?>

    <?php if ($appSettings->enable_socials && !isset($disableButton)) { ?>
        <a id="open_socials" href="javascript:void(0)" class="share-icon" >
            <i class="icon share-circle"></i>
        </a>
    <?php }?>
    
    <?php if ($appSettings->enable_socials) { ?>
    	<!-- Modal -->
    	<div id="socials" style="display:none;">
    		<div id="dialog-container" class="jbd-container">
    			<div class="titleBar">
    				<span class="dialogTitle" id="dialogTitle"></span>
    			</div>
    			<div class="dialogContent">
    				<div class="row">
    					<div class="col-md-3">
    						<div class="item-image text-center">
    							<?php if ($view == 'company' || $view == 'companies') {
									$itemId = $company->id;
									$itemType = STATISTIC_ITEM_BUSINESS; ?>
    								<?php if (!empty($company->logoLocation)) { ?>
    									<img src="<?php echo BD_PICTURES_PATH.$company->logoLocation ?>" alt="<?php echo $this->escape($this->company->name)?>" class="img-responsive"/>
    								<?php } else { ?>
    									<img src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>" alt="<?php echo $this->escape($this->company->name)?>" class="img-responsive" />
    								<?php } ?>
    							<?php
				} ?>
    							<?php if ($view == 'offer') {
									$itemId = $this->offer->id;
									$itemType = STATISTIC_ITEM_OFFER; ?>
    								<?php if (!empty($this->offer->pictures[0]->picture_path)) { ?>
    									<img src="<?php echo BD_PICTURES_PATH.$this->offer->pictures[0]->picture_path ?>" alt="<?php echo $this->escape($this->offer->subject)?>" class="img-responsive"/>
    								<?php } else { ?>
    									<img src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>" alt="<?php echo $this->escape($this->offer->subject)?>" class="img-responsive" />
    								<?php } ?>
    							<?php
				} ?>
    							<?php if ($view == 'event') {
									$itemId = $this->event->id;
									$itemType = STATISTIC_ITEM_EVENT; ?>
    								<?php if (!empty($this->event->pictures[0]->picture_path)) { ?>
    									<img src="<?php echo BD_PICTURES_PATH.$this->event->pictures[0]->picture_path ?>" alt="<?php echo $this->escape($this->event->name)?>" class="img-responsive"/>
    
    								<?php } else { ?>
    									<img src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>" alt="<?php echo $this->escape($this->event->name)?>" class="img-responsive" />
    								<?php } ?>
    							<?php
				} ?>
								<?php if ($view == 'trip') {
									$itemId = $this->trip->id;
									?>
    								<?php if (!empty($this->trip->pictures[0]->picture_path)) { ?>
    									<img src="<?php echo BD_PICTURES_PATH.$this->trip->pictures[0]->picture_path ?>" alt="<?php echo $this->escape($this->trip->name)?>" class="img-responsive"/>
    
    								<?php } else { ?>
    									<img src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>" alt="<?php echo $this->escape($this->trip->name)?>" class="img-responsive" />
    								<?php } ?>
    							<?php
				} ?>
    						</div>
    					</div>
    					<div class="col-md-9">
    						<div class="row share">
    							<div class="col-md-12">
    								<?php if ($view == 'company' || $view == 'companies') { ?>
    									<h4><?php echo isset($this->company->name)?$this->company->name:"" ; ?></h4>
    									<?php if (!empty($company->slogan)) { ?>
    										<p><?php echo $company->slogan; ?></p>
    									<?php } else { ?>
    										<p><?php echo $company->typeName; ?></p>
    									<?php } ?>
    								<?php } ?>
    								<?php if ($view == 'offer') { ?>
    									<h4><?php echo isset($this->offer->subject)?$this->offer->subject:"" ; ?></h4>
    									<?php if (!empty($this->offer->short_description)) { ?>
    										<p><?php echo $this->offer->short_description; ?></p>
    									<?php
										} else {
											$address = JBusinessUtil::getAddressText($this->offer);
											if (!empty($address)) {?>
                                                <p><i class="icon map-marker"></i> <?php echo $address; ?></p>
    										<?php }
										} ?>
    								<?php } ?>
    								<?php if ($view == 'event') { ?>
    									<h4><?php echo isset($this->event->name)?$this->event->name:"" ; ?></h4>
    									<?php if (!empty($this->event->short_description)) { ?>
    										<p><?php echo $this->event->short_description; ?></p>
    									<?php } else {
											$address = JBusinessUtil::getAddressText($this->event);
											if (!empty($address)) {?>
                                                <p><i class="icon map-marker"></i> <?php echo $address; ?></p>
                                            <?php } ?>
                                        <?php
										} ?>
    								<?php } ?>
									<?php if ($view == 'trip') { ?>
    									<h4><?php echo isset($this->trip->name)?$this->trip->name:"" ; ?></h4>
    									<?php if (!empty($this->trip->description)) { ?>
    										<p><?php echo JBusinessUtil::truncate($this->trip->description, 200, '...') ?></p>
    									<?php } else {
											$address = JBusinessUtil::getAddressText($this->trip);
											if (!empty($address)) {?>
                                                <p><i class="icon map-marker"></i> <?php echo $address; ?></p>
                                            <?php } ?>
                                        <?php
										} ?>
    								<?php } ?>
    							</div>
    							<div class="col-md-12">
    								<ul>
    									<li>
											<!-- Your share button code -->
											<div class="fb-share-button" 
												data-href="<?php echo htmlspecialchars($url, ENT_QUOTES)?>" 
												data-layout="button">
											</div>
										</li>
    									<li>
    										<a href="https://twitter.com/share" class="twitter-share-button">Tweet</a>
    									</li>
    									<li>
    										<script type="IN/Share" data-counter="right"></script>
    									</li>
    								</ul>
    							</div>
    						</div>
    					</div>
    				</div>
    			</div>
    		</div>
    	</div>
    
    	<script type="text/javascript">
    		window.addEventListener('load', function() {
    			jQuery('#open_socials').click(function() {
    					<?php if ($view == 'company' || $view == 'companies' || $view == 'offer' || $view == 'event') { ?>
    			    	jbdUtils.increaseShareClicks(<?php echo $itemId ?>, <?php echo $itemType ?>);
    				<?php } ?>
					
                    // Facebook
                    (function(d, s, id) {
					var js, fjs = d.getElementsByTagName(s)[0];
					if (d.getElementById(id)) return;
					js = d.createElement(s); js.id = id;
					js.src = "https://connect.facebook.net/<?php echo $langageTab?>/sdk.js#xfbml=1&version=v3.0";
					fjs.parentNode.insertBefore(js, fjs);
					}(document, 'script', 'facebook-jssdk'));

                    // Twitter
                    !function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';
                    if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';
                    fjs.parentNode.insertBefore(js,fjs);}}
                    (document, 'script', 'twitter-wjs');

                    // Linkedin
                    let script = document.createElement('script');
                    script.src = "https://platform.linkedin.com/in.js";
                    document.head.appendChild(script);

                    jQuery('#socials').jbdModal();
                });
    		});
    	</script>
		
    <?php } ?>
</div>