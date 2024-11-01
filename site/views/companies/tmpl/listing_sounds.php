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

<div class='sounds-container'>
	<?php if(!empty($this->sounds)){ ?>
		<div class="row">
			<?php foreach( $this->sounds as $sound ){
				if(!empty($sound->iframe)) { ?>
					<div class="col-md-6">
						<?php echo ($sound->iframe) ?>
					</div>
				<?php } ?>
			<?php } ?>
		</div>
	<?php } ?>
</div>