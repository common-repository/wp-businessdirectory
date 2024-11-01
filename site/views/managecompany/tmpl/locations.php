<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 
defined('_JEXEC') or die('Restricted access');

$isProfile = true;

?>
<style>
.alert, .alert h4 {
    color: #C09853;
}


.alert h4 {
    margin: 0;
}
p {
    margin: 0 0 10px;
}

.alert {
    background-color: #FCF8E3;
    border: 1px solid #FBEED5;
    border-radius: 4px;
    margin-bottom: 20px;
    padding: 8px 35px 8px 14px;
    text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
}

.alert .close {
    line-height: 20px;
    position: relative;
    right: -21px;
    top: -2px;
}

.close {
    color: #000000;
    float: right;
    font-size: 20px;
    font-weight: bold;
    line-height: 20px;
    opacity: 0.2;
    text-shadow: 0 1px 0 #FFFFFF;
}
</style>


<?php

include(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'company'.DS.'tmpl'.DS.'locations.php');
?>
<script>
	var isProfile = true;
</script>
