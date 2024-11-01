<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 
defined('_JEXEC') or die('Restricted access');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
$path = JPATH_COMPONENT_SITE .DS.'assets'.DS. 'css' . DS . 'custom.css';
?>

<div id="jbd-container" class="jbd-container">
	<fieldset class='adminform'>
		<legend><?php echo JText::_('LNG_CUSTOM_CSS',true) ?></legend>		
			<div id="customcss-content">
				<fieldset class="adminform">
					<textarea rows="50" name="css-content" id="css-content" style="width:100%;max-width:100%;" class="pt-2 mt-2" placeholder="Place your css code here"><?php
						if(file_exists($path)){
							echo $this->cssFile->content;
						}
						?></textarea>
				</fieldset>
				<?php echo JHTML::_( 'form.token' ); ?>
			</div>		
	</fieldset>
</div>