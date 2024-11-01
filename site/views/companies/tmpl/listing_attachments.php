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

<div class="attachments">
	<ul>
		<?php foreach($this->company->attachments as $attachment) {?>
				<li>
					<?php if (!empty($attachment)){?>
						<div class="attachment-info">
							<a class="attachment-link" href="<?php echo BD_ATTACHMENT_PATH . $attachment->path ?>" target="_blank">
								<img class="icon" src="<?php echo $attachment->properties->icon; ?>"/>
                                <div class="truncate-text"><?php echo !empty($attachment->name) ? $this->escape($attachment->name) : basename($attachment->path) ?></div>
							</a>
							<div><?php echo "[" . strtolower($attachment->properties->fileProperties['extension']) . ", ".(!empty($attachment->properties->nrPages)?$attachment->properties->nrPages." ".JText::_("LNG_PAGES").", ":"").$attachment->properties->size; ?>] </div>
						</div>
					<?php } ?>
				</li>
		<?php }?>
	</ul>
</div>