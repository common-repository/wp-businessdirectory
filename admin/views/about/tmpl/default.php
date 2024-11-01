<?php
/**
 * @package    WBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2019 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

?>
<form action="index.php" method="post" name="adminForm">
	<input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName()?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="about" />
	<?php echo JHTML::_( 'form.token' ); ?> 
</form>

	<table class="adminlist">
				<thead>
					<tr>
						<th width="871" nowrap="nowrap"><div align="left"><?php echo JText::_("LNG_ABOUT")?></div></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
						
						<p align="center">
							<?php echo JText::_('LNG_ABOUT_APPLICATION'); ?>
						</p>
						  <p align="center">
							<img 
								src = "<?php echo get_site_url() ."components/".JBusinessUtil::getComponentName()."/assets/img/logo.png"?>"
								alt="image"
							>
								&nbsp;</p>
						  <p>&nbsp;</p></td>
					</tr>				
					<tr>
					  <td>&nbsp;</td>
				  </tr>				
				
				</tbody>
			</table>