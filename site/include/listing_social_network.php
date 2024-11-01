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

<?php if ($showData && (isset($company->packageFeatures) && in_array(SOCIAL_NETWORKS, $company->packageFeatures) || !$appSettings->enable_packages)){ ?>
    <div class="social-networks">
        <?php if(!empty($company->facebook)) { ?>
            <div class="social-network">
                <a title="Facebook"
                        target="_blank" href="<?php echo $this->escape($company->facebook) ?>"><i class="icon facebook-blue"></i></a>
                </div>
        <?php } ?>
        <?php if(!empty($company->twitter)) { ?>
            <div class="social-network">
                <a title="Twitter"
                        target="_blank" href="<?php echo $this->escape($company->facebook) ?>"><i class="icon twitter-blue"></i></a>
                </div>
        <?php } ?>
        <?php if(!empty($company->linkedin)) { ?>
            <div class="social-network">
                <a title="LinkedIn"
                        target="_blank" href="<?php echo $this->escape($company->linkedin) ?>"><i class="icon linkedin-blue"></i></a>
                </div>
        <?php } ?>
        <?php if(!empty($company->skype)) { ?>
            <div class="social-network">
                <a title="Skype"
                        target="_blank" href="<?php echo $this->escape($company->skype) ?>"><i class="icon skype-blue"></i></a>
                </div>
        <?php } ?>
        <?php if(!empty($company->instagram)) { ?>
            <div class="social-network">
                <a title="Instagram"
                        target="_blank" href="<?php echo $this->escape($company->instagram) ?>"><i class="icon instagram-blue"></i></a>
                </div>
        <?php } ?>
        <?php if(!empty($company->pinterest)) { ?>
            <div class="social-network">
                <a title="Pinterest"
                        target="_blank" href="<?php echo $this->escape($company->pinterest) ?>"><i class="icon pinterest-blue"></i></a>
                </div>
        <?php } ?>
        <?php if(!empty($company->whatsapp)) { ?>
            <div class="social-network">
                <a title="WhatsApp"
                        target="_blank" href="<?php echo $this->escape($company->whatsapp) ?>"><i class="icon whatsapp-blue"></i></a>
                </div>
        <?php } ?>
    </div>
<?php } ?>