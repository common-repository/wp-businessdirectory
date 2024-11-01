<?php defined('_JEXEC') or die('Restricted access'); 
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
?>
<div id="jbd-container" class="jbd-container jbd-edit-container">
    <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=updates');?>" method="post" name="adminForm" id="adminForm">
    	<input type="hidden" name="task" value="" />
    	<input type="hidden" name="controller" value="" />
    	<input type="hidden" name="boxchecked" value="1" />
    	
    	<?php echo JHTML::_( 'form.token' ); ?> 
    
    	<fieldset class="boxed">
    		 <h3><?php echo JText::_('LNG_UPDATES',true); ?></h3>
    		 <div class="row">
    		 	<div class="col-md-6">
          		 	<div class="form-group">
                        <label for="name"><?php echo JText::_('LNG_ORDER_ID')?> </label>
                        <input type="text" name="orderId" id="orderId" value="<?php echo isset($this->appSettings->order_id)?$this->appSettings->order_id:""?>">
                        <?php echo JText::_('LNG_ORDER_NOTICE')?>
                    </div>
          		 	<div class="form-group">
                        <label for="name"><?php echo JText::_('LNG_ORDER_EMAIL')?> </label>
                        <input type="text" name="orderEmail" id="orderEmail" value="<?php echo isset($this->appSettings->order_email)?$this->appSettings->order_email:""?>">
                        <?php echo JText::_('LNG_ORDER_EMAIL_NOTICE')?>
                    </div>
                    <div class="form-group">
                    	<?php echo JText::_('LNG_CURRENT_VERSION').' : <b><span class="text-success">'.$this->currentVersion."</span></b>" ?>
                    	<div id="orderData">
			  				<?php echo $this->expirationDate;?>
			  			</div>
                    </div>
    		 	</div>
    		 	<div class="col-md-4 offset-2">
					<div style="border:1px solid #ccc;width:300px;padding:10px;background:#fff"><span class="badge badge-warning rounded-circle"><i class="la la-exclamation"></i></span> <?php echo JText::_('LNG_UPDATE_NOTICE',true)?></div>	 		
    		 	</div>
    		 </div>
    	</fieldset>			
    </form>
</div>