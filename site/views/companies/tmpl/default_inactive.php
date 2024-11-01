<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 
defined('_JEXEC') or die('Restricted access');
require_once 'header.php';
?>

<div class="item-inactive">
	<img src="<?php echo BD_PICTURES_PATH.'/item-disabled.svg' ?>">
	<div class="title"><?php echo isset($this->company->name) ? $this->escape($this->company->name) : "" ; ?></div>
	<p><?php echo JText::_("LNG_COMPANY_INACTIVE")?></p>
	<a class="btn btn-primary" href="<?php echo JBusinessUtil::getWebsiteURL(true) ?>"><i class="la la-arrow-left"></i> <?php echo JText::_('LNG_GO_TO_HOMEPAGE'); ?></a>
</div>
