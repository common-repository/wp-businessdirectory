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

<div class="row">
    <?php
    if(!empty($this->associatedCompanies) && $appSettings->enable_linked_listings){
        $index = 0;
        foreach ($this->associatedCompanies as $company){
            $index++;
            ?>
            <div class="col-lg-4 col-sm-6 col-12">
                <div class="card place-card">
                    <div class="place-card-body">
                        <a <?php echo $this->newTab; ?> href="<?php echo JBusinessUtil::getCompanyLink($company) ?>"></a>
				        <?php if(!empty($company->logoLocation) ){?>
                            <img title="<?php echo $company->name?>" alt="<?php echo $company->name?>" src="<?php echo BD_PICTURES_PATH.$company->logoLocation ?>" >
				        <?php }else{ ?>
                            <img title="<?php echo $company->name?>" alt="<?php echo $company->name?>" src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>" >
				        <?php } ?>
                        <div class="card-hoverable">
                        </div>
                    </div>
                    <div class="place-card-info">
                        <div class="place-card-info-title">
                            <a class="item-title" <?php echo $this->newTab; ?> href="<?php echo JBusinessUtil::getCompanyLink($company) ?>"><span ><?php echo $company->name?></span></a>
					        <?php if(!empty($company->city) || !empty($company->county)){?>
                                <div><i class="icon map-marker"></i> <?php echo $company->city ?>, <?php echo $company->county?></div>
					        <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
  	  <?php } ?> 
    <?php } ?>
   
</div>



