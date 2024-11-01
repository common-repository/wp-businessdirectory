<?php
$view = JFactory::getApplication()->input->get("view");
?>

<!-- Modal -->
<div id="legend" class="jbd-container" style="display: none">    
    <div class="jmodal-sm">
        <div class="jmodal-header">
            <p class="jmodal-header-title"><?php echo JText::_('LNG_STATUS_MESSAGES_LEGEND'); ?></p>
            <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
        </div>
        <div class="jmodal-body">
			<div class="row">
				<div class="col-12">
					<dl class="dl-horizontal">
						<?php if ($view == "managecompanyeventappointments") {?>
							<dt><span class="status-badge badge-success"><?php echo JText::_('LNG_CONFIRMED'); ?></span></dt>
							<dd><?php echo JText::_('LNG_CONFIRMED_LEGEND'); ?></dd>
							<dt><span class="status-badge badge-primary"><?php echo JText::_('LNG_UNCONFIRMED'); ?></span></dt>
							<dd><?php echo JText::_('LNG_UNCONFIRMED_LEGEND'); ?></dd>
							<dt><span class="status-badge badge-danger"><?php echo JText::_('LNG_CANCELED'); ?></span></dt>
							<dd><?php echo JText::_('LNG_CANCELED_LEGEND'); ?></dd>
						<?php } else if ($view == "managecampaigns") { ?>							
							<dt><span class="status-badge badge-primary"><?php echo JText::_('LNG_ACTIVE'); ?></span></dt>
							<dd><?php echo JText::_('LNG_ACTIVE_LEGEND'); ?></dd>
							<dt><span class="status-badge badge-danger"><?php echo JText::_('LNG_NOT_ACTIVE'); ?></span></dt>
							<dd><?php echo JText::_('LNG_NOT_ACTIVE_LEGEND'); ?></dd>
						<?php } else if ($view == "managelistingrequestquotes" || $view == "managerequestquotes") { ?>							
							<dt><span class="status-badge badge-success"><?php echo JText::_('LNG_HIRED'); ?></span></dt>
							<dd><?php echo JText::_('LNG_HIRED_LEGEND'); ?></dd>
							<dt><span class="status-badge badge-light"><?php echo JText::_('LNG_CLOSED'); ?></span></dt>
							<dd><?php echo JText::_('LNG_CLOSED_LEGEND'); ?></dd>		
							<dt><span class="status-badge badge-info"><?php echo JText::_('LNG_ACTIVE'); ?></span></dt>
							<dd><?php echo JText::_('LNG_ACTIVE_LEGEND'); ?></dd>		
						<?php } else if (in_array($view, array("managecompanyservicereservations", "managecompanyofferorders", "managecompanyeventreservations"))) {?>
							<dt><span class="status-badge badge-primary"><?php echo JText::_('LNG_CREATED'); ?></span></dt>
							<dd><?php echo JText::_('LNG_ORDER_CREATED_LEGEND'); ?></dd>
							<dt><span class="status-badge badge-success"><?php echo JText::_('LNG_CONFIRMED'); ?></span></dt>
							<dd><?php echo JText::_('LNG_ORDER_CONFIRMED_LEGEND'); ?></dd>
							<dt><span class="status-badge badge-primary"><?php echo JText::_('LNG_UNCONFIRMED'); ?></span></dt>
							<dd><?php echo JText::_('LNG_ORDER_UNCONFIRMED_LEGEND'); ?></dd>
							<dt><span class="status-badge badge-success"><?php echo JText::_('LNG_COMPLETED'); ?></span></dt>
							<dd><?php echo JText::_('LNG_ORDER_COMPLETED_LEGEND'); ?></dd>
							<dt><span class="status-badge badge-danger"><?php echo JText::_('LNG_CANCELED'); ?></span></dt>
							<dd><?php echo JText::_('LNG_ORDER_CANCELED_LEGEND'); ?></dd>
						<?php } else { ?>
							<dt><span class="status-badge badge-success"><?php echo JText::_('LNG_PUBLISHED'); ?></span></dt>
							<dd><?php echo JText::_('LNG_PUBLISHED_LEGEND'); ?></dd>
							<dt><span class="status-badge badge-warning warn"><?php echo JText::_('LNG_UNPUBLISHED'); ?></span></dt>
							<dd><?php echo JText::_('LNG_UNPUBLISHED_LEGEND'); ?></dd>
							<dt><span class="status-badge badge-primary"><?php echo JText::_('LNG_CLAIM_PENDING'); ?></span></dt>
							<dd><?php echo JText::_('LNG_CLAIM_PENDING_LEGEND'); ?></dd>
							<dt><span class="status-badge badge-info"><?php echo JText::_('LNG_PENDING'); ?></span></dt>
							<dd><?php echo JText::_('LNG_PENDING_LEGEND'); ?></dd>
							<dt><span class="status-badge badge-warning"><?php echo JText::_('LNG_EXPIRED'); ?></span></dt>
							<dd><?php echo JText::_('LNG_EXPIRED_LEGEND'); ?></dd>
							<dt><span class="status-badge badge-warning warn"><?php echo JText::_('LNG_NOT_INCLUDED'); ?></span></dt>
							<dd><?php echo JText::_('LNG_NOT_INCLUDED_LEGEND'); ?></dd>
							<dt><span class="status-badge badge-warning warn"><?php echo JText::_('LNG_DEACTIVATED'); ?></span></dt>
							<dd><?php echo JText::_('LNG_DEACTIVATED_LEGEND'); ?></dd>
							<dt><span class="status-badge badge-success"><?php echo JText::_('LNG_APPROVED'); ?></span></dt>
							<dd><?php echo JText::_('LNG_APPROVED_LEGEND'); ?></dd>
							<dt><span class="status-badge badge-danger"><?php echo JText::_('LNG_DISAPPROVED'); ?></span></dt>
							<dd><?php echo JText::_('LNG_DISAPPROVED_LEGEND'); ?></dd>
						<?php } ?>
					</dl>
				</div>
			</div>
		</div>
    </div>
</div>

<script>
    window.addEventListener('load', function() {
		jQuery('.status-badge').click(function() {
			jQuery('#legend').jbdModal();

			!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');
		});
	});
</script>