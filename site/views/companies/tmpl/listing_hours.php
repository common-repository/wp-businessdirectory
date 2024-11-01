<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 
defined('_JEXEC') or die('Restricted access');
?>

<div class="business-hours">
	<fieldset class="fieldset-business_hours">
		<div>
			<strong><i class="icon clock"></i> <?php echo JText::_('LNG_OPENING_HOURS')?></strong>
            <?php if($this->company->opening_status == COMPANY_OPEN_BY_TIMETABLE){ ?>
                <div class="small right"><?php echo JText::_('LNG_GMT')." ".$this->company->time_zone  ;  ?></div>
			    
                <?php 
                    foreach($this->company->business_hours as $index => $day) { 
                        echo JBusinessUtil::renderOpeningDay($day);
                    }
                ?>
                
            <?php } else {
                $statusInfo = JBusinessUtil::getOpeningStatus($this->company->opening_status);
                echo '<div class="ml-3 font-weight-bold my-2 badge badge-'.$statusInfo->class.'">'.$statusInfo->status.'</div>';
             }?>
		</div>

		<?php if(!empty($this->company->notes_hours)){ ?>
			<div style="display: block" class="business-notes"><?php echo $this->escape($this->company->notes_hours) ?></div>
		<?php } ?>
	</fieldset>
</div>
