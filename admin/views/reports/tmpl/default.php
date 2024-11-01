<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 
defined('_JEXEC') or die('Restricted access');
function preparequickIconButton($url, $image, $text, $description=null) { ?>
	<li class="option-button">
		<a class="box box-inset" href="<?php  echo $url ?>">
			<?php echo JHTML::_('image',BD_ASSETS_FOLDER_PATH.'images/'.$image, $text); ?>	
			<h3><?php echo $text; ?></h3>
			<p><?php echo $description ?></p>
		</a>
	</li>
<?php } ?>

<div id="jbd-container" class="jbd-container">
    <div id='business-cpanel'>
    	<ul>
    		<?php echo preparequickIconButton( "index.php?option=".JBusinessUtil::getComponentName()."&view=reports&layout=standard", 'settings_48_48_icon.gif', JText::_('LNG_REPORTS_STANDARD') );?>
    		<?php echo preparequickIconButton( "index.php?option=".JBusinessUtil::getComponentName()."&view=reports&layout=statistics", 'settings_48_48_icon.gif', JText::_('LNG_REPORTS_STATISTICS') );?>
    		<?php echo preparequickIconButton( "index.php?option=".JBusinessUtil::getComponentName()."&view=reports&layout=income", 'settings_48_48_icon.gif', JText::_('LNG_REPORTS_INCOME') );?>
    	</ul>
    </div>
    
    <form action="index.php" method="post" name="adminForm" id="adminForm">
    	<input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName()?>"/>
    </form>	
</div>
<script>
	window.addEventListener('load', function() {
		jQuery("#accordion-info").accordion({
			heightStyle: "content"
		});
	});
</script>

<?php echo $this->loadTemplate('export'); ?>
