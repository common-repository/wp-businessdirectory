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

$document = JFactory::getDocument();
$config = JBusinessUtil::getSiteConfig();

$title = JText::_("LNG_CONTROL_PANEL").' | '.$config->sitename;
JBusinessUtil::setMetaData($title, "", "", true);
$menuItemId = JBusinessUtil::getActiveMenuItem();

$user = JBusinessUtil::getUser();
if($user->ID == 0){
	$app = JFactory::getApplication();
	$return = 'index.php?option=com_jbusinessdirectory&view=userdashboard'.$menuItemId;
    $app->redirect(JBusinessUtil::getLoginUrl($return, false));
}

$appSettings =  JBusinessUtil::getApplicationSettings();
$enablePackages = $appSettings->enable_packages;
$enableOffers = $appSettings->enable_offers;
?>

<style>
#content-wrapper{
	margin: 20px;
	padding: 0px;
}

.tooltip {
	border-style:none !important;
}
</style>

<div id="jbd-container" class="jbd-container jdb-dashboard">
    <div id="user-options">
    	<?php if($this->actions->get('directory.access.controlpanel') || !$appSettings->front_end_acl){ ?>
    		<div class="row pt-5">
    			<?php if($this->actions->get('directory.access.listing.service.reservation')|| !$appSettings->front_end_acl) {
                    if ($this->appSettings->enable_services == 1 && file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/models/companyservice.php')) {
                        ?>
                        <div class="col-lg-4">
                            <div class="card jbox-card h-100 border border-bottom-success clickable"
                                 onclick="openLink('<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=manageuserservicereservations') ?>')">
                                <div class="jbox-header">
                                    <p><?php echo JTEXT::_("LNG_SERVICE_BOOKING_DATA") ?>
                                        <span class="d-block small pt-1"><?php echo JTEXT::_("LNG_SERVICE_BOOKING_DATA_INFO") ?></span>
                                    </p>
                                    <div class="badge transparent"><i class="la la-4x la-tasks text-success"></i></div>
                                </div>
                                <div class="jbox-body">
                                    <div>
                                        <small class="stats-label"><?php echo JText::_("LNG_SERVICE_BOOKINGS") ?></small>
                                        <h4 class="text-success"><?php echo $this->statistics->serviceBookings; ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                <?php } }?>
    			
    			<?php if($enableOffers && ($this->actions->get('directory.access.offers')|| !$appSettings->front_end_acl)
                && $this->appSettings->enable_offers && $this->appSettings->enable_offer_selling && file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/models/offerorder.php')
                ){?>
    				<div class="col-lg-4">
    					<div class="card jbox-card h-100 border border-bottom-success clickable" onclick="openLink('<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=manageuserofferorders') ?>')">
    						<div class="jbox-header">
    							<p>
    								<?php echo JTEXT::_("LNG_OFFER_ORDERS_DATA") ?>
    								<span class="d-block small pt-1"><?php echo JTEXT::_("LNG_OFFER_ORDERS_DATA_INFO") ?></span>
    							</p>
    							<div class="badge transparent"><i class="la la-4x la-certificate text-info"></i></div>
    						</div>
    						<div class="jbox-body">
                                <div>
                                    <small class="stats-label"><?php echo JText::_("LNG_OFFER_ORDERS")?></small>
                                    <h4 class="text-success"><?php echo $this->statistics->offerOrders; ?></h4>
                                </div>
    						</div>
    					</div>
    				</div>
    			<?php }?>
    			
    			<?php if($appSettings->enable_events && ($this->actions->get('directory.access.event.tickets')|| !$appSettings->front_end_acl)
                && $this->appSettings->enable_event_reservation && file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/models/eventticket.php')
                ){?>
    				<div class="col-lg-4">
    					<div class="card jbox-card h-100 border border-bottom-success clickable" onclick="openLink('<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=manageusereventreservations') ?>')">
    						<div class="jbox-header">
    							<p>
    								<?php echo JTEXT::_("LNG_EVENT_RESERVATION_DATA") ?>
    								<span class="d-block small pt-1"><?php echo JTEXT::_("LNG_EVENT_RESERVATION_DATA_INFO") ?></span>
    							</p>
    							<div class="badge transparent"><i class="la la-4x la-calendar text-warning"></i></div>
    						</div>
    						<div class="jbox-body">
    							 <div>
                                    <small class="stats-label"><?php echo JText::_("LNG_EVENT_RESERVATIONS")?></small>
                                    <h4 class="text-success"><?php echo $this->statistics->eventReservations; ?></h4>
                                </div>
    						</div>
    					</div>
    				</div>
    			<?php } ?>
    		</div>

    		<div class="row py-5">
    			<?php if($appSettings->enable_bookmarks && ($this->actions->get('directory.access.bookmarks') || !$appSettings->front_end_acl)) { ?>
    				<div class="col-lg-4">
    					<div class="card jbox-card h-100 clickable" onclick="openLink('<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=managebookmarks&user_dashboard=1') ?>')">
    						<div class="jbox-header">
    							<p>
    								<?php echo JTEXT::_("LNG_MANAGE_YOUR_BOOKMARKS") ?>
    								<span class="d-block small pt-1"><?php echo JTEXT::_("LNG_BOOKMARKS_INFO") ?></span>
    							</p>
    							<div class="badge transparent"><i class="la la-4x la-bookmark text-danger"></i></div>
    						</div>
    					</div>
    				</div>
    			<?php } ?>
                <?php if($appSettings->enable_reviews && ($this->actions->get('directory.access.reviews') || !$appSettings->front_end_acl)) { ?>
                    <div class="col-lg-4">
                        <div class="card jbox-card h-100 clickable" onclick="openLink('<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=manageuserreviews') ?>')">
                            <div class="jbox-header">
                                <p>
                                    <?php echo JTEXT::_("LNG_MANAGE_YOUR_REVIEWS") ?>
                                    <span class="d-block small pt-1"><?php echo JTEXT::_("LNG_REVIEWS_INFO") ?></span>
                                </p>
                                <div class="badge transparent"><i class="la la-4x la-comment text-info"></i></div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <?php if($this->appSettings->show_contact_form && ($this->actions->get('directory.access.messages') || !$appSettings->front_end_acl)) { ?>
                    <div class="col-lg-4">
                        <div class="card jbox-card h-100 clickable" onclick="openLink('<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=manageusermessages') ?>')">
                            <div class="jbox-header">
                                <p>
                                    <?php echo JTEXT::_("LNG_MANAGE_YOUR_MESSAGES") ?>
                                    <span class="d-block small pt-1"><?php echo JTEXT::_("LNG_MESSAGES_INFO") ?></span>
                                </p>
                                <div class="badge transparent"><i class="la la-4x la-inbox text-warning"></i></div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
    		</div>
    	<?php } else {
    			echo JText::_("LNG_NOT_AUTHORIZED");
    		}
    	?>
    </div>
</div>

<script>
    function openLink(link){
    	document.location.href=link;
    }
</script>
