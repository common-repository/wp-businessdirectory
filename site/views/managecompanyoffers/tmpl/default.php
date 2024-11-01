
<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 
defined('_JEXEC') or die('Restricted access');
$appSettings = JBusinessUtil::getApplicationSettings();

$menuItemId = JBusinessUtil::getActiveMenuItem();
JBusinessUtil::checkPermissions("directory.access.offers", "managecompanyoffers");

$isProfile = true;
?>
<script>
	var isProfile = true;
</script>
<style>
#header-box, #control-panel-link {
	display: none;
}

.tooltip {
    border-style:none !important;
}

  .tooltip-inner {
        background-color: rgba(0,0,0,0.55);
        max-width:600px;
        padding:2px 2px;
        text-align:center;
        border-radius:4px;
    }
</style>

<?php

if(!$this->isCreateOfferAllow){
    JFactory::getApplication()->enqueueMessage(JText::_('LNG_OFFER_CREATION_NOT_ALLOWED'),"notice");
    return;
}

if(empty($this->items)){
	if($this->state->get('filter.type') == OFFER_TYPE_OFFER){
		$actionURL = JRoute::_('index.php?option=com_jbusinessdirectory&task=managecompanyoffer.add&filter_type='.$this->state->get('filter.type'));
	    echo JBusinessUtil::getNewItemMessageBlock(JText::_("LNG_OFFER"),JText::_("LNG_OFFERS"),$actionURL);
	}else{
		$actionURL = JRoute::_('index.php?option=com_jbusinessdirectory&task=managecompanyoffer.add&filter_type='.$this->state->get('filter.type'));
		echo JBusinessUtil::getNewItemMessageBlock(JText::_("LNG_PRODUCT"),JText::_("LNG_PRODUCTS"),$actionURL);
	}
    return;
}

?>
<div class="button-row  justify-content-end">
	<?php if ($appSettings->max_offers > $this->total && $this->isCreateOfferAllow || $this->state->get('filter.type') == OFFER_TYPE_PRODUCT) { ?>
	
			<button type="submit" class="btn btn-success button-add" onclick="jbdOffers.addOffer()">
				<span class="ui-button-text"><i class="la la-plus-sign"></i> <?php echo JText::_("LNG_ADD_NEW")?></span>
			</button>
	<?php }elseif ($appSettings->max_offers <= $this->total) {
        JFactory::getApplication()->enqueueMessage(JText::_('LNG_MAX_OFFERS_REACHED'),"notice");
    } ?>
</div>

<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyoffers'.$menuItemId);?>" method="post" name="adminForm" id="adminForm">
	<div id="editcell">
		<table class="dir-panel-table responsive-simple">			
			<tbody>
				<?php 
				$nrcrt = 1;
				if(!empty($this->items)){
					foreach($this->items as $offer) { ?>
						<tr class="jtable-body-row row<?php echo $nrcrt%2 ?>">
							<td class="jtable-body-row-data" align="left">
								<div class="item-head">
									<div class="item-image text-center">
										<?php if (!empty($offer->picture_path)) { ?>
											<a href='<?php echo JRoute::_( 'index.php?option=com_jbusinessdirectory&task=managecompanyoffer.edit&'.JSession::getFormToken().'=1&id='. $offer->id.'&filter_type='.$this->state->get('filter.type') )?>'>
												<img 
													src="<?php echo BD_PICTURES_PATH.$offer->picture_path ?>" 
													class="img-circle"
												/>
											</a>
										<?php } else { ?>
											<a href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=managecompanyoffer.edit&'.JSession::getFormToken().'=1&id='. $offer->id.'&filter_type='.$this->state->get('filter.type') ) ?>">
												<img 
													src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>" 
													class="img-circle"
												/>
											</a>
										<?php } ?>
									</div>
									<div class="item-name text-left">
										<div class="item-title">
											<a href='<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=managecompanyoffer.edit&'.JSession::getFormToken().'=1&id='.$offer->id.'&filter_type='.$this->state->get('filter.type') )?>'
												title="<?php echo JText::_('LNG_CLICK_TO_EDIT'); ?>"> 
												<strong><?php echo $offer->subject?></strong>
											</a>
											<div class="item-alias">
                                            	<?php echo $offer->alias ?>
                                       		</div>
										</div>
										<div>
											<?php if (count($offer->checklist) > 0) { ?>
												

												<div id="<?php echo $offer->id ?>"
													rel="tooltip" data-toggle="tooltip"
													data-trigger="click" data-placement="left" data-html="true" data-title=
													"
													<div>
														<table class='checklist'>
															<tbody>
															<?php foreach ($offer->checklist as $key => $val) { ?>
																<tr>
																	<td class='jtable-body-row-data'>
																		<?php echo $val->name ?>
																	</div>
																	<td class='jtable-body-row-data status <?php echo $val->status ? 'status_done' : ''; ?>'>
																		<i class='la la-<?php echo $val->status ? 'check' : 'exclamation'; ?>'></i>
																	</div>
																</tr>
															<?php } ?>
															</tbody>
														</table>
													</div>
												">
													<div class="item-label"><?php echo JText::_("LNG_COMPLETED") ?></div>
													<div class="progress ">
														<div class="progress-bar" role="progressbar" style="width: <?php echo $offer->progress * 100 ?>%" aria-valuenow="<?php echo $offer->progress * 100 ?>" aria-valuemin="0" aria-valuemax="100"></div>
													</div>
												</div>
											<?php } ?>
                                    	</div>
									</div>									
								</div>
							</td>
					      
							<td  class="jtable-body-row-data">
								<div class="item-label"><?php echo JText::_("LNG_PRICE") ?></div>
								<div class="item-value"><?php echo $offer->price?></div>
								<?php if(!empty(floatval($offer->specialPrice))) { ?>
									<div class="item-label"><?php echo JText::_("LNG_SPECIAL_PRICE") ?></div>
									<div class="item-value"><?php echo $offer->specialPrice ?></div>
								<?php } ?>
							</td>													

							<?php if($this->state->get('filter.type') == OFFER_TYPE_OFFER){ ?>
								<td  class="jtable-body-row-data">
										<div class="item-label"><?php echo JText::_("LNG_START_DATE") ?></div>
										<div class="item-value"><?php echo JBusinessUtil::getDateGeneralShortFormat( $offer->startDate); ?></div>
										<div class="item-label"><?php echo JText::_("LNG_END_DATE") ?></div>
										<div class="item-value"><?php echo JBusinessUtil::getDateGeneralShortFormat( $offer->endDate); ?></div>
								</td>
							<?php } ?>
							<td class="jtable-body-row-data" valign="top" align="center">	
								<div class="item-status"> 
									<?php if(($offer->state == 1) && ($offer->approved == 1)) {
										if($offer->expired){
											echo '<span class="status-badge badge-warning warn2">'.JText::_("LNG_EXPIRED").'</span>';
										}else if($offer->not_visible){
											echo '<span class="status-badge badge-warning">'.JText::_("LNG_NOT_VISIBLE").'</span>';
										}elseif(!$offer->allow_offers){
											echo '<span class="status-badge badge-warning warn">'.JText::_("LNG_NOT_INCLUDED").'</span>';
										}else{
											echo '<span class="status-badge badge-success">'.JText::_("LNG_PUBLISHED").'</span>';
										}
									} else {
										switch($offer->approved) {
											case -1:
												echo '<span class="status-badge badge-danger">'.JText::_("LNG_DISAPPROVED").'</span>';
												break;
											case 0:
												echo '<span class="status-badge badge-info">'.JText::_("LNG_PENDING").'</span>';
												break;
											case 1:
												echo '<span class="status-badge badge-warning warn">'.JText::_("LNG_UNPUBLISHED").'</span>';
												break;
										}
									} ?>
								
									<div class="item-actions">
										<?php if($offer->approved == 1) { ?>
											<a onclick="document.location.href = '<?php echo JRoute::_( 'index.php?option=com_jbusinessdirectory&task=managecompanyoffer.chageState&id='. $offer->id )?> '"
												title="<?php echo JText::_('LNG_CLICK_TO_CHANGE_STATE'); ?>" class="jtable-btn">
												<i class="<?php echo $offer->state==0?"la la-check text-success":"la la-ban text-warning"?>"></i>
											</a>
										<?php } ?>
										<a href="javascript:void(0);" onclick="jbdOffers.editOffer(<?php echo $offer->id ?>)"
											title="<?php echo JText::_('LNG_CLICK_TO_EDIT'); ?>" class="jtable-btn">
											<i class="la la-pencil"></i>
										</a>
										<a target="_blank" href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=offer&offerId='.$offer->id) ?>"
											title="<?php echo JText::_('LNG_CLICK_TO_VIEW'); ?>" class="jtable-btn"> 
											<i class="la la-eye"></i>
										</a>
										<a href="javascript:void(0);" onclick="jbdOffers.deleteOffer(<?php echo $offer->id ?>)"
											title="<?php echo JText::_('LNG_CLICK_TO_DELETE'); ?>" class="jtable-btn">
											<i class="la la-trash"></i>
										</a>
									</div>
								</div>
							</td>
						</tr>
				<?php } 
				}
				?>
			</tbody>			
		</table>
		<div class="pagination" <?php echo $this->pagination->total==0 ? 'style="display:none"':''?>>
			<?php echo $this->pagination->getListFooter(); ?>
			<div class="clear"></div>
		</div>
	</div>
	<input type="hidden" name="option"	value="<?php echo JBusinessUtil::getComponentName()?>" />
	<input type="hidden" name="task" id="task" value="" /> 
	<input type="hidden" name="id" id="id" value="" />
    <input type="hidden" name="filter_type" id="filter_type" value="<?php echo $this->state->get('filter.type') ?>" />
	<input type="hidden" name="companyId" id="companyId" value="<?php echo $this->companyId ?>" />
	<?php echo JHTML::_('form.token'); ?> 
</form>
<div class="clear"></div>

<?php require_once JPATH_COMPONENT_SITE . "/include/status_legend.php" ?>

<script>
	window.addEventListener('load', function() {
        jQuery('[rel="tooltip"]').tooltip();

		jQuery(".button-add").click(function() {
            jQuery(this).addClass("loader")
        })
	});
</script>