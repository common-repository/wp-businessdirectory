<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
?>

<div class="business-hours">
	<fieldset class="fieldset-business_hours">
		<div>
			<div class="small right"><?php echo JText::_('LNG_GMT')." ".$this->company->time_zone  ;  ?></div>
			<strong><i class="la la-clock-o"></i> <?php echo JText::_('LNG_TRAIL_WEEK_HOURS')?></strong>
            <?php 
            $range = JBusinessUtil::processDateRange($appSettings->trail_weeks_dates);
            $dates = JBusinessUtil::getAllDatesInInterval($range->startDate, $range->endDate);
            foreach ($dates as $index => $date) {
                if (isset($company->trailHours[$date])) {
                $day = $company->trailHours[$date];
                ?>
				<div class="business-hour" itemprop="openingHours">
                    <div class="day" style="width: 55%;"><?php echo date('D', strtotime($date)).' '.JBusinessUtil::getDateGeneralFormat($date) ?></div>
                    <div class="business-hour-time">
                        <?php if ($day->status) { ?>
                            <div class="business-hours-wrap">
                                <span class="start">
                                    <?php echo JBusinessUtil::convertTimeToFormat($day->startHour) ?>
                                </span>
                                <?php if($day->breakStatus) { ?>
                                        <span class="end">
                                            - <?php echo JBusinessUtil::convertTimeToFormat($day->breakStartHour) ?>
                                        </span>
                                    </div>
                                    <div class="business-hours-wrap">
                                        <span class="start">
                                            <?php echo JBusinessUtil::convertTimeToFormat($day->breakEndHour) ?>
                                        </span>
                                <?php } ?>
                                <span class="end">
                                     - <?php echo JBusinessUtil::convertTimeToFormat($day->endHour) ?>
                                </span>
                            </div>
                        <?php } else { ?>
                            <span class="end"><?php echo JText::_('LNG_CLOSED'); ?></span>
                        <?php } ?>
                    </div>
                </div>
			    <?php } ?>
			<?php } ?>
		</div>

		<?php if(!empty($this->company->trail_weeks_address)){ ?>
			<div style="display: block" class="business-notes"><b><?php echo JText::_('LNG_ADDRESS') ?>:</b> <?php echo $this->escape($this->company->trail_weeks_address) ?></div>
		<?php } ?>
	</fieldset>
</div>
