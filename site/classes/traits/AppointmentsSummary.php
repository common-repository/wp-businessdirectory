<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

trait AppointmentsSummary
{
	/**
	 * Create the summary for the service that has been booked
	 *
	 * @param $serviceDetails object containing the booked service details
	 *
	 * @return string
	 * @since 5.0.0
	 */
	public static function getServiceSummary($serviceDetails) {
		ob_start();
	?>

		<div class="order-items pl-1">
			<div class="">
				<?php echo JText::_('LNG_BOOKING_DETAILS'); ?>
			</div>

			<?php if(!empty($serviceDetails->price)){ ?>
				<div class="order-price">
					<?php echo JBusinessUtil::getPriceFormat($serviceDetails->price, $serviceDetails->currency_id); ?>
				</div>
			<?php } ?>

			<div class="order-details">
				<?php echo JText::_("LNG_Service") ?>
			</div>

			<div class="order-service">
				<?php echo $serviceDetails->serviceName; ?>
			</div>
			
			<div class="order-section">
				<div class="order-item-title"> <i class="la la-calendar"></i> <?php echo JBusinessUtil::getDateGeneralFormat($serviceDetails->date) ?> </div>
			</div>

			<div class="order-section">
				<div class="order-item-title"> <i class="la la-clock-o"></i> <?php echo JBusinessUtil::convertTimeToFormat($serviceDetails->hour) ?></div>
			</div>

			<div class="order-section">
				<div class="order-item-title"> <i class="la la-hourglass"></i> <?php echo JBusinessUtil::formatTimePeriod($serviceDetails->duration, 1) ?></div>
			</div>

			<div class="order-item-section">
				<?php if(!empty($serviceDetails->image)){ ?>
					<div class="order-item-image">
						<img class="w-100" src="<?php echo BD_PICTURES_PATH.$serviceDetails->image ?>" />
					</div>
				<?php } ?>
				<div class="order-item-cnt">
					<div class="order-details">
						<?php echo JText::_("LNG_PROVIDED_BY") ?>
					</div>
					<div class="order-service">
						<?php echo $serviceDetails->providerName; ?>
					</div>
					<div class="order-details">
						<?php echo $serviceDetails->companyName ?>
					</div>
					
				</div>
			</div>

			<div class="order-section">
				<div class="order-item-title"><i class="la la-map-marker"></i> <?php echo JBusinessUtil::getAddressText($serviceDetails) ?></div>
			</div>
			
			<?php if(!empty($serviceDetails->phone)){?>
				<div class="order-section">
					<div class="order-item-title"><i class="la la-phone"></i> <?php echo $serviceDetails->phone ?> </div>
				</div>
			<?php } ?>

			<div class="order-spacer">

			</div>

			<div class="order-section">
				<div class="order-item-title"><?php echo $serviceDetails->serviceName ?></div>
				<div class="order-item-desc"><?php echo JBusinessUtil::getPriceFormat($serviceDetails->price, $serviceDetails->currency_id); ?></div>
			</div>

			<?php if ($serviceDetails->vat_amount > 0) { ?>
				<div class="order-section">
					<div class="order-item-title"><?php echo JText::_("LNG_VAT") ?> <span class="text-small">(<?php echo $serviceDetails->vat . "%" ?>)</span></div>
					<div class="order-item-desc"><?php echo JBusinessUtil::getPriceFormat($serviceDetails->vat_amount, $serviceDetails->currency_id) ?></div>
				</div>
			<?php } ?>

			<?php
				if (!empty($serviceDetails->taxes)) {
					foreach ($serviceDetails->taxes as $tax) {
			?>
					<div class="order-section">
						<div class="order-item-title"><?php echo $tax->tax_name ?> <span class="text-small"><?php echo ($tax->tax_type == 2) ? "( " . $tax->tax_amount . " %)" : "" ?></span></div>
						<div class="order-item-desc"><?php echo JBusinessUtil::getPriceFormat($tax->tax_calc_amount, $serviceDetails->currency_id) ?></div>
					</div>
				<?php } ?>
			<?php } ?>

			<div class="order-section">
				<div class="order-item-title"><?php echo JText::_("LNG_TOTAL") ?></div>
				<div class="order-item-desc"><?php echo JBusinessUtil::getPriceFormat($serviceDetails->amount, $serviceDetails->currency_id) ?></div>
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