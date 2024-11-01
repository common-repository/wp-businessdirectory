
<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

JBusinessUtil::checkPermissions("directory.access.payment.config", "managepaymentprocessor");
$isProfile = true;
?>
<script>
    var isProfile = true;
</script>
<div class="jbd-front-end">
	<?php include(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'paymentprocessor'.DS.'tmpl'.DS.'edit.php'); ?>
</div>
<div class="clear"></div>
