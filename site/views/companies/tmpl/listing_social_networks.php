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

<?php if(($showData && isset($this->package->features) && in_array(SOCIAL_NETWORKS, $this->package->features) || !$appSettings->enable_packages)
						&& ((!empty($this->company->linkedin) || !empty($this->company->youtube) ||!empty($this->company->facebook) || !empty($this->company->twitter) 
						     || !empty($this->company->linkedin) || !empty($this->company->skype) || !empty($this->company->instagram) || !empty($this->company->pinterest || !empty($this->company->whatsapp))))){ ?> 
	<div id="social-networks-container">
		<ul class="socials-network">
			<?php if(!empty($this->company->facebook)){ ?>
			<li >
				<a title="Follow us on Facebook" target="_blank" class="share-social  la la-facebook-f" href="<?php echo $this->escape($this->company->facebook) ?>"></a>
			</li>
			<?php } ?>
			<?php if(!empty($this->company->twitter)){ ?>
			<li >
				<a title="Follow us on Twitter" target="_blank" class="share-social  la la-twitter" href="<?php echo $this->escape($this->company->twitter) ?>"></a>
			</li>
			<?php } ?>
			<?php if(!empty($this->company->linkedin)){ ?>
			<li >
				<a title="Follow us on LinkedIn" target="_blank" class="share-social  la la-linkedin" href="<?php echo $this->escape($this->company->linkedin)?>"></a>
			</li>
			<?php } ?>
			<?php if(!empty($this->company->skype)){ ?>
			<li >
				<a title="Skype" target="_blank" class="share-social la la-skype" href="skype:<?php echo $this->escape($this->company->skype)?>"></a>
			</li>
			<?php } ?>
			<?php if(!empty($this->company->youtube)){ ?>
			<li >
				<a title="Follow us on YouTube" target="_blank" class="share-social  la la-youtube" href="<?php echo $this->escape($this->company->youtube)?>"></a>
			</li>
			<?php } ?>
			<?php if(!empty($this->company->instagram)){ ?>
			<li >
				<a title="Follow us on Instagram" target="_blank" class="share-social  la la-instagram" href="<?php echo $this->escape($this->company->instagram)?>"></a>
			</li>
			<?php } ?>
			<?php if(!empty($this->company->tiktok)){ ?>
			<li >
				<a title="Follow us on Tiktok" target="_blank" class="share-social la" href="<?php echo $this->escape($this->company->tiktok)?>"><i class="icon tiktok"></i></a>
			</li>
			<?php } ?>
			<?php if(!empty($this->company->pinterest)){ ?>
			<li >
				<a title="Follow us on Pinterest" target="_blank" class="share-social  la la-pinterest" href="<?php echo $this->escape($this->company->pinterest)?>"></a>
			</li>
			<?php } ?>
            <?php if(!empty($this->company->whatsapp)){ ?>
                <li >
                    <a id="whatsapp-link" title="Ping us on WhatsApp" target="_blank" class="share-social la la-whatsapp" href="https://api.whatsapp.com/send?phone=<?php echo intval($this->company->whatsapp) ?>&text=<?php echo JText::_("LNG_HELLO") ?>!"></a>
                </li>
            <?php } ?>
		</ul>
		<div class="clear"></div>
	</div>
<?php } ?>