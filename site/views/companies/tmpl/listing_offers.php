<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 
defined('_JEXEC') or die('Restricted access');
$offerAttributeConfig = JBusinessUtil::getAttributeConfiguration(DEFAULT_ATTRIBUTE_TYPE_OFFER);
?>
<div class="row">
	<?php
		if(isset($this->offers) && !empty($this->offers)){
		    $index = 0;
			foreach ($this->offers as $offer){
			    $index++;
	?>
    	<div class="col-lg-4 col-sm-6 col-12">
    		<div class="jitem-card">
    			<div class="jitem-img-wrap small">
    				<a href="<?php echo $offer->link ?>"></a>
    				<?php if(!empty($offer->picture_path) ){?>
    					<img title="<?php echo $this->escape($offer->subject) ?>" alt="<?php echo $this->escape($offer->subject) ?>" src="<?php echo BD_PICTURES_PATH.$offer->picture_path ?>" >
    				<?php }else{ ?>
    					<img title="<?php echo $this->escape($offer->subject) ?>" alt="<?php echo $this->escape($offer->subject) ?>" src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>" >
    				<?php } ?>
    			</div>
    			<div class="jitem-body">
    				<div class="jitem-body-content">
        				<div class="jitem-title">
        					<a href="<?php echo  $offer->link ?>"><span ><?php echo $offer->subject?></span></a>
        				</div>
        				<div class="jitem-desc text-small">
                            <?php if (!empty(JBusinessUtil::composeAddress($offer->address, $offer->city))){ ?>
        					    <div><i class="icon map-marker"></i> <?php echo JBusinessUtil::composeAddress($offer->address, $offer->city) ?></div>
        					<?php } ?>

                            <?php if(!JBusinessUtil::emptyDate($offer->startDate) || !JBusinessUtil::emptyDate($offer->endDate)){ ?>
                                <div class="offer-dates">
                                    <i class="icon calendar"></i>
                                    <?php
                                        echo JBusinessUtil::getDateGeneralFormat($offer->startDate);
                                    ?>
                                </div>
                            <?php } ?>

                            <div class="offer-price">
                                <div>
                                    <?php if(!empty($offer->specialPrice)){?>
                                        <span class="price"><?php echo JBusinessUtil::getPriceFormat($offer->specialPrice, $offer->currencyId); ?></span>
                                    <?php }?>

                                    <?php if(!empty($offer->price) ){ ?>
                                        <span class="price <?php echo $offer->specialPrice>0 ?"old-price":"" ?>"><?php echo JBusinessUtil::getPriceFormat($offer->price, $offer->currencyId) ?></span>
                                        <?php if(!empty($offer->specialPrice) && !empty($offer->price) && $offer->specialPrice < $offer->price){ ?>
                                            <span class="discount">(-<?php echo JBusinessUtil::getPriceDiscount($offer->specialPrice, $offer->price) ?>%)</span>	
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                                
                                <?php if ($this->defaultAttributes["price_text"]!=ATTRIBUTE_NOT_SHOW) { ?>
                                    <div class="price-text-list">
                                        <?php if (!empty($offer->price_text)) { ?>
                                            <span class="price-text"><?php echo $offer->price_text ?></span>
                                        <?php }elseif (empty($offer->price) && empty($offer->specialPrice) && ($appSettings->show_offer_free)){ ?>
                                            <span class="price-text"><?php echo JText::_('LNG_FREE') ?></span>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>

                            <?php if($this->appSettings->enable_bookmarks && false) { ?>
                                <?php if(!empty($offer->bookmark)){?>
                                    <a href="javascript:jbdUtils.showUpdateBookmarkDialog(<?php echo $user->ID==0?"1":"0"?>, <?php echo $offer->id ?>')"  title="<?php echo JText::_("LNG_UPDATE_BOOKMARK")?>" class="bookmark right"><i class="la la-heart"></i></a>
                                <?php }else{?>
                                    <a href="javascript:jbdUtils.addBookmark(<?php echo $user->ID==0?"1":"0"?>, 'add-bookmark-offer-<?php echo $offer->id ?>')" title="<?php echo JText::_("LNG_ADD_BOOKMARK")?>" class="bookmark right"><i class="la la-heart-o"></i></a>
                                <?php } ?>
                            <?php } ?>
        				</div>
        			</div>
        		</div>
    		</div>
    	</div>
        <?php if($user->ID>0 && false){?>
            <div id="add-bookmark-offer-<?php echo $offer->id ?>" class="jbd-container" style="display: none">    
                <div class="jmodal-sm">
                    <div class="jmodal-header">
                        <p class="jmodal-header-title"><?php echo JText::_('LNG_ADD_BOOKMARK') ?></p>
                        <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
                    </div>
                    <div class="jmodal-body">
                    <form id="bookmarkFrm" name="bookmarkFrm" action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory') ?>" method="post">
                                <div class="review-repsonse">
                                    <fieldset>
                                        <div class="form-item">
                                            <label><?php echo JText::_('LNG_NOTE')?>:</label>
                                            <div class="outer_input">
                                                <textarea rows="5" name="note" id="note" cols="50" ></textarea><br>
                                            </div>
                                        </div>
    
                                        <div class="clearfix clear-left">
                                            <div class="button-row ">
                                                <button type="submit" class="btn btn-success">
                                                    <span class="ui-button-text"><?php echo JText::_("LNG_ADD")?></span>
                                                </button>
                                                <button type="button" class="btn btn-dark" onclick="jQuery.jbdModal.close()">
                                                    <span class="ui-button-text"><?php echo JText::_("LNG_CANCEL")?></span>
                                                </button>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
    
                            <?php echo JHTML::_( 'form.token' ); ?>
                            <input type='hidden' name='task' value='offer.addBookmark'/>
                            <input type='hidden' name='user_id' value='<?php echo $user->ID?>'/>
                            <input type='hidden' name='item_type' value='<?php echo BOOKMARK_TYPE_OFFER ?>'/>
                            <input type='hidden' name='item_link' value='<?php echo JBusinessUtil::getCompanyLink($this->company) ?>'/>
                            <input type="hidden" name='item_id' value="<?php echo $offer->id?>" />
                    </form>         
                </div>
            </div>
        <?php } ?>
    
        <?php if($user->ID>0 && false){?>
            <div id="update-bookmark-offer-<?php echo $offer->id ?>" class="jbd-container" style="display: none">    
                <div class="jmodal-sm">
                    <div class="jmodal-header">
                        <p class="jmodal-header-title"><?php echo JText::_('LNG_UPDATE_BOOKMARK') ?></p>
                        <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
                    </div>
                    <div class="jmodal-body">
                    <form id="updateBookmarkFrm" name="bookmarkFrm" action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory') ?>" method="post">
                                <div class="review-repsonse">
                                    <fieldset>
                                        <div class="form-item">
                                            <a href="javascript:jbdUtils.removeBookmark('offer')" class="red"> <?php echo JText::_("LNG_REMOVE_BOOKMARK")?></a>
                                        </div>
                                        <div class="form-item">
                                            <label><?php echo JText::_('LNG_NOTE')?>:</label>
                                            <div class="outer_input">
                                                <textarea rows="5" name="note" id="note" cols="50" ><?php echo isset($offer->bookmark)?$offer->bookmark->note:"" ?></textarea>
                                            </div>
                                        </div>
    
                                        <div class="clearfix clear-left">
                                            <div class="button-row ">
                                                <button type="submit" class="btn">
                                                    <span class="ui-button-text"><?php echo JText::_("LNG_UPDATE")?></span>
                                                </button>
                                                <button type="button" class="btn btn-dark" onclick="jQuery.jbdModal.close()">
                                                    <span class="ui-button-text"><?php echo JText::_("LNG_CANCEL")?></span>
                                                </button>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
    
                        <?php echo JHTML::_( 'form.token' ); ?>
                        <input type='hidden' id="task" name='task' value='offer.updateBookmark'/>
                        <input type='hidden' name='id' value='<?php echo $offer->bookmark->id ?>'/>
                        <input type='hidden' name='user_id' value='<?php echo $user->ID?>'/>
                        <input type='hidden' name='item_type' value='<?php echo BOOKMARK_TYPE_OFFER ?>'/>
                        <input type='hidden' name='item_link' value='<?php echo JBusinessUtil::getCompanyLink($this->company) ?>'/>
                        <input type="hidden" name="item_id" value="<?php echo $offer->id?>" />
                    </form>      
                </div>
            </div>
        <?php } ?>
   	 <?php } ?>
    <?php } ?>
</div>