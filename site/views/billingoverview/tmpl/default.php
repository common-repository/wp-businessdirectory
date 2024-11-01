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

$menuItemId = JBusinessUtil::getActiveMenuItem();

$user = JBusinessUtil::getUser();
if($user->ID == 0){
	$app = JFactory::getApplication();
	$return = 'index.php?option=com_jbusinessdirectory&view=billingoverview';
    $app->redirect(JBusinessUtil::getLoginUrl($return, false));
}
?>
<style>
	.pdf-download-btn:hover i {
		filter: brightness(0) invert(1);
	}
</style>
<?php
	if(empty($this->items)){
		echo JBusinessUtil::getNoItemMessageBlock(JText::_("LNG_ORDER"),JText::_("LNG_ORDERS"));
		return;
	}
?>

<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=billingoverview');?>" method="post" name="adminForm" id="adminForm">
	<div id="billing-overview">
		<?php foreach($this->items as $item){?>
			<?php $counter = 0; ?>
			<?php $showMore = false; ?>
			<div class="billing-item js-billing-<?php echo $item->business_id ?>">
				<div class="row">
					<div class="col-12">
						<div class="item-name"><?php echo $item->business_name ?></div>
					</div>
				</div>
				<div class="row mb-5">
					<div class="col-md-6 col-12">
						<div class="jitem-card card-plain card-round horizontal">
							<div class="jitem-wrapper">
								<div class="jitem-section">
									<div class="jitem-title text-large">
										<?php echo $item->package_name ?>
									</div>
									<div class="jitem-subtitle">
										<?php echo JBusinessUtil::getPriceFormat($item->package_price) ?> / <?php echo $item->package_info ?>
									</div>
								</div>
								<div class="jitem-section" style="display:none">
									<a class="btn btn-sm btn-primary" href="#"><?php echo JText::_("LNG_UPGRADE") ?></a>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-6 col-12">
						<div class="jitem-card card-plain card-round horizontal">
							<div class="jitem-wrapper">
								<div class="jitem-section">
									<div class="jitem-title text-large">
										<?php echo JText::_("LNG_NEXT_PAYMENT"); ?>
									</div>
									<div class="jitem-subtitle">
										<?php echo JBusinessUtil::getDateGeneralShortFormat($item->next_payment_date) ?>
									</div>
								</div>
								<div class="jitem-section" style="display:none">
									<a class="btn btn-sm btn-primary" href="#"><?php echo JText::_("LNG_UPGRADE") ?></a>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row billing-orders">
					<?php foreach($item->orders as $order){?>
						<div class="col-md-6 col-12">
							<div class="jitem-card card-plain card-round horizontal">
								<div class="jitem-wrapper">
									<div class="">
										<div class="jitem-title">
											<div><?php echo ($order->order_id) ?></div>
										</div>
										<div class="jitem-subtitle">
											<?php echo JBusinessUtil::getPriceFormat($order->amount) ?> | <?php echo JBUsinessUtil::getDateGeneralShortFormat($order->created) ?>
										</div>
									</div>
									<div class="jitem-section justify-content-end pl-md-3">
										<?php if($order->state == 0){?>
											<button type="button" class="btn btn-success btn-sm" onclick="payOrder(<?php echo $order->id ?>, <?php echo $order->company_id ?>)">
												<?php echo JText::_("LNG_PAY")?>
											</button>
										<?php }?>
										<a href='<?php echo JBusinessUtil::getWebsiteUrl(true).( 'index.php?option=com_jbusinessdirectory&task=orders.generateInvoicePDF&invoiceId='. $order->id .'&'.JSession::getFormToken().'=1' )?>'
											title='<?php echo JText::_('LNG_DOWNLOAD'); ?>'
											class="btn btn-outline-primary btn-sm pdf-download-btn ml-1"
											target="_blank">
											<i class="icon pdf-download"></i>
                                        </a>
										<button type="button" class="btn btn-primary btn-sm" onclick="showInvoice(<?php echo $order->id; ?>)">
											<?php echo JText::_("LNG_VIEW")?>
										</button>
									</div>
								</div>
							</div>
						</div>
						<?php $counter ++; ?>
						
						<?php if($counter == 4){ ?>
							<?php $showMore = true; ?>
							</div>
							<div class="row more-orders js-more-orders-<?php echo $item->business_id?>">
						<?php } ?>

					<?php } ?>
				</div>

				<?php if($showMore){ ?>
					<div class="show-more show-more-ordes text-center" data-id="<?php echo $item->business_id?>">
						<?php echo JText::_("LNG_VIEW_MORE") ?>
					</div>
					<div class="show-more show-less-ordes text-center" data-id="<?php echo $item->business_id?>">
						<?php echo JText::_("LNG_VIEW_LESS") ?>
					</div>
				<?php } ?>
			</div>
		<?php } ?>
	</div>
	<?php echo JHTML::_('form.token'); ?>
</form>

<form id="payment-form" name="payment-form" method="post" action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=billingdetails.checkBillingDetails'.$menuItemId) ?>">
	<input type="hidden" name="orderId" id="orderId" value="" />
    <input type="hidden" name="companyId" id="companyId" value="" />
</form>

<div id="invoice" class="jbd-container" style="display: none">    
    <div class="jmodal-md">
        <div class="jmodal-header">
            <p class="jmodal-header-title"><?php echo JText::_('LNG_INVOICE') ?></p>
            <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
        </div>
        <div class="jmodal-body">
			<iframe id="invoiceIfr" src="about:blank" scrolling="no" onload="jbdUtils.resizeIframe(this)">
			</iframe>
		</div>            
    </div>
</div>



<script>
	// starting the script on page load
	window.addEventListener('load', function(){
		jQuery(".show-more").click(function(){
			$id = jQuery(this).attr("data-id");
			jQuery(".js-billing-"+$id).toggleClass("open");
		});
	});		
	
	function showInvoice(invoice){
		var baseUrl = "<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=invoice&tmpl=component'); ?>";
		baseUrl = baseUrl + "&invoiceId="+invoice;
		jQuery("#invoiceIfr").attr("src",baseUrl);

		var options = {
         	    modalClass: "jbd-modal jbd-invoice"
        }
		jQuery('#invoice').jbdModal(options);
	}

	function payOrder(orderId,companyId){
		jQuery("#orderId").val(orderId);
        jQuery("#companyId").val(companyId);
		jQuery("#payment-form").submit();
	}
</script>