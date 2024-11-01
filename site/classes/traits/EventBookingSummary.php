<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

trait EventBookingSummary
{
	/**
	 * Create the booking details summary
	 *
	 * @param $bookingDetails object
	 *
	 * @return string
	 * @since 5.0.0
	 */
	public static function getBookingSummary($bookingDetails) {

		ob_start();
		?>
			<div class="order-items">
				
				<div class="order-item-section">
					<?php if(!empty($bookingDetails->event->pictures)){ ?>
						<div class="order-item-image">
							<img class="w-100" src="<?php echo BD_PICTURES_PATH . $bookingDetails->event->pictures[0]->picture_path  ?>" />
						</div>
					<?php } ?>
					<div class="order-item-cnt">
						<div class="order-service">
							<?php echo $bookingDetails->event->name; ?>
						</div>
						<div class="order-details">
							<?php echo JText::_("LNG_PRICE") ?>
						</div>
						<?php if(!empty($bookingDetails->amount)){ ?>
							<div class="order-price">
								<?php echo JBusinessUtil::getPriceFormat($bookingDetails->amount, $bookingDetails->currency_id); ?>
							</div>
						<?php } ?>
					</div>
				</div>

				<div class="order-section">
					<div class="order-item-title"> <i class="la la-calendar"></i> <?php echo JBusinessUtil::getDateGeneralFormat($bookingDetails->event->start_date) ?> </div>
				</div>
	
				<div class="order-section">
					<div class="order-item-title"> <i class="la la-clock-o"></i> <?php echo JBusinessUtil::convertTimeToFormat($bookingDetails->event->start_time) ?></div>
				</div>
	
				<div class="order-section">
					<div class="order-item-title"><i class="la la-map-marker"></i> <?php echo JBusinessUtil::getAddressText($bookingDetails->event) ?></div>
				</div>
				
				<div class="order-section">
					<div class="order-item-title"><i class="la la-phone"></i> <?php echo $bookingDetails->event->contact_phone ?> </div>
				</div>

				<div class="order-section">
					<div class="order-item-title"><i class="la la-envelope"></i> <?php echo $bookingDetails->event->contact_email ?> </div>
				</div>
	
				<div class="order-spacer">
				</div>
	
				<div class="order-section">
					<div class="order-item-title"><?php echo JText::_("LNG_TICKETS_INFO") ?> </div>
				</div>

				<?php foreach ($bookingDetails->tickets as $ticket) { ?>
					<div class="order-section">
						<div class="order-item-title"><?php echo $ticket->ticket_quantity . " x " . $ticket->name ?></div>
						<div class="order-item-desc"><?php echo JBusinessUtil::getPriceFormat($ticket->price * $ticket->ticket_quantity, $bookingDetails->event->currency_id) ?></div>
					</div>
				<?php } ?>

				<div class="order-section">
						<div class="order-item-title"><?php echo JText::_("LNG_SUBTOTAL") ?></div>
						<div class="order-item-desc"><?php echo JBusinessUtil::getPriceFormat($bookingDetails->initial_amount, $bookingDetails->currency_id) ?></div>
					</div>

				<?php if ($bookingDetails->vat_amount > 0) { ?>
					<div class="order-section">
						<div class="order-item-title"><?php echo JText::_("LNG_VAT") ?> <span class="text-small">(<?php echo $bookingDetails->vat . "%" ?>)</span></div>
						<div class="order-item-desc"><?php echo JBusinessUtil::getPriceFormat($bookingDetails->vat_amount, $bookingDetails->currency_id) ?></div>
					</div>
				<?php } ?>
	
				<?php
					if (!empty($bookingDetails->taxes)) {
						foreach ($bookingDetails->taxes as $tax) {
				?>
						<div class="order-section">
							<div class="order-item-title"><?php echo $tax->tax_name ?> <span class="text-small"><?php echo ($tax->tax_type == 2) ? "( " . $tax->tax_amount . " %)" : "" ?></span></div>
							<div class="order-item-desc"><?php echo JBusinessUtil::getPriceFormat($tax->tax_calc_amount, $bookingDetails->currency_id) ?></div>
						</div>
					<?php } ?>
				<?php } ?>
			
				<div class="order-section">
					<div class="order-item-title"><?php echo JText::_("LNG_TOTAL") ?></div>
					<div class="order-item-desc"><?php echo JBusinessUtil::getPriceFormat($bookingDetails->amount, $bookingDetails->currency_id) ?></div>
				</div>
	
			</div>
	
		<?php

		$result = ob_get_contents();
		ob_end_clean();
	
		return $result;
	}

	/**
	 * Create the buyer details summary
	 *
	 * @param $buyerDetails object containing the buyer details
	 *
	 * @return string
	 * @since 5.0.0
	 */
	public static function getBuyerDetailsSummary($buyerDetails) {
		ob_start();

	?>
		<div class="billing-details">
			<div class="title"><?php echo JText::_("LNG_BUYER_DETAILS") ?></div>
			<div class="detail-spacer"></div>
			<div class="billing-item">
				<div class="billing-item-title"><?php echo JText::_("LNG_FULL_NAME") ?></div>
				<div class="billing-item-desc"><?php echo $buyerDetails->first_name . " " . $buyerDetails->last_name ?></div>
			</div>
			<div class="billing-item">
				<div class="billing-item-title"><?php echo JText::_("LNG_ADDRESS") ?></div>
				<div class="billing-item-desc">
					<?php echo JBusinessUtil::getAddressText($buyerDetails) ?>
				</div>
			</div>
			<div class="billing-item">
				<div class="billing-item-title"><?php echo JText::_("LNG_EMAIL") ?></div>
				<div class="billing-item-desc">
					<?php echo $buyerDetails->email ?>
				</div>
			</div>
			<div class="billing-item">
				<div class="billing-item-title"><?php echo JText::_("LNG_PHONE") ?></div>
				<div class="billing-item-desc"><?php echo $buyerDetails->phone ?></div>
			</div>
		</div>
		<?php
		
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}
}