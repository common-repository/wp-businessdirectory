<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

trait TripBookingSummary
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
					<?php if(!empty($bookingDetails->trip->pictures)){ ?>
						<div class="order-item-image">
							<img class="w-100" src="<?php echo BD_PICTURES_PATH . $bookingDetails->trip->pictures[0]->picture_path  ?>" />
						</div>
					<?php } ?>
					<div class="order-item-cnt">
						<div class="order-service">
							<?php echo $bookingDetails->trip->name; ?>
						</div>
					</div>
				</div>

				<div class="order-section">
					<div class="order-item-title"> <i class="la la-calendar"></i> <?php echo JBusinessUtil::getDateGeneralFormat($bookingDetails->trip_date) ?> </div>
				</div>
	
				<div class="order-section">
					<div class="order-item-title"> <i class="la la-clock-o"></i> <?php echo JBusinessUtil::convertTimeToFormat($bookingDetails->trip_time) ?></div>
				</div>
                <div class="order-section">
					<div class="order-item-desc"> <?php echo JText::_('LNG_CONTACT_DETAILS') ?> </div>
				</div>
				<div class="order-section">
					<div class="order-item-title"><i class="la la-user"></i> <?php echo $bookingDetails->trip->organizer ?> </div>
				</div>
                <div class="order-section">
					<div class="order-item-title"><i class="la la-phone"></i> <?php echo $bookingDetails->trip->phone ?> </div>
				</div>

				<div class="order-section">
					<div class="order-item-title"><i class="la la-envelope"></i> <?php echo $bookingDetails->trip->email ?> </div>
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