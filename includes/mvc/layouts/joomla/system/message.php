<?php
/**
 * @package     JBD.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

$msgList = $displayData['msgList'];

?>
<div class="jbd-container">
	<div id="system-message-container">
		<?php if (is_array($msgList) && !empty($msgList)) : ?>
			<div id="system-message">
				<?php foreach ($msgList as $type => $msgs) : ?>
					<div class="alert alert-<?php echo $type; ?>">

						<?php if (!empty($msgs)) : ?>
							<h4 class="alert-heading"><?php echo JText::_($type); ?></h4>
							<div>
								<?php foreach ($msgs as $msg) : ?>
									<div class="alert-content"><?php echo $msg; ?></div>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</div>