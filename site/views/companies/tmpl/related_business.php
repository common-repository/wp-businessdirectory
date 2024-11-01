<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
$newTab = ($this->appSettings->open_listing_on_new_tab)?" target='_blank'":"";
?>

<?php if(isset($this->realtedCompanies) && count($this->realtedCompanies)){ ?>
	<div class='row'>
    	<?php
            $index = 0;
            foreach ($this->realtedCompanies as $rCompany){
                $index++;
        ?>
        <div class="col-lg-4 col-sm-6 col-12">
    		<div class="card place-card">
    			<div class="place-card-body">
    				 <a <?php echo $newTab; ?> href="<?php echo JBusinessUtil::getCompanyLink($rCompany) ?>"></a>
    				 <?php if(!empty($rCompany->logoLocation) ){?>
                                <img title="<?php echo $rCompany->name?>" alt="<?php echo $rCompany->name?>" src="<?php echo BD_PICTURES_PATH.$rCompany->logoLocation ?>" >
                            <?php }else{ ?>
                                <img title="<?php echo $rCompany->name?>" alt="<?php echo $rCompany->name?>" src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>" >
                            <?php } ?>
    				<div class="card-hoverable">
    				</div>
    			</div>
    			<div class="place-card-info">
    				<div class="place-card-info-title">
    					<a class="item-title" <?php echo $newTab; ?> href="<?php echo JBusinessUtil::getCompanyLink($rCompany) ?>"><span ><?php echo $rCompany->name?></span></a>
    					<?php if(!empty($rCompany->city) || !empty($rCompany->county)){?>
    						<div><i class="icon map-marker"></i> <?php echo $rCompany->city ?>, <?php echo $rCompany->county?></div>
    					<?php } ?>
    				</div>
    			</div>
    		</div>
    	</div>
    <?php } ?>
	</div>
<?php } ?>


