<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
$type = $this->type;
?>

<div id="terms-conditions">
    <?php if ($type == 'reviews') {
        echo $this->reviewsTermsConditions;
    } elseif ($type == 'contact') {
        echo $this->contactTermsConditions;
    } elseif ($type == 'privacy') {
        echo $this->privacyPolicy;
    } else {
        echo $this->generalTermsConditions; 
    }
    ?>

</div>